<?php

namespace Drupal\dckyiv_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

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
    $url = Url::fromRoute('user.login')->toString();

    $current= \Drupal::currentUser();
    if (!$current->id()) {
      $url = Url::fromRoute('user.page')->toString();
    }

    return ['#markup' => '<a href="' . $url . '" class="login_icon"></a>'];
  }

}
