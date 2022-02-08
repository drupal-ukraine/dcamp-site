<?php

namespace Drupal\dckyiv_commerce\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\paragraphs\ParagraphInterface;
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
  public function getAttendeeData(Paragraph $paragraph, OrderItem $order_item, Order $order) {

    $data = [
      'first_name' => $paragraph->field_attendee_firstname->value,
      'last_name' => $paragraph->field_attendee_secondname->value,
      'status' => empty($paragraph->field_attendee_status->value) ? -1 : $paragraph->field_attendee_status->value,
      't_shirt_size' => $order_item->field_t_shirt_size->value,
      'attender_id' => $paragraph->id(),
    ];

    return new JsonResponse($data);
  }

  /**
   * Update attendee status.
   *
   * @param \Drupal\paragraphs\ParagraphInterface $paragraph
   *   Paragraph instance.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function updateAttendeeStatus(ParagraphInterface $paragraph) {
    $paragraph->field_attendee_status->value = 1;
    try {
      $paragraph->save();
      return JsonResponse::create('ok');
    }
    catch (\Exception $e) {
      return JsonResponse::create('bad', 404);
    }
  }

}
