<?php

namespace Drupal\dckyiv_core\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Drupal\dckyiv_core\TimetableService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Plugin implementation of formatter with add to calendar link.
 *
 * @FieldFormatter(
 *   id = "entity_reference_entity_view_timetable_presentation",
 *   label = @Translation("Timetable presentation with calendar link"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class TimetablePresentationFormatter extends EntityReferenceEntityFormatter {

  use StringTranslationTrait;

  /**
   * The timetable service.
   *
   * @var \Drupal\dckyiv_core\TimetableService
   */
  protected $timetableService;

  /**
   * The current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $currentRequest;

  /**
   * Constructs a EntityReferenceEntityFormatter instance.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings settings.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entity_display_repository
   *   The entity display repository.
   * @param \Drupal\dckyiv_core\TimetableService $timetable_service
   *   The timetable service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $current_request
   *   The current request.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, LoggerChannelFactoryInterface $logger_factory, EntityTypeManagerInterface $entity_type_manager, EntityDisplayRepositoryInterface $entity_display_repository, TimetableService $timetable_service, RequestStack $request_stack) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings, $logger_factory, $entity_type_manager, $entity_display_repository);
    $this->timetableService = $timetable_service;
    $this->currentRequest = $request_stack->getCurrentRequest();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('logger.factory'),
      $container->get('entity_type.manager'),
      $container->get('entity_display.repository'),
      $container->get('dckyive_core.timetable'),
      $container->get('request_stack')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'address' => 'Mercure Kyiv Congress Hall, м.Київ, вул. Вадима Гетьмана, 6',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $elements = parent::settingsForm($form, $form_state);

    $elements['address'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Address'),
      '#default_value' => $this->getSetting('address'),
      '#required' => TRUE,
    ];

    return $elements;

  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);

    /** @var \Drupal\paragraphs\Entity\Paragraph $presentationParagraph */
    $presentationParagraph = $items->getEntity();
    $nodes = $items->referencedEntities();
    /** @var \Drupal\node\Entity\Node $presentationNode */
    $presentationNode = reset($nodes);
    if (empty($presentationParagraph) || empty($presentationNode)) {
      return $elements;
    }

    $info = $this->timetableService->prepareCalendarInfo($presentationParagraph, $presentationNode);
    if (empty($info)) {
      return $elements;
    }

    $elements[] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['calendar-wrapper row'],
      ],
      // My Google Calendar link
      'google_calendar' => [
        '#type' => 'link',
        '#title' => $this->t('Add to My Google calendar'),
        '#url' => Url::fromUri('http://www.google.com/calendar/event', [
          'attributes' => [
            'target' => '_blank',
            'class' => [
              'add-to-calendar',
              'add-to-google-calendar'
            ],
          ],
          'query' => [
            'action' => 'TEMPLATE',
            'text' => $this->t('Session @title', ['@title' => $info['title']]),
            'dates' => $info['dates']['both'],
            'sprop' => 'website:' . $this->currentRequest->getHttpHost(),
            'location' => $this->getSetting('address'),
            'details' => $this->t("@place \n@description", [
              '@place' => $info['place'],
              '@description' => $info['description'],
            ]),
            'website' => Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString(),
          ],
        ]),
      ],
      // iCalendar link.
      'icalendar' => [
        '#type' => 'link',
        '#title' => $this->t('Add to iCalendar'),
        '#url' => Url::fromRoute('dckyiv_core.ical',
          [
            'paragraph' => $presentationParagraph->id(),
            'node' => $presentationNode->id(),
          ],
          [
            'attributes' => [
              'class' => [
                'add-to-calendar',
                'add-to-icalendar',
              ],
            ],
          ]),
      ]
    ];

    return $elements;
  }

}
