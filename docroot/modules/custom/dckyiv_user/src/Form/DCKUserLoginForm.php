<?php

namespace Drupal\dckyiv_user\Form;

use Drupal\Core\Render\Element;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\user\Form\UserLoginForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\dckyiv_user\FbConnectLink;
use Drupal\Core\Flood\FloodInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\user\UserAuthInterface;
use Drupal\user\UserStorageInterface;

/**
 * Class IfwUserLoginForm.
 *
 * @package Drupal\dckyiv_user\Form
 */
class DCKUserLoginForm extends UserLoginForm {

  use StringTranslationTrait;

  /**
   * Instance of fblink service.
   *
   * @var \Drupal\dckyiv_user\FbConnectLink
   */
  protected $fbConnectLink;

  /**
   * Constructs a new UserLoginForm.
   *
   * @param \Drupal\Core\Flood\FloodInterface $flood
   *   The flood service.
   * @param \Drupal\user\UserStorageInterface $user_storage
   *   The user storage.
   * @param \Drupal\user\UserAuthInterface $user_auth
   *   The user authentication object.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   * @param \Drupal\dckyiv_user\FbConnectLink $fbConnectLink
   *   FbConnect link service instance.
   */
  public function __construct(FloodInterface $flood, UserStorageInterface $user_storage, UserAuthInterface $user_auth, RendererInterface $renderer, FbConnectLink $fbConnectLink) {
    parent::__construct($flood, $user_storage, $user_auth, $renderer);
    $this->fbConnectLink = $fbConnectLink;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('flood'),
      $container->get('entity.manager')->getStorage('user'),
      $container->get('user.auth'),
      $container->get('renderer'),
      $container->get('dckyiv_user.fb_connect_link')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Add information about return_to page.
    $request = $this->getRequest();
    $baseUrl = $request->getSchemeAndHttpHost();
    $referer = $request->server->get('HTTP_REFERER');
    $referer_uri = str_replace($baseUrl, '', $referer);

    // Check that referer is valid.
    $referer_url = NULL;
    if (!empty($referer_uri) && (strpos($referer, $baseUrl) !== FALSE)) {
      $referer_url = Url::fromUserInput($referer_uri);
    }

    $url = $this->fbConnectLink->getUrl($referer_url);
    $form['fb_login'] = [
      '#type' => 'link',
      '#title' => $this->t('Login with Facebook'),
      '#url' => $url,
      '#attributes' => ['class' => ['fb-login']],
      '#access' => TRUE,
    ];

    $form['separator'] = [
      '#markup' => '<div class="separator"><span>' . t('or') . '</span></div>',
    ];

    $config = $this->config('system.site');
    // Display login form:
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#size' => 60,
      '#maxlength' => USERNAME_MAX_LENGTH,
      '#description' => $this->t('Enter your @s username.', ['@s' => $config->get('name')]),
      '#required' => TRUE,
      '#attributes' => [
        'autocorrect' => 'none',
        'autocapitalize' => 'none',
        'spellcheck' => 'false',
        'autofocus' => 'autofocus',
      ],
    ];

    $form['pass'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
      '#size' => 60,
      '#description' => $this->t('Enter the password that accompanies your username.'),
      '#required' => TRUE,
    ];


    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = ['#type' => 'submit', '#value' => $this->t('Log in')];

    $form['separator_second'] = [
      '#markup' => '<div class="separator_second"></div>',
    ];

    $form['register'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['user-register'],
      ],
      'message' => [
        '#markup' => t("Don't have an account? "),
      ],
      'link' => [
        '#type' => 'link',
        '#title' => $this->t('Register'),
        '#url' => Url::fromRoute('user.register'),
      ],
      '#access' => TRUE,
    ];

    $form['#validate'][] = '::validateName';
    $form['#validate'][] = '::validateAuthentication';
    $form['#validate'][] = '::validateFinal';

    if (!empty($referer_uri) && (strpos($referer, $baseUrl) !== FALSE)) {
      $form['ifw_referer'] = [
        '#type' => 'hidden',
        '#value' => $referer_uri,
      ];
    }

    // Use previous class in order not to break styling.
    $form['#attributes']['class'][] = 'user-login-form';

    $elements = Element::getVisibleChildren($form);
    foreach ($elements as $num => $name) {
      $form[$name]['#weight'] = $num;
    }

    // Since we are adding referer info to the form we have to disable cache.
    $form['#cache'] = ['max-age' => 0];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $account = $this->userStorage->load($form_state->get('uid'));
    user_login_finalize($account);
  }

}
