<?php

/**
 * @file
 * Script to create test WordPress posts.
 */

// Include the image handler.
require_once __DIR__ . '/includes/image-handler.php';

// Get the image ID using the helper function.
$image_path = __DIR__ . '/images/card.png';
$image_id = pressx_ensure_image($image_path);

// Sample article data.
$articles = [
  [
    'title' => 'Getting Started with PressX',
    'content' => '<p>PressX is a modern WordPress development framework that combines the power of WordPress with cutting-edge frontend technologies. This guide will help you get started with building your first PressX project.</p>
      <h2>Prerequisites</h2>
      <ul>
        <li>Basic knowledge of WordPress</li>
        <li>Familiarity with React and Next.js</li>
        <li>Node.js and PHP installed on your machine</li>
      </ul>
      <h2>Installation</h2>
      <p>Follow these steps to set up your development environment and create your first PressX project.</p>',
    'tags' => ['Tutorial', 'Getting Started', 'Development'],
  ],
  [
    'title' => 'Building Custom Components in PressX',
    'content' => '<p>One of the key features of PressX is its component-based architecture. Learn how to create, customize, and integrate components into your WordPress site.</p>
      <h2>Component Structure</h2>
      <p>PressX components follow a modular structure that promotes reusability and maintainability. Each component consists of:</p>
      <ul>
        <li>TypeScript/React code</li>
        <li>Tailwind CSS styles</li>
        <li>WordPress integration</li>
      </ul>',
    'tags' => ['Components', 'Development', 'Tutorial'],
  ],
  [
    'title' => 'Advanced PressX Features',
    'content' => '<p>Discover the advanced features that make PressX a powerful framework for modern WordPress development.</p>
      <h2>Key Features</h2>
      <ul>
        <li>Server-side rendering</li>
        <li>Static site generation</li>
        <li>GraphQL API integration</li>
        <li>Component library</li>
      </ul>
      <p>These features enable developers to build fast, scalable, and maintainable WordPress sites.</p>',
    'tags' => ['Features', 'Advanced', 'Development'],
  ],
  [
    'title' => 'PressX Performance Optimization Guide',
    'content' => '<p>Learn how to optimize your PressX site for maximum performance and user experience.</p>
      <h2>Optimization Techniques</h2>
      <ul>
        <li>Image optimization and lazy loading</li>
        <li>Code splitting and bundle optimization</li>
        <li>Caching strategies</li>
        <li>Performance monitoring</li>
      </ul>
      <h2>Best Practices</h2>
      <p>Follow these best practices to ensure your PressX site performs at its best:</p>
      <ul>
        <li>Implement proper image sizing and formats</li>
        <li>Utilize the built-in caching mechanisms</li>
        <li>Monitor and optimize database queries</li>
      </ul>',
    'tags' => ['Performance', 'Optimization', 'Best Practices'],
  ],
  [
    'title' => 'Headless WordPress with PressX',
    'content' => '<p>Explore the benefits and implementation of headless WordPress using PressX as your frontend framework.</p>
      <h2>Benefits of Headless</h2>
      <ul>
        <li>Improved performance and scalability</li>
        <li>Better developer experience</li>
        <li>Flexible content delivery</li>
        <li>Enhanced security</li>
      </ul>
      <h2>Implementation Guide</h2>
      <p>Learn how to set up and configure your headless WordPress site with PressX:</p>
      <ul>
        <li>WordPress configuration</li>
        <li>GraphQL schema setup</li>
        <li>Frontend integration</li>
        <li>Deployment strategies</li>
      </ul>',
    'tags' => ['Headless', 'WordPress', 'Architecture'],
  ],
  [
    'title' => 'Creating Dynamic Layouts with PressX',
    'content' => '<p>Master the art of creating flexible and dynamic layouts using PressX\'s powerful component system.</p>
      <h2>Layout Components</h2>
      <ul>
        <li>Grid systems</li>
        <li>Flex containers</li>
        <li>Responsive design patterns</li>
      </ul>
      <h2>Advanced Techniques</h2>
      <p>Discover advanced layout techniques:</p>
      <ul>
        <li>Dynamic component rendering</li>
        <li>Conditional layouts</li>
        <li>Custom grid systems</li>
        <li>Responsive breakpoints</li>
      </ul>
      <p>Learn how to combine these techniques to create powerful, flexible layouts that adapt to any content structure.</p>',
    'tags' => ['Layout', 'Design', 'Components'],
  ],
];

// Get the admin user.
$admin = get_user_by('login', 'admin');
if (!$admin) {
  echo "Error: Admin user not found.\n";
  exit(1);
}

// Create the articles.
foreach ($articles as $article) {
  // Prepare post data with admin author.
  $post_data = [
    'post_title' => $article['title'],
    'post_content' => $article['content'],
    'post_status' => 'publish',
    'post_type' => 'post',
    'post_author' => $admin->ID,
  ];

  // Insert the post.
  $post_id = wp_insert_post($post_data);

  if (is_wp_error($post_id)) {
    echo "Error creating article '{$article['title']}': " . $post_id->get_error_message() . "\n";
    continue;
  }

  // Set featured image if we have one.
  if ($image_id) {
    set_post_thumbnail($post_id, $image_id);
  }

  // Add tags.
  if (!empty($article['tags'])) {
    wp_set_post_tags($post_id, $article['tags']);
  }

  echo "Created article: {$article['title']} (ID: {$post_id})\n";
}

echo "\nArticles created successfully! 🎉\n";
echo "View your articles at:\n";
echo "http://pressx.ddev.site/\n";
echo "http://pressx.ddev.site:3333/\n";
