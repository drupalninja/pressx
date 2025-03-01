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
    'post_status' => 'publish',
    'post_type' => 'landing',
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

  // Add sections based on the PressX design.
  $image_url = $image_id ? wp_get_attachment_url($image_id) : '';

  $sections = [
    [
      '_type' => 'hero',
      'hero_layout' => 'image_bottom',
      'heading' => 'PressX Resources',
      'summary' => 'Explore our collection of resources to help you get the most out of PressX.',
      'media' => $image_url,
      'link_title' => 'Get Started',
      'link_url' => '#primary-cta',
      'link2_title' => 'Learn More',
      'link2_url' => '#secondary-cta',
    ],
    [
      '_type' => 'recent_posts',
      'title' => 'Latest Articles',
      'post_type' => 'post',
      'count' => 3,
    ],
    [
      '_type' => 'side_by_side',
      'eyebrow' => 'Documentation',
      'layout' => 'image_right',
      'title' => 'Comprehensive Documentation',
      'summary' => 'Access our detailed documentation to learn how to use PressX effectively.',
      'media' => $image_url,
      'link_title' => 'View Docs',
      'link_url' => '#docs',
    ],
    [
      '_type' => 'card_group',
      'title' => 'Helpful Resources',
      'cards' => [
        [
          'type' => 'stat',
          'heading' => 'Tutorials',
          'body' => 'Step-by-step guides to help you get started with PressX.',
          'icon' => 'book',
        ],
        [
          'type' => 'stat',
          'heading' => 'API Reference',
          'body' => 'Detailed API documentation for developers.',
          'icon' => 'code',
        ],
        [
          'type' => 'stat',
          'heading' => 'Community',
          'body' => 'Join our community of developers and get help from peers.',
          'icon' => 'users',
        ],
      ],
    ],
    [
      '_type' => 'text',
      'title' => 'Start Using PressX Today',
      'body' => '<p>Ready to take your web development to the next level? Get started with PressX now.</p>',
      'text_layout' => 'default',
      'link_title' => 'Get Started',
      'link_url' => '#get-started',
    ],
  ];

  // Update meta values in Carbon Fields format.
  if (function_exists('carbon_set_post_meta')) {
    carbon_set_post_meta($page_id, 'sections', $sections);
    WP_CLI::log("Added Carbon Fields sections to the resources page.");
  }
  else {
    WP_CLI::warning("Carbon Fields not available. Sections not added.");
  }

  // Get the post slug.
  $post = get_post($page_id);
  $slug = $post->post_name;

  WP_CLI::success("Resources page created successfully!");
  WP_CLI::log("ID: {$page_id}");
  WP_CLI::log("Slug: {$slug}");
  WP_CLI::log("View your page at:");
  WP_CLI::log("http://pressx.ddev.site/landing/{$slug}");
  WP_CLI::log("http://pressx.ddev.site:3333/{$slug} (Next.js)");

  return TRUE;
}
