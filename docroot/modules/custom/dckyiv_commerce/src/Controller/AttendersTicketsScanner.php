<?php

namespace Drupal\dckyiv_commerce\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Drupal\Core\Extension\ModuleHandler;

/**
 * Attenders Scanner controller.
 */
class AttendersTicketsScanner extends ControllerBase {

  /**
   * Module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandler
   */
  protected $moduleHandler;


  /**
   * Constructs a DckyivCommerceController object.
   *
   * @param \Drupal\Core\Extension\ModuleHandler $moduleHandler
   *   ModuleHandler service.
   */
  public function __construct(ModuleHandler $moduleHandler) {
    $this->moduleHandler = $moduleHandler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('module_handler')
    );
  }

  /**
   * Attendees scanner.
   *
   * @return BinaryFileResponse
   *   The reports build array.
   */
  public function attendersScanner(Request $request) {

    $module_path = $this->moduleHandler
      ->getModule('dckyiv_commerce')
      ->getPath();

    //return new BinaryFileResponse($module_path . '/dckyiv-qr-scanner/dist/index.html');
    return [
      '#type' => 'html_tag',
      '#tag' => 'iframe',
      '#attributes' => [
        'src' => '/' . $module_path . '/dckyiv-qr-scanner/dist/index.html',
        'width' => '100%',
        'height' => '500px',
        'border' => 0,
        ],
    ];
  }

}
