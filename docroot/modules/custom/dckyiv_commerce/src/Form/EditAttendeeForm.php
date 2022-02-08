<?php

namespace Drupal\dckyiv_commerce\Form;

use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_order\Entity\OrderItemInterface;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for profile forms.
 */
class EditAttendeeForm extends ContentEntityForm {

  /**
   * Constructs a ContentEntityForm object.
   *
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository service.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface|null $entity_type_bundle_info
   *   The entity type bundle service.
   * @param \Drupal\Component\Datetime\TimeInterface|null $time
   *   The time service.
   */
  public function __construct(
    EntityRepositoryInterface $entity_repository,
    RouteMatchInterface $route_match,
    EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL,
    TimeInterface $time = NULL) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.repository'),
      $container->get('current_route_match'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $build_info = $form_state->getBuildInfo();
    $paragraph = $build_info['callback_object']->getEntity();
    $form_state->set('paragraph', $paragraph);
    $commerce_order_item = $this->routeMatch->getParameter('commerce_order_item');
    $form_state->set('commerce_order_item', $commerce_order_item);

    $field = $commerce_order_item->get('field_t_shirt_size');
    $definition = $field->getFieldDefinition();
    $settings = $definition->getSettings();
    $form['order_item_settings'] = [
      '#type' => 'container',
      '#tree' => TRUE,
    ];
    $form['order_item_settings']['field_t_shirt_size'] = [
      '#type' => 'select',
      '#title' => $definition->label(),
      '#options' => $settings['allowed_values'],
      '#default_value' => !empty($field[0]->value) ? $field[0]->value : '_none_',
      '#required' => TRUE,
    ];

    $field = $commerce_order_item->get('field_t_shirt_type');
    $definition = $field->getFieldDefinition();
    $settings = $definition->getSettings();
    $form['order_item_settings']['field_t_shirt_type'] = [
      '#type' => 'select',
      '#title' => $definition->label(),
      '#options' => $settings['allowed_values'],
      '#default_value' => !empty($field[0]->value) ? $field[0]->value : '_none_',
      '#required' => TRUE,
    ];
    $form['field_attendee_status']['#access'] = FALSE;
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
    $paragraph = $form_state->get('paragraph');
    /** @var \Drupal\commerce_order\Entity\OrderItemInterface $original_commerce_order_item */
    $original_commerce_order_item = $form_state->get('commerce_order_item');
    /** @var \Drupal\commerce_order\Entity\OrderInterface $commerce_order */
    $commerce_order = $original_commerce_order_item->getOrder();
    $commerce_order_item = NULL;
    $order_item_settings = $form_state->getValue('order_item_settings');

    // Checking existing ordet item with changed parameters.
    /** @var \Drupal\commerce_order\Entity\OrderItemInterface $item */
    foreach ($commerce_order->getItems() as $item) {
      if ($item->field_t_shirt_type->value == $order_item_settings['field_t_shirt_type']
        && $item->field_t_shirt_size->value == $order_item_settings['field_t_shirt_size']) {
        $commerce_order_item = $item;
        break;
      }
    }

    // Creating new order item if there is no item with requested parameters.
    if (empty($commerce_order_item)) {
      $commerce_order_item = OrderItem::create([
        'title' => $original_commerce_order_item->getTitle(),
        'type' => 'drupal_camp_ticket',
        'field_t_shirt_type' => $order_item_settings['field_t_shirt_type'],
        'purchased_entity' => $original_commerce_order_item->getPurchasedEntity(),
        'field_t_shirt_size' => $order_item_settings['field_t_shirt_size'],
        'unit_price' => $original_commerce_order_item->getUnitPrice(),
      ]);
      $commerce_order_item->save();
      $commerce_order_item = $this->reloadEntity($commerce_order_item);
      $commerce_order->addItem($commerce_order_item);
      $commerce_order->save();
    }

    // Change order item for attendee.
    if ($commerce_order_item->id() != $original_commerce_order_item->id()) {
      // Lookup attendee paragraph key.
      foreach ($original_commerce_order_item->field_attendee as $key => $value) {
        if ($value->get('entity')->getTarget()->getValue()->id() == $paragraph->id()) {
          // Remove attendee from previous order item.
          $original_commerce_order_item->field_attendee->removeItem($key);
          $this->saveOrderItem($original_commerce_order_item);

          // Add attendee to new order item.
          $commerce_order_item->field_attendee->appendItem($paragraph);
          $this->saveOrderItem($commerce_order_item);
          $paragraph->setParentEntity($commerce_order_item, 'field_attendee');
          $paragraph->save();

          // Remove order item if there is not items.
          if ($original_commerce_order_item->field_attendee->isEmpty()) {
            $commerce_order->removeItem($original_commerce_order_item);
            $commerce_order->save();
            $original_commerce_order_item->delete();
          }
        }
      }
    }

    parent::submitForm($form, $form_state);
  }

  /**
   * Reloads the given entity from the storage and returns it.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to be reloaded.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The reloaded entity.
   */
  protected function reloadEntity(EntityInterface $entity) {
    $controller = $this->entityTypeManager->getStorage($entity->getEntityTypeId());
    $controller->resetCache([$entity->id()]);
    return $controller->load($entity->id());
  }

  /**
   * Saves order item with additinonal actions.
   *
   * @param \Drupal\commerce_order\Entity\OrderItemInterface $order_item
   *   Order item.
   */
  protected function saveOrderItem(OrderItemInterface $order_item) {
    $order_item->setQuantity(count($order_item->field_attendee));
    $order_item->save();
  }

}
