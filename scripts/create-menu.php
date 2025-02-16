<?php
/**
 * @file
 * Script to create default menu items.
 */

// Create the menu if it doesn't exist.
$menu_name = 'Primary Navigation';
$menu_exists = wp_get_nav_menu_object($menu_name);

if (!$menu_exists) {
  $menu_id = wp_create_nav_menu($menu_name);
}
else {
  $menu_id = $menu_exists->term_id;
}

// Define the menu items.
$menu_items = [
  [
    'title' => 'Home',
    'url' => home_url('/'),
    'order' => 1,
  ],
  [
    'title' => 'Features',
    'url' => home_url('/features'),
    'order' => 2,
  ],
  [
    'title' => 'Pricing',
    'url' => home_url('/pricing'),
    'order' => 3,
  ],
  [
    'title' => 'Resources',
    'url' => home_url('/resources'),
    'order' => 4,
  ],
  [
    'title' => 'Get Started',
    'url' => home_url('/get-started'),
    'order' => 5,
  ],
  [
    'title' => 'Contact',
    'url' => home_url('/contact'),
    'order' => 6,
  ],
];

// Remove existing menu items.
$existing_items = wp_get_nav_menu_items($menu_id);
if ($existing_items) {
  foreach ($existing_items as $item) {
    wp_delete_post($item->ID, TRUE);
  }
}

// Add the menu items.
foreach ($menu_items as $item) {
  wp_update_nav_menu_item($menu_id, 0, [
    'menu-item-title' => $item['title'],
    'menu-item-url' => $item['url'],
    'menu-item-status' => 'publish',
    'menu-item-type' => 'custom',
    'menu-item-position' => $item['order'],
  ]);
}

// Assign menu to the primary location.
$locations = get_theme_mod('nav_menu_locations');
$locations['primary'] = $menu_id;
set_theme_mod('nav_menu_locations', $locations);

echo "\nMenu created successfully! 🎉\n";
echo "----------------------------------------\n";
echo "Menu Name: {$menu_name}\n";
echo "Menu ID: {$menu_id}\n";
echo "Location: primary\n";
echo "Items created:\n";
foreach ($menu_items as $item) {
  echo "- {$item['title']}\n";
}
