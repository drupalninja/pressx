<?php

/**
 * @file
 * Script to create an AI-generated landing page using OpenRouter or Groq with Pexels image search.
 */

// Include the image handler files.
require_once __DIR__ . '/includes/image-handler.php';
require_once __DIR__ . '/includes/pexels-image-handler.php';

// Get the API keys from wp-config.php.
$_pressx_openrouter_api_key = defined('OPENROUTER_API_KEY') ? OPENROUTER_API_KEY : '';
$_pressx_groq_api_key = defined('GROQ_API_KEY') ? GROQ_API_KEY : '';

// Check if Pexels image search is enabled.
$_pressx_use_pexels_images = defined('PRESSX_USE_PEXELS_IMAGES') ? PRESSX_USE_PEXELS_IMAGES : FALSE;

// Add color and formatting constants at the top of the file
define('COLOR_GREEN', "\033[0;32m");
define('COLOR_YELLOW', "\033[0;33m");
define('COLOR_RED', "\033[0;31m");
define('COLOR_BLUE', "\033[0;34m");
define('COLOR_RESET', "\033[0m");

if (!$_pressx_openrouter_api_key && !$_pressx_groq_api_key) {
  echo COLOR_RED . "❌ Error: Neither OPENROUTER_API_KEY nor GROQ_API_KEY is defined in wp-config.php.\n" . COLOR_RESET;
  echo "Please add at least one of the following to your wp-config.php file:\n";
  echo "'define( 'OPENROUTER_API_KEY', 'your-api-key-here' );'\n";
  echo "'define( 'GROQ_API_KEY', 'your-api-key-here' );'\n";
  exit(1);
}

// Check which API to use based on configuration
$preferred_api = defined('PRESSX_AI_API') ? PRESSX_AI_API : 'openrouter';

// No icon validation is needed

// Check if an existing landing page ID was provided.
$existing_landing_id = isset($argv[1]) ? intval($argv[1]) : 0;

/**
 * Makes an AI request to the configured API.
 *
 * @param string $prompt
 *   The prompt to send to the API.
 * @param string|NULL $system_prompt
 *   Optional system prompt to include.
 *
 * @return array
 *   The API response data.
 */
function make_ai_request($prompt, $system_prompt = NULL) {
    global $_pressx_openrouter_api_key, $_pressx_groq_api_key, $preferred_api;

    // If globals aren't working, get the values directly
    if (empty($_pressx_openrouter_api_key)) {
        $_pressx_openrouter_api_key = defined('OPENROUTER_API_KEY') ? OPENROUTER_API_KEY : '';
    }

    if (empty($_pressx_groq_api_key)) {
        $_pressx_groq_api_key = defined('GROQ_API_KEY') ? GROQ_API_KEY : '';
    }

    if (empty($preferred_api)) {
        $preferred_api = defined('PRESSX_AI_API') ? PRESSX_AI_API : 'openrouter';
    }

    $url = '';
    $headers = [];
    $model = '';

    // Helper function to configure Groq
    $configure_groq = function() use ($_pressx_groq_api_key) {
        return [
            'url' => 'https://api.groq.com/openai/v1/chat/completions',
            'headers' => [
                'Authorization: Bearer ' . $_pressx_groq_api_key,
                'Content-Type: application/json',
            ],
            'model' => defined('GROQ_MODEL') ? GROQ_MODEL : 'llama-3.3-70b-versatile'
        ];
    };

    // Helper function to configure OpenRouter
    $configure_openrouter = function() use ($_pressx_openrouter_api_key) {
        return [
            'url' => 'https://openrouter.ai/api/v1/chat/completions',
            'headers' => [
                'Authorization: Bearer ' . $_pressx_openrouter_api_key,
                'Content-Type: application/json',
                'HTTP-Referer: http://localhost',
                'X-Title: PressX',
            ],
            'model' => defined('OPENROUTER_MODEL') ? OPENROUTER_MODEL : 'mistralai/mixtral-8x7b-instruct'
        ];
    };

    // Initialize config array
    $config = NULL;

    // Try preferred API first
    if ($preferred_api === 'groq' && $_pressx_groq_api_key) {
        $config = $configure_groq();
    } elseif ($preferred_api === 'openrouter' && $_pressx_openrouter_api_key) {
        $config = $configure_openrouter();
    }
    // Handle fallback if preferred API isn't available
    elseif ($preferred_api === 'groq' && !$_pressx_groq_api_key && $_pressx_openrouter_api_key) {
        echo COLOR_YELLOW . "⚠️ Warning: Groq API is configured but GROQ_API_KEY is not set. Falling back to OpenRouter.\n" . COLOR_RESET;
        $config = $configure_openrouter();
    } elseif ($preferred_api === 'openrouter' && !$_pressx_openrouter_api_key && $_pressx_groq_api_key) {
        echo COLOR_YELLOW . "⚠️ Warning: OpenRouter API is configured but OPENROUTER_API_KEY is not set. Falling back to Groq.\n" . COLOR_RESET;
        $config = $configure_groq();
    }

    // Check if we have a valid configuration
    if (!$config) {
        // Just use any available API
        if ($_pressx_groq_api_key) {
            echo COLOR_YELLOW . "🔄 Using available Groq API.\n" . COLOR_RESET;
            $config = $configure_groq();
        } elseif ($_pressx_openrouter_api_key) {
            echo COLOR_YELLOW . "🔄 Using available OpenRouter API.\n" . COLOR_RESET;
            $config = $configure_openrouter();
        } else {
            echo COLOR_RED . "❌ Error: No valid API configuration available.\n" . COLOR_RESET;
            exit(1);
        }
    }

    // Set the configuration
    $url = $config['url'];
    $headers = $config['headers'];
    $model = $config['model'];

    echo COLOR_BLUE . "🔍 Using API: " . ($url === 'https://api.groq.com/openai/v1/chat/completions' ? 'Groq' : 'OpenRouter') . COLOR_RESET . "\n";
    echo COLOR_BLUE . "🤖 Model: " . $model . COLOR_RESET . "\n";

    $data = [
        'model' => $model,
        'messages' => array_filter([
            $system_prompt ? ['role' => 'system', 'content' => $system_prompt] : NULL,
            ['role' => 'user', 'content' => $prompt]
        ]),
        'temperature' => 0.7,
        'max_tokens' => 4000,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status !== 200) {
        throw new Exception("API request failed with status $status: $response");
    }

    $result = json_decode($response, TRUE);
    return $result;
}

// If an existing landing page ID was provided, get its content.
if ($existing_landing_id) {
  $existing_post = get_post($existing_landing_id);

  if (!$existing_post || $existing_post->post_type !== 'landing') {
    echo "Error: Invalid landing page ID or landing page not found.\n";
    exit(1);
  }

  $existing_title = $existing_post->post_title;
  $existing_sections = carbon_get_post_meta($existing_landing_id, 'sections');

  echo "Using existing landing page: \"{$existing_title}\" (ID: {$existing_landing_id})\n";
  echo "Found " . count($existing_sections) . " sections in the existing landing page.\n";
}

// Add a function to display a welcome message
function display_welcome_message() {
  echo COLOR_BLUE . "🚀 Welcome to PressX AI Landing Page Generator\n" . COLOR_RESET;
  echo COLOR_YELLOW . "Create stunning, AI-generated landing pages with ease!\n\n" . COLOR_RESET;
}

// Add a function to validate and sanitize user input
function sanitize_prompt($prompt) {
  // Remove any potentially harmful characters
  $sanitized = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $prompt);

  // Trim and convert to lowercase
  $sanitized = strtolower(trim($sanitized));

  // Ensure the prompt is not empty
  if (empty($sanitized)) {
    echo COLOR_RED . "❌ Error: Prompt cannot be empty.\n" . COLOR_RESET;
    exit(1);
  }

  return $sanitized;
}

// Add a function to provide suggestions if the prompt is too generic
function suggest_prompt_improvements($prompt) {
  $generic_prompts = [
    'coffee' => "Try something more specific like 'artisan coffee shop in urban setting'",
    'shop' => "Be more descriptive, e.g., 'boutique clothing store for sustainable fashion'",
    'business' => "Add more context, like 'tech startup focusing on AI innovation'",
    'website' => "Provide more details, such as 'photography portfolio for wedding photographers'",
  ];

  foreach ($generic_prompts as $keyword => $suggestion) {
    if (strpos($prompt, $keyword) !== false) {
      echo COLOR_YELLOW . "💡 Tip: " . $suggestion . "\n" . COLOR_RESET;
      break;
    }
  }
}

// Modify the script's initial setup
function initialize_ai_landing_page_generator() {
  // Clear the screen (works on most Unix-like systems)
  system('clear');

  // Display welcome message
  display_welcome_message();

  // Check API key configuration
  if (!defined('OPENROUTER_API_KEY') && !defined('GROQ_API_KEY')) {
    echo COLOR_RED . "❌ Error: No AI API keys configured.\n" . COLOR_RESET;
    echo "Please add OPENROUTER_API_KEY or GROQ_API_KEY to your wp-config.php\n";
    exit(1);
  }
}

// Modify the existing script to use the new initialization
initialize_ai_landing_page_generator();

// Include the create-landing.php file to get the template sections.
ob_start();
$original_post_id = $post_id ?? NULL;
include __DIR__ . '/create-landing.php';
$template_sections = $sections;
if ($original_post_id !== NULL) {
  $post_id = $original_post_id;
}
ob_end_clean();

echo COLOR_GREEN . "📋 Loaded " . count($template_sections) . " template sections from create-landing.php.\n" . COLOR_RESET;

// Prompt the user for input with improved guidance
echo COLOR_BLUE . "🌟 What kind of landing page would you like to create?\n" . COLOR_RESET;
echo "Your prompt will be combined with template sections from create-landing.php.\n";
echo COLOR_YELLOW . "Examples:\n" . COLOR_RESET;
echo "  • Create a landing page for a coffee shop\n";
echo "  • Sustainable fashion brand website\n";
echo "  • Tech startup portfolio\n";
echo "\n> ";

// Read and sanitize user input
$prompt = sanitize_prompt(trim(fgets(STDIN)));

// Provide suggestions for improvement if needed
suggest_prompt_improvements($prompt);

// Get the default image ID using the helper function.
$image_path = __DIR__ . '/images/card.png';
$default_image_id = pressx_ensure_image($image_path);
$default_image_url = $default_image_id ? wp_get_attachment_url($default_image_id) : '';

// Define the available section types.
$section_types = [
  'hero',
  'text',
  'side_by_side',
  'card_group',
  'gallery',
  'quote',
  'logo_collection',
  'accordion',
];

// Call OpenRouter API to generate landing page content.
echo COLOR_GREEN . "📝 Generating landing page content based on prompt: \"" . $prompt . "\"...\n" . COLOR_RESET;

$system_prompt = "You are a landing page content generator specialized in creating compelling, targeted content based on user prompts.

Your task is to generate content for a landing page that SPECIFICALLY addresses the user's prompt. The content should be tailored to the topic, business, or purpose described in the prompt.

Generate exactly 6 different section types from this list: hero, pricing, side_by_side, card_group, gallery, quote, logo_collection, accordion.

IMPORTANT: If you include a gallery section, it MUST have EXACTLY 4 media_items. No more, no less.

IMPORTANT: The content MUST be customized to match the user's prompt. For example, if they ask for a coffee shop landing page, all headings, text, and content should be about coffee, cafes, etc.

IMPORTANT FOR CARD GROUPS: When creating a card_group section, you MUST create EXACTLY 3 CARDS.
- Each card MUST include an array of 3 DIFFERENT Lucide icons that are semantically relevant to the card's content.
- The first icon should be the most appropriate/preferred icon.
- The second and third icons are alternative suggestions.
- Icons MUST be valid Lucide icon names.
- Icons should relate to the card's theme or content.

IMPORTANT FOR IMAGES: For each section that can include images (hero, side_by_side, gallery items, etc.), include an \\\"image_search\\\" field with a specific search phrase that would find a relevant image. For example, for a coffee shop, you might use \\\"barista pouring latte art\\\" or \\\"cozy coffee shop interior\\\".

Your response MUST be only valid JSON with an array of 6 sections, each containing at minimum a '_type' field from the section types list. Each section type has specific fields based on its type:

1. hero: Must include heading, summary, image_search, and can optionally include hero_layout, link_title, link_url, link2_title, link2_url.
2. text: Must include title, body, and can optionally include text_layout.
3. side_by_side: Must include title, summary, image_search, and can optionally include layout, features, link_title, link_url.
4. card_group: Must include title, summary, cards array (each with title, body, and icons array with 3 different Lucide icon names).
5. gallery: Must include title, summary, media_items array (each with title, summary, and image_search).
6. quote: Must include quote, author, image_search.
7. logo_collection: Must include title, summary.
8. accordion: Must include title, summary, items array (each with title, body).

Your response format should be valid JSON that looks EXACTLY like this (with your content):
{
  \"sections\": [
    {
      \"_type\": \"card_group\",
      \"cards\": [
        {
          \"title\": \"Card Title\",
          \"body\": \"Card description\",
          \"icons\": [\"preferred-icon\", \"alternative-icon-1\", \"alternative-icon-2\"]
        }
      ]
    }
    ...more sections...
  ]
}";

$user_prompt = $prompt;

// If we have existing sections from a provided ID, include them in the prompt.
if (!empty($existing_sections)) {
  $existing_sections_json = json_encode($existing_sections, JSON_PRETTY_PRINT);
  $user_prompt .= "\n\nHere is the content of an existing landing page titled \"{$existing_title}\" that you should use as a reference or starting point. You can modify, improve, or completely change this content based on my prompt above:\n\n" . $existing_sections_json;
}
else {
  // Otherwise, use the template sections from create-landing.php.
  $template_sections_json = json_encode($template_sections, JSON_PRETTY_PRINT);
  $user_prompt .= "\n\nHere are template sections that you can use as a reference for structure, but you MUST replace all content with new content that specifically addresses my prompt above. The template is just for format reference, not for content:\n\n" . $template_sections_json;
}

try {
  $response_data = make_ai_request($user_prompt, $system_prompt);
} catch (Exception $e) {
  echo "Error: " . $e->getMessage() . "\n";
  exit(1);
}

$sections = NULL;

// Extract the AI response content
$ai_response = $response_data['choices'][0]['message']['content'] ?? '';

// Try to parse the JSON from the response
$json_start = strpos($ai_response, '{');
$json_end = strrpos($ai_response, '}');

if ($json_start !== false && $json_end !== false) {
  $json_content = substr($ai_response, $json_start, $json_end - $json_start + 1);
  $parsed_data = json_decode($json_content, TRUE);

  if (isset($parsed_data['sections'])) {
    $sections = $parsed_data['sections'];
    echo "Successfully extracted sections from response.\n";
  }
}

// If we couldn't parse the response, show an error
if (!$sections) {
  echo "Error: Failed to parse valid JSON sections from the model response.\n";
  echo "Response: " . print_r($ai_response, TRUE) . "\n";
  exit(1);
}

// Ensure we have exactly 6 sections.
if (count($sections) > 6) {
  $sections = array_slice($sections, 0, 6);
  echo "Limiting to 6 sections as requested.\n";
}
elseif (count($sections) < 6) {
  echo "Warning: Only " . count($sections) . " sections were generated (expected 6).\n";
}

// Process the sections to add image URLs and validate Lucide icons.
foreach ($sections as &$section) {
  // Handle image search for sections that need it.
  if ($_pressx_use_pexels_images) {
    if (in_array($section['_type'], ['hero', 'side_by_side', 'quote']) && isset($section['image_search'])) {
      echo COLOR_BLUE . "🖼️ Searching for image: " . $section['image_search'] . COLOR_RESET . "\n";
      $image_url = pressx_get_pexels_image($section['image_search']);

      if ($image_url) {
        echo "Found image: " . $image_url . "\n";
        // Import the image to WordPress media library.
        $image_id = pressx_import_pexels_image($image_url, $section['image_search']);

        if (!is_wp_error($image_id)) {
          $section['media'] = wp_get_attachment_url($image_id);
          echo "Imported image as attachment ID: " . $image_id . "\n";
        } else {
          // Fallback to default image.
          $section['media'] = $default_image_url;
          echo "Failed to import image, using default placeholder.\n";
        }
      } else {
        // Fallback to default image.
        $section['media'] = $default_image_url;
        echo "No image found, using default placeholder.\n";
      }
    } else if (in_array($section['_type'], ['hero', 'side_by_side', 'quote'])) {
      // No image search provided, use default.
      $section['media'] = $default_image_url;
    }

    // Handle gallery items.
    if ($section['_type'] === 'gallery' && isset($section['media_items']) && is_array($section['media_items'])) {
      foreach ($section['media_items'] as &$item) {
        if (isset($item['image_search'])) {
          echo COLOR_BLUE . "🖼️ Searching for gallery image: " . $item['image_search'] . COLOR_RESET . "\n";
          $image_url = pressx_get_pexels_image($item['image_search']);

          if ($image_url) {
            echo "Found gallery image: " . $image_url . "\n";
            $image_id = pressx_import_pexels_image($image_url, $item['image_search']);

            if (!is_wp_error($image_id)) {
              $item['media'] = wp_get_attachment_url($image_id);
              echo "Imported gallery image as attachment ID: " . $image_id . "\n";
            } else {
              // Fallback to default image.
              $item['media'] = $default_image_url;
              echo "Failed to import gallery image, using default placeholder.\n";
            }
          } else {
            // Fallback to default image.
            $item['media'] = $default_image_url;
            echo "No gallery image found, using default placeholder.\n";
          }
        } else {
          // No image search provided, use default.
          $item['media'] = $default_image_url;
        }
      }
    }
  } else {
    // Use default image if Pexels search is disabled.
    if (in_array($section['_type'], ['hero', 'side_by_side', 'quote'])) {
      $section['media'] = $default_image_url;
    }
  }

  // Ensure card groups have exactly 3 items
  if ($section['_type'] === 'card_group' && isset($section['cards'])) {
    // Limit to first 3 cards
    $section['cards'] = array_slice($section['cards'], 0, 3);

    // If fewer than 3 cards, add placeholder cards
    while (count($section['cards']) < 3) {
      $section['cards'][] = [
        'title' => 'Additional Card ' . (count($section['cards']) + 1),
        'body' => 'Placeholder description for additional card.',
        'icons' => ['star'],
        'icon' => 'star'
      ];
      echo "Added placeholder card to reach 3 items.\n";
    }

    foreach ($section['cards'] as &$card) {
      // If icons are not set, default to star
      $icon_options = $card['icons'] ?? ['star'];

      // Validate each icon, keeping the first valid icon as primary
      $validated_icons = [];
      foreach ($icon_options as $icon) {
        // Validate the icon
        $validated_icon_options = validate_lucide_icon($icon);

        // If the first option is not the original icon, try the next options
        if ($validated_icon_options[0] !== $icon) {
          // Try the other options in the order they were returned
          for ($i = 0; $i < count($validated_icon_options); $i++) {
            // If this icon is valid, use it
            if ($validated_icon_options[$i] !== 'star') {
              $validated_icons[] = $validated_icon_options[$i];
              break;
            }
          }
        } else {
          // Original icon was valid, use it
          $validated_icons[] = $icon;
        }
      }

      // If no valid icons were found, default to star
      if (empty($validated_icons)) {
        $validated_icons = ['star'];
      }

      // Use the first validated icon as primary
      $card['icon'] = $validated_icons[0];

      // If more than one icon was found, store alternatives
      if (count($validated_icons) > 1) {
        $card['icon_alternatives'] = array_slice($validated_icons, 1);
      }
    }
  }

  // Ensure gallery sections always have exactly 4 items
  if ($section['_type'] === 'gallery') {
    if (!isset($section['media_items']) || !is_array($section['media_items'])) {
      $section['media_items'] = [];
    }

    // If fewer than 4 items, add more
    while (count($section['media_items']) < 4) {
      $section['media_items'][] = [
        'title' => 'Gallery Item ' . (count($section['media_items']) + 1),
        'summary' => 'Additional gallery item',
        'media' => $default_image_url
      ];
      echo "Added missing gallery item to reach 4 items.\n";
    }

    // If more than 4 items, trim the excess
    if (count($section['media_items']) > 4) {
      echo "Trimming gallery from " . count($section['media_items']) . " to 4 items.\n";
      $section['media_items'] = array_slice($section['media_items'], 0, 4);
    }

    // Add media URLs to all gallery items if not already set
    foreach ($section['media_items'] as &$item) {
      if (!isset($item['media'])) {
        $item['media'] = $default_image_url;
      }
    }
  }

  // Add logo IDs to logo collection.
  if ($section['_type'] === 'logo_collection') {
    $section['logos'] = [
      $default_image_id,
      $default_image_id,
      $default_image_id,
      $default_image_id,
      $default_image_id,
      $default_image_id,
    ];
  }

  // Add media URLs to carousel items if they exist.
  if ($section['_type'] === 'carousel' && isset($section['items'])) {
    foreach ($section['items'] as &$item) {
      $item['media'] = $default_image_url;
    }
  }

  // Add media URLs to card items if they're custom type.
  if ($section['_type'] === 'card_group' && isset($section['cards'])) {
    foreach ($section['cards'] as &$card) {
      if (isset($card['type']) && $card['type'] === 'custom') {
        $card['media'] = $default_image_url;
      }
    }
  }
}

// Create a new landing page with a title based on the prompt.
$title = "AI Landing Page: " . ucfirst(preg_replace('/^create\s+a\s+landing\s+page\s+for\s+a\s+/i', '', $prompt));
$title = trim($title);

// If we're using an existing landing page, include that in the title.
if ($existing_landing_id) {
  $title = "AI Modified: " . $existing_title;
}

$post_data = [
  'post_title'    => $title,
  'post_status'   => 'publish',
  'post_type'     => 'landing',
  'post_name'     => sanitize_title($title),
];

$post_id = wp_insert_post($post_data);

if (is_wp_error($post_id)) {
  echo "Error creating landing page: " . $post_id->get_error_message() . "\n";
  exit(1);
}

// Update meta values in Carbon Fields format.
carbon_set_post_meta($post_id, 'sections', $sections);

// Get the post slug.
$post = get_post($post_id);
$slug = $post->post_name;

echo "\n" . COLOR_GREEN . "🎉 AI Landing page created successfully!\n" . COLOR_RESET;
echo "----------------------------------------\n";
echo COLOR_BLUE . "🆔 ID: " . COLOR_RESET . "{$post_id}\n";
echo COLOR_BLUE . "🔗 Slug: " . COLOR_RESET . "{$slug}\n";
echo "\n" . COLOR_GREEN . "🌐 View your page at:\n" . COLOR_RESET;
echo COLOR_YELLOW . "• WordPress: " . COLOR_RESET . "http://pressx.ddev.site/landing/{$slug}\n";
echo COLOR_YELLOW . "• Next.js: " . COLOR_RESET . "http://pressx.ddev.site:3333/{$slug}\n";

/**
 * Validate a Lucide icon name against the icon names file.
 *
 * @param string $icon
 *   The icon name to validate.
 *
 * @return array
 *   An array of validated icon names.
 */
function validate_lucide_icon($icon) {
  // Path to the Lucide icon names file.
  $icon_file = __DIR__ . '/includes/lucide-icon-names.txt';

  // If file doesn't exist, default to star.
  if (!file_exists($icon_file)) {
    echo "Warning: Lucide icon names file not found. Using default 'star' icon.\n";
    return ['star'];
  }

  // Read icon names from file.
  $icon_names = array_filter(array_map('trim', file($icon_file, FILE_IGNORE_NEW_LINES)));

  // If no icons found, default to star.
  if (empty($icon_names)) {
    echo "Warning: No icon names found in the file. Using default 'star' icon.\n";
    return ['star'];
  }

  // Normalize the input icon name
  $normalized_icon = strtolower(str_replace([' ', '_'], '-', $icon));

  // Exact matches first (case-insensitive, with normalization)
  $exact_matches = array_filter($icon_names, function($name) use ($normalized_icon) {
    return strtolower(str_replace([' ', '_'], '-', $name)) === $normalized_icon;
  });

  // If exact match found, return up to 5 matches.
  if (!empty($exact_matches)) {
    return array_slice($exact_matches, 0, 5);
  }

  // Partial matches (contains the icon name)
  $partial_matches = array_filter($icon_names, function($name) use ($normalized_icon) {
    $normalized_name = strtolower(str_replace([' ', '_'], '-', $name));
    return
      strpos($normalized_name, $normalized_icon) !== false ||
      strpos($normalized_icon, $normalized_name) !== false;
  });

  // If partial matches found, return up to 5 matches.
  if (!empty($partial_matches)) {
    return array_slice($partial_matches, 0, 5);
  }

  // If no match found, default to star.
  return ['star'];
}
