<?php

namespace Drupal\challenge_component\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;

/**
 * SubmitPhotoVideoForm class.
 */
class SubmitPhotoVideoForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'submit_photo_video_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $options = NULL) {
    $form['#prefix'] = '<div id="modal_example_form">';
    $form['#suffix'] = '</div>';
    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['photo'] = [
      '#type' => 'submit',
      '#value' => $this->t('PHOTO'),
      '#attributes' => [
        'class' => [
          'use-ajax',
        ],
      ],
      '#ajax' => [
        'callback' => [$this, 'submitPhotoFormAjax'],
        'event' => 'click',
      ],
    ];
    $form['actions']['video'] = [
      '#type' => 'submit',
      '#value' => $this->t('VIDEO'),
      '#attributes' => [
        'class' => [
          'use-ajax',
        ],
      ],
      '#ajax' => [
        'callback' => [$this, 'submitVideoFormAjax'],
        'event' => 'click',
      ],
    ];

    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';

    return $form;
  }

  /**
   * AJAX callback handler that displays any errors or a success message.
   */
  public function submitPhotoFormAjax(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $node = \Drupal\node\Entity\Node::create(['type' => 'submission']);
    $modal_form = \Drupal::service('entity.form_builder')->getForm($node);
    $response->addCommand(new OpenModalDialogCommand('Photo Challenge', $modal_form, ['width' => '800']));
    return $response;
  }

  public function submitVideoFormAjax(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $node = \Drupal\node\Entity\Node::create(['type' => 'submission']);
    $modal_form = \Drupal::service('entity.form_builder')->getForm($node);
    $response->addCommand(new OpenModalDialogCommand('Video Challenge', $modal_form, ['width' => '800']));
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return ['config.submit_photo_video_form'];
  }

}
