<?php

namespace Drupal\dckyiv_user\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\user\Entity\User;
use Drupal\user\ProfileForm;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Drupal\Core\Url;
use Drupal\dckyiv_user\FbConnectLink;
use Drupal\Core\Entity\EntityRepositoryInterface;

/**
 * Class IfwProfileForm.
 */
class DCKUserRegisterForm extends ProfileForm {

  use StringTranslationTrait;
  /**
   * Event Dispatcher object.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * Definition of Drupal\dckyiv_user\FbConnectLink.
   *
   * @var \Drupal\dckyiv_user\FbConnectLink
   */
  protected $fbConnectLink;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    EntityRepositoryInterface $entity_repository,
    LanguageManagerInterface $language_manager,
    EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL,
    TimeInterface $time = NULL,
    FbConnectLink $fbConnectLink
   ) {
    parent::__construct($entity_repository, $language_manager, $entity_type_bundle_info, $time);
    $this->fbConnectLink = $fbConnectLink;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.repository'),
      $container->get('language_manager'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('dckyiv_user.fb_connect_link')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dckyiv_user_public_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $form['#attributes']['class'][] = 'user-register-form';

    $url = $this->fbConnectLink->getUrl();

    $form['fb_login'] = [
      '#type' => 'link',
      '#weight' => -2,
      '#title' => t('Sign Up with Facebook'),
      '#url' => $url,
      '#attributes' => ['class' => ['fb-login']],
      '#access' => TRUE,
    ];

    $form['separator'] = [
      '#weight' => -1,
      '#markup' => '<div class="separator"><span>' . $this->t('or') . '</span></div>',
    ];

    $form['action']['#weight'] = 10;

    // Add information about return_to page.
    $request = $this->getRequest();
    $baseUrl = $request->getSchemeAndHttpHost();
    $referer = $request->server->get('HTTP_REFERER');
    $referer_uri = str_replace($baseUrl, '', $referer);

    if (!empty($referer_uri) && (strpos($referer, $baseUrl) !== FALSE)) {
      $form['ifw_referer'] = [
        '#type' => 'hidden',
        '#value' => $referer_uri,
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    /*
     * Since we have deleted UniqueName constraint from User Entity
     * we have to make that validation in code at this validation method.
     */
    $name = $form_state->getValue('name');
    $query = \Drupal::entityQuery('user')
      ->condition('name', $name)
      ->execute();
    if (!empty($query)) {
      $form_state->setErrorByName('name', 'Your username should be unique');
    }

    // Add subscriber role by default.
    $form_state->setValue(
      'roles',
      [
        'subscriber' => 'subscriber',
      ]
    );
    parent::validateForm($form, $form_state);

  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $account = $this->entity;
    $account->save();
    $form_state->setValue('uid', $account->id());
    $user = User::load($account->id());
    user_login_finalize($user);
  }

}
