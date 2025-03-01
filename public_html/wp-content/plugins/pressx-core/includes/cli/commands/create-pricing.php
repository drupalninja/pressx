<?php

/**
 * @file
 * Script to create the pricing page.
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Creates the pricing page.
 *
 * @param bool $force
 *   Whether to force recreation of the pricing page even if it already exists.
 *
 * @return bool
 *   TRUE if successful, FALSE otherwise.
 */
function pressx_create_pricing($force = FALSE) {
  // Include the image handler.
  require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'image-handler.php';

  // Get the image ID using the helper function.
  $image_path = plugin_dir_path(dirname(dirname(__FILE__))) . 'images/card.png';
  $image_id = pressx_ensure_image($image_path);

  // Check if the page already exists.
  $existing_page = get_page_by_path('pricing');

  if ($existing_page && !$force) {
    WP_CLI::log("Pricing page already exists. Skipping.");
    return TRUE;
  }

  // Prepare the page data.
  $page_args = [
    'post_title' => 'PressX Pricing',
    'post_name' => 'pricing',
    'post_status' => 'publish',
    'post_type' => 'landing',
  ];

  // If the page exists and force is true, update it.
  if ($existing_page && $force) {
    $page_args['ID'] = $existing_page->ID;
    $page_id = wp_update_post($page_args);
    WP_CLI::log("Updated pricing page.");
  }
  else {
    // Otherwise, create a new page.
    $page_id = wp_insert_post($page_args);
    WP_CLI::log("Created pricing page.");
  }

  // Set the featured image if provided.
  if ($page_id && $image_id) {
    set_post_thumbnail($page_id, $image_id);
  }

  // Add the hero section.
  $image_url = $image_id ? wp_get_attachment_url($image_id) : '';

  $sections = [
    [
      '_type' => 'hero',
      'hero_layout' => 'image_bottom',
      'heading' => 'Empower Your Content with PressX Today',
      'summary' => "Discover the power of a decoupled CMS that adapts to your needs. With PressX, you can create, manage, and scale your content effortlessly.",
      'media' => $image_url,
      'link_title' => 'Get Started',
      'link_url' => '#get-started',
      'link2_title' => 'Learn More',
      'link2_url' => '#learn-more',
    ],
    [
      '_type' => 'side_by_side',
      'eyebrow' => 'Explore',
      'layout' => 'image_right',
      'title' => 'Unleash Your Content with PressX',
      'summary' => "PressX is a powerful decoupled CMS that streamlines content management and enhances user experience. With its flexible architecture, you can easily integrate and scale your digital projects.",
      'media' => $image_url,
      'features' => [
        [
          '_type' => 'bullet',
          'text' => 'Seamless integration, customizable solutions, and robust performance for all your content needs.',
          'title' => 'Key Features',
        ],
        [
          '_type' => 'bullet',
          'text' => 'Experience unmatched flexibility and control over your content delivery and management.',
          'title' => 'Why Choose',
        ],
      ],
      'link_title' => 'Learn More',
      'link_url' => '#learn-more',
    ],
    [
      '_type' => 'pricing',
      'eyebrow' => 'Tailored PressX Offerings',
      'title' => 'Unlock the Full Potential of PressX',
      'summary' => 'Tailor your PressX experience: choose between self-managed and full-service options',
      'includes_label' => 'Includes',
      'cards' => [
        [
          'eyebrow' => 'PressX CMS Platform',
          'title' => 'Free',
          'monthly_label' => '',
          'features' => [
            ['text' => 'Full access to open source features'],
            ['text' => 'Community support'],
            ['text' => 'Documentation'],
            ['text' => 'AI development features'],
          ],
          'cta_text' => 'Get Started',
          'cta_link' => '#',
        ],
        [
          'eyebrow' => 'Paid Services',
          'title' => 'Contact Us',
          'monthly_label' => '',
          'features' => [
            ['text' => 'Custom development'],
            ['text' => 'Content migration'],
            ['text' => 'Ongoing support'],
            ['text' => 'Consulting services'],
          ],
          'cta_text' => 'Contact Sales',
          'cta_link' => '#',
        ],
      ],
    ],
    [
      '_type' => 'side_by_side',
      'layout' => 'image_left',
      'title' => 'Get Started with PressX',
      'summary' => 'Join us today and unlock the full potential of your content management experience.',
      'media' => $image_url,
      'link_title' => 'Sign Up',
      'link_url' => '#sign-up',
    ],
  ];

  // Update meta values in Carbon Fields format.
  if (function_exists('carbon_set_post_meta')) {
    carbon_set_post_meta($page_id, 'sections', $sections);
    WP_CLI::log("Added Carbon Fields sections to the pricing page.");
  }
  else {
    WP_CLI::warning("Carbon Fields not available. Sections not added.");
  }

  // Get the post slug.
  $post = get_post($page_id);
  $slug = $post->post_name;

  WP_CLI::success("Pricing page created successfully!");
  WP_CLI::log("ID: {$page_id}");
  WP_CLI::log("Slug: {$slug}");
  WP_CLI::log("View your page at:");
  WP_CLI::log("http://pressx.ddev.site/landing/{$slug}");
  WP_CLI::log("http://pressx.ddev.site:3333/{$slug} (Next.js)");

  return TRUE;
}
