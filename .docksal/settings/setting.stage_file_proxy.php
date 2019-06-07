<?php

/**
 * @file
 * File proxy settings file.
 */

if (php_sapi_name() != 'cli' &&
  strpos($_SERVER['HTTP_HOST'], 'https://drupalcampkyiv.org/') === FALSE) {
    $config['stage_file_proxy.settings']['origin'] = 'https://drupalcampkyiv.org/';
}
