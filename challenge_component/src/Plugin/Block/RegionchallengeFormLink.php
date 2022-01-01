<?php
/**
 * @file
 * Contains \Drupal\challenge_component\Plugin\Block\RegionchallengeFormLink.
 */

namespace Drupal\challenge_component\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
use \Drupal\Core\Routing;

/**
 * Provides a 'RegionchallengeFormLink' block.
 *
 * @Block(
 *   id = "region_challenge_form_link",
 *   admin_label = @Translation("Region Challenge Form Link"),
 *   category = @Translation("Custom")
 * )
 */
class RegionchallengeFormLink extends BlockBase
{
  /**
   * {@inheritdoc}
   */
  public function build()
  {
  global $base_url;
  $path = \Drupal::request()->getpathInfo();
  $arg  = explode('/',$path);
  $path = $base_url . '/node/add/submission?nid=' .$arg[2];
  $output = '<div class="submissions-link"><div class="submissions-link-1"><a href="' . $path .'">Create Submission</a></div><div class="submissions-link-2"> <a href="/node/'.$arg[2].'/submissions-dashboard/data.csv">EXPORT</a></div></div>';
  return [
      '#type' => 'markup',
      '#markup' => $output,
      '#cache' => [
            'max-age' => 0,
          ]
    ];
  }
}
   