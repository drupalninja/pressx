<?php
/**
 * @file
 * Script to create a test landing page with a hero section.
 */

// Create a new landing page.
$post_data = [
  'post_title'    => 'Test Landing Page ' . time(),
  'post_status'   => 'publish',
  'post_type'     => 'landing',
];

$post_id = wp_insert_post($post_data);

if (is_wp_error($post_id)) {
  echo "Error creating landing page: " . $post_id->get_error_message() . "\n";
  exit(1);
}

// Add the hero section.
$sections = [
  [
    '_type' => 'hero',
    'title' => 'Welcome to Our Test Landing Page',
    'description' => 'This is an automatically generated test page with a hero section',
    'background_image' => '',
    'cta_text' => 'Get Started',
    'cta_link' => '#primary-cta'
  ]
];

// Update meta values in Carbon Fields format.
update_post_meta($post_id, '_sections|||0|value', 'hero');
update_post_meta($post_id, '_sections|title|0|0|value', 'Welcome to Our Test Landing Page');
update_post_meta($post_id, '_sections|description|0|0|value', 'This is an automatically generated test page with a hero section');
update_post_meta($post_id, '_sections|background_image|0|0|value', '');
update_post_meta($post_id, '_sections|cta_text|0|0|value', 'Get Started');
update_post_meta($post_id, '_sections|cta_link|0|0|value', '#primary-cta');

// Get the post slug.
$post = get_post($post_id);
$slug = $post->post_name;

echo "\nLanding page created successfully! 🎉\n";
echo "----------------------------------------\n";
echo "ID: {$post_id}\n";
echo "Slug: {$slug}\n";
echo "\nView your page at:\n";
echo "http://pressx.ddev.site/landing/{$slug}\n";
echo "http://pressx.ddev.site:3000/landing/{$slug} (Next.js)\n";
