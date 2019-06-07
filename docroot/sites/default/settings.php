<?php

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';

/**
 * If there is a local settings or prod file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}

$stage_file_proxy_settings = __DIR__ . "/setting.stage_file_proxy.php";
if (file_exists($stage_file_proxy_settings)) {
  include $stage_file_proxy_settings;
}

$prod_settings = __DIR__ . "/prod.settings.php";
if (file_exists($prod_settings)) {
  include $prod_settings;
}

$config_directories = [
  CONFIG_SYNC_DIRECTORY => '../config/sync',
];

/**
 * The default list of directories that will be ignored by Drupal's file API.
 *
 * By default ignore node_modules and bower_components folders to avoid issues
 * with common frontend tools and recursive scanning of directories looking for
 * extensions.
 *
 * @see file_scan_directory()
 * @see \Drupal\Core\Extension\ExtensionDiscovery::scanDirectory()
 */
$settings['file_scan_ignore_directories'] = [
  'node_modules',
  'bower_components',
];
