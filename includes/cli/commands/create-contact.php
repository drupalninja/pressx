<?php

/**
 * @file
 * Script to create the contact page.
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Creates the contact page.
 *
 * @param bool $force
 *   Whether to force recreation of the contact page even if it already exists.
 *
 * @return bool
 *   TRUE if successful, FALSE otherwise.
 */
function pressx_create_contact($force = FALSE) {
  // Include the image handler.
  require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'image-handler.php';

  // Get the image ID using the helper function.
  $image_path = plugin_dir_path(dirname(dirname(__FILE__))) . 'includes/images/card.png';
  $image_id = pressx_ensure_image($image_path);

  // Check if the page already exists.
  $existing_page = get_page_by_path('contact');

  if ($existing_page && !$force) {
    WP_CLI::log("Contact page already exists. Skipping.");
    return TRUE;
  }

  // Prepare the page data.
  $page_args = [
    'post_title' => 'Contact Us',
    'post_name' => 'contact',
    'post_content' => '<h1>Contact Us</h1>
      <p>We\'d love to hear from you. Please use the form below to get in touch.</p>

      <h2>Contact Information</h2>
      <p>Email: info@pressx.com</p>
      <p>Phone: (123) 456-7890</p>
      <p>Address: 123 Main St, Anytown, USA</p>',
    'post_status' => 'publish',
    'post_type' => 'page',
  ];

  // If the page exists and force is true, update it.
  if ($existing_page && $force) {
    $page_args['ID'] = $existing_page->ID;
    $page_id = wp_update_post($page_args);
    WP_CLI::log("Updated contact page.");
  }
  else {
    // Otherwise, create a new page.
    $page_id = wp_insert_post($page_args);
    WP_CLI::log("Created contact page.");
  }

  // Set the featured image if provided.
  if ($page_id && $image_id) {
    set_post_thumbnail($page_id, $image_id);
  }

  // Set meta fields.
  if ($page_id) {
    update_post_meta($page_id, '_wp_page_template', 'default');
    update_post_meta($page_id, '_next_page_template', 'contact');
  }

  return TRUE;
}
