<?php

namespace Drupal\challenge_component\Ajax;

use Drupal\Core\Ajax\CommandInterface;
use Drupal\file\Entity\File;

/**
 * Command to trigger an event when managed file upload is complete.
 */
class ManagedFileUploadCompleteEventCommand implements CommandInterface {

  /**
   * Implements Drupal\Core\Ajax\CommandInterface:render().
   */
  public function render() {
    $tempstore = \Drupal::service('tempstore.private');
    $store = $tempstore->get('challenge_component');
    $file_data = $store->get('file_data');
    
    return [
      'command' => 'triggerManagedFileUploadComplete',
      'files' => $file_data,
    ];
  }

}