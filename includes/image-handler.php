<?php

/**
 * @file
 * Helper functions for handling images in PressX scripts.
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Ensures an image exists in the WordPress media library.
 *
 * @param string $image_path
 *   The path to the image file.
 *
 * @return int|null
 *   The attachment ID if successful, NULL otherwise.
 */
function pressx_ensure_image($image_path) {
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
    $existing_attachment = new WP_Query([
      'post_type' => 'attachment',
      'title' => sanitize_file_name($filename),
      'post_status' => 'inherit',
      'posts_per_page' => 1,
    ]);

    if (!$existing_attachment->have_posts()) {
      // Insert the attachment.
      $image_id = wp_insert_attachment($attachment, $target_path);

      // Generate metadata for the attachment.
      $attachment_data = wp_generate_attachment_metadata($image_id, $target_path);
      wp_update_attachment_metadata($image_id, $attachment_data);
    }
    else {
      $image_id = $existing_attachment->posts[0]->ID;
    }
  }

  return $image_id;
}

