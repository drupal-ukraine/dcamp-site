<?php

namespace Drupal\dckyiv_commerce\Form;

use Drupal\commerce_cart\Form\AddToCartForm as AddToCartFormParent;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AddToCartForm
 *
 * @package Drupal\dckyiv_commerce\Form
 */
class AddToCartForm extends AddToCartFormParent {

  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Redirect user to the /cart page after submit.
    $form_state->setRedirect('commerce_cart.page');

  }

}
