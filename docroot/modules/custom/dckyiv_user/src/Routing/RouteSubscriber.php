<?php

namespace Drupal\dckyiv_user\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('user.login')) {
      $route->setDefaults([
        '_form' => '\Drupal\dckyiv_user\Form\DCKUserLoginForm',
        '_title' => 'Log in to DrupalCamp',
      ]);
      $route->setOption('no_cache', TRUE);
    }

    if ($route = $collection->get('user.register')) {
      $route->setOption('no_cache', TRUE);
    }

  }

}
