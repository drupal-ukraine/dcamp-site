<?php

namespace Drupal\dckyiv_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'User Icon' Block.
 *
 * @Block(
 *   id = "login_icon",
 *   admin_label = @Translation("Login Icon"),
 *   category = @Translation("Icons"),
 * )
 */
class LoginIconBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return ['#markup' => '<div class="login_icon"></div>'];
  }

}
