<?php

/**
 * @file
 * Script to create the features page.
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Creates the features page.
 *
 * @param bool $force
 *   Whether to force recreation of the features page even if it already exists.
 *
 * @return bool
 *   TRUE if successful, FALSE otherwise.
 */
function pressx_create_features($force = FALSE) {
  // Include the image handler.
  require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'image-handler.php';

  // Get the image ID using the helper function.
  $image_path = plugin_dir_path(dirname(dirname(__FILE__))) . 'includes/images/card.png';
  $image_id = pressx_ensure_image($image_path);

  // Check if the page already exists.
  $existing_page = get_page_by_path('features');

  if ($existing_page && !$force) {
    WP_CLI::log("Features page already exists. Skipping.");
    return TRUE;
  }

  // Prepare the page data.
  $page_args = [
    'post_title' => 'Features',
    'post_name' => 'features',
    'post_content' => '<h1>Features</h1>
      <p>Discover all the powerful features of PressX.</p>

      <h2>Headless WordPress</h2>
      <p>Use WordPress as a headless CMS with a modern frontend.</p>

      <h2>Next.js Integration</h2>
      <p>Leverage the power of Next.js for a fast, SEO-friendly frontend.</p>

      <h2>GraphQL API</h2>
      <p>Access your content through a flexible GraphQL API.</p>',
    'post_status' => 'publish',
    'post_type' => 'page',
  ];

  // If the page exists and force is true, update it.
  if ($existing_page && $force) {
    $page_args['ID'] = $existing_page->ID;
    $page_id = wp_update_post($page_args);
    WP_CLI::log("Updated features page.");
  }
  else {
    // Otherwise, create a new page.
    $page_id = wp_insert_post($page_args);
    WP_CLI::log("Created features page.");
  }

  // Set the featured image if provided.
  if ($page_id && $image_id) {
    set_post_thumbnail($page_id, $image_id);
  }

  // Set meta fields.
  if ($page_id) {
    update_post_meta($page_id, '_wp_page_template', 'default');
    update_post_meta($page_id, '_next_page_template', 'features');
  }

  return TRUE;
}
