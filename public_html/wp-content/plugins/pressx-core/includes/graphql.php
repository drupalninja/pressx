<?php
/**
 * @file
 * GraphQL configuration for PressX.
 *
 * @package PressX
 */

/**
 * Configure WPGraphQL to allow access to private posts.
 */
function pressx_configure_graphql() {
  // Debug: Log when this function is called
  error_log('PressX GraphQL configuration initialized');

  // Allow access to private posts when authenticated.
  add_filter(
    'graphql_post_object_connection_query_args',
    function ($query_args, $source, $args, $context, $info) {
      // Debug: Log the current user status
      error_log('GraphQL query: User logged in: ' . (is_user_logged_in() ? 'yes' : 'no'));

      // Always include private posts regardless of authentication
      if (isset($query_args['post_status'])) {
        if (is_array($query_args['post_status'])) {
          if (!in_array('private', $query_args['post_status'])) {
            $query_args['post_status'][] = 'private';
          }
          if (!in_array('draft', $query_args['post_status'])) {
            $query_args['post_status'][] = 'draft';
          }
          if (!in_array('pending', $query_args['post_status'])) {
            $query_args['post_status'][] = 'pending';
          }
        } else {
          $query_args['post_status'] = [$query_args['post_status'], 'private', 'draft', 'pending'];
        }
      } else {
        $query_args['post_status'] = ['publish', 'private', 'draft', 'pending'];
      }

      // Debug: Log the modified query args
      error_log('Modified query args: ' . print_r($query_args, true));

      return $query_args;
    },
    10,
    5
  );

  // Always allow access to private posts
  add_filter(
    'graphql_data_is_private',
    function ($is_private, $model_name, $data, $visibility_cap, $context, $info) {
      // Debug: Log when this filter is called
      error_log('graphql_data_is_private filter called for model: ' . $model_name);

      // If it's a post, always allow access
      if ($model_name === 'Post') {
        error_log('Allowing access to private post data');
        return FALSE;
      }
      return $is_private;
    },
    10,
    6
  );

  // Modify the post data access to allow private posts
  add_filter(
    'graphql_post_object_by_id_args',
    function ($args, $source, $gql_args, $context, $info) {
      // Debug: Log the post ID being requested
      if (isset($gql_args['id'])) {
        error_log('Post requested by ID: ' . $gql_args['id']);
      }

      // Always include private posts regardless of authentication
      if (isset($args['post_status'])) {
        if (is_array($args['post_status'])) {
          if (!in_array('private', $args['post_status'])) {
            $args['post_status'][] = 'private';
          }
          if (!in_array('draft', $args['post_status'])) {
            $args['post_status'][] = 'draft';
          }
          if (!in_array('pending', $args['post_status'])) {
            $args['post_status'][] = 'pending';
          }
        } else {
          $args['post_status'] = [$args['post_status'], 'private', 'draft', 'pending'];
        }
      } else {
        $args['post_status'] = ['publish', 'private', 'draft', 'pending'];
      }

      // Debug: Log the modified args
      error_log('Modified post_object_by_id args: ' . print_r($args, true));

      return $args;
    },
    10,
    5
  );

  // Add a filter to debug the post query
  add_filter(
    'graphql_resolve_post_object',
    function ($post, $args, $context, $info) {
      if ($post) {
        error_log('Post resolved: ID=' . $post->ID . ', Status=' . $post->post_status);
      } else {
        error_log('Post resolution failed: post is null');
      }
      return $post;
    },
    10,
    4
  );
}

// Initialize GraphQL configuration.
add_action('graphql_init', 'pressx_configure_graphql');
