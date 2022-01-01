<?php

namespace Drupal\challenge_component\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Provides a block for Cubic form shared content.
 *
 * @Block(
 *   id = "Search_block",
 *   admin_label = @Translation("Search Block"),
 * )
 */
class  SearchBlock extends BlockBase  implements ContainerFactoryPluginInterface {

  /**
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
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
      $plugin_definition
    );
  }

  /**
    * {@inheritdoc}
    */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('\Drupal\challenge_component\Form\SearchForm');
    //return $form;
    return [
      '#theme' => 'search_block',
      '#custom_form' => $form,
      '#cache' => [
        'max-age' => 0,
      ]
    ];
  }
}