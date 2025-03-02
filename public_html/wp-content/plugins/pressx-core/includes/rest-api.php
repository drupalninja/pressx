<?php

/**
 * @file
 * REST API implementation for PressX.
 */

if (!defined('ABSPATH')) {
  exit;
}

use WPGraphQL\JWT_Authentication\Auth;

/**
 * Register REST API routes for PressX.
 */
function pressx_register_rest_routes() {
  register_rest_route('pressx/v1', '/chat', [
    'methods' => 'POST',
    'callback' => 'pressx_chat_callback',
    // Require JWT authentication.
    'permission_callback' => function () {
      // Check for JWT authentication.
      $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
      if (strpos($auth_header, 'Bearer ') === 0) {
        $token = substr($auth_header, 7);
        // Verify JWT token using wp-graphql-jwt-authentication plugin.
        if (class_exists('WPGraphQL\JWT_Authentication\Auth') && method_exists('WPGraphQL\JWT_Authentication\Auth', 'validate_token')) {
          return Auth::validate_token($token);
        }
      }

      return FALSE;
    },
    'args' => [
      'message' => [
        'required' => TRUE,
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      ],
    ],
  ]);
}

add_action('rest_api_init', 'pressx_register_rest_routes');

/**
 * Callback for the chat endpoint.
 *
 * @param WP_REST_Request $request
 *   The request object.
 *
 * @return WP_REST_Response
 *   The response object.
 */
function pressx_chat_callback(WP_REST_Request $request) {
  // Get the message from the request.
  $message = $request->get_param('message');

  // Check if API keys are configured.
  if (!defined('OPENROUTER_API_KEY') && !defined('GROQ_API_KEY')) {
    return new WP_Error(
      'ai_api_not_configured',
      'AI API keys are not configured. Please set up OPENROUTER_API_KEY or GROQ_API_KEY in wp-config.php.',
      ['status' => 500]
    );
  }

  // Check if the message is a command to create an AI landing page.
  $command_pattern = '/create\s+(?:an\s+)?(?:ai\s+)?landing\s+page\s+(?:for\s+)?(?:a\s+)?/i';
  if (preg_match($command_pattern, $message)) {
    // Extract the prompt from the message.
    $prompt_pattern = '/create\s+(?:an\s+)?(?:ai\s+)?landing\s+page\s+(?:for\s+)?(?:a\s+)?(.*)/i';
    preg_match($prompt_pattern, $message, $matches);

    if (!empty($matches[1])) {
      $prompt = trim($matches[1]);
      try {
        // Include the CLI command file.
        $file_path = plugin_dir_path(dirname(__FILE__)) . 'includes/cli/commands/create-ai-landing.php';
        if (!file_exists($file_path)) {
          return new WP_Error(
            'file_not_found',
            'Required file not found: ' . $file_path,
            ['status' => 500]
          );
        }

        require_once $file_path;

        if (!function_exists('pressx_create_ai_landing')) {
          return new WP_Error(
            'function_not_found',
            'Required function not found: pressx_create_ai_landing',
            ['status' => 500]
          );
        }

        // Call the function with is_cli set to FALSE.
        $result = pressx_create_ai_landing($prompt, FALSE);

        if ($result && isset($result['post_id'])) {
          $post = get_post($result['post_id']);
          $permalink = get_permalink($post->ID);

          return new WP_REST_Response([
            'response' => "I've created an AI landing page about \"$prompt\". You can view it at: $permalink",
            'links' => [
              [
                'text' => "View \"$post->post_title\"",
                'url' => $permalink,
              ],
            ],
            'command_executed' => 'create_ai_landing',
          ], 200);
        }
        else {
          return new WP_REST_Response([
            'response' => "I tried to create an AI landing page about \"$prompt\", but encountered an error. Please try again with a more specific topic.",
            'command_executed' => 'create_ai_landing',
            'command_failed' => TRUE,
          ], 200);
        }
      }
      catch (Exception $e) {
        return new WP_REST_Response([
          'response' => "I couldn't create an AI landing page due to an error: " . $e->getMessage(),
          'command_executed' => 'create_ai_landing',
          'command_failed' => TRUE,
        ], 200);
      }
    }
    else {
      // No prompt provided, ask for one.
      return new WP_REST_Response([
        'response' => "I'd be happy to create an AI landing page for you. What topic or business would you like it to be about?",
        'command_detected' => 'create_ai_landing',
        'needs_more_info' => TRUE,
      ], 200);
    }
  }

  // If not a command, proceed with regular AI response.
  // Create the system prompt.
  $system_prompt = "You are a helpful assistant for the PressX website. Your role is to provide concise, accurate information about PressX features, WordPress, Next.js, and web development topics. Keep your responses friendly, informative, and to the point. When appropriate, suggest relevant content or features from the PressX platform that might help the user.";

  try {
    // Make the AI request using the shared utility function.
    $response = pressx_ai_request($message, $system_prompt);

    // Find relevant links based on the content.
    $links = pressx_find_relevant_links($response);

    return new WP_REST_Response([
      'response' => $response,
      'links' => $links,
    ], 200);
  }
  catch (Exception $e) {
    return new WP_REST_Response([
      'error' => $e->getMessage(),
      'response' => 'Sorry, I encountered an error. Please try again later.',
    ], 500);
  }
}
