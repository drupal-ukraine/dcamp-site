<?php

namespace Drupal\dckyiv_commerce\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_order\Entity\OrderItem;

/**
 * Attenders Scanner controller.
 */
class AttendeeData extends ControllerBase {

  /**
   * Attendees scanner.
   */
  public function getAttendeeData(Paragraph $paragraph, OrderItem $order_item,  Order $order) {

    $data = [
      'first_name' => $paragraph->field_attendee_firstname->value,
      'last_name' => $paragraph->field_attendee_secondname->value,
      'status' => $paragraph->field_attendee_status->value,
    ];

    return new JsonResponse($data);
  }

}
