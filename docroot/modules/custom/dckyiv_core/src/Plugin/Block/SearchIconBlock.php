<?php

namespace Drupal\dckyiv_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Search Icon' Block.
 *
 * @Block(
 *   id = "search_icon",
 *   admin_label = @Translation("Search Icon"),
 *   category = @Translation("Icons"),
 * )
 */
class SearchIconBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return ['#markup' => '<div class="search_icon"></div>'];
  }

}
