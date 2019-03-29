<?php

namespace Drupal\dckyiv_commerce\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\ContentEntityForm;
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
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
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
    $attendee_info_default = 'user';
    if (!$paragraph->field_attendee_firstname->isEmpty() || !$paragraph->field_attendee_secondname->isEmpty()) {
      $attendee_info_default = 'name';
    }
    if (!$paragraph->field_attendee_email->isEmpty()) {
      $attendee_info_default = 'email';
    }
    $form['attende_info'] = [
      '#type' => 'radios',
      '#options' => [
        'user' => t('User'),
        'name' => t('Name'),
        'email' => t('Email'),
      ],
      '#default_value' => $attendee_info_default,
      '#weight' => -1,
    ];
  
    $form['field_attendee_email']['#states'] = [
      'visible' => [
        ':input[name="attende_info"]' => ['value' => 'email'],
      ],
    ];
  
    $form['field_site_user']['#states'] = [
      'visible' => [
        ':input[name="attende_info"]' => ['value' => 'user'],
      ],
    ];
  
  
    $form['field_attendee_firstname']['#states'] = [
      'visible' => [
        ':input[name="attende_info"]' => ['value' => 'name'],
      ],
    ];
  
  
    $form['field_attendee_secondname']['#states'] = [
      'visible' => [
        ':input[name="attende_info"]' => ['value' => 'name'],
      ],
    ];
  
    $form['field_attendee_status']['#access'] = FALSE;
    $form_state->set('commerce_order_item', $this->routeMatch->getParameter('commerce_order_item'));
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $attende_info = $form_state->getValue('attende_info');
    switch ($attende_info) {
      case 'user':
        $form_state->setValue(['field_attendee_email', '0'], NULL);
        $form_state->setValue(['field_attendee_firstname', '0'], NULL);
        $form_state->setValue(['field_attendee_secondname', '0'], NULL);
        break;

      case 'name':
        $form_state->setValue(['field_attendee_email', '0'], NULL);
        $form_state->setValue(['field_site_user', '0', 'target_id'], NULL);
        break;

      case 'email':
        $form_state->setValue(['field_attendee_firstname', '0'], NULL);
        $form_state->setValue(['field_attendee_secondname', '0'], NULL);
        $form_state->setValue(['field_site_user', '0', 'target_id'], NULL);
        break;
    }

    parent::submitForm($form, $form_state);
  }

}
