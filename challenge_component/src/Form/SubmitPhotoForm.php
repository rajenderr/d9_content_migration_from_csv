<?php

namespace Drupal\challenge_component\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use DateTime;
use Drupal\taxonomy\Entity\Term;

/**
 * SubmitPhotoForm class.
 */
class SubmitPhotoForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $options = NULL) {
    $node = \Drupal::routeMatch()->getParameter('node');
    $type = '';
    if ($node instanceof \Drupal\node\NodeInterface) {
      $nid = $node->id();
      $node_details = Node::load($nid);
      $type = $node_details->getType();
    }
    $enable_submissions = 0;
    $enable_voting = 0;
    if ($type == 'challenge') {
      $types_of_challenge = $node_details->get('field_types_of_challenge')->getValue()[0]['value'];
      $social_challenge_enabled = $node_details->get('field_social_challenge')->getValue()[0]['value'];
      $challenge_type_id = $node_details->get('field_type_of_challenge')
        ->getValue()[0]['target_id'];
      $challenge_name = Term::load($challenge_type_id)->get('name')->value;
      $challenge_start_date = $node_details->get('field_challenge_date_range')
        ->getValue()[0]['value'];
      $challenge_end_date = $node_details->get('field_challenge_date_range')
        ->getValue()[0]['end_value'];
      $challenge_date = new DateTime($challenge_end_date);
      $challenge_timer_date = $challenge_date->format('Y-m-d H:i:s');
      $voting_start_date = $node_details->get('field_voting_date_range')
        ->getValue()[0]['value'];
      $voting_end_date = $node_details->get('field_voting_date_range')
        ->getValue()[0]['end_value'];
      $voting_date = new DateTime($voting_end_date);
      $voting_timer_date = $voting_date->format('Y-m-d H:i:s');
      $enable_submissions = $node_details->get('field_enable_submissions')
        ->getValue()[0]['value'];
      $enable_voting = $node_details->get('field_enable_voting')
        ->getValue()[0]['value'];
    }

    if ($enable_submissions && $social_challenge_enabled == 0) {
      if ($types_of_challenge == "photo_challenge") {
        $title1 = t('SUBMIT A PHOTO');
      }
      elseif ($types_of_challenge == "video_challenge") {
        $title1 = t('SUBMIT A VIDEO');
      }
      elseif ($types_of_challenge == "photo_and_video_challenge") {
        $title1 = t('SUBMIT A PHOTO/VIDEO');
      }
      $title2 = t('FOR THIS CHALLENGE');
      $title3 = t('Or Vote for your favourites!');
      $title = '<div class = "submit-photo-title"><p>' . $title1 . '</p></div>';
      $sub_title1 = '<div class = "submit-photo-subtitle1"><p>' . $title2 . '</p></div>';
      $sub_title2 = '<div class = "submit-photo-subtitle2"><p>' . $title3 . '</p></div>';
      $timer = '<div id="jquery-countdown-timer"></div><div id="jquery-countdown-timer-note"></div>';
      $submit_vote_button = '<div class = "submit-vote-button"><a href = "#">Vote Now</a></div>';
      $build['content'] = [
        '#markup' => $title . $sub_title1 . $sub_title2 . $timer,
      ];
      $settings = [
        'unixtimestamp' => strtotime($challenge_timer_date),
        'fontsize' => '24',
      ];
      $build['#attached']['library'][] = 'challenge_component/challenge.component.submit.photo';
      $build['#attached']['drupalSettings']['countdown'] = $settings;
      $form['submit_photo'] = [
        '#type' => 'markup',
        'value' => $build,
      ];
      if ($types_of_challenge == "video_challenge" || $types_of_challenge == "photo_challenge") {
        $form['open_modal'] = [
          '#type' => 'link',
          '#prefix' => '<div class = "submit-photo-button">',
          '#suffix' => '</div>',
          '#title' => $this->t('Submit'),
          '#url' => Url::fromRoute('submit_photo.open_modal_form', ['challenge_id' => $nid]),
          '#attributes' => [
            'class' => [
              'use-ajax',
              'button',
            ],
          ],
        ];
      }
      if ($types_of_challenge == "photo_and_video_challenge") {
        $form['open_modal'] = [
          '#type' => 'link',
          '#prefix' => '<div class = "submit-photo-button">',
          '#suffix' => '</div>',
          '#title' => $this->t('Submit ---'.$select_value),
          '#url' => Url::fromRoute('submit_photo_video.form',['challenge_id' => $nid]),
          '#attributes' => [
            'class' => [
              'use-ajax',
              'button',
            ],
          ],
        ];
      }
      if ($enable_voting) {
        $form['submit_vote'] = [
          '#type' => 'link',
          '#prefix' => '<div class = "submit-vote-button">',
          '#suffix' => '</div>',
          '#title' => $this->t('Vote'),
          '#url' => Url::fromRoute('<front>'),
          '#attributes' => [
            'class' => [
              'use-ajax',
              'button',
            ],
          ],
        ];
      }
    }
    elseif ($enable_voting && $social_challenge_enabled == 0) {
      $title1 = t('VOTE FOR YOUR');
      $title2 = t('FAVORITES NOW');
      $title3 = t('Submissions have closed!');
      $title = '<div class = "submit-photo-title"><p>' . $title1 . '</p></div>';
      $sub_title1 = '<div class = "submit-photo-subtitle1"><p>' . $title2 . '</p></div>';
      $sub_title2 = '<div class = "submit-photo-subtitle2"><p>' . $title3 . '</p></div>';
      $timer = '<div id="jquery-countdown-timer-voting"></div><div id="jquery-countdown-timer-note-voting"></div>';
      $settings = [
        'unixtimestampvoting' => strtotime($voting_timer_date),
        'fontsize' => '24',
      ];
      $build['content'] = [
        '#markup' => $title . $sub_title1 . $sub_title2 . $timer,
      ];
      $build['#attached']['library'][] = 'challenge_component/challenge.component.submit.photo';
      $build['#attached']['drupalSettings']['countdown'] = $settings;
      $form['submit_photo'] = [
        '#type' => 'markup',
        'value' => $build,
      ];
      $form['submit_vote'] = [
        '#type' => 'link',
        '#prefix' => '<div class = "submit-vote-button">',
        '#suffix' => '</div>',
        '#title' => $this->t('Vote'),
        '#url' => Url::fromRoute('<front>'),
        '#attributes' => [
          'class' => [
            'use-ajax',
            'button',
          ],
        ],
      ];
      
      return $form;
    }
    else {
      return [
        '#type' => 'markup',
        '#markup' => '',
      ];
    }
    // Attach the library for pop-up dialogs/modals.
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'submit_photo_form';
  }

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return ['config.submit_photo_form'];
  }

}
