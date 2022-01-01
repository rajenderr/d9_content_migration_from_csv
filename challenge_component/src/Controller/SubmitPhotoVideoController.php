<?php

namespace Drupal\challenge_component\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Database\Connection;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;
use \Drupal\node\Entity\Node;

/**
 * SubmitPhotoVideoController class.
 */
class SubmitPhotoVideoController extends ControllerBase {

  /**
   * @var \Drupal\Core\Database\Connection
   */
  private $database;

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilder
   */
  protected $formBuilder;

  /**
   * The SubmitPhotoVideoController constructor.
   *
   * @param \Drupal\Core\Form\FormBuilder $formBuilder
   *   The form builder.
   */
  public function __construct(FormBuilder $formBuilder, Connection $database) {
    $this->formBuilder = $formBuilder;
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The Drupal service container.
   *
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder'),
      $container->get('database')
    );
  }

  /**
   * Callback for opening the modal form.
   */
  public function openSelectModalForm() {
    $response = new AjaxResponse();
    $modal_form = $this->formBuilder->getForm('Drupal\challenge_component\Form\SubmitPhotoVideoForm');
    $response->addCommand(new OpenModalDialogCommand('Type of Submission', $modal_form, ['width' => '800']));
    return $response;
  }

  /**
   * Callback for opening the modal form.
   */
  public function openSelectModalImageVideoForm() {
    $response = new AjaxResponse();
    $modal_form = $this->formBuilder->getForm('Drupal\challenge_component\Form\SubmitPhotoOrVideoChoiceForm');
    $response->addCommand(new OpenModalDialogCommand('Type of Submission', $modal_form, ['width' => '800']));
    return $response;
  }

  /**
   * Callback for opening the modal form.
   */
  public function loadMoreGalleryItems(Request $response) {
    $nid = $_REQUEST['nid'];
    $existingCount = $_REQUEST['existingCount'];
    $current_items = $_REQUEST['currentItems'];
    $inital_count = $_REQUEST['initialCount'];
    $limit = $inital_count + $current_items;
    $select = $this->database->select('node_field_data', 'nd');
    $select->join('node__field_add_component', 'ac', 'nd.nid = ac.entity_id');
    $select->join('paragraph__field_gallery_items', 'gi', 'ac.field_add_component_target_id = gi.entity_id');
    $select->condition('nd.type', "pro", "=");
    $select->condition('nd.nid', $nid, "=");
    $select->condition('nd.status', 1, "=");
    $select->fields('nd', ['nid', 'title']);
    $select->fields('ac', ['field_add_component_target_id']);
    $select->fields('gi', ['field_gallery_items_target_id']);
    $select->orderBy('gi.field_gallery_items_target_id', 'DESC');
    $entries = $select->execute()->fetchAll();
    
    $entries = \Drupal::entityQuery('media')
        ->condition('bundle', ['image', 'video', 'remote_video'], 'IN')
        ->condition('field_pro_media', $nid, '=')
        ->condition('field_show_in_pro_gallery', 1)
        ->condition('status', 1)
        ->sort('created' , 'DESC')
        ->execute();

    foreach ($entries as $key => $value) {
      $media_entity_load = Media::load($value);
      if (is_object($media_entity_load->field_media_image)) {
        $fid = $media_entity_load->field_media_image[0]->getValue()['target_id'];
        $file = File::load($fid);
        $media_data[$key]['media_type'] = $file->getMimeType();
        $media_data[$key]['media_url'] = file_create_url($file->getFileUri());
      }
      if (is_object($media_entity_load->field_media_video_file)) {
        $fid = $media_entity_load->field_media_video_file[0]->getValue()['target_id'];
        $file = File::load($fid);
        $media_data[$key]['media_type'] = $file->getMimeType();
        $media_data[$key]['media_url'] = file_create_url($file->getFileUri());
      }
      if (is_object($media_entity_load->field_media_oembed_video)) {
        $media_data[$key]['media_type'] = 'remote_video';
        $video_url = $media_entity_load->field_media_oembed_video[0]->getValue()['value'];
        $media_items[$key]['media_url'] = \Drupal::service('sony_core.default')->getremotevideoembedurl($video_url);
        
      }
       $media_data[$key]['mid'] = $media_entity_load->id();
      $media_data[$key]['media_name'] = $media_entity_load->label();
      $body_entities = $media_entity_load->get('field_body')->referencedEntities();
      if (!empty($body_entities)) {
        $lang_code = \Drupal::languageManager()->getCurrentLanguage()->getId();
        if($body_entities[0]->hasTranslation($lang_code)){
          $body = $body_entities[0]->getTranslation($lang_code);
        }
        else {
          $body = $body_entities[0];
        }
      }
      $media_data[$key]['media_body'] = (isset($body)) ? $body->label() : '';
      $lens_entities = $media_entity_load->get('field_lens')->referencedEntities();
      if (!empty($lens_entities)) {
        if($lens_entities[0]->hasTranslation($lang_code)){
          $lens = $lens_entities[0]->getTranslation($lang_code);
        }
        else {
          $lens = $lens_entities[0];
        }
      }
      $media_data[$key]['media_lens'] = (isset($lens)) ? $lens->label() : '';
      $media_data[$key]['media_aperture'] = $media_entity_load->get('field_aperture')->value;
      $media_data[$key]['media_exposure'] = $media_entity_load->get('field_exposure_media')->value;
      $media_data[$key]['media_iso'] = $media_entity_load->get('field_iso_media')->value;
      $media_data[$key]['media_focal_length'] = $media_entity_load->get('field_focal_length_media')->value;
      $media_data[$key]['media_location'] = $media_entity_load->get('field_location_alias')->value;

      $pro_media = $media_entity_load->get('field_pro_media')->getValue();
      if (!empty($pro_media)) {
        $pro_media_id = $pro_media[0]['target_id'];
        $pro_media_data = Node::load($pro_media_id);
        $media_data[$key]['pro_name'] = $pro_media_data->label();
        if (!empty($pro_media_data->field_profile_image->entity)) {
          $media_data[$key]['pro_image'] = file_create_url($pro_media_data->field_profile_image->entity->getFileUri());
        }
      }
    }
    return new JsonResponse(array_slice($media_data, 0, $limit));
  }

  /**
   * Callback for updating Submission node.
   */
  public function updateSubmissionNode(Request $response) {
    $nid = \Drupal::request()->request->get('nid');
    $field_name = \Drupal::request()->request->get('field');
    $field_val = \Drupal::request()->request->get('value');

    if ($nid) {
      $node = Node::load($nid);
      $node->set($field_name, $field_val);
      $node->save();
      $message = "node updated successfully.";
    }
    return new JsonResponse($message);
  }
}
