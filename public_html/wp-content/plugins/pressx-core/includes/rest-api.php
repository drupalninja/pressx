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

  // Get the API keys from wp-config.php.
  $openrouter_api_key = defined('OPENROUTER_API_KEY') ? OPENROUTER_API_KEY : '';
  $groq_api_key = defined('GROQ_API_KEY') ? GROQ_API_KEY : '';

  if (!$openrouter_api_key && !$groq_api_key) {
    return new WP_REST_Response([
      'error' => 'AI API keys are not configured.',
      'response' => 'Sorry, the AI assistant is not properly configured. Please contact the site administrator.',
    ], 500);
  }

  // Check if this is a command to create an AI landing page.
  $create_landing_pattern = '/create\s+(an?\s+)?ai\s+landing\s+(page|site)/i';
  if (preg_match($create_landing_pattern, $message, $matches)) {
    // Extract the topic/prompt from the message.
    $prompt_pattern = '/for\s+(?:a|an)?\s*(.+?)(?:\s*\?|\s*$)/i';
    $prompt = '';

    if (preg_match($prompt_pattern, $message, $prompt_matches)) {
      $prompt = trim($prompt_matches[1]);
    }
    else {
      // If no specific topic found, use everything after "create ai landing page".
      $parts = preg_split($create_landing_pattern, $message, 2);
      if (isset($parts[1])) {
        $prompt = trim($parts[1]);
      }
    }

    // If we have a prompt, try to create a landing page.
    if (!empty($prompt)) {
      try {
        // Include the CLI command file if it's not already included.
        require_once ABSPATH . 'wp-content/plugins/pressx-core/includes/cli/commands/create-ai-landing.php';

        // Call the function to create an AI landing page.
        $result = pressx_create_ai_landing([
          'prompt' => $prompt,
          'from_api' => TRUE,
        ]);

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
