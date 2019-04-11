<?php

namespace Drupal\dckyiv_commerce\EventSubscriber;

use Drupal\address\Plugin\Field\FieldType\AddressItem;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\commerce_cart\CartProviderInterface;
use Drupal\state_machine\Event\WorkflowTransitionEvent;

class OrderEventSubscriber implements EventSubscriberInterface {

  /**
   * The cart provider.
   *
   * @var CartProviderInterface
   */
  protected $cartProvider;

  /**
   * Constructs a new OrderEventSubscriber object.
   *
   * @param CartProviderInterface $cart_provider
   *   The cart provider.
   */
  public function __construct(CartProviderInterface $cart_provider) {
    $this->cartProvider = $cart_provider;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [
      'commerce_order.place.pre_transition' => 'finalizeAttendee',
    ];
    return $events;
  }

  /**
   * Finalizes order attendees when the order is placed.
   *
   * @param WorkflowTransitionEvent $event
   *   The workflow transition event.
   * @throws
   */
  public function finalizeAttendee(WorkflowTransitionEvent $event) {
    /** @var OrderInterface $order */
    $order = $event->getEntity();
    $profile = $order->getBillingProfile();

    if ($order->bundle() != 'drupal_camp_ticket') {
      return;
    }

    foreach ($order->getItems() as $commerce_order_item)  {
      $quantity = (int) $commerce_order_item->getQuantity();
      $items = $quantity;
      $field_value = $commerce_order_item->get('field_attendee');
      if (!empty($field_value)
        && $field_value->count() > $quantity) {
        $items = $field_value->count();
      }

      for ($delta = 0; $delta < $items; $delta++) {
        if (empty($commerce_order_item->field_attendee[$delta])) {
          $default_values = [
            'type' => 'attendee',
          ];
          if ($delta == 0 && !empty($profile->address[0])) {
            /** @var AddressItem $address */
            $address = $profile->address[0];
            $default_values['field_attendee_firstname'] = $address->getGivenName();
            $default_values['field_attendee_secondname'] = $address->getFamilyName();
          }
          $paragraph = Paragraph::create($default_values);
          $paragraph->is_new = TRUE;
          $paragraph->save();
          $commerce_order_item->field_attendee[$delta] = [
            'target_id' => $paragraph->id(),
            'target_revision_id' => $paragraph->getRevisionId(),
          ];
        }

        if ($delta >= $quantity) {
          $value = $commerce_order_item->field_attendee[$delta]->getValue();
          $paragraph = empty($value['target_id']) ? NULL : Paragraph::load($value['target_id']);
          if (!empty($paragraph)) {
            $paragraph->delete();
            $commerce_order_item->field_attendee[$delta] = NULL;
          }
        }

        $commerce_order_item->save();
      }
    }
  }

}
