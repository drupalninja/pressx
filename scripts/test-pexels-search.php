<?php
/**
 * @file
 * Test script for Pexels image search functionality.
 */

// Include the Pexels image handler.
require_once __DIR__ . '/includes/pexels-image-handler.php';

// Define test search queries.
$test_queries = [
  'barista pouring latte art',
  'cozy coffee shop interior',
  'coffee beans roasting',
  'coffee shop owners',
  'espresso machine',
];

echo "Testing Pexels Image Search Functionality\n";
echo "======================================\n\n";

// Test each query.
foreach ($test_queries as $query) {
  echo "Testing query: $query\n";
  echo "-------------------\n";

  // Get image URL.
  $image_url = pressx_get_pexels_image($query);

  if ($image_url) {
    echo "SUCCESS: Found image URL: $image_url\n";
  } else {
    echo "FAILED: No image found for query: $query\n";
  }

  echo "\n";
}

// Test gallery functionality.
echo "Testing Gallery Functionality\n";
echo "==========================\n\n";

$gallery_query = "modern coffee shop";
echo "Testing gallery query: $gallery_query\n";
echo "-------------------------\n";

// Get gallery images (4 images).
$gallery_images = pressx_get_pexels_gallery_images($gallery_query, 4);

if (!empty($gallery_images)) {
  echo "SUCCESS: Found " . count($gallery_images) . " images for gallery\n";

  // Display each image URL.
  foreach ($gallery_images as $index => $image_url) {
    echo "Image " . ($index + 1) . ": $image_url\n";
  }

} else {
  echo "FAILED: No gallery images found for query: $gallery_query\n";
}

echo "\nTest completed.\n";
