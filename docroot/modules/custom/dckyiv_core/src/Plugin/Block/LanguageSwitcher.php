<?php

namespace Drupal\dckyiv_core\Plugin\Block;

use Drupal\language\Plugin\Block\LanguageBlock;
use Drupal\Core\Url;

/**
 * Provides a 'Language switcher' block.
 *
 * @Block(
 *   id = "dckyiv_language_block",
 *   admin_label = @Translation("DCK: Language switcher"),
 *   category = @Translation("Language"),
 *   deriver = "Drupal\language\Plugin\Derivative\LanguageBlock"
 * )
 */
class LanguageSwitcher extends LanguageBlock {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $route_name = $this->pathMatcher->isFrontPage() ? '<front>' : '<current>';
    $type = $this->getDerivativeId();
    $links = $this->languageManager->getLanguageSwitchLinks($type, Url::fromRoute($route_name));
    $currentLanguage = $this->languageManager->getCurrentLanguage()->getId();

    // Show all languages except current.
    foreach ($links->links as $key => $link_list) {
      if ($key == $currentLanguage) {
        unset($links->links[$key]);
      }
    }
    if (isset($links->links)) {
      $build = [
        '#theme' => 'links__language_block',
        '#links' => $links->links,
        '#attributes' => [
          'class' => [
            "language-switcher-{$links->method_id}",
          ],
        ],
        '#set_active_class' => TRUE,
      ];
    }
    return $build;
  }

}
