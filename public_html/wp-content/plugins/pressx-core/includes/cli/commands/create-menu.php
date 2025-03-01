<?php

/**
 * @file
 * Script to create the main navigation menu.
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Creates the main navigation menu.
 *
 * @param bool $force
 *   Whether to force recreation of the menu even if it already exists.
 *
 * @return bool
 *   TRUE if successful, FALSE otherwise.
 */
function pressx_create_menu($force = FALSE) {
  $menu_name = 'Primary Navigation';
  $menu_exists = wp_get_nav_menu_object($menu_name);

  // If the menu exists and we're not forcing recreation, skip.
  if ($menu_exists && !$force) {
    WP_CLI::log("Menu '$menu_name' already exists. Skipping.");
    return TRUE;
  }

  // If the menu exists and we're forcing recreation, delete it first.
  if ($menu_exists && $force) {
    wp_delete_nav_menu($menu_name);
    WP_CLI::log("Deleted existing menu '$menu_name'.");
  }

  // Create the menu.
  $menu_id = wp_create_nav_menu($menu_name);
  if (is_wp_error($menu_id)) {
    WP_CLI::error("Failed to create menu '$menu_name': " . $menu_id->get_error_message());
    return FALSE;
  }

  WP_CLI::log("Created menu '$menu_name'.");

  // Define menu items.
  $menu_items = [
    [
      'title' => 'Home',
      'url' => home_url('/'),
      'status' => 'publish',
    ],
    [
      'title' => 'Features',
      'url' => home_url('/features/'),
      'status' => 'publish',
    ],
    [
      'title' => 'Pricing',
      'url' => home_url('/pricing/'),
      'status' => 'publish',
    ],
    [
      'title' => 'Resources',
      'url' => home_url('/resources/'),
      'status' => 'publish',
    ],
    [
      'title' => 'Blog',
      'url' => home_url('/blog/'),
      'status' => 'publish',
    ],
    [
      'title' => 'Contact',
      'url' => home_url('/contact/'),
      'status' => 'publish',
    ],
  ];

  // Add menu items.
  foreach ($menu_items as $item) {
    $item_id = wp_update_nav_menu_item($menu_id, 0, [
      'menu-item-title' => $item['title'],
      'menu-item-url' => $item['url'],
      'menu-item-status' => $item['status'],
    ]);

    if (is_wp_error($item_id)) {
      WP_CLI::warning("Failed to add menu item '{$item['title']}': " . $item_id->get_error_message());
    }
    else {
      WP_CLI::log("Added menu item '{$item['title']}'.");
    }
  }

  // Assign menu to primary location.
  $locations = get_theme_mod('nav_menu_locations');
  $locations['primary'] = $menu_id;
  set_theme_mod('nav_menu_locations', $locations);
  WP_CLI::log("Assigned menu to primary location.");

  return TRUE;
}
