<?php

namespace Drupal\challenge_component\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Entity\EntityTypeManager;

/**
 * Provides a block for Cubic form shared content.
 *
 * @Block(
 *   id = "search_result_block",
 *   admin_label = @Translation("Search Result Block"),
 * )
 */
class  SearchResultBlock extends BlockBase  implements ContainerFactoryPluginInterface {

  /**
   * Symfony\Component\HttpFoundation\RequestStack definition.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   * @param \Drupal\Core\Entity\EntityTypeManager $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration,
    $plugin_id,
    $plugin_definition,
    RequestStack $request_stack,
    EntityTypeManager $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->requestStack = $request_stack->getCurrentRequest();
    $this->entityTypeManager = $entity_type_manager;
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
      $container->get('request_stack'),
      $container->get('entity_type.manager'),
    );
  }

  /**
    * {@inheritdoc}
    */
  public function build() {
    $keyword = $this->requestStack->get('keyword', '');
    // Check if article theme exist in url
    
    if ($keyword === '') {
      $themes = $this->requestStack->get('lense', '');
      // Get first item.
      if (is_array($themes)) {
        $theme = array_shift($themes);
        $item = explode(':', $theme);
        if (count($item) > 1 && $item[0] === 'article_theme') {
          // The $item[1] is term id.
          try {
            $term = $this->entityTypeManager->getStorage('taxonomy_term')->load($item[1]);
            $keyword = $term->getName();
          }
          catch (PluginException $e) {
            $this->logger->error($e->getMessage());
          }
        }
      }
    }
    return [
      '#theme' => 'search_result_block',
      '#keyword' => $keyword,
      '#cache' => [
        'max-age' => 0,
      ]
    ];
  }
}