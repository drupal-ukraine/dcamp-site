<?php

namespace Drupal\dckyiv_commerce\EventSubscriber;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\slack\Slack;
use Drupal\state_machine\Event\WorkflowTransitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Sends us slack notification once order is payed.
 */
class OrderNotifySlack implements EventSubscriberInterface {

  use StringTranslationTrait;

  /**
   * @var \Drupal\slack\Slack
   */
  protected $slack;

  /**
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * OrderNotifySlack constructor.
   *
   * @param \Drupal\slack\Slack $slack
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerChannelFactory
   */
  public function __construct(Slack $slack, LoggerChannelFactoryInterface $loggerChannelFactory) {

    $this->slack = $slack;
    $this->logger = $loggerChannelFactory->get('dckyiv_slack');
  }

  /**
   * Sends an order confirmation email.
   *
   * @param \Drupal\state_machine\Event\WorkflowTransitionEvent $event
   *   The event we subscribed to.
   */
  public function sendSlack(WorkflowTransitionEvent $event) {
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $event->getEntity();

    $author = $order->getCustomer()->getDisplayName();
    $price = $order->getTotalPrice();

    $order_url = Url::fromUserInput('/admin/commerce/orders/' . $order->id(), ['absolute'=> TRUE ]);

    try {
      $this->slack->sendMessage($author . ' has payed ' . $price->getNumber() . ' ' . $price->getCurrencyCode() . ' \n'
        . 'Details: ' .$order_url->toString()
      );
    } catch (\Exception $e) {
      $this->logger->error('DC Kyiv slack error: ' . $e->getMessage());
    }

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = ['commerce_order.place.post_transition' => ['sendSlack', -100]];
    return $events;
  }

}
