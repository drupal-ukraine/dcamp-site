<?php

/**
 * @file
 * Contains \Drupal\dckyiv_iconset\Plugin\SocialMediaLinks\Iconset\DCKyivIconset.
 */

namespace Drupal\dckyiv_iconset\Plugin\SocialMediaLinks\Iconset;

use Drupal\social_media_links\IconsetBase;
use Drupal\social_media_links\IconsetInterface;

/**
 * Provides 'dckyiv_iconset' iconset.
 *
 * @Iconset(
 *   id = "dckyiv_iconset",
 *   name = "DCKyiv iconset",
 * )
 */
class DCKyivIconset extends IconsetBase implements IconsetInterface {

  public function getStyle() {
  }

  public function getIconPath($iconName, $style) {
  }
}
