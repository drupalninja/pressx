<?php
/**
 * @file
 * Script to create a test landing page with a hero section.
 */

// First, ensure the image exists in WordPress media library.
$image_path = __DIR__ . '/images/card.webp';
$image_id = NULL;

// Check if image exists locally.
if (file_exists($image_path)) {
  // Import the image into WordPress media library if not already there.
  $upload_dir = wp_upload_dir();
  $filename = basename($image_path);
  $wp_filetype = wp_check_filetype($filename);

  // Prepare the file array.
  $attachment = [
    'post_mime_type' => $wp_filetype['type'],
    'post_title' => sanitize_file_name($filename),
    'post_content' => '',
    'post_status' => 'inherit',
  ];

  // Copy the file to the uploads directory.
  $target_path = $upload_dir['path'] . '/' . $filename;
  if (!file_exists($target_path)) {
    copy($image_path, $target_path);
  }

  // Check if image already exists in media library.
  $existing_attachment = get_page_by_title($filename, OBJECT, 'attachment');

  if (!$existing_attachment) {
    // Insert the attachment.
    $image_id = wp_insert_attachment($attachment, $target_path);

    // Generate metadata for the attachment.
    $attachment_data = wp_generate_attachment_metadata($image_id, $target_path);
    wp_update_attachment_metadata($image_id, $attachment_data);
  }
  else {
    $image_id = $existing_attachment->ID;
  }
}

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
$image_url = $image_id ? wp_get_attachment_url($image_id) : '';

$sections = [
  [
    '_type' => 'hero',
    'hero_layout' => 'image_top',
    'heading' => 'Welcome to Our Test Landing Page',
    'summary' => 'This is an automatically generated test page with a hero section',
    'media' => $image_url,
    'link_title' => 'Get Started',
    'link_url' => '#primary-cta',
    'link2_title' => 'Learn More',
    'link2_url' => '#secondary-cta',
    'modifier' => 'max-w-4xl',
  ]
];

// Update meta values in Carbon Fields format.
update_post_meta($post_id, '_sections|||0|value', 'hero');
update_post_meta($post_id, '_sections|hero_layout|0|0|value', 'image_top');
update_post_meta($post_id, '_sections|heading|0|0|value', 'Welcome to Our Test Landing Page');
update_post_meta($post_id, '_sections|summary|0|0|value', 'This is an automatically generated test page with a hero section');
update_post_meta($post_id, '_sections|media|0|0|value', $image_url);
update_post_meta($post_id, '_sections|link_title|0|0|value', 'Get Started');
update_post_meta($post_id, '_sections|link_url|0|0|value', '#primary-cta');
update_post_meta($post_id, '_sections|link2_title|0|0|value', 'Learn More');
update_post_meta($post_id, '_sections|link2_url|0|0|value', '#secondary-cta');
update_post_meta($post_id, '_sections|modifier|0|0|value', 'max-w-4xl');

// Get the post slug.
$post = get_post($post_id);
$slug = $post->post_name;

echo "\nLanding page created successfully! 🎉\n";
echo "----------------------------------------\n";
echo "ID: {$post_id}\n";
echo "Slug: {$slug}\n";
echo "\nView your page at:\n";
echo "http://pressx.ddev.site/landing/{$slug}\n";
echo "http://pressx.ddev.site:3333/landing/{$slug} (Next.js)\n";
