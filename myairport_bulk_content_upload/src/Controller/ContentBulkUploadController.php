<?php
/**
 * @file
 * Contains \Drupal\myairport_bulk_content_upload\Controller\ContentBulkUploadController.
 */
namespace Drupal\myairport_bulk_content_upload\Controller;
use Drupal\Core\Controller\ControllerBase;

/**
 * ContentBulkUploadController.
 */
class ContentBulkUploadController extends ControllerBase {
	/**
   * Generates an Content bulk upload page.
   */
  public function myairport_bulk_content_upload() {
    $form = \Drupal::formBuilder()->getForm('Drupal\myairport_bulk_content_upload\Form\MyairportBulkContentUploadForm');
    return $form;
  }
}