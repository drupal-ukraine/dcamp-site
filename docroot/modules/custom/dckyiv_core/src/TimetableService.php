<?php

namespace Drupal\dckyiv_core;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class TimetableService with helper functions.
 *
 * @package Drupal\dckyiv_core
 */
class TimetableService {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $currentRequest;

  /**
   * TimetableService constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Symfony\Component\HttpFoundation\RequestStack $current_request
   *   The current request.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, RequestStack $request_stack) {
    $this->entityTypeManager = $entity_type_manager;
    $this->currentRequest = $request_stack->getCurrentRequest();
  }

  /**
   * Prepare information for calendars.
   *
   * @param $presentationParagraph \Drupal\paragraphs\ParagraphInterface
   *  Presentation paragraph entity.
   * @param $presentationNode \Drupal\node\NodeInterface
   *  Presentation node.
   *
   * @return array
   */
  public function prepareCalendarInfo($presentationParagraph, $presentationNode) {
    /** @var \Drupal\paragraphs\Entity\Paragraph $presentationParagraph */
    $slotParagraph = $presentationParagraph->getParentEntity();
    $dateRange = $slotParagraph->get('field_time_slot_start_end')->getValue();
    if (empty($dateRange)) {
      return [];
    }

    $fieldPlace = $presentationParagraph->get('field_place')->getValue();
    $fieldDescription = $presentationNode->get('field_presentation_description')->getValue();
    $dates = $this->rfc3339Date($dateRange[0]['value'], $dateRange[0]['end_value']);

    return [
      'uid' => $presentationNode->bundle() . '-' . $presentationNode->id() . '@' . $this->currentRequest->getHttpHost(),
      'start_date' => $dates['start'],
      'end_date' => $dates['end'],
      'dates' => $dates,
      'title' => strip_tags($presentationNode->label()),
      'place' => !empty($fieldPlace) ? strip_tags($fieldPlace[0]['value']) : '',
      'description' => !empty($fieldDescription) ? strip_tags($fieldDescription[0]['value']) : '',
    ];
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
