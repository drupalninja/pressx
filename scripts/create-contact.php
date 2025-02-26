<?php

/**
 * @file
 * Script to create a test landing page with components from the PressX design.
 */

// Include the image handler.
require_once __DIR__ . '/includes/image-handler.php';

// Get the image ID using the helper function.
$image_path = __DIR__ . '/images/card.png';
$image_id = pressx_ensure_image($image_path);

// Create a new landing page.
$post_data = [
  'post_title'    => 'Contact Us',
  'post_status'   => 'publish',
  'post_type'     => 'landing',
  'post_name'     => 'contact',
];

$post_id = wp_insert_post($post_data);

if (is_wp_error($post_id)) {
  echo "Error creating landing page: " . $post_id->get_error_message() . "\n";
  exit(1);
}

// Add sections based on the PressX screenshot
$image_url = $image_id ? wp_get_attachment_url($image_id) : '';

$sections = [
  [
    '_type' => 'hero',
    'hero_layout' => 'image_top',
    'heading' => 'Contact Us',
    'media' => $image_url,
    'summary' => 'Have a question about PressX? We\'re here to help! Drop us a message and we\'ll get back to you soon.',
  ],
  [
    '_type' => 'embed',
    'script' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d50704.05332036616!2d-122.12246645666515!3d37.413396126075966!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808fb7495bec0189%3A0x42d5d96b3d3ba747!2sMountain%20View%2C%20CA!5e0!3m2!1sen!2sus!4v1677532753348!5m2!1sen!2sus" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
    'max_width' => '800px',
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
echo "http://pressx.ddev.site/landing/{$slug}\n";
echo "http://pressx.ddev.site:3333/{$slug} (Next.js)\n";
