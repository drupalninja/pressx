<?php

/**
 * @file
 * Script to create a Get Started landing page based on the provided design.
 */

// Include the image handler.
require_once __DIR__ . '/includes/image-handler.php';

// Get the image ID using the helper function.
$image_path = __DIR__ . '/images/card.png';
$image_id = pressx_ensure_image($image_path);

// Create a new landing page.
$post_data = [
  'post_title'    => 'Get Started',
  'post_status'   => 'publish',
  'post_type'     => 'landing',
  'post_name'     => 'get-started',
];

$post_id = wp_insert_post($post_data);

if (is_wp_error($post_id)) {
  echo "Error creating landing page: " . $post_id->get_error_message() . "\n";
  exit(1);
}

// Add the hero section.
$image_url = $image_id ? wp_get_attachment_url($image_id) : '';

$sections = [
  [
    '_type' => 'hero',
    'hero_layout' => 'image_bottom',
    'heading' => 'Get Started',
    'summary' => 'Whether you\'re a designer or a developer, DrupalX provides the perfect foundation to start customizing to your specific needs.',
    'media' => $image_url,
  ],
  [
    '_type' => 'side_by_side',
    'eyebrow' => 'Bootstrap UI kits',
    'layout' => 'image_right',
    'title' => 'DrupalX for Designers',
    'summary' => 'Leverage a variety of Figma community templates built on Bootstrap 5 to jumpstart your design process.',
    'media' => $image_url,
    'link_title' => 'Browse Figma UI kits',
    'link_url' => '#figma-kits',
  ],
  [
    '_type' => 'side_by_side',
    'eyebrow' => 'Project template',
    'layout' => 'image_left',
    'title' => 'DrupalX for Developers',
    'summary' => 'Visit our GitHub repository to download the DrupalX project template and start building your site today.',
    'media' => $image_url,
    'link_title' => 'Find out more',
    'link_url' => '#github-repo',
  ],
];

// Update meta values in Carbon Fields format.
carbon_set_post_meta($post_id, 'sections', $sections);

// Get the post slug.
$post = get_post($post_id);
$slug = $post->post_name;

echo "\nLanding page created successfully! 🎉\n";
echo "----------------------------------------\n";
echo "ID: {$post_id}\n";
echo "Slug: {$slug}\n";
echo "\nView your page at:\n";
echo "http://pressx.ddev.site/{$slug}\n";
echo "http://pressx.ddev.site:3333/{$slug} (Next.js)\n";
