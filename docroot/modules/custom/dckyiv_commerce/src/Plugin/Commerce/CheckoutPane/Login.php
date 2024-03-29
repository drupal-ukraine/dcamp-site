<?php

namespace Drupal\dckyiv_commerce\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\Login as CheckoutLogin;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the login pane.
 *
 * @CommerceCheckoutPane(
 *   id = "dckyiv_login",
 *   label = @Translation("DCK: Login or continue as guest"),
 *   default_step = "login",
 * )
 */
class Login extends CheckoutLogin {

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $form = parent::buildPaneForm($pane_form, $form_state, $complete_form);
    // $i = 1;
    // $url = $this->fbConnectLink->getUrl();
    /*$form['returning_customer']['fb_login'] = [
    '#type' => 'link',
    '#title' => $this->t('Login with Facebook'),
    '#url' => '',
    '#attributes' => ['class' => ['fb-login']],
    '#access' => TRUE,
    ];*/
    // @todo Change the autogenerated stub
    return $form;
  }

}
