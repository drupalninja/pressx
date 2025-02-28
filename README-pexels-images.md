# Pexels Image Search for PressX

This feature allows you to generate dynamic placeholder images for your AI-generated landing pages using the Pexels API.

## Configuration

1. First, you'll need to get a Pexels API key from [Pexels API](https://www.pexels.com/api/).

2. Add the following to your `wp-config.php` file:

```php
// Enable Pexels image search
define("PRESSX_USE_PEXELS_IMAGES", true);

// Add your Pexels API key
define("PEXELS_API_KEY", "your-api-key-here");
```

## How It Works

1. When generating a landing page, the AI model includes an `image_search` field for each section that can have images.

2. For each section with an `image_search` field, the script:
   - Searches Pexels for relevant images using the provided search phrase
   - Selects a high-quality image based on various criteria (size, aspect ratio, etc.)
   - Downloads and imports the image to the WordPress media library
   - Uses the imported image in the section

3. The script uses the official Pexels API to find high-quality, royalty-free images.

## Gallery Support

For gallery sections, you can use a single search query to retrieve multiple images:

1. The gallery function retrieves multiple images (default: 4) from a single search query
2. All images share the same theme/topic, creating a cohesive gallery
3. Images are selected based on quality and size
4. The script automatically handles pagination and filtering

Example usage:
```php
// Get 4 images for a gallery with the search query "coffee shop interior"
$gallery_images = pressx_get_pexels_gallery_images("coffee shop interior", 4);

// Each image can then be imported to the media library
$attachment_ids = [];
foreach ($gallery_images as $image_url) {
    $attachment_ids[] = pressx_import_pexels_image($image_url, "Gallery Image");
}
```

## Features

- **Official API Integration**: Uses the official Pexels API for reliable access to high-quality images
- **Smart Image Selection**: Sorts images by size and quality to select the best options
- **Gallery Support**: Get multiple themed images from a single search query
- **Automatic Fallback**: Uses default placeholder images if no suitable images are found
- **WordPress Integration**: Seamlessly imports images into the WordPress media library

## Notes

- The script includes fallback to default placeholder images if no suitable images are found
- All images from Pexels are free to use and require no attribution (though attribution is appreciated)
- The Pexels API has rate limits, so the script includes error handling for API failures
- Minimum image dimensions are enforced to ensure high-quality results
