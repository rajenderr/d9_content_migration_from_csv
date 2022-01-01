<?php
/**
 * @file
 * Contains \Drupal\challenge_component\Plugin\Block\ChangeRegionBlock.
 */

namespace Drupal\challenge_component\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ChangeRegionBlock' block.
 *
 * @Block(
 *   id = "change_region_block",
 *   admin_label = @Translation("Change Region Block"),
 *   category = @Translation("Custom")
 * )
 */
class ChangeRegionBlock extends BlockBase
{
  /**
   * {@inheritdoc}
   */
  public function build()
  {
    $menu_name = 'change-regions-links';
    $menu_tree = \Drupal::menuTree();
    $parameters = $menu_tree->getCurrentRouteMenuTreeParameters($menu_name);
    $parameters->setMinDepth(0);
    $tree = $menu_tree->load($menu_name, $parameters);
    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
    $tree = $menu_tree->transform($tree, $manipulators);
    $list = [];

    foreach ($tree as $item) {
      $title = $item->link->getTitle();
      $url = $item->link->getUrlObject()->toUriString();
      $list[$title] = $url;
    }
    $output['sections'] = [
      '#items' => $list,
      '#host' => \Drupal::request()->getSchemeAndHttpHost(),
    ];
    return $output;
  }
}
