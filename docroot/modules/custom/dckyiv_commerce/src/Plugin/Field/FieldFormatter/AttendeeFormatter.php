<?php

namespace Drupal\dckyiv_commerce\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Url;
use Drupal\entity_reference_revisions\Plugin\Field\FieldFormatter\EntityReferenceRevisionsEntityFormatter;
use Drupal\field\FieldConfigInterface;

/**
 * Plugin implementation of the 'paragraph_summary' formatter.
 *
 * @FieldFormatter(
 *   id = "paragraph_attendee",
 *   label = @Translation("Attendee rendered entity"),
 *   field_types = {
 *     "entity_reference_revisions"
 *   }
 * )
 */
class AttendeeFormatter extends EntityReferenceRevisionsEntityFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $view_mode = $this->getSetting('view_mode');
    $elements = [];

    $commerce_order_item = $items->getParent()->getValue();
    $quantity = (int) $commerce_order_item->getQuantity();

    $entities = $this->getEntitiesToView($items, $langcode);
    for ($delta = 0; $delta < $quantity; $delta++) {
      if (isset($entities[$delta])) {
        $entity = $entities[$delta];
        // Protect ourselves from recursive rendering.
        static $depth = 0;
        $depth++;
        if ($depth > 20) {
          $this->loggerFactory->get('entity')->error(
            'Recursive rendering detected when rendering entity @entity_type @entity_id. Aborting rendering.',
            [
              '@entity_type' => $entity->getEntityTypeId(),
              '@entity_id' => $entity->id(),
            ]
          );
          return $elements;
        }
        $view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity->getEntityTypeId());
        $elements[$delta] = $view_builder->view($entity, $view_mode, $entity->language()->getId());

        // Add a resource attribute to set the mapping property's value to the
        // entity's url. Since we don't know what the markup of the entity will
        // be, we shouldn't rely on it for structured data such as RDFa.
        if (!empty($items[$delta]->_attributes) && !$entity->isNew() && $entity->hasLinkTemplate('canonical')) {
          $items[$delta]->_attributes += ['resource' => $entity->toUrl()->toString()];
        }
        $depth = 0;

        // Check if attendee info has been filled.
        foreach ($entity->getFields() as $field) {
          if (!$field->getFieldDefinition() instanceof FieldConfigInterface) {
            continue;
          }
          $empty = $entity->get($field->getFieldDefinition()->getName())->isEmpty();
          if (!$empty) {
            break;
          }
        }

        $elements[$delta]['edit-attendee'] = [
          '#type' => 'container',
          '#weight' => 10,
          'link' => [
            '#type' => 'link',
            '#weight' => 10,
            '#title' => $empty ? $this->t('Add info') : $this->t('edit'),
            '#url' => Url::fromRoute('dckyiv_commerce.attendee_edit', [
              'user' => $commerce_order_item->getOrder()->getCustomerId(),
              'commerce_order_item' => $commerce_order_item->id(),
              'attendee_paragraph' => $entity->id(),
            ], [
              'query' => [
                'destination' => \Drupal::service('path.current')->getPath(),
              ],
              'attributes' => [
                'class' => ['use-ajax'],
                'data-dialog-type' => 'modal',
              ],
            ]),
          ],
        ];

        if (!$entity->get('field_attendee_email')->isEmpty()) {
          $elements[$delta]['send-email-attendee'] = [
            '#type' => 'container',
            '#weight' => 10,
          ];
          if (!$entity->get('field_attendee_email_sent')->isEmpty()
          && $ticket_sent = $entity->get('field_attendee_email_sent')->value) {
            $elements[$delta]['send-email-attendee']['sent-info'] = [
              '#type' => 'container',
              'markup' => [
                '#markup' => $this->t('Ticket sent @time', [
                  '@time' => \Drupal::service('date.formatter')->format($ticket_sent, 'short'),
                ]),
              ],
            ];
          }
          $elements[$delta]['send-email-attendee']['link'] = [
            '#type' => 'link',
            '#weight' => 10,
            '#title' => isset($ticket_sent) ? $this->t('resend ticket') : $this->t('send ticket'),
            '#url' => Url::fromRoute('dckyiv_commerce.attendee_send_ticket', [
              'user' => $commerce_order_item->getOrder()->getCustomerId(),
              'commerce_order_item' => $commerce_order_item->id(),
              'attendee_paragraph' => $entity->id(),
            ], [
              'query' => [
                'destination' => \Drupal::service('path.current')->getPath(),
              ],
            ]),
          ];
        }
      }
    }

    return $elements;
  }

}
