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
    'post_status' => 'publish',
    'post_type' => 'landing',
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

  // Add sections based on the PressX design.
  $image_url = $image_id ? wp_get_attachment_url($image_id) : '';

  $sections = [
    [
      '_type' => 'hero',
      'hero_layout' => 'image_bottom',
      'heading' => 'Get in Touch with PressX',
      'summary' => 'We\'d love to hear from you. Reach out to our team with any questions, feedback, or inquiries about our services.',
      'media' => $image_url,
      'link_title' => 'Contact Now',
      'link_url' => '#contact-form',
      'link2_title' => 'Learn More',
      'link2_url' => '/features',
    ],
    [
      '_type' => 'side_by_side',
      'eyebrow' => 'Contact Information',
      'layout' => 'image_right',
      'title' => 'How to Reach Us',
      'summary' => 'Our team is available to assist you with any questions or concerns you may have about PressX.',
      'media' => $image_url,
      'features' => [
        [
          '_type' => 'bullet',
          'text' => 'Email: info@pressx.com',
          'icon' => 'mail',
        ],
        [
          '_type' => 'bullet',
          'text' => 'Phone: (123) 456-7890',
          'icon' => 'phone',
        ],
        [
          '_type' => 'bullet',
          'text' => 'Address: 123 Main St, Anytown, USA',
          'icon' => 'map-pin',
        ],
      ],
    ],
    [
      '_type' => 'text',
      'title' => 'Send Us a Message',
      'body' => '<p>Fill out the form below and we\'ll get back to you as soon as possible.</p>',
      'text_layout' => 'default',
    ],
    [
      '_type' => 'form',
      'title' => 'Contact Form',
      'form_id' => 'contact-form',
    ],
    [
      '_type' => 'card_group',
      'title' => 'Our Team',
      'cards' => [
        [
          'type' => 'stat',
          'heading' => 'Sales Team',
          'body' => 'For inquiries about our products and services',
          'icon' => 'shopping-cart',
        ],
        [
          'type' => 'stat',
          'heading' => 'Support Team',
          'body' => 'For technical assistance and troubleshooting',
          'icon' => 'life-buoy',
        ],
        [
          'type' => 'stat',
          'heading' => 'Development Team',
          'body' => 'For custom development and integration questions',
          'icon' => 'code',
        ],
      ],
    ],
  ];

  // Update meta values in Carbon Fields format.
  if (function_exists('carbon_set_post_meta')) {
    carbon_set_post_meta($page_id, 'sections', $sections);
    WP_CLI::log("Added Carbon Fields sections to the contact page.");
  }
  else {
    WP_CLI::warning("Carbon Fields not available. Sections not added.");
  }

  // Get the post slug.
  $post = get_post($page_id);
  $slug = $post->post_name;

  WP_CLI::success("Contact page created successfully!");
  WP_CLI::log("ID: {$page_id}");
  WP_CLI::log("Slug: {$slug}");
  WP_CLI::log("View your page at:");
  WP_CLI::log("http://pressx.ddev.site/landing/{$slug}");
  WP_CLI::log("http://pressx.ddev.site:3333/{$slug} (Next.js)");

  return TRUE;
}
