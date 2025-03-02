<?php
/**
 * @file
 * Pexels API image search handler for generating dynamic placeholder images.
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Fetches an image from Pexels based on a search query.
 *
 * @param string $query
 *   The search query to find an image for.
 *
 * @return string|null
 *   The URL of the found image, or null if none found.
 */
function pressx_get_pexels_image($query) {
  // Get the API key from WordPress options or constants.
  $api_key = defined('PEXELS_API_KEY') ? PEXELS_API_KEY : get_option('pressx_pexels_api_key');

  if (empty($api_key)) {
    WP_CLI::warning("Pexels API key not found. Please set PEXELS_API_KEY constant or pressx_pexels_api_key option.");
    return NULL;
  }

  // Prepare the API request.
  $url = 'https://api.pexels.com/v1/search?query=' . urlencode($query) . '&per_page=5&orientation=landscape';
  $args = [
    'headers' => [
      'Authorization' => $api_key,
    ],
  ];

  // Make the API request.
  $response = wp_remote_get($url, $args);

  // Check for errors.
  if (is_wp_error($response)) {
    WP_CLI::warning("Error fetching image from Pexels: " . $response->get_error_message());
    return NULL;
  }

  // Parse the response.
  $body = wp_remote_retrieve_body($response);
  $data = json_decode($body, TRUE);

  // Check if we got any photos.
  if (empty($data['photos'])) {
    WP_CLI::warning("No images found for query: $query");
    return NULL;
  }

  // Sort photos by size to get the highest quality ones.
  usort($data['photos'], function($a, $b) {
    return ($b['width'] * $b['height']) - ($a['width'] * $a['height']);
  });

  // Get a random image from the top results for variety.
  $top_photos = array_slice($data['photos'], 0, min(5, count($data['photos'])));
  $selected_photo = $top_photos[array_rand($top_photos)];

  // Return the original high-resolution image.
  return $selected_photo['src']['original'];
}

/**
 * Fetches multiple images from Pexels based on a search query.
 *
 * @param string $query
 *   The search query to find images for.
 * @param int $count
 *   The number of images to fetch.
 *
 * @return array
 *   An array of image URLs.
 */
function pressx_get_pexels_gallery_images($query, $count = 4) {
  // Get the API key from WordPress options or constants.
  $api_key = defined('PEXELS_API_KEY') ? PEXELS_API_KEY : get_option('pressx_pexels_api_key');

  if (empty($api_key)) {
    WP_CLI::warning("Pexels API key not found. Please set PEXELS_API_KEY constant or pressx_pexels_api_key option.");
    return [];
  }

  // Prepare the API request.
  $url = 'https://api.pexels.com/v1/search?query=' . urlencode($query) . '&per_page=' . intval($count);
  $args = [
    'headers' => [
      'Authorization' => $api_key,
    ],
  ];

  // Make the API request.
  $response = wp_remote_get($url, $args);

  // Check for errors.
  if (is_wp_error($response)) {
    WP_CLI::warning("Error fetching images from Pexels: " . $response->get_error_message());
    return [];
  }

  // Parse the response.
  $body = wp_remote_retrieve_body($response);
  $data = json_decode($body, TRUE);

  // Check if we got any photos.
  if (empty($data['photos'])) {
    WP_CLI::warning("No images found for query: $query");
    return [];
  }

  // Extract the URLs of the photos.
  $images = [];
  foreach ($data['photos'] as $photo) {
    $images[] = $photo['src']['large'];
  }

  return $images;
}

/**
 * Import an image from Pexels into the media library.
 *
 * @param string $image_url
 *   The URL of the image to import.
 * @param string $alt_text
 *   The alt text for the image.
 * @param string $caption
 *   The caption for the image.
 *
 * @return int|WP_Error
 *   The attachment ID or WP_Error on failure.
 */
function pressx_import_pexels_image($image_url = '', $alt_text = '', $caption = '') {
  // Include the file that contains the download_url function.
  require_once ABSPATH . 'wp-admin/includes/file.php';
  require_once ABSPATH . 'wp-admin/includes/media.php';
  require_once ABSPATH . 'wp-admin/includes/image.php';

  // If no image URL is provided, search for one.
  if (empty($image_url)) {
    return NULL;
  }

  // Generate a unique filename.
  $filename = basename($image_url);

  // Clean up the filename by removing URL parameters.
  $filename = preg_replace('/\?.*$/', '', $filename);

  // If the URL doesn't have a file extension, try to determine it.
  if (!pathinfo($filename, PATHINFO_EXTENSION)) {
    $filename .= '.jpg';  // Default to jpg for Pexels images.
  }

  if (empty($alt_text)) {
    $alt_text = 'Pexels Image - ' . sanitize_title($filename);
  }

  if (empty($caption)) {
    $caption = 'Pexels Image - ' . sanitize_title($filename);
  }

  // Download the image.
  $tmp_file = download_url($image_url);
  if (is_wp_error($tmp_file)) {
    WP_CLI::warning("Error downloading image: " . $tmp_file->get_error_message());
    return NULL;
  }

  // Prepare the file array.
  $file_array = [
    'name' => $filename,
    'tmp_name' => $tmp_file,
  ];

  // Check the file type.
  $wp_filetype = wp_check_filetype($filename, NULL);
  if (empty($wp_filetype['type'])) {
    @unlink($tmp_file);
    WP_CLI::warning("Invalid file type for image: $filename");
    return NULL;
  }

  // Import the image into the media library.
  $attachment_id = media_handle_sideload($file_array, 0, $caption);

  // Clean up the temporary file.
  @unlink($tmp_file);

  // Check for errors.
  if (is_wp_error($attachment_id)) {
    WP_CLI::warning("Error importing image: " . $attachment_id->get_error_message());
    return NULL;
  }

  return $attachment_id;
}

