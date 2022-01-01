<?php

namespace Drupal\challenge_component\Plugin\Block;

use Drupal\node\NodeInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;

/**
 * Provides a block for Cubic form shared content.
 *
 * @Block(
 *   id = "explore_gallery_block",
 *   admin_label = @Translation("Custom Explore Gallery"),
 * )
 */
class ExploreGalleryBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Database\Connection
   */
  private $database;

  /**
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Connection $database) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->database = $database;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $no_of_items = 12;
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      $nid = $node->id();
    }
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
        $media_items[$key]['media_type'] = $file->getMimeType();
        $media_items[$key]['media_url'] = file_create_url($file->getFileUri());
        $media_items[$key]['media_title'] = $media_entity_load->get('field_image_name')->value;
      }
      
      if (is_object($media_entity_load->field_media_video_file)) {
        $fid = $media_entity_load->field_media_video_file[0]->getValue()['target_id'];
        $file = File::load($fid);
        $media_items[$key]['media_type'] = $file->getMimeType();
        $media_items[$key]['media_url'] = file_create_url($file->getFileUri());
        $media_items[$key]['media_title'] = $media_entity_load->get('field_video_name')->value;
      }
      if (is_object($media_entity_load->field_media_oembed_video)) {
        $media_items[$key]['media_type'] = 'remote_video';
        $video_url = $media_entity_load->field_media_oembed_video[0]->getValue()['value'];
        $media_items[$key]['media_url'] = \Drupal::service('sony_core.default')->getremotevideoembedurl($video_url);
        $media_items[$key]['media_title'] = $media_entity_load->get('field_video_name')->value;
      }
      
      $media_items[$key]['mid'] = $media_entity_load->id();
      $media_items[$key]['media_name'] = $media_entity_load->label();
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
      $media_items[$key]['media_body'] = (isset($body)) ? $body->label() : '';
      $lens_entities = $media_entity_load->get('field_lens')->referencedEntities();
      if (!empty($lens_entities)) {
        if($lens_entities[0]->hasTranslation($lang_code)){
          $lens = $lens_entities[0]->getTranslation($lang_code);
        }
        else {
          $lens = $lens_entities[0];
        }
      }
      $media_items[$key]['media_lens'] = (isset($lens)) ? $lens->label() : '';
      
      $media_items[$key]['media_aperture'] = $media_entity_load->get('field_aperture')->value;
      $media_items[$key]['media_exposure'] = $media_entity_load->get('field_exposure_media')->value;
      $media_items[$key]['media_iso'] = $media_entity_load->get('field_iso_media')->value;
      $media_items[$key]['media_focal_length'] = $media_entity_load->get('field_focal_length_media')->value;
      $media_items[$key]['media_location'] = $media_entity_load->get('field_location_alias')->value;

      $pro_media = $media_entity_load->get('field_pro_media')->getValue();
      if (!empty($pro_media)) {
        $pro_media_id = $pro_media[0]['target_id'];
        $pro_media_data = Node::load($pro_media_id);
        $media_items[$key]['pro_name'] = $pro_media_data->label();
        if (!empty($pro_media_data->field_profile_image->entity)) {
          $media_items[$key]['pro_image'] = file_create_url($pro_media_data->field_profile_image->entity->getFileUri());
        }
      }
      
      
    }
    if (count($entries) > $no_of_items) {
      $media_data = array_slice($media_items, 0, $no_of_items);
    }
    else {
      $media_data = $media_items;
    }
    return [
      '#theme' => 'explore_gallery',
      '#media_data' => $media_data,
      '#media_data_count' => count($media_items),
      '#node_nid' => $nid,
      '#current_items' => count($media_data),
      '#no_of_items' => $no_of_items,
      '#attached' => [
        'drupalSettings' => [
          'no_of_items' => $no_of_items,
        ],
        'library' => ['challenge_component/explore_gallery'],
      ],
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

}
