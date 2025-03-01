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
function pressx_create_ai_landing($options = []) {
  // Include the image handler.
  require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'image-handler.php';

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

  // Check which API to use based on configuration
  $preferred_api = defined('PRESSX_AI_API') ? PRESSX_AI_API : 'openrouter';

  // Get the default image ID for fallback
  $default_image_path = plugin_dir_path(dirname(dirname(__FILE__))) . 'images/card.png';
  $default_image_id = pressx_ensure_image($default_image_path);

  // Get the prompt from options or ask the user
  $prompt = isset($options['prompt']) ? $options['prompt'] : NULL;
  if (!$prompt) {
    $prompt = readline("Enter a prompt for your AI landing page (e.g., 'coffee shop'): ");
  }

  // Sanitize the prompt
  $prompt = sanitize_prompt($prompt);

  // Suggest improvements if the prompt is too generic
  suggest_prompt_improvements($prompt);

  WP_CLI::log("Generating AI landing page for: " . $prompt);

  // Generate the landing page content using AI
  try {
    // Create the system prompt
    $system_prompt = "You are an expert landing page designer. Create a compelling landing page for a {$prompt} business. " .
      "The landing page should include a hero section, features, benefits, and a call to action. " .
      "Format your response as a JSON object with the following structure: " .
      "{ \"title\": \"Page Title\", \"sections\": [ ... array of section objects ... ] }";

    // Make the AI request
    WP_CLI::log("Making AI request...");
    $result = make_ai_request($prompt, $system_prompt);

    // Extract the content from the response
    $content = $result['choices'][0]['message']['content'];

    // Parse the JSON response
    $landing_data = json_decode($content, TRUE);

    if (!$landing_data || !isset($landing_data['title']) || !isset($landing_data['sections'])) {
      WP_CLI::error("Failed to parse AI response. Invalid JSON format.");
      return FALSE;
    }

    // Create a slug from the title and append a timestamp to ensure uniqueness
    $base_slug = sanitize_title($landing_data['title']);
    $timestamp = date('YmdHis');
    $slug = $base_slug . '-' . $timestamp;

    // Prepare the landing page data
    $landing_args = [
      'post_title' => $landing_data['title'],
      'post_name' => $slug,
      'post_status' => 'publish',
      'post_type' => 'landing',
    ];

    // Create a new landing page
    $landing_id = wp_insert_post($landing_args);
    WP_CLI::log("Created landing page: {$landing_data['title']}");

    // Set the featured image
    if ($landing_id && $default_image_id) {
      set_post_thumbnail($landing_id, $default_image_id);
    }

    // Process the sections and add them to the landing page
    if ($landing_id && !empty($landing_data['sections'])) {
      // Check if Carbon Fields is available
      if (function_exists('carbon_set_post_meta')) {
        // Save sections to Carbon Fields meta
        carbon_set_post_meta($landing_id, 'sections', $landing_data['sections']);
        WP_CLI::log("Added sections to landing page.");
      }
      else {
        WP_CLI::warning("Carbon Fields not available. Sections not added.");
      }
    }

    // Log success with URL
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
    WP_CLI::warning("Groq API is configured but GROQ_API_KEY is not set. Falling back to OpenRouter.");
    $config = $configure_openrouter();
  } elseif ($preferred_api === 'openrouter' && !$_pressx_openrouter_api_key && $_pressx_groq_api_key) {
    WP_CLI::warning("OpenRouter API is configured but OPENROUTER_API_KEY is not set. Falling back to Groq.");
    $config = $configure_groq();
  }

  // Check if we have a valid configuration
  if (!$config) {
    // Just use any available API
    if ($_pressx_groq_api_key) {
      WP_CLI::log("Using available Groq API.");
      $config = $configure_groq();
    } elseif ($_pressx_openrouter_api_key) {
      WP_CLI::log("Using available OpenRouter API.");
      $config = $configure_openrouter();
    } else {
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
