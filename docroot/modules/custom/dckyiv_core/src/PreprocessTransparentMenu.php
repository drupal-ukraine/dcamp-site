<?php

namespace Drupal\dckyiv_core;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\NodeInterface;

/**
 * Preprocess Transparent Menu class.
 */
class PreprocessTransparentMenu implements ContainerInjectionInterface {

  /**
   * The currency storage.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The entity type manager.
   */
  public function __construct(RouteMatchInterface $routeMatch) {
    $this->routeMatch = $routeMatch;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_route_match')
    );
  }

  /**
   * Preprocess method.
   *
   * Add/deletes class based on the Transparent Menu
   * boolean at the node content.
   *
   * @param array $variables
   *   Template vars.
   */
  public function preprocessHtml(array &$variables) {

    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->routeMatch->getParameter('node');
    if (
      ($node instanceof NodeInterface)
      && ($node->hasField('field_transparent_header'))
      && ($node->field_transparent_header->value)
    ) {
      $variables['attributes']['class'][] = 'transparent-header';
    }
  }

}
