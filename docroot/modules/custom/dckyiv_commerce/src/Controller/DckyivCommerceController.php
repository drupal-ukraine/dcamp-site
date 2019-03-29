<?php

namespace Drupal\dckyiv_commerce\Controller;

use Drupal\commerce_order\Entity\OrderItemInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\user\UserInterface;
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
  protected $entity_form_builder;

  /**
   * Constructs a DckyivCommerceController object.
   *
   * @param \Drupal\Core\Entity\EntityFormBuilderInterface $entity_form_builder
   * The entity form builder.
   */
  public function __construct(EntityFormBuilderInterface $entity_form_builder) {
    $this->entityFormBuilder = $entity_form_builder;

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.form_builder')
    );
  }

  /**
   * Attende edit form callback page.
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

}
