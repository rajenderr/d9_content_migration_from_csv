<?php
/**
 * @file
 * Contains \Drupal\challenge_component\Plugin\Block\RecommendedForYouArticlesBlock.
 */

namespace Drupal\challenge_component\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Database\Connection;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;

/**
 * Provides a 'RecommendedForYouArticlesBlock' block.
 *
 * @Block(
 *   id = "recommended_for_you_articles_block",
 *   admin_label = @Translation("Submit Photo Block"),
 *   category = @Translation("Custom")
 * )
 */

class RecommendedForYouArticlesBlock extends BlockBase {

  /**
   * {@inheritdoc}
  */ 
  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof \Drupal\node\NodeInterface) {
      $nid = $node->id();
      $node_details = Node::load($nid);
      $type = $node_details->getType();
    }
    if ($type == 'article') {
      $article_theme = $node_details->get('field_article_theme')->getValue()[0]['target_id'];
      $article_categories = $node_details->get('field_tags')->getValue()[0]['target_id'];
      $domain_access_arr = $node_details->get('field_domain_access')->getValue();
      foreach ($domain_access_arr as $key => $value) {
        $domains[] = $value['target_id'];
      }
      $query = \Drupal::database()->select('node_field_data', 'nd');
      $query->distinct();
      $query->join('node__field_article_theme', 'at', 'nd.nid = at.entity_id');
      $query->join('node__field_tags', 'ft', 'nd.nid = ft.entity_id');
      $query->join('node__field_article_thumbnail', 'atn', 'nd.nid = atn.entity_id');
      $query->join('node__field_domain_access', 'da', 'nd.nid = da.entity_id');
      $query->condition('at.field_article_theme_target_id', $article_theme);
      $query->condition('ft.field_tags_target_id', $article_categories);
      $query->condition('nd.status', 1, '=');
      $query->condition('nd.nid', $nid, '!=');
      $query->condition('da.field_domain_access_target_id', $domains, 'IN');
      $query->condition('nd.type', 'article', '=');
      $query->fields('nd',['title', 'nid']);
      $query->range(0, 3);
      $query->fields('at',['field_article_theme_target_id']);
      $query->fields('ft',['field_tags_target_id']);
      $query->fields('atn',['field_article_thumbnail_target_id']);
      $query->orderBy('nd.nid', 'DESC');
      $output = $query->execute()->fetchAll();
      foreach ($output as $key => $value) {
        $article_data[$key]['title'] = $value->title;
        $article_data[$key]['nid'] = $value->nid;
        $article_data[$key]['artical_theme'] = Term::load($value->field_tags_target_id)->getName();
        $media_entity_load = Media::load($value->field_article_thumbnail_target_id); 
        $article_data[$key]['image_url'] = $media_entity_load->field_media_image->entity->getFileUri();
        // print_r($media_entity_load->field_media_image->entity->getFileUri());die;
      }
    }

    return [
      '#theme' => 'recommended_for_you_articles',
      '#article_data' => $article_data,
      '#cache' => [
        'max-age' => 0,
      ]
    ];
  }
}
