<?php

/**
 * @file
 * Script to create the get started page.
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Creates the get started page.
 *
 * @param bool $force
 *   Whether to force recreation of the get started page even if it already exists.
 *
 * @return bool
 *   TRUE if successful, FALSE otherwise.
 */
function pressx_create_get_started($force = FALSE) {
  // Include the image handler.
  require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'image-handler.php';

  // Get the image ID using the helper function.
  $image_path = plugin_dir_path(dirname(dirname(__FILE__))) . 'includes/images/card.png';
  $image_id = pressx_ensure_image($image_path);

  // Check if the page already exists.
  $existing_page = get_page_by_path('get-started');

  if ($existing_page && !$force) {
    WP_CLI::log("Get started page already exists. Skipping.");
    return TRUE;
  }

  // Prepare the page data.
  $page_args = [
    'post_title' => 'Get Started',
    'post_name' => 'get-started',
    'post_content' => '<h1>Get Started with PressX</h1>
      <p>Follow these steps to get started with PressX.</p>

      <h2>Step 1: Installation</h2>
      <p>Install PressX using the provided installation script.</p>

      <h2>Step 2: Configuration</h2>
      <p>Configure your PressX installation to match your requirements.</p>

      <h2>Step 3: Content Creation</h2>
      <p>Start creating content using the WordPress admin interface.</p>

      <h2>Step 4: Frontend Development</h2>
      <p>Customize the Next.js frontend to match your brand.</p>',
    'post_status' => 'publish',
    'post_type' => 'page',
  ];

  // If the page exists and force is true, update it.
  if ($existing_page && $force) {
    $page_args['ID'] = $existing_page->ID;
    $page_id = wp_update_post($page_args);
    WP_CLI::log("Updated get started page.");
  }
  else {
    // Otherwise, create a new page.
    $page_id = wp_insert_post($page_args);
    WP_CLI::log("Created get started page.");
  }

  // Set the featured image if provided.
  if ($page_id && $image_id) {
    set_post_thumbnail($page_id, $image_id);
  }

  // Set meta fields.
  if ($page_id) {
    update_post_meta($page_id, '_wp_page_template', 'default');
    update_post_meta($page_id, '_next_page_template', 'get-started');
  }

  return TRUE;
}
