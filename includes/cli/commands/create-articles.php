<?php

/**
 * @file
 * Script to create sample articles.
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Creates sample articles.
 *
 * @param bool $force
 *   Whether to force recreation of articles even if they already exist.
 *
 * @return bool
 *   TRUE if successful, FALSE otherwise.
 */
function pressx_create_articles($force = FALSE) {
  // Include the image handler.
  require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'image-handler.php';

  // Get the image ID using the helper function.
  $image_path = plugin_dir_path(dirname(dirname(__FILE__))) . 'includes/images/card.png';
  $image_id = pressx_ensure_image($image_path);

  // Sample articles data.
  $articles = [
    [
      'title' => 'Getting Started with Headless WordPress',
      'slug' => 'getting-started-with-headless-wordpress',
      'content' => '<h2>Introduction to Headless WordPress</h2>
        <p>Headless WordPress is a modern approach to WordPress development that separates the frontend from the backend.</p>

        <h3>Benefits of Headless WordPress</h3>
        <ul>
          <li>Improved performance</li>
          <li>Better security</li>
          <li>More flexibility in frontend development</li>
          <li>Better developer experience</li>
        </ul>

        <h3>Getting Started</h3>
        <p>To get started with headless WordPress, you need to set up a WordPress installation and a frontend framework like Next.js.</p>',
      'excerpt' => 'Learn how to get started with headless WordPress and leverage its benefits for your next project.',
      'tags' => ['WordPress', 'Headless CMS', 'Tutorial'],
    ],
    [
      'title' => 'Building with Next.js and WordPress',
      'slug' => 'building-with-nextjs-and-wordpress',
      'content' => '<h2>Next.js and WordPress: A Powerful Combination</h2>
        <p>Next.js is a React framework that enables server-side rendering and static site generation, making it a perfect match for WordPress as a headless CMS.</p>

        <h3>Setting Up Next.js</h3>
        <p>To set up Next.js with WordPress, you need to create a new Next.js project and configure it to fetch data from your WordPress installation.</p>

        <h3>Fetching Data from WordPress</h3>
        <p>Next.js can fetch data from WordPress using the REST API or GraphQL.</p>',
      'excerpt' => 'Discover how to build modern websites using Next.js as a frontend for WordPress.',
      'tags' => ['Next.js', 'WordPress', 'React'],
    ],
    [
      'title' => 'Using GraphQL with WordPress',
      'slug' => 'using-graphql-with-wordpress',
      'content' => '<h2>GraphQL and WordPress</h2>
        <p>GraphQL is a query language for APIs that allows clients to request exactly the data they need, making it a great choice for headless WordPress.</p>

        <h3>Setting Up GraphQL in WordPress</h3>
        <p>To use GraphQL with WordPress, you need to install and configure the WPGraphQL plugin.</p>

        <h3>Querying Data with GraphQL</h3>
        <p>GraphQL allows you to query multiple resources in a single request, reducing the number of API calls needed.</p>',
      'excerpt' => 'Learn how to use GraphQL with WordPress to build more efficient and flexible APIs.',
      'tags' => ['GraphQL', 'WordPress', 'API'],
    ],
  ];

  // Create or update each article.
  foreach ($articles as $article_data) {
    // Check if the article already exists.
    $existing_post = get_page_by_path($article_data['slug'], OBJECT, 'post');

    if ($existing_post && !$force) {
      WP_CLI::log("Article '{$article_data['title']}' already exists. Skipping.");
      continue;
    }

    // Prepare the article data.
    $post_args = [
      'post_title' => $article_data['title'],
      'post_name' => $article_data['slug'],
      'post_content' => $article_data['content'],
      'post_excerpt' => $article_data['excerpt'],
      'post_status' => 'publish',
      'post_type' => 'post',
    ];

    // If the article exists and force is true, update it.
    if ($existing_post && $force) {
      $post_args['ID'] = $existing_post->ID;
      $post_id = wp_update_post($post_args);
      WP_CLI::log("Updated article: {$article_data['title']}");
    }
    else {
      // Otherwise, create a new article.
      $post_id = wp_insert_post($post_args);
      WP_CLI::log("Created article: {$article_data['title']}");
    }

    // Set the featured image if provided.
    if (!empty($post_id) && $image_id) {
      set_post_thumbnail($post_id, $image_id);
    }

    // Set tags if provided.
    if (!empty($post_id) && !empty($article_data['tags'])) {
      wp_set_post_tags($post_id, $article_data['tags']);
    }
  }

  return TRUE;
}
