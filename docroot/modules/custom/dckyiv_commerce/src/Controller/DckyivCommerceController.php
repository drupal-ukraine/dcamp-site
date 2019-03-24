<?php

namespace Drupal\dckyiv_commerce\Controller;

use Drupal\commerce_order\Entity\OrderItemInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\user\UserInterface;

/**
 * DckyivCommerceController.
 */
class DckyivCommerceController extends ControllerBase {

  /**
   * Attende edit form callback page.
   *
   * @return array
   *   The attendee form.
   */
  public function attendeeFormEdit(UserInterface $user, OrderItemInterface $commerce_order_item, $attendee_paragraph) {
    $form = \Drupal::service('entity.form_builder')->getForm($attendee_paragraph, 'edit');
    return $form;
  }

  /**
   * Attende add form callback page.
   *
   * @param UserInterface $user
   *   The user object.
   * @param OrderItemInterface $commerce_order_item
   *   The commerce order item object.
   *
   * @throws
   *
   * @return array
   *   The attendee form.
   */
  public function attendeeFormAdd(UserInterface $user, OrderItemInterface $commerce_order_item) {
    /** @var ParagraphInterface $attendee_paragraph */
    $attendee_paragraph = Paragraph::create(['type' => 'attendee']);
    $attendee_paragraph->isNew();
    $form = \Drupal::service('entity.form_builder')->getForm($attendee_paragraph, 'default');
    return $form;
  }

}
