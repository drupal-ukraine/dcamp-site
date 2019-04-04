<?php

namespace Drupal\dckyiv_commerce\Controller;

use Drupal\commerce_order\Entity\OrderItemInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\user\UserInterface;
use Drupal\views\Views;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * DckyivCommerceController.
 */
class DckyivCommerceController extends ControllerBase {

  /**
   * The entity form builder.
   *
   * @var \Drupal\Core\Entity\EntityFormBuilderInterface
   */
  protected $entityFormBuilder;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Constructs a DckyivCommerceController object.
   *
   * @param \Drupal\Core\Entity\EntityFormBuilderInterface $entity_form_builder
   * The entity form builder.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   * The entity field manager.
   */
  public function __construct(EntityFormBuilderInterface $entity_form_builder, EntityFieldManagerInterface $entity_field_manager) {
    $this->entityFormBuilder = $entity_form_builder;
    $this->entityFieldManager = $entity_field_manager;

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.form_builder'),
      $container->get('entity_field.manager')
    );
  }

  /**
   * Attendee edit form callback page.
   *
   * @return array
   *   The attendee form.
   */
  public function attendeeFormEdit(UserInterface $user, OrderItemInterface $commerce_order_item, ParagraphInterface $attendee_paragraph) {
    if ($commerce_order_item->id() != $attendee_paragraph->getParentEntity()->id()
      || $commerce_order_item->getOrder()->getCustomerId() != $user->id()) {
      throw new AccessDeniedHttpException();
    }
    $form = $this->entityFormBuilder->getForm($attendee_paragraph, 'edit_attendee');
    return $form;
  }

  /**
   * Attendees report.
   *
   * @return array
   *   The reports build array.
   */
  public function attendeesReport() {
    $bundle_fields = $this->entityFieldManager->getFieldDefinitions('commerce_order_item', 'drupal_camp_ticket');
    if (empty($bundle_fields['field_t_shirt_size'])
    || empty($bundle_fields['field_t_shirt_size']->getFieldStorageDefinition()->getSetting('allowed_values'))) {
      return ['#markup' => 'Tshirt field setting is wrong'];
    }
    $build = [];
    $allowed_values = $bundle_fields['field_t_shirt_size']->getSetting('allowed_values');
    foreach ($allowed_values as $key => $value) {
      $args = [$key];
      $view = Views::getView('attendees_overview');
      $view->setArguments($args);
      $view->setDisplay('default');
      $view->preExecute();
      $view->execute();
      if (empty($view->result)) {
        continue;
      }
      $build['report_' . $key] = [
        '#type' => 'details',
        '#title' => $this->t('Tshirt size "@size": @count', [
            '@size' => $value,
            '@count' => count($view->result),
          ]),
        '#open' => TRUE,
        'table' => $view->render(),
      ];
    }
    return $build;
  }

}
