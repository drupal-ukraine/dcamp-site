<?php

namespace Drupal\dckyiv_commerce\Controller;

use Drupal\commerce_order\Entity\OrderItemInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Url;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\user\UserInterface;
use Drupal\views\Views;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
   * The mail manager service.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Constructs a DckyivCommerceController object.
   *
   * @param \Drupal\Core\Entity\EntityFormBuilderInterface $entity_form_builder
   * The entity form builder.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   * The entity field manager.
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   * The mail manager service.
   */
  public function __construct(EntityFormBuilderInterface $entity_form_builder, EntityFieldManagerInterface $entity_field_manager, MailManagerInterface $mail_manager, Renderer $renderer) {
    $this->entityFormBuilder = $entity_form_builder;
    $this->entityFieldManager = $entity_field_manager;
    $this->mailManager = $mail_manager;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.form_builder'),
      $container->get('entity_field.manager'),
      $container->get('plugin.manager.mail'),
      $container->get('renderer')
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
   * Tshirts report.
   *
   * @return array
   *   The reports build array.
   */
  public function tshirtsReport() {
    $bundle_fields = $this->entityFieldManager->getFieldDefinitions('commerce_order_item', 'drupal_camp_ticket');
    if (empty($bundle_fields['field_t_shirt_size'])
    || empty($bundle_fields['field_t_shirt_size']->getFieldStorageDefinition()->getSetting('allowed_values'))) {
      return ['#markup' => 'Tshirt field setting is wrong'];
    }
    $build = [];
    $allowed_values = $bundle_fields['field_t_shirt_size']->getSetting('allowed_values');

    $total = 0;

    foreach ($allowed_values as $key => $value) {
      $args = [$key];
      $view = Views::getView('attenders_overview');
      $view->setArguments($args);
      $view->setDisplay('tshirts_overview');
      $view->preExecute();
      $view->execute();
      if (empty($view->result)) {
        continue;
      }

      $count = count($view->result);

      $build['report_' . $key] = [
        '#type' => 'details',
        '#title' => $this->t('Tshirt size "@size": @count', [
            '@size' => $value,
            '@count' => $count,
          ]),
        '#open' => FALSE,
        'table' => $view->render(),
      ];

      $total +=$count;
    }

    $build['total'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => 'Total tickets:' . $total,

    ];

    return $build;
  }

  /**
   * Attendees report.
   *
   * @return array
   *   The reports build array.
   */
  public function attendeesReport() {
    $field_name = 'field_attendee_status';
    $bundle_fields = $this->entityFieldManager->getFieldDefinitions('paragraph', 'attendee');
    if (empty($bundle_fields[$field_name])
      || empty($bundle_fields[$field_name]->getSetting('allowed_values'))) {
      return ['#markup' => 'No attendees in this list'];
    }
    $build = [];
    $allowed_values = $bundle_fields[$field_name]->getSetting('allowed_values');


    $total = 0;

    foreach ($allowed_values as $key => $value) {
      $args = [$key];
      $view = Views::getView('attenders_overview');
      $view->setArguments($args);
      $view->setDisplay('attendees_overview');
      $view->preExecute();
      $view->execute();
      if (empty($view->result)) {
        continue;
      }

      $count = count($view->result);

      $build['report_' . $key] = [
        '#type' => 'details',
        '#title' => $this->t('@title: @count', [
          '@title' => $value,
          '@count' => $count,
        ]),
        '#open' => FALSE,
        'table' => $view->render(),
      ];

      $total +=$count;
    }

    $build['total'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => 'Total tickets:' . $total,

    ];

    return $build;
  }

  /**
   * Attendee send ticket callback page.
   *
   * @param UserInterface $user
   * @param OrderItemInterface $commerce_order_item
   * @param ParagraphInterface $attendee_paragraph
   *
   * @return array|RedirectResponse
   */
  public function sendTicket(UserInterface $user, OrderItemInterface $commerce_order_item, ParagraphInterface $attendee_paragraph) {
    $to = $attendee_paragraph->get('field_attendee_email')->value;
    if (empty($to)) {
      $message = $this->t('Mail was not sent to attendee from order :order_id. Empty recipient address.', [
        ':order_id' => $commerce_order_item->getOrderId(),
      ]);
      $this->getLogger('dckyiv_commerce')->warning($message);
      $this->messenger()->addWarning($message);
    }
    else {
      $order = $commerce_order_item->getOrder();
      $params = [
        'headers' => [
          'Content-Type' => 'text/html; charset=UTF-8;',
          'Content-Transfer-Encoding' => '8Bit',
        ],
        'from' => $order->getStore()->getEmail(),
        'subject' => 'Ticket info',
        'attendee_paragraph' => $attendee_paragraph,
      ];

      $viewBuilder = $this->entityTypeManager()->getViewBuilder('paragraph');
      $build = $viewBuilder->view($attendee_paragraph, 'mail');
      $params['body'] = \Drupal::service('renderer')->executeInRenderContext(new RenderContext(), function () use ($build) {
        return $this->renderer->render($build);
      });
      $langcode = $this->languageManager()->getDefaultLanguage()->getId();

      $result = $this->mailManager->mail('commerce_order', 'receipt', $to, $langcode, $params);
      if ($result['send']) {
        $attendee_paragraph->set('field_attendee_email_sent', time());
        $attendee_paragraph->save();
      }
      $this->messenger()->addStatus($this->t('Ticket has been sent to @mail', [
        '@mail' => $to,
      ]));
    }
    return new RedirectResponse(Url::fromRoute('view.my_camp_tickets.my_tickets_page', [
      'user' => $user->id(),
    ])->toString());
  }

}
