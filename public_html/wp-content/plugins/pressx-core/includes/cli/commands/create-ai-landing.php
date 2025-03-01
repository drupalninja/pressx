<?php

/**
 * @file
 * Script to create the AI landing page.
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Creates the AI landing page.
 *
 * @param bool $force
 *   Whether to force recreation of the AI landing page even if it already exists.
 *
 * @return bool
 *   TRUE if successful, FALSE otherwise.
 */
function pressx_create_ai_landing($force = FALSE) {
  // Include the image handler.
  require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'image-handler.php';

  // Get the image ID using the helper function.
  $image_path = plugin_dir_path(dirname(dirname(__FILE__))) . 'includes/images/card.png';
  $image_id = pressx_ensure_image($image_path);

  // Check if the AI landing page already exists.
  $existing_landing = get_posts([
    'post_type' => 'landing',
    'name' => 'ai',
    'post_status' => 'publish',
    'posts_per_page' => 1,
  ]);

  if (!empty($existing_landing) && !$force) {
    WP_CLI::log("AI landing page already exists. Skipping.");
    return TRUE;
  }

  // Prepare the AI landing page data.
  $landing_args = [
    'post_title' => 'AI Solutions',
    'post_name' => 'ai',
    'post_status' => 'publish',
    'post_type' => 'landing',
  ];

  // If the AI landing page exists and force is true, update it.
  if (!empty($existing_landing) && $force) {
    $landing_args['ID'] = $existing_landing[0]->ID;
    $landing_id = wp_update_post($landing_args);
    WP_CLI::log("Updated AI landing page.");
  }
  else {
    // Otherwise, create a new AI landing page.
    $landing_id = wp_insert_post($landing_args);
    WP_CLI::log("Created AI landing page.");
  }

  // Set the featured image if provided.
  if ($landing_id && $image_id) {
    set_post_thumbnail($landing_id, $image_id);
  }

  // Set Carbon Fields meta.
  if ($landing_id) {
    // Define sections for the AI landing page.
    $sections = [
      [
        '_type' => 'hero',
        'hero_layout' => 'image_top',
        'heading' => 'AI-Powered **Solutions**',
        'summary' => 'Leverage the power of artificial intelligence to transform your business.',
        'media' => $image_id,
        'link_title' => 'Get Started',
        'link_url' => '/get-started',
        'link2_title' => 'Learn More',
        'link2_url' => '/features',
      ],
      [
        '_type' => 'text',
        'eyebrow' => 'AI Solutions',
        'title' => 'Transform Your Business with AI',
        'body' => '<p>Our AI-powered solutions help businesses automate processes, gain insights from data, and deliver personalized experiences to customers.</p>',
        'text_layout' => 'default',
        'link_title' => 'Learn More',
        'link_url' => '/features',
      ],
      [
        '_type' => 'card_group',
        'title' => 'Key AI Features',
        'cards' => [
          [
            'type' => 'stat',
            'heading' => 'Natural Language Processing',
            'body' => 'Understand and generate human language with advanced NLP models.',
            'icon' => 'message-square',
          ],
          [
            'type' => 'stat',
            'heading' => 'Computer Vision',
            'body' => 'Analyze and interpret visual information from images and videos.',
            'icon' => 'eye',
          ],
          [
            'type' => 'stat',
            'heading' => 'Predictive Analytics',
            'body' => 'Forecast trends and behaviors with machine learning algorithms.',
            'icon' => 'trending-up',
          ],
        ],
      ],
      [
        '_type' => 'side_by_side',
        'layout' => 'image_right',
        'title' => 'AI-Powered Automation',
        'summary' => 'Automate repetitive tasks and workflows with intelligent AI solutions.',
        'media' => $image_id,
        'features' => [
          [
            '_type' => 'bullet',
            'text' => 'Workflow Automation',
            'icon' => 'check',
          ],
          [
            '_type' => 'bullet',
            'text' => 'Document Processing',
            'icon' => 'check',
          ],
          [
            '_type' => 'bullet',
            'text' => 'Data Analysis',
            'icon' => 'check',
          ],
        ],
      ],
      [
        '_type' => 'quote',
        'quote' => 'The AI solutions provided by PressX have transformed how we operate, saving us countless hours and improving our customer experience.',
        'author' => 'Jane Smith',
        'job_title' => 'CEO, Example Company',
        'media' => $image_id,
      ],
      [
        '_type' => 'accordion',
        'title' => 'Frequently Asked Questions',
        'items' => [
          [
            'title' => 'What is artificial intelligence?',
            'content' => '<p>Artificial intelligence (AI) refers to the simulation of human intelligence in machines that are programmed to think like humans and mimic their actions. The term may also be applied to any machine that exhibits traits associated with a human mind such as learning and problem-solving.</p>',
          ],
          [
            'title' => 'How can AI benefit my business?',
            'content' => '<p>AI can benefit your business in numerous ways, including automating repetitive tasks, analyzing large amounts of data to extract insights, personalizing customer experiences, improving decision-making, and enhancing security through advanced threat detection.</p>',
          ],
          [
            'title' => 'Is AI difficult to implement?',
            'content' => '<p>With PressX, implementing AI solutions is straightforward. Our platform provides pre-built AI components that can be easily integrated into your existing systems without requiring deep technical expertise.</p>',
          ],
        ],
      ],
      [
        '_type' => 'gallery',
        'title' => 'AI in Action',
        'images' => [
          $image_id,
          $image_id,
          $image_id,
          $image_id,
        ],
      ],
      [
        '_type' => 'newsletter',
        'title' => 'Stay Updated on AI Innovations',
        'summary' => 'Subscribe to our newsletter to receive the latest updates on AI technologies and how they can benefit your business.',
        'button_text' => 'Subscribe',
      ],
    ];

    // Check if Carbon Fields is available.
    if (function_exists('carbon_set_post_meta')) {
      // Save sections to Carbon Fields meta.
      carbon_set_post_meta($landing_id, 'sections', $sections);
      WP_CLI::log("Added sections to AI landing page.");
    }
    else {
      WP_CLI::warning("Carbon Fields not available. Sections not added.");
    }

    // Log success with URL.
    $permalink = get_permalink($landing_id);
    WP_CLI::success("AI landing page created with ID: $landing_id, slug: ai");
    WP_CLI::log("View page: $permalink");
    WP_CLI::log("Edit page: " . admin_url("post.php?post=$landing_id&action=edit"));
  }

  return TRUE;
}
