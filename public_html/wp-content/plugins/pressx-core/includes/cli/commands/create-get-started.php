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
    'post_title' => 'Get Started with PressX',
    'post_name' => 'get-started',
    'post_status' => 'publish',
    'post_type' => 'landing',
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

  // Add sections based on the PressX design.
  $image_url = $image_id ? wp_get_attachment_url($image_id) : '';

  $sections = [
    [
      '_type' => 'hero',
      'hero_layout' => 'image_bottom',
      'heading' => 'Get Started with PressX',
      'summary' => 'Follow these simple steps to begin your journey with PressX and unlock the full potential of your web development projects.',
      'media' => $image_url,
      'link_title' => 'Start Now',
      'link_url' => '#step1',
      'link2_title' => 'Learn More',
      'link2_url' => '/features',
    ],
    [
      '_type' => 'side_by_side',
      'eyebrow' => 'Step 1',
      'layout' => 'image_right',
      'title' => 'Installation',
      'summary' => 'Install PressX using the provided installation script. Our streamlined setup process ensures you can get up and running quickly.',
      'media' => $image_url,
      'link_title' => 'Installation Guide',
      'link_url' => '#installation',
    ],
    [
      '_type' => 'side_by_side',
      'eyebrow' => 'Step 2',
      'layout' => 'image_left',
      'title' => 'Configuration',
      'summary' => 'Configure your PressX installation to match your specific requirements. Our flexible settings allow for complete customization.',
      'media' => $image_url,
      'link_title' => 'Configuration Guide',
      'link_url' => '#configuration',
    ],
    [
      '_type' => 'side_by_side',
      'eyebrow' => 'Step 3',
      'layout' => 'image_right',
      'title' => 'Content Creation',
      'summary' => 'Start creating content using the WordPress admin interface. PressX extends WordPress with powerful content management capabilities.',
      'media' => $image_url,
      'link_title' => 'Content Guide',
      'link_url' => '#content',
    ],
    [
      '_type' => 'side_by_side',
      'eyebrow' => 'Step 4',
      'layout' => 'image_left',
      'title' => 'Frontend Development',
      'summary' => 'Customize the Next.js frontend to match your brand. PressX provides a modern development experience with all the tools you need.',
      'media' => $image_url,
      'link_title' => 'Frontend Guide',
      'link_url' => '#frontend',
    ],
    [
      '_type' => 'text',
      'title' => 'Ready to Begin?',
      'body' => '<p>Now that you understand the process, it\'s time to start your PressX journey. Our comprehensive documentation and support team are here to help you every step of the way.</p>',
      'text_layout' => 'default',
      'link_title' => 'Start Installation',
      'link_url' => '#installation',
      'link2_title' => 'Contact Support',
      'link2_url' => '/contact',
    ],
  ];

  // Update meta values in Carbon Fields format.
  if (function_exists('carbon_set_post_meta')) {
    carbon_set_post_meta($page_id, 'sections', $sections);
    WP_CLI::log("Added Carbon Fields sections to the get started page.");
  }
  else {
    WP_CLI::warning("Carbon Fields not available. Sections not added.");
  }

  // Get the post slug.
  $post = get_post($page_id);
  $slug = $post->post_name;

  WP_CLI::success("Get started page created successfully!");
  WP_CLI::log("ID: {$page_id}");
  WP_CLI::log("Slug: {$slug}");
  WP_CLI::log("View your page at:");
  WP_CLI::log("http://pressx.ddev.site/landing/{$slug}");
  WP_CLI::log("http://pressx.ddev.site:3333/{$slug} (Next.js)");

  return TRUE;
}
