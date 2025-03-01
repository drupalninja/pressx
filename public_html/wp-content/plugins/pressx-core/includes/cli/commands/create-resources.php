<?php

/**
 * @file
 * Script to create the resources page.
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Creates the resources page.
 *
 * @param bool $force
 *   Whether to force recreation of the resources page even if it already exists.
 *
 * @return bool
 *   TRUE if successful, FALSE otherwise.
 */
function pressx_create_resources($force = FALSE) {
  // Include the image handler.
  require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'image-handler.php';

  // Get the image ID using the helper function.
  $image_path = plugin_dir_path(dirname(dirname(__FILE__))) . 'includes/images/card.png';
  $image_id = pressx_ensure_image($image_path);

  // Check if the page already exists.
  $existing_page = get_page_by_path('resources');

  if ($existing_page && !$force) {
    WP_CLI::log("Resources page already exists. Skipping.");
    return TRUE;
  }

  // Prepare the page data.
  $page_args = [
    'post_title' => 'Resources',
    'post_name' => 'resources',
    'post_content' => '<h1>Resources</h1>
      <p>Helpful resources to get the most out of PressX.</p>

      <h2>Documentation</h2>
      <p>Comprehensive documentation to help you get started.</p>

      <h2>Tutorials</h2>
      <p>Step-by-step tutorials for common tasks.</p>

      <h2>API Reference</h2>
      <p>Detailed API reference for developers.</p>',
    'post_status' => 'publish',
    'post_type' => 'page',
  ];

  // If the page exists and force is true, update it.
  if ($existing_page && $force) {
    $page_args['ID'] = $existing_page->ID;
    $page_id = wp_update_post($page_args);
    WP_CLI::log("Updated resources page.");
  }
  else {
    // Otherwise, create a new page.
    $page_id = wp_insert_post($page_args);
    WP_CLI::log("Created resources page.");
  }

  // Set the featured image if provided.
  if ($page_id && $image_id) {
    set_post_thumbnail($page_id, $image_id);
  }

  // Set meta fields.
  if ($page_id) {
    update_post_meta($page_id, '_wp_page_template', 'default');
    update_post_meta($page_id, '_next_page_template', 'resources');
  }

  return TRUE;
}
