<?php

namespace Drupal\dckyiv_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'User Icon' Block.
 *
 * @Block(
 *   id = "login_icon",
 *   admin_label = @Translation("Login Icon"),
 *   category = @Translation("Icons"),
 * )
 */
class LoginIconBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user')
    );
  }

  /**
   * Login block.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   Current user instance.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      'link' => [
        '#type' => 'link',
        '#title' => '',
        '#url' => Url::fromRoute(
          $this->currentUser->isAuthenticated() ? 'user.page' : 'user.login'
        ) ,
        '#attributes' => ['class' => ['login_icon']],
      ]
    ];
  }

  /**
   * {@inheritdocs}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(['user.roles'], parent::getCacheContexts());
  }

}
