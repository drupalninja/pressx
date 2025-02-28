<?php
/**
 * @file
 * Pexels API image search handler for generating dynamic placeholder images.
 */

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
    // Explicitly read the API key from wp-config.php.
    $wp_config_path = dirname(dirname(__DIR__)) . '/public_html/wp-config.php';

    if (file_exists($wp_config_path)) {
        $wp_config_contents = file_get_contents($wp_config_path);
        preg_match("/define\(\s*'PEXELS_API_KEY'\s*,\s*'([^']+)'\s*\);/", $wp_config_contents, $matches);
        $api_key = $matches[1] ?? '';
    } else {
        $api_key = defined('PEXELS_API_KEY') ? PEXELS_API_KEY : '';
    }

    if (empty($api_key)) {
        error_log('Pexels API key is not configured');
        return NULL;
    }

    $search_url = 'https://api.pexels.com/v1/search?' . http_build_query([
        'query' => $query,
        'per_page' => 5,
        'orientation' => 'landscape'
    ]);

    $ch = curl_init($search_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: ' . $api_key
    ]);

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status !== 200) {
        error_log("Pexels API request failed with status $status");
        return NULL;
    }

    $data = json_decode($response, TRUE);
    if (empty($data['photos'])) {
        return NULL;
    }

    // Sort photos by size to get the highest quality ones
    usort($data['photos'], function($a, $b) {
        return ($b['width'] * $b['height']) - ($a['width'] * $a['height']);
    });

    // Get a random image from the top 5 results
    $selected_photo = $data['photos'][array_rand(array_slice($data['photos'], 0, min(5, count($data['photos']))))];
    return $selected_photo['src']['original'];
}

/**
 * Fetches multiple images from Pexels based on a single search query.
 *
 * @param string $query
 *   The search query to find images for.
 * @param int $count
 *   The number of images to return (default: 4).
 *
 * @return array
 *   An array of image URLs, may be empty if no images found.
 */
function pressx_get_pexels_gallery_images($query, $count = 4) {
    // Explicitly read the API key from wp-config.php.
    $wp_config_path = dirname(dirname(__DIR__)) . '/public_html/wp-config.php';

    if (file_exists($wp_config_path)) {
        $wp_config_contents = file_get_contents($wp_config_path);
        preg_match("/define\(\s*'PEXELS_API_KEY'\s*,\s*'([^']+)'\s*\);/", $wp_config_contents, $matches);
        $api_key = $matches[1] ?? '';
    } else {
        $api_key = defined('PEXELS_API_KEY') ? PEXELS_API_KEY : '';
    }

    if (empty($api_key)) {
        error_log('Pexels API key is not configured');
        return [];
    }

    $search_url = 'https://api.pexels.com/v1/search?' . http_build_query([
        'query' => $query,
        'per_page' => max(10, $count * 2), // Get more images than needed to allow for filtering
        'orientation' => 'landscape'
    ]);

    $ch = curl_init($search_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: ' . $api_key
    ]);

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status !== 200) {
        error_log("Pexels API request failed with status $status");
        return [];
    }

    $data = json_decode($response, TRUE);
    if (empty($data['photos'])) {
        return [];
    }

    // Sort photos by size to get the highest quality ones
    usort($data['photos'], function($a, $b) {
        return ($b['width'] * $b['height']) - ($a['width'] * $a['height']);
    });

    // Get the top images up to the requested count
    $selected_photos = array_slice($data['photos'], 0, $count);

    // Extract just the URLs
    return array_map(function($photo) {
        return $photo['src']['original'];
    }, $selected_photos);
}

/**
 * Downloads an image from Pexels and adds it to the WordPress media library.
 *
 * @param string $image_url
 *   The URL of the image to download.
 * @param string $title
 *   Optional title for the image.
 *
 * @return int|WP_Error
 *   The attachment ID on success, WP_Error on failure.
 */
function pressx_import_pexels_image($image_url, $title = '') {
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    // Generate a title if none provided
    if (empty($title)) {
        $title = 'Pexels Image ' . date('Y-m-d H:i:s');
    }

    // Download the file
    $tmp = download_url($image_url);

    if (is_wp_error($tmp)) {
        error_log("Failed to download image: " . $tmp->get_error_message());
        return $tmp;
    }

    // Prepare file array for media_handle_sideload
    $file_array = [
        'name' => basename($image_url),
        'tmp_name' => $tmp,
    ];

    // If the URL doesn't have a file extension, try to determine it
    if (!pathinfo($file_array['name'], PATHINFO_EXTENSION)) {
        $file_array['name'] .= '.jpg';  // Default to jpg
    }

    // Add the image to the media library
    $attachment_id = media_handle_sideload($file_array, 0, $title);

    // Clean up the temporary file
    if (file_exists($tmp)) {
        @unlink($tmp);
    }

    return $attachment_id;
}
