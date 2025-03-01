<?php

/**
 * @file
 * Script to create the landing page.
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Creates the landing page.
 *
 * @param bool $force
 *   Whether to force recreation of the landing page even if it already exists.
 *
 * @return bool
 *   TRUE if successful, FALSE otherwise.
 */
function pressx_create_landing($force = FALSE) {
  // Include the image handler.
  require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'image-handler.php';

  // Get the image ID using the helper function.
  $image_path = plugin_dir_path(dirname(dirname(__FILE__))) . 'includes/images/card.png';
  $image_id = pressx_ensure_image($image_path);

  // Check if the landing page already exists.
  $existing_landing = get_posts([
    'post_type' => 'landing',
    'name' => 'main-landing',
    'post_status' => 'publish',
    'posts_per_page' => 1,
  ]);

  if (!empty($existing_landing) && !$force) {
    WP_CLI::log("Landing page already exists. Skipping.");
    return TRUE;
  }

  // Prepare the landing page data.
  $landing_args = [
    'post_title' => 'Main Landing Page',
    'post_name' => 'main-landing',
    'post_status' => 'publish',
    'post_type' => 'landing',
  ];

  // If the landing page exists and force is true, update it.
  if (!empty($existing_landing) && $force) {
    $landing_args['ID'] = $existing_landing[0]->ID;
    $landing_id = wp_update_post($landing_args);
    WP_CLI::log("Updated landing page.");
  }
  else {
    // Otherwise, create a new landing page.
    $landing_id = wp_insert_post($landing_args);
    WP_CLI::log("Created landing page.");
  }

  // Set the featured image if provided.
  if ($landing_id && $image_id) {
    set_post_thumbnail($landing_id, $image_id);
  }

  // Set Carbon Fields meta.
  if ($landing_id) {
    // Define sections for the landing page.
    $sections = [
      [
        '_type' => 'hero',
        'hero_layout' => 'image_top',
        'heading' => 'Welcome to **PressX**',
        'summary' => 'A modern headless WordPress setup with Next.js frontend.',
        'media' => $image_id,
        'link_title' => 'Get Started',
        'link_url' => '/get-started',
        'link2_title' => 'Learn More',
        'link2_url' => '/features',
      ],
      [
        '_type' => 'text',
        'eyebrow' => 'About PressX',
        'title' => 'Modern Headless WordPress',
        'body' => '<p>PressX is a modern headless WordPress setup with a Next.js frontend. It provides a powerful and flexible way to build websites and applications.</p>',
        'text_layout' => 'default',
        'link_title' => 'Learn More',
        'link_url' => '/features',
      ],
      [
        '_type' => 'card_group',
        'title' => 'Key Features',
        'cards' => [
          [
            'type' => 'stat',
            'heading' => 'Headless WordPress',
            'body' => 'Use WordPress as a headless CMS with a modern frontend.',
            'icon' => 'wordpress',
          ],
          [
            'type' => 'stat',
            'heading' => 'Next.js Integration',
            'body' => 'Leverage the power of Next.js for a fast, SEO-friendly frontend.',
            'icon' => 'nextjs',
          ],
          [
            'type' => 'stat',
            'heading' => 'GraphQL API',
            'body' => 'Access your content through a flexible GraphQL API.',
            'icon' => 'graphql',
          ],
        ],
      ],
    ];

    // Save sections to Carbon Fields meta.
    carbon_set_post_meta($landing_id, 'sections', $sections);
    WP_CLI::log("Added sections to landing page.");
  }

  return TRUE;
}
