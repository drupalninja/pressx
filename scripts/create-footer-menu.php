<?php

/**
 * @file
 * Script to create default footer menu items.
 */

// Create the menu if it doesn't exist.
$menu_name = 'Footer Navigation';
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
    'title' => 'Privacy Policy',
    'url' => home_url('/privacy-policy'),
    'order' => 1,
  ],
  [
    'title' => 'Terms of Use',
    'url' => home_url('/terms-of-service'),
    'order' => 2,
  ],
  [
    'title' => 'Contact',
    'url' => home_url('/contact'),
    'order' => 3,
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
  $menu_item_data = [
    'menu-item-title' => $item['title'],
    'menu-item-url' => $item['url'],
    'menu-item-status' => 'publish',
    'menu-item-type' => 'custom',
    'menu-item-position' => $item['order'],
  ];

  wp_update_nav_menu_item($menu_id, 0, $menu_item_data);
}

// Assign menu to the footer location.
$locations = get_theme_mod('nav_menu_locations') ?: [];
$locations['footer'] = $menu_id;
set_theme_mod('nav_menu_locations', $locations);

echo "\nFooter menu created successfully! 🎉\n";
echo "----------------------------------------\n";
echo "Menu Name: {$menu_name}\n";
echo "Menu ID: {$menu_id}\n";
echo "Location: footer\n";
echo "Items created:\n";
foreach ($menu_items as $item) {
  echo "- {$item['title']}\n";
}