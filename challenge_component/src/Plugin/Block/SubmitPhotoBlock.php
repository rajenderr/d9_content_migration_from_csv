<?php
/**
 * @file
 * Contains \Drupal\challenge_component\Plugin\Block\SubmitPhotoBlock.
 */

namespace Drupal\challenge_component\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use DateTime;
use Drupal\taxonomy\Entity\Term;

/**
 * Provides a 'SubmitPhotoBlock' block.
 *
 * @Block(
 *   id = "submit_photo_block",
 *   admin_label = @Translation("Submit Photo Block"),
 *   category = @Translation("Custom")
 * )
 */
class SubmitPhotoBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');
    $type = '';
    if ($node instanceof \Drupal\node\NodeInterface) {
      $nid = $node->id();
      $node_details = Node::load($nid);
      $type = $node_details->getType();
    }
    $enable_submissions = 0;
    $enable_voting = 0;
    $block_voting = 0;
    if ($type == 'challenge') {
      $types_of_challenge = $node_details->get('field_types_of_challenge')->getValue()[0]['value'];
      $social_challenge_enabled = $node_details->get('field_social_challenge')->getValue()[0]['value'];
      $challenge_type_id = $node_details->get('field_type_of_challenge')->getValue()[0]['target_id'];
      //$challenge_name = Term::load($challenge_type_id);
      $challenge_start_date = $node_details->get('field_challenge_date_range')->getValue()[0]['value'];
      $challenge_end_date = $node_details->get('field_challenge_date_range')->getValue()[0]['end_value'];
      $challenge_date = new DateTime($challenge_end_date);
      $challenge_timer_date = $challenge_date->format('Y-m-d H:i:s');
      $voting_start_date = $node_details->get('field_voting_date_range')->getValue()[0]['value'];
      $voting_end_date = $node_details->get('field_voting_date_range')->getValue()[0]['end_value'];
      $voting_date       = new DateTime($voting_end_date);
      $voting_timer_date = $voting_date->format('Y-m-d H:i:s');
      $enable_submissions = $node_details->get('field_enable_submissions')
        ->getValue()[0]['value'];
      $enable_voting = $node_details->get('field_enable_voting')
        ->getValue()[0]['value'];
    }
    $response = new AjaxResponse();
    if ($types_of_challenge == "photo_challenge"){
      $modal_form = "photo";
    }
    elseif ($types_of_challenge == "video_challenge") {
      $modal_form = "video";
    }
    elseif ($types_of_challenge == "photo_and_video_challenge") {
      $modal_form = "photo_video";
    }
    if ($enable_voting && $enable_submissions && $social_challenge_enabled == 0) {
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
      if (strtotime($challenge_timer_date) >= REQUEST_TIME) {
        $title3 = t('Or Vote for your favourites!');
      } else {
        $title3 = t('Submissions have been closed!');
      }
      $title = '<div class = "submit-photo-title"><p>' . $title1 . '</p></div>';
      $sub_title1 = '<div class = "submit-photo-subtitle1"><p>' . $title2 . '</p></div>';
      $sub_title2 = '<div class = "submit-photo-subtitle2"><p>' . $title3 . '</p></div>';
      $timer = '<div id="jquery-countdown-timer"></div><div id="jquery-countdown-timer-note"></div>';
      $submit_vote_button = '<div class = "submit-vote-button"><a href = "#">Vote Now</a></div>';
      $build['content'] = [
        '#markup' => $title . $sub_title1 . $sub_title2 . $timer . $submit_vote_button,
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
    }
    if ($enable_voting && $social_challenge_enabled == 0 && !empty($voting_timer_date)) {
      $block_voting = 1;
      $title1 = t('VOTE FOR YOUR');
      $title2 = t('FAVORITES NOW');

      if (strtotime($voting_timer_date) >= REQUEST_TIME) {
        $title3 = t('Voting is going on, vote for your favourites!');
        $submit_vote_button = '<div class = "submit-vote-button-voting"><a href = "#">Vote Now</a></div>';
      } else {
        $title3 = t('Voting have been closed!'); 
        $submit_vote_button = '<div class = "submit-vote-button-voting">Voting Closed</div>';
      }
      $title = '<div class = "submit-photo-title"><p>' . $title1 . '</p></div>';
      $sub_title1 = '<div class = "submit-photo-subtitle1"><p>' . $title2 . '</p></div>';
      $sub_title2 = '<div class = "submit-photo-subtitle2"><p>' . $title3 . '</p></div>';
      $timer = '<div id="jquery-countdown-timer-voting"></div><div id="jquery-countdown-timer-note-voting"></div>';
      $_build['content'] = [
        '#markup' => $title . $sub_title1 . $sub_title2 . $timer . $submit_vote_button,
      ];
      $settings = [
        'unixtimestampvoting' => strtotime($voting_timer_date),
        'fontsize' => '24',
      ];
      $_build['#attached']['library'][] = 'challenge_component/challenge.component.submit.photo';
      $_build['#attached']['drupalSettings']['countdownvoting'] = $settings;
      
    }
    //$response->addCommand(new OpenModalDialogCommand('My Modal Form', $modal_form, ['width' => '800']));
    //return $modal_form;
    return [
      '#theme' => 'submit_challenges_block',
      '#block_content' => $modal_form,
      "#block_build_content" => $build,
      "#block_voting_content" => $_build,
      "#block_voting" => $block_voting,
      '#cache' => [
        'max-age' => 0,
      ]
    ];
  }

}
