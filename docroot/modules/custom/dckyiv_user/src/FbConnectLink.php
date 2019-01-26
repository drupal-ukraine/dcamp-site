<?php

namespace Drupal\dckyiv_user;

use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class FbConnectLink.
 */
class FbConnectLink {

  /**
   * Symfony\Component\HttpFoundation\RequestStack definition.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Constructs a new FbConnectLink object.
   */
  public function __construct(RequestStack $request_stack) {
    $this->requestStack = $request_stack;
  }

  /**
   * Central place to build the FB connect link.
   *
   * Determine the post login behaviour:
   *  1. postLoginPath uri query parameter
   *  2. method argument
   *  3. default redirect to user edit form.
   *
   * @param \Drupal\Core\Url|null $postLoginUrl
   *   The url object where to redirect user after login.
   *
   * @return \Drupal\Core\Url
   *   The BF connect URL.
   */
  public function getUrl(Url $postLoginUrl = NULL) {
    $postLoginPath = $this->requestStack->getCurrentRequest()->get('postLoginPath');
    $url = Url::fromRoute('simple_fb_connect.redirect_to_fb');

    // Default redirect behaviour is to redirect to user edit form.
    if (empty($postLoginUrl)) {
      $postLoginUrl = Url::fromRoute('<front>');
    }

    // If postLoginPath doesn't exists in the query parameters - use default
    // one, or one defined as method argument.
    if (empty($postLoginPath) && !empty($postLoginUrl)) {
      $postLoginPath = $postLoginUrl->toString();
    }

    if (!empty($postLoginPath)) {
      $url->setRouteParameter('postLoginPath', $postLoginPath);
    }

    return $url;
  }

}
