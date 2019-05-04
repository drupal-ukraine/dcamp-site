<?php

namespace Drupal\dckyiv_core\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;
use Drupal\Core\Link;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;

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
    // @TODO Add check paragraph entities has correct type/fields.
    $presentation_paragraph = $items->getEntity();
    $slot_paragraph = $presentation_paragraph->getParentEntity();
    $dateRange = $slot_paragraph->field_time_slot_start_end[0];
    $rfc_dates = $this->rfc3339Date($dateRange->value, $dateRange->end_value);

    $nodes = $items->referencedEntities();
    $node = reset($nodes);

    // @TODO Add check if field value is present.
    $title = $node->title[0]->getValue();
    $place = $presentation_paragraph->field_place[0]->getValue();
    $description = $node->field_presentation_description[0]->getValue();

    $elements = parent::viewElements($items, $langcode);
    $google_url = Link::fromTextAndUrl(
      $this->t('Add to My calendar'),
      Url::fromUri('http://www.google.com/calendar/event', [
        'attributes' => [
          'target' => '_blank',
          'class' => [
            'add-to-calendar',
            ''
          ],
        ],
        'query' => [
          'action' => 'TEMPLATE',
          'text' => $this->t('Session @title', ['@title' => strip_tags($title['value'])]),
          'dates' => $rfc_dates['both'],
          'sprop' => 'website:' . $_SERVER['HTTP_HOST'],
          'location' => $this->getSetting('address'),
          'details' => $this->t("@place \n@description", [
            '@place' => strip_tags($place['value']),
            '@description' => strip_tags($description['value']),
          ]),
          'website' => Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString(),
        ],
      ])
    )->toString();

    $elements[] = [
      '#markup' => $google_url
    ];

    return $elements;
  }

  /**
   * Returns an array containing RFC 3339 formatted start and end dates.
   *
   * @param $start
   *   Start date.
   * @param $end
   *   End date.
   *
   * @return array
   */
  protected function rfc3339Date($start, $end) {
    if (!$end) {
      $end = $start;
    }

    $start_timestamp = strtotime($start . 'UTC');
    $end_timestamp = strtotime($end . 'UTC');
  
    $diff_timestamp = $end_timestamp - $start_timestamp;

    $start_date = gmdate('Ymd', $start_timestamp) . 'T' . gmdate('His', $start_timestamp) . 'Z';
    $local_start_date = date('Ymd', $start_timestamp) . 'T' . date('His', $start_timestamp) . '';
    $end_date = gmdate('Ymd', $end_timestamp) . 'T' . gmdate('His', $end_timestamp) . 'Z';
    $local_end_date = date('Ymd', $end_timestamp) . 'T' . date('His', $end_timestamp) . '';
  
    $diff_hours = str_pad(round(($diff_timestamp / 60) / 60), 2, '0', STR_PAD_LEFT);
    $diff_minutes = str_pad(abs(round($diff_timestamp / 60) - ($diff_hours * 60)), 2, '0', STR_PAD_LEFT);

    $duration = $diff_hours . $diff_minutes;

    return array(
      'start' => $start_date,
      'end' => $end_date,
      'both' => $start_date . '/' . $end_date,
      'local_start' => $local_start_date,
      'local_end' => $local_end_date,
      'duration' => $duration,
    );
  }

}
