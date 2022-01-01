<?php
namespace Drupal\myairport_bulk_content_upload;
use \Drupal\node\Entity\Node;
use \Drupal\file\Entity\File;
use Drupal\Core\Entity\ContentEntityType;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Database\Connection;


class MyairportBulkContent {
  
  public static function myairportAddNew($node_data, &$context){
    $context['sandbox']['current_item'] = $node_data;
    $message = 'Creating Node';
    $results = array();
    create_node($node_data);
    $context['message'] = $message;
    $context['results'][] = $node_data;
  }

  public static function myairportAddNewContentFinishedCallback($success, $results, $operations) {
    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'One content processed.', '@count node Processed.'
      );
    }
    else {
      $error_operation = reset($operations);
      $message = t('An error occurred while processing %error_operation with arguments: @arguments', array(
      '%error_operation' => $error_operation[0],
      '@arguments' => print_r($error_operation[1], TRUE)
      ));
      \Drupal::messenger()->addError($message);
    }
    foreach ($results as $result) {
      \Drupal::messenger()->addStatus(count($results).' node Processed.');
    }
  }
}

function create_node($node_data) {
  global $base_url;
  $connection = \Drupal::database();
  $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
  $path = $node_data['path']; 
  $airport = $node_data['airport'];
  $category = $node_data['category'];
  $language = $node_data['language'];
  $title = $node_data['name'];
  $promotions = $node_data['promotions'];
  $shortDesc = $node_data['shortDesc'];
  $subCategory = $node_data['subCategory'];
  $terminal = $node_data['terminal'];
  $type = $node_data['type'];
  $level = $node_data['level'];    
  $longitude = $node_data['longitude'];
  $locationId = $node_data['locationId'];
  $squareImage = $node_data['squareImage'];
  $square_image_path_raw = str_replace('\\', '/', $squareImage);
  $square_image_path = preg_replace('#[/\\\\]+#', '/', $square_image_path_raw);
  $banner = $node_data['banner']; 
  $banner_image_path_raw = str_replace('\\', '/', $banner);
  $banner_image_path = preg_replace('#[/\\\\]+#', '/', $banner_image_path_raw);
  $taxanomy_manager = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
  $airport_term_tree = $taxanomy_manager->loadTree('airport');
  foreach ($airport_term_tree as $airport_term) {
    if($airport_term->name == $airport) {
      $airport_tid = $airport_term->tid;
    }
  }
  
  $categories_term_tree = $taxanomy_manager->loadTree('categories');
  foreach ($categories_term_tree as $categories_term) {
    if($categories_term->name == $category) {
      $categories_tid = $categories_term->tid;
    }
  }

  $level_term_tree = $taxanomy_manager->loadTree('airport_levels');
  foreach ($level_term_tree as $level_term) {
    if($level_term->name == $level) {
      $level_tid = $level_term->tid;
    }
  }
  
  $square_image_uri = 'public://'.$square_image_path;
  // check first if the file exists for the uri    
  $square_image_file = \Drupal::entityTypeManager()->getStorage('file')->loadByProperties(['uri' => $square_image_uri]);
  $square_image_file = reset($square_image_file);
  // if not create a file
  if (!$square_image_file) {
    $square_image_file = File::create([
      'uri' => $square_image_uri,
    ]);
    $square_image_file->save();
  }

  $banner_image_uri = 'public://'.$banner_image_path;
  // check first if the file exists for the uri    
  $banner_image_file = \Drupal::entityTypeManager()->getStorage('file')->loadByProperties(['uri' => $banner_image_uri]);
  $banner_image_file = reset($banner_image_file);
  // if not create a file
  if (!$banner_image_file) {
    $banner_image_file = File::create([
      'uri' => $banner_image_uri,
    ]);
    $banner_image_file->save();
  }

  // Create node.
  if ($title) {
    $node = Node::create(['type' => 'point_of_interest']);
    $node->set('title', $title);
    $node->set('body', ['value' => $shortDesc]);
    $node->set('field_square_image', $square_image_file->id());
    $node->set('field_banner_image', $banner_image_file->id());
    if ($airport_tid) {
      $node->set('field_airports', $airport_tid);
    }
    if ($categories_tid) {
      $node->set('field_category', $categories_tid);
    }
    if ($level_tid) {
      $node->set('field_airport_level', $level_tid);
    }
    $node->set('field_lat_long', $longitude);
    $node->enforceIsNew();
    $node->save();
  } 
}
