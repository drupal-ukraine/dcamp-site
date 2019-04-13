<?php

namespace Drupal\commerce_liqpay\PluginForm\OffsiteRedirect;

use Drupal\commerce_payment\PluginForm\PaymentOffsiteForm as BasePaymentOffsiteForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Url;

/**
 * Provides the class for payment off-site form.
 *
 * Provide a buildConfigurationForm() method which calls buildRedirectForm()
 * with the right parameters.
 */
class LiqpaymentForm extends BasePaymentOffsiteForm {

  /**
   * Gateway plugin.
   *
   * @var \Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayInterface
   */
  private $paymentGatewayPlugin;

  /**
   * Getting plugin's configuration.
   *
   * @param string $configuration
   *   Configuration name.
   *
   * @return mixed
   *   Configuration value.
   */
  private function getConfiguration($configuration) {
    return $this->paymentGatewayPlugin->getConfiguration()[$configuration];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    /** @var \Drupal\commerce_payment\Entity\PaymentInterface $payment */
    $payment = $this->entity;
    $this->paymentGatewayPlugin = $payment->getPaymentGateway()->getPlugin();

    $order        = $payment->getOrder();
    $redirect_url = $this->getConfiguration('action_url');
    $total_price  = $order->getTotalPrice();

    $data = [
      'version'     => $this->getConfiguration('version'),
      'public_key'  => $this->getConfiguration('public_key'),
      'sandbox'     => ($this->getConfiguration('mode') == 'test') ? 1 : 0,
      'action'      => $this->getConfiguration('action'),
      'order_id'    => $payment->getOrderId(),
      'language'    => \Drupal::languageManager()->getCurrentLanguage()->getId(),
      'amount'      => $total_price ? $total_price->getNumber() : 0,
      'currency'    => $total_price->getCurrencyCode(),
      'description' => \Drupal::token()->replace($this->getConfiguration('description'), ['commerce_order' => $order]),
      'result_url'  => $form['#return_url'],
    ];

    if ($this->getConfiguration('server_notifications') !== NULL) {
     $data['server_url'] = $this->paymentGatewayPlugin->getNotifyUrl()->toString();
    }

    $private_key       = $this->getConfiguration('private_key');
    $json_data         = base64_encode(Json::encode($data));
    $data['data']      = $json_data;
    $data['signature'] = base64_encode(sha1($private_key . $json_data . $private_key, 1));

    return $this->buildRedirectForm($form, $form_state, $redirect_url, $data, 'post');

  }

}
