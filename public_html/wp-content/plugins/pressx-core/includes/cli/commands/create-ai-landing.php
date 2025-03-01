<?php

/**
 * @file
 * Script to create an AI-generated landing page using OpenRouter or Groq with Pexels image search.
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Creates an AI-generated landing page.
 *
 * @param array $options
 *   Additional options for the command.
 *
 * @return bool
 *   TRUE if successful, FALSE otherwise.
 */
function pressx_create_ai_landing(array $options = []) {
  // Include the image handler.
  require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'image-handler.php';

  // Include the Pexels image handler if we're using Pexels images.
  if (defined('PRESSX_USE_PEXELS_IMAGES') && PRESSX_USE_PEXELS_IMAGES) {
    require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'pexels-image-handler.php';
  }

  // Get the API keys from wp-config.php.
  $_pressx_openrouter_api_key = defined('OPENROUTER_API_KEY') ? OPENROUTER_API_KEY : '';
  $_pressx_groq_api_key = defined('GROQ_API_KEY') ? GROQ_API_KEY : '';

  // Check if Pexels image search is enabled.
  $_pressx_use_pexels_images = defined('PRESSX_USE_PEXELS_IMAGES') ? PRESSX_USE_PEXELS_IMAGES : FALSE;

  if (!$_pressx_openrouter_api_key && !$_pressx_groq_api_key) {
    WP_CLI::error("Neither OPENROUTER_API_KEY nor GROQ_API_KEY is defined in wp-config.php.\n" .
      "Please add at least one of the following to your wp-config.php file:\n" .
      "define('OPENROUTER_API_KEY', 'your-api-key-here');\n" .
      "define('GROQ_API_KEY', 'your-api-key-here');");
    return FALSE;
  }

  // Check which API to use based on configuration.
  $preferred_api = defined('PRESSX_AI_API') ? PRESSX_AI_API : 'openrouter';

  // Get the default image ID for fallback.
  $default_image_path = plugin_dir_path(dirname(dirname(__FILE__))) . 'images/card.png';
  $default_image_id = pressx_ensure_image($default_image_path);

  // Get the prompt from options or ask the user.
  $prompt = $options['prompt'] ?? NULL;
  if (!$prompt) {
    $prompt = readline("Enter a prompt for your AI landing page (e.g., 'coffee shop'): ");
  }

  // Sanitize the prompt.
  $prompt = sanitize_prompt($prompt);

  // Suggest improvements if the prompt is too generic.
  suggest_prompt_improvements($prompt);

  WP_CLI::log("Generating AI landing page for: " . $prompt);

  // Generate the landing page content using AI.
  try {
    // Create the system prompt.
    $system_prompt = "You are a landing page content generator specialized in creating compelling, targeted content based on user prompts.

Your task is to generate content for a landing page that SPECIFICALLY addresses the user's prompt. The content should be tailored to the topic, business, or purpose described in the prompt.

Generate exactly 6 different section types from this list: hero, text, side_by_side, card_group, gallery, quote, logo_collection, accordion.

IMPORTANT: If you include a gallery section, it MUST have EXACTLY 4 media_items. No more, no less.

IMPORTANT: The content MUST be customized to match the user's prompt. For example, if they ask for a coffee shop landing page, all headings, text, and content should be about coffee, cafes, etc.

IMPORTANT FOR CARD GROUPS: When creating a card_group section, you MUST create EXACTLY 3 CARDS.
- Each card MUST include an array of 3 DIFFERENT Lucide icons that are semantically relevant to the card's content.
- The first icon should be the most appropriate/preferred icon.
- The second and third icons are alternative suggestions.
- Icons MUST be valid Lucide icon names.
- Icons should relate to the card's theme or content.

IMPORTANT FOR IMAGES: For each section that can include images (hero, side_by_side, gallery items, etc.), include an \"image_search\" field with a specific search phrase that would find a relevant image. For example, for a coffee shop, you might use \"barista pouring latte art\" or \"cozy coffee shop interior\".

Your response MUST be only valid JSON with the following structure:
{
  \"title\": \"Page Title\",
  \"sections\": [
    {
      \"type\": \"hero\",
      \"content\": {
        \"headline\": \"Main Headline\",
        \"subheadline\": \"Supporting text that explains the value proposition\",
        \"image_search\": \"specific image search term\",
        \"cta\": {
          \"text\": \"Button Text\",
          \"url\": \"/action-page\"
        }
      }
    },
    {
      \"type\": \"features\",
      \"content\": [
        {
          \"title\": \"Feature Title\",
          \"description\": \"Feature description\",
          \"icons\": [\"preferred-icon\", \"alternative-icon-1\", \"alternative-icon-2\"]
        },
        {
          \"title\": \"Feature Title\",
          \"description\": \"Feature description\",
          \"icons\": [\"preferred-icon\", \"alternative-icon-1\", \"alternative-icon-2\"]
        },
        {
          \"title\": \"Feature Title\",
          \"description\": \"Feature description\",
          \"icons\": [\"preferred-icon\", \"alternative-icon-1\", \"alternative-icon-2\"]
        }
      ]
    },
    {
      \"type\": \"benefits\",
      \"content\": {
        \"headline\": \"Why Choose Us?\",
        \"points\": [
          \"Benefit point 1\",
          \"Benefit point 2\",
          \"Benefit point 3\"
        ]
      }
    },
    {
      \"type\": \"call_to_action\",
      \"content\": {
        \"headline\": \"Ready to Get Started?\",
        \"description\": \"Take the next step\",
        \"cta\": {
          \"text\": \"Get Started\",
          \"url\": \"/signup\"
        }
      }
    }
  ]
}";

    // Make the AI request.
    WP_CLI::log("Making AI request...");
    $result = make_ai_request($prompt, $system_prompt);

    // Extract the content from the response.
    $content = $result['choices'][0]['message']['content'];

    // Debug the raw AI response.
    WP_CLI::log("Raw AI response: " . $content);

    // Extract the JSON part from the response by finding the first { and last }.
    $json_start = strpos($content, '{');
    $json_end = strrpos($content, '}');

    if ($json_start !== FALSE && $json_end !== FALSE) {
      $json_content = substr($content, $json_start, $json_end - $json_start + 1);
      // Parse the JSON response.
      $landing_data = json_decode($json_content, TRUE);
    }
    else {
      // Fallback to direct parsing if we can't find valid JSON markers.
      $landing_data = json_decode($content, TRUE);
    }

    // Debug the parsed data.
    WP_CLI::log("Parsed landing data: " . print_r($landing_data, TRUE));

    if (!$landing_data || !isset($landing_data['title']) || !isset($landing_data['sections'])) {
      WP_CLI::error("Failed to parse AI response. Invalid JSON format.");
      return FALSE;
    }

    // Debug the sections from the parsed data
    WP_CLI::log("Sections from parsed data: " . print_r($landing_data['sections'], TRUE));

    // Transform the sections to match the expected format
    $transformed_sections = [];
    foreach ($landing_data['sections'] as $section) {
      // Skip if no type is defined
      if (!isset($section['type'])) {
        continue;
      }

      $transformed_section = [
        '_type' => $section['type']
      ];

      // Transform based on section type
      switch ($section['type']) {
        case 'hero':
          if (isset($section['content'])) {
            $transformed_section['heading'] = $section['content']['headline'] ?? '';
            $transformed_section['summary'] = $section['content']['subheadline'] ?? '';
            $transformed_section['image_search'] = $prompt;

            // Handle different image field names
            if (isset($section['content']['backgroundImage'])) {
              $transformed_section['media'] = $section['content']['backgroundImage'];
            }
            elseif (isset($section['content']['background_image'])) {
              $transformed_section['media'] = $section['content']['background_image'];
            }
            else {
              $transformed_section['media'] = '';
            }

            $transformed_section['hero_layout'] = 'image_top';

            // Handle different CTA field names
            if (isset($section['content']['cta'])) {
              $transformed_section['link_title'] = $section['content']['cta']['text'] ?? '';
              $transformed_section['link_url'] = $section['content']['cta']['url'] ?? '';
            }
            elseif (isset($section['content']['callToAction'])) {
              $transformed_section['link_title'] = $section['content']['callToAction']['text'] ?? '';
              $transformed_section['link_url'] = $section['content']['callToAction']['url'] ?? '';
            }
          }
          break;

        case 'features':
        case 'benefits':
          if (isset($section['content'])) {
            $transformed_section['_type'] = 'card_group';

            // Handle different content structures
            if (is_array($section['content']) && !isset($section['content']['headline']) && !isset($section['content']['title'])) {
              // Content is directly an array of items
              $transformed_section['title'] = 'Features';
              $transformed_section['summary'] = '';

              $transformed_section['cards'] = [];
              foreach ($section['content'] as $item) {
                if (is_array($item) && (isset($item['title']) || isset($item['description']))) {
                  $card = [
                    'title' => $item['title'] ?? '',
                    'body' => $item['description'] ?? '',
                    'icons' => ['star'],
                    'icon' => 'star'
                  ];
                  $transformed_section['cards'][] = $card;
                }
              }
            } else {
              // Content has headline/title and items
              // Use headline or title, whichever is available
              $transformed_section['title'] = $section['content']['headline'] ?? $section['content']['title'] ?? '';
              $transformed_section['summary'] = $section['content']['description'] ?? '';

              // Handle different item structures
              if (isset($section['content']['items']) && is_array($section['content']['items'])) {
                $transformed_section['cards'] = [];
                foreach ($section['content']['items'] as $item) {
                  $card = [
                    'title' => $item['title'] ?? '',
                    'body' => $item['description'] ?? '',
                    'icons' => ['star'],
                    'icon' => 'star'
                  ];
                  $transformed_section['cards'][] = $card;
                }
              } elseif (isset($section['content']['points']) && is_array($section['content']['points'])) {
                // Handle points array (for benefits section)
                $transformed_section['cards'] = [];
                foreach ($section['content']['points'] as $index => $point) {
                  $card = [
                    'title' => 'Benefit ' . ($index + 1),
                    'body' => $point,
                    'icons' => ['star'],
                    'icon' => 'star'
                  ];
                  $transformed_section['cards'][] = $card;
                }
              }
            }

            // Ensure we have at least 3 cards
            if (!isset($transformed_section['cards']) || !is_array($transformed_section['cards'])) {
              $transformed_section['cards'] = [];
            }

            while (count($transformed_section['cards']) < 3) {
              $transformed_section['cards'][] = [
                'title' => 'Feature ' . (count($transformed_section['cards']) + 1),
                'body' => 'Additional feature description.',
                'icons' => ['star'],
                'icon' => 'star'
              ];
            }
          }
          break;

        case 'call-to-action':
        case 'call_to_action':
          $transformed_section['_type'] = 'text';
          if (isset($section['content'])) {
            $transformed_section['title'] = $section['content']['headline'] ?? '';
            $transformed_section['body'] = $section['content']['description'] ?? $section['content']['subheadline'] ?? '';

            // Handle different button field names
            if (isset($section['content']['cta'])) {
              $transformed_section['link_title'] = $section['content']['cta']['text'] ?? '';
              $transformed_section['link_url'] = $section['content']['cta']['url'] ?? '';
            }
            elseif (isset($section['content']['button'])) {
              $transformed_section['link_title'] = $section['content']['button']['text'] ?? '';
              $transformed_section['link_url'] = $section['content']['button']['url'] ?? '';
            }
          }
          break;

        default:
          // For unknown types, just copy the content
          if (isset($section['content'])) {
            foreach ($section['content'] as $key => $value) {
              $transformed_section[$key] = $value;
            }
          }
          break;
      }

      $transformed_sections[] = $transformed_section;
    }

    // Replace the original sections with the transformed ones
    $landing_data['sections'] = $transformed_sections;

    WP_CLI::log("Transformed sections: " . print_r($landing_data['sections'], TRUE));

    // Create a slug from the title and append a timestamp to ensure uniqueness.
    $base_slug = sanitize_title($landing_data['title']);
    $timestamp = date('YmdHis');
    $slug = $base_slug . '-' . $timestamp;

    // Prepare the landing page data.
    $landing_args = [
      'post_title' => $landing_data['title'],
      'post_name' => $slug,
      'post_status' => 'publish',
      'post_type' => 'landing',
    ];

    // Create a new landing page.
    $landing_id = wp_insert_post($landing_args);
    WP_CLI::log("Created landing page: {$landing_data['title']}");

    // Set the featured image.
    if ($landing_id && $default_image_id) {
      set_post_thumbnail($landing_id, $default_image_id);
    }

    // Process the sections and add them to the landing page.
    if ($landing_id && !empty($landing_data['sections'])) {
      // Make sure Carbon Fields is loaded.
      if (!function_exists('carbon_set_post_meta')) {
        // Try to boot Carbon Fields if it's available but not initialized.
        if (function_exists('carbon_fields_boot_app')) {
          carbon_fields_boot_app();
          // Give it a moment to initialize
          sleep(1);
        } else {
          // Try to include Carbon Fields directly
          $carbon_fields_path = ABSPATH . 'wp-content/plugins/carbon-fields/carbon-fields.php';
          if (file_exists($carbon_fields_path)) {
            include_once($carbon_fields_path);
            if (function_exists('carbon_fields_boot_app')) {
              carbon_fields_boot_app();
              // Give it a moment to initialize
              sleep(1);
            }
          }
        }
      }

      // Check if Carbon Fields is available.
      if (function_exists('carbon_set_post_meta')) {
        // Process sections to ensure they have the correct structure.
        $processed_sections = [];

        foreach ($landing_data['sections'] as $section) {
          // Ensure each section has a _type.
          if (!isset($section['_type'])) {
            continue;
          }

          // Process card groups to ensure they have exactly 3 cards with valid icons.
          if ($section['_type'] === 'card_group' && isset($section['cards'])) {
            // Limit to first 3 cards.
            $section['cards'] = array_slice($section['cards'], 0, 3);

            // If fewer than 3 cards, add placeholder cards.
            while (count($section['cards']) < 3) {
              $section['cards'][] = [
                'title' => 'Additional Card ' . (count($section['cards']) + 1),
                'body' => 'Placeholder description for additional card.',
                'icons' => ['star'],
                'icon' => 'star'
              ];
              WP_CLI::log("Added placeholder card to reach 3 items.");
            }

            // Process icons for each card.
            foreach ($section['cards'] as &$card) {
              if (isset($card['icons']) && is_array($card['icons']) && !empty($card['icons'])) {
                // Use the first icon as the primary icon.
                $card['icon'] = $card['icons'][0];
              }
              else {
                $card['icon'] = 'star';
                $card['icons'] = ['star'];
              }
            }
          }

          // Ensure gallery sections have exactly 4 items.
          if ($section['_type'] === 'gallery') {
            if (!isset($section['media_items']) || !is_array($section['media_items'])) {
              $section['media_items'] = [];
            }

            // Get the default image URL.
            $default_image_url = $default_image_id ? wp_get_attachment_url($default_image_id) : '';

            // If fewer than 4 items, add more.
            while (count($section['media_items']) < 4) {
              $section['media_items'][] = [
                'title' => 'Gallery Item ' . (count($section['media_items']) + 1),
                'summary' => 'Additional gallery item.',
                'media' => $default_image_url
              ];
              WP_CLI::log("Added missing gallery item to reach 4 items.");
            }

            // If more than 4 items, trim the excess.
            if (count($section['media_items']) > 4) {
              WP_CLI::log("Trimming gallery from " . count($section['media_items']) . " to 4 items.");
              $section['media_items'] = array_slice($section['media_items'], 0, 4);
            }

            // Add media URLs to all gallery items if not already set.
            foreach ($section['media_items'] as &$item) {
              if (!isset($item['media'])) {
                $item['media'] = $default_image_url;
              }
            }
          }

          // Add the processed section.
          $processed_sections[] = $section;
        }

        // Process images for sections that need them if Pexels is enabled.
        if ($_pressx_use_pexels_images) {
          foreach ($processed_sections as &$section) {
            // Handle image search for hero, side_by_side, and quote sections.
            if (in_array($section['_type'], ['hero', 'side_by_side', 'quote']) && isset($section['image_search'])) {
              WP_CLI::log("Searching for image: " . $section['image_search']);
              $image_url = pressx_get_pexels_image($section['image_search']);

              if ($image_url) {
                WP_CLI::log("Found image: " . $image_url);
                // Import the image to WordPress media library.
                $image_id = pressx_import_pexels_image($image_url, $section['image_search']);

                if ($image_id) {
                  $section['media'] = wp_get_attachment_url($image_id);
                  WP_CLI::log("Imported image as attachment ID: " . $image_id);
                }
                else {
                  // Fallback to default image.
                  $section['media'] = $default_image_id ? wp_get_attachment_url($default_image_id) : '';
                  WP_CLI::log("Failed to import image, using default placeholder.");
                }
              }
              else {
                // Fallback to default image.
                $section['media'] = $default_image_id ? wp_get_attachment_url($default_image_id) : '';
                WP_CLI::log("No image found, using default placeholder.");
              }
            }

            // Handle gallery items.
            if ($section['_type'] === 'gallery' && isset($section['media_items']) && is_array($section['media_items'])) {
              foreach ($section['media_items'] as &$item) {
                if (isset($item['image_search'])) {
                  WP_CLI::log("Searching for gallery image: " . $item['image_search']);
                  $image_url = pressx_get_pexels_image($item['image_search']);

                  if ($image_url) {
                    WP_CLI::log("Found gallery image: " . $image_url);
                    // Import the image to WordPress media library.
                    $image_id = pressx_import_pexels_image($image_url, $item['image_search']);

                    if ($image_id) {
                      $item['media'] = wp_get_attachment_url($image_id);
                      WP_CLI::log("Imported gallery image as attachment ID: " . $image_id);
                    }
                  }
                }
              }
            }
          }
        }

        // Save the processed sections to Carbon Fields meta.
        WP_CLI::log("About to save " . count($processed_sections) . " sections to Carbon Fields meta.");

        // Simply save the data using carbon_set_post_meta.
        carbon_set_post_meta($landing_id, 'sections', $processed_sections);

        // Verify the sections were saved.
        $saved_sections = carbon_get_post_meta($landing_id, 'sections');
        WP_CLI::log("Retrieved sections count: " . (is_array($saved_sections) ? count($saved_sections) : "Not an array"));

        WP_CLI::log("Added and processed sections to landing page.");
      }
      else {
        WP_CLI::warning("Carbon Fields not available. Sections not added.");
      }
    }

    // Log success with URL.
    $permalink = get_permalink($landing_id);
    WP_CLI::success("AI landing page created with ID: $landing_id, slug: $slug");
    WP_CLI::log("View page: $permalink");
    WP_CLI::log("Edit page: " . admin_url("post.php?post=$landing_id&action=edit"));

    return TRUE;
  }
  catch (Exception $e) {
    WP_CLI::error("Error generating AI landing page: " . $e->getMessage());
    return FALSE;
  }
}

/**
 * Makes an AI request to the configured API.
 *
 * @param string $prompt
 *   The prompt to send to the API.
 * @param string|null $system_prompt
 *   Optional system prompt to include.
 *
 * @return array
 *   The API response data.
 */
function make_ai_request($prompt, $system_prompt = NULL) {
  global $_pressx_openrouter_api_key, $_pressx_groq_api_key, $preferred_api;

  // If globals aren't working, get the values directly.
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

  // Helper function to configure Groq.
  $configure_groq = function () use ($_pressx_groq_api_key) {
    return [
      'url' => 'https://api.groq.com/openai/v1/chat/completions',
      'headers' => [
        'Authorization: Bearer ' . $_pressx_groq_api_key,
        'Content-Type: application/json',
      ],
      'model' => defined('GROQ_MODEL') ? GROQ_MODEL : 'llama-3.3-70b-versatile'
    ];
  };

  // Helper function to configure OpenRouter.
  $configure_openrouter = function () use ($_pressx_openrouter_api_key) {
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

  // Initialize config array.
  $config = NULL;

  // Try preferred API first.
  if ($preferred_api === 'groq' && $_pressx_groq_api_key) {
    $config = $configure_groq();
  }
  elseif ($preferred_api === 'openrouter' && $_pressx_openrouter_api_key) {
    $config = $configure_openrouter();
  }
  // Handle fallback if preferred API isn't available.
  elseif ($preferred_api === 'groq' && !$_pressx_groq_api_key && $_pressx_openrouter_api_key) {
    WP_CLI::warning("Groq API is configured but GROQ_API_KEY is not set. Falling back to OpenRouter.");
    $config = $configure_openrouter();
  }
  elseif ($preferred_api === 'openrouter' && !$_pressx_openrouter_api_key && $_pressx_groq_api_key) {
    WP_CLI::warning("OpenRouter API is configured but OPENROUTER_API_KEY is not set. Falling back to Groq.");
    $config = $configure_groq();
  }

  // Check if we have a valid configuration.
  if (!$config) {
    // Just use any available API.
    if ($_pressx_groq_api_key) {
      WP_CLI::log("Using available Groq API.");
      $config = $configure_groq();
    }
    elseif ($_pressx_openrouter_api_key) {
      WP_CLI::log("Using available OpenRouter API.");
      $config = $configure_openrouter();
    }
    else {
      WP_CLI::error("No valid API configuration available.");
      exit(1);
    }
  }

  // Set the configuration
  $url = $config['url'];
  $headers = $config['headers'];
  $model = $config['model'];

  WP_CLI::log("Using API: " . ($url === 'https://api.groq.com/openai/v1/chat/completions' ? 'Groq' : 'OpenRouter'));
  WP_CLI::log("Model: " . $model);

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

/**
 * Sanitizes a prompt.
 *
 * @param string $prompt
 *   The prompt to sanitize.
 *
 * @return string
 *   The sanitized prompt.
 */
function sanitize_prompt($prompt) {
  // Remove any potentially harmful characters
  $sanitized = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $prompt);

  // Trim and convert to lowercase
  $sanitized = strtolower(trim($sanitized));

  // Ensure the prompt is not empty
  if (empty($sanitized)) {
    WP_CLI::error("Prompt cannot be empty.");
    exit(1);
  }

  return $sanitized;
}

/**
 * Suggests improvements for generic prompts.
 *
 * @param string $prompt
 *   The prompt to check.
 */
function suggest_prompt_improvements($prompt) {
  $generic_prompts = [
    'coffee' => "Try something more specific like 'artisan coffee shop in urban setting'",
    'shop' => "Be more descriptive, e.g., 'boutique clothing store for sustainable fashion'",
    'business' => "Add more context, like 'tech startup focusing on AI innovation'",
    'website' => "Provide more details, such as 'photography portfolio for wedding photographers'",
  ];

  foreach ($generic_prompts as $keyword => $suggestion) {
    if (strpos($prompt, $keyword) !== false) {
      WP_CLI::log("Tip: " . $suggestion);
      break;
    }
  }
}
