<?php

namespace Drupal\dckyiv_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\dckyiv_core\TimetableService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * iCalendar controller.
 */
class IcalController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The timetable service.
   *
   * @var \Drupal\dckyiv_core\TimetableService
   */
  protected $timetableService;

  /**
   * iCalendar controller constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   Renderer
   * @param \Drupal\dckyiv_core\TimetableService $timetable_service
   *   The timetable service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, RendererInterface $renderer, TimetableService $timetable_service) {
    $this->entityTypeManager = $entity_type_manager;
    $this->renderer = $renderer;
    $this->timetableService = $timetable_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('renderer'),
      $container->get('dckyive_core.timetable')
    );
  }

  /**
   * Get .ics file for iCalendar
   *
   * @param $paragraph \Drupal\paragraphs\Entity\Paragraph Presentation paragraph
   * @param $node \Drupal\node\Entity\Node Presentation node
   *
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function iCalendar($paragraph, $node) {
    if (is_numeric($paragraph)) {
      $paragraph = $this->entityTypeManager->getStorage('paragraph')->load($paragraph);
    }
    if (is_numeric($node)) {
      $node = $this->entityTypeManager->getStorage('node')->load($node);
    }

    $info = $this->timetableService->prepareCalendarInfo($paragraph, $node);
    $filename = preg_replace('/[\x00-\x1F]/u', '_', $info['title']);

    $build = [
      '#theme' => 'dckyiv_core_ical',
      '#info' => $info,
      '#cache' => ['max-age' => 0],
    ];
    $output = $this->renderer->renderRoot($build);

    $response = new Response();
    $response->headers->set('Content-Type', 'application/calendar; charset=utf-8');
    $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '.ics"');
    $response->setContent($output);

    return $response;
  }

}
