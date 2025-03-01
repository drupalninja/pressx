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
  $image_path = plugin_dir_path(dirname(dirname(__FILE__))) . 'images/card.png';
  $image_id = pressx_ensure_image($image_path);

  // Check if the page already exists.
  $existing_page = get_page_by_path('features');

  if ($existing_page && !$force) {
    WP_CLI::log("Features page already exists. Skipping.");
    return TRUE;
  }

  // Prepare the page data.
  $page_args = [
    'post_title' => 'PressX Features',
    'post_name' => 'features',
    'post_status' => 'publish',
    'post_type' => 'landing',
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

  // Add sections based on the PressX design.
  $image_url = $image_id ? wp_get_attachment_url($image_id) : '';

  $sections = [
    [
      '_type' => 'hero',
      'hero_layout' => 'image_bottom',
      'heading' => 'Empower Your Web Development with PressX',
      'summary' => "Discover the future of web development with PressX, where innovation meets reliability. Our platform offers cutting-edge tools that streamline your workflow and elevate your projects.",
      'media' => $image_url,
      'link_title' => 'Get Started',
      'link_url' => '#primary-cta',
      'link2_title' => 'Learn More',
      'link2_url' => '#secondary-cta',
    ],
    [
      '_type' => 'side_by_side',
      'eyebrow' => 'Customizable Components',
      'layout' => 'image_right',
      'title' => 'Discover the Future of Web Development with PressX\'s Innovative Tools',
      'summary' => "At PressX, we empower developers with cutting-edge tools and a modern tech stack, ensuring seamless integration and unparalleled performance that elevate your web projects.",
      'media' => $image_url,
      'features' => [
        [
          '_type' => 'bullet',
          'text' => 'Streamlined workflows for faster project delivery',
          'icon' => 'box',
        ],
        [
          '_type' => 'bullet',
          'text' => 'Streamlined workflows for faster project delivery',
          'icon' => 'box',
        ],
        [
          '_type' => 'bullet',
          'text' => 'User-friendly interface for all skill levels',
          'icon' => 'box',
        ],
      ],
    ],
    [
      '_type' => 'side_by_side',
      'eyebrow' => 'Dependable',
      'layout' => 'image_left',
      'title' => 'Experience Unmatched Reliability and Scalability Today',
      'summary' => "At PressX, we prioritize reliability and scalability to ensure your web projects thrive. Our robust platform adapts seamlessly to your growing needs, empowering you to build with confidence.",
      'media' => $image_url,
      'link_title' => 'Explore',
      'link_url' => '#explore',
    ],
    [
      '_type' => 'card_group',
      'title' => 'Discover Our Cutting-Edge Features',
      'cards' => [
        [
          'type' => 'stat',
          'heading' => 'User-friendly Interface',
          'body' => 'Navigate effortlessly with our intuitive design',
          'icon' => 'box',
        ],
        [
          'type' => 'stat',
          'heading' => 'Customizable Modules',
          'body' => 'Tailor your experience with flexible module options',
          'icon' => 'box',
        ],
        [
          'type' => 'stat',
          'heading' => 'Comprehensive Support',
          'body' => 'Get assistance anytime with our dedicated support team',
          'icon' => 'box',
        ],
      ],
    ],
    [
      '_type' => 'side_by_side',
      'eyebrow' => 'Innovate',
      'layout' => 'image_right',
      'title' => 'Experience the Future of Web Development',
      'summary' => "PressX empowers you to build dynamic websites with ease. Our platform combines cutting-edge technology with user-friendly features.",
      'media' => $image_url,
      'features' => [
        [
          '_type' => 'stat',
          'title' => 'Seamless Integration',
          'summary' => 'Easily connect with various tools and services to enhance your web projects.',
        ],
        [
          '_type' => 'stat',
          'title' => 'Robust Security',
          'summary' => 'Protect your website with advanced security features and regular updates.',
        ],
      ],
      'link_title' => 'Learn More',
      'link_url' => '#learn-more',
    ],
    [
      '_type' => 'text',
      'title' => 'Start Your Journey with PressX',
      'body' => '<p>Unlock your web development potential today with our innovative and user-friendly platform.</p>',
      'text_layout' => 'default',
      'link_title' => 'Get Started',
      'link_url' => '#get-started',
      'link2_title' => 'Learn More',
      'link2_url' => '#learn-more',
    ],
    [
      '_type' => 'accordion',
      'title' => 'FAQs',
      'items' => [
        [
          'title' => 'What is PressX?',
          'body' => 'PressX is a modern web development platform that combines the power of Wordpress with cutting-edge frontend technologies.',
        ],
        [
          'title' => 'Is PressX easy to use?',
          'body' => 'Yes! PressX is designed with user experience in mind, making it accessible for developers of all skill levels.',
        ],
        [
          'title' => 'What features does it offer?',
          'body' => 'PressX offers a comprehensive suite of features including customizable modules, responsive design tools, and robust security features.',
        ],
        [
          'title' => 'Is support available?',
          'body' => 'Absolutely! We provide dedicated support to all our users with various service level options.',
        ],
        [
          'title' => 'Can I try it?',
          'body' => 'Yes, you can try PressX with our free tier or request a demo to see its capabilities firsthand.',
        ],
      ],
    ],
  ];

  // Update meta values in Carbon Fields format.
  if (function_exists('carbon_set_post_meta')) {
    carbon_set_post_meta($page_id, 'sections', $sections);
    WP_CLI::log("Added Carbon Fields sections to the features page.");
  }
  else {
    WP_CLI::warning("Carbon Fields not available. Sections not added.");
  }

  // Get the post slug.
  $post = get_post($page_id);
  $slug = $post->post_name;

  WP_CLI::success("Features page created successfully!");
  WP_CLI::log("ID: {$page_id}");
  WP_CLI::log("Slug: {$slug}");
  WP_CLI::log("View your page at:");
  WP_CLI::log("http://pressx.ddev.site/landing/{$slug}");
  WP_CLI::log("http://pressx.ddev.site:3333/{$slug} (Next.js)");

  return TRUE;
}
