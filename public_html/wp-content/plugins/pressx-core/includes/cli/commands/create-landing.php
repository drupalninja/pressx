<?php

/**
 * @file
 * Script to create the landing page.
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Creates the landing page.
 *
 * @param bool $force
 *   Whether to force recreation of the landing page even if it already exists.
 *
 * @return bool
 *   TRUE if successful, FALSE otherwise.
 */
function pressx_create_landing($force = FALSE) {
  // Include the image handler.
  require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'image-handler.php';

  // Get the image ID using the helper function.
  $image_path = plugin_dir_path(dirname(dirname(__FILE__))) . 'images/card.png';
  $image_id = pressx_ensure_image($image_path);

  // Create a new landing page with timestamp.
  $timestamp = time();
  $page_args = [
    'post_title' => 'Test Landing Page ' . $timestamp,
    'post_name' => 'test-landing-page-' . $timestamp,
    'post_status' => 'publish',
    'post_type' => 'landing',
  ];

  // Create a new landing page.
  $page_id = wp_insert_post($page_args);

  if (is_wp_error($page_id)) {
    WP_CLI::error("Error creating landing page: " . $page_id->get_error_message());
    return FALSE;
  }

  WP_CLI::log("Created landing page.");

  // Set the featured image if provided.
  if ($page_id && $image_id) {
    set_post_thumbnail($page_id, $image_id);
  }

  // Add the hero section.
  $image_url = $image_id ? wp_get_attachment_url($image_id) : '';

  $sections = [
    [
      '_type' => 'hero',
      'hero_layout' => 'image_top',
      'heading' => '**Empower** Your Web Development with **PressX**',
      'summary' => "Experience the next-gen platform that seamlessly integrates " .
                   "WordPress's powerful CMS with innovative front-end technologies. " .
                   "Elevate your projects with unmatched flexibility and performance.",
      'media' => $image_url,
      'link_title' => 'Start',
      'link_url' => '#primary-cta',
      'link2_title' => 'Explore',
      'link2_url' => '#secondary-cta',
    ],
    [
      '_type' => 'recent_posts',
      'title' => 'Latest from Our Blog',
    ],
    [
      '_type' => 'logo_collection',
      'title' => 'Trusted by Leading Companies',
      'logos' => [
        $image_id,
        $image_id,
        $image_id,
        $image_id,
        $image_id,
        $image_id,
      ],
    ],
    [
      '_type' => 'accordion',
      'title' => 'Frequently Asked Questions',
      'items' => [
        [
          'title' => 'What makes PressX different?',
          'body' => 'PressX combines the power of WordPress with modern frontend technologies, ' .
            'offering a seamless headless CMS experience.',
          'link_title' => 'Learn More',
          'link_url' => '/features',
        ],
        [
          'title' => 'How does the headless setup work?',
          'body' => 'PressX uses WordPress as a headless CMS, exposing content through a GraphQL API. The frontend is built with Next.js, allowing for server-side rendering, static site generation, and optimal performance.',
          'link_title' => 'View Documentation',
          'link_url' => '/docs',
        ],
        [
          'title' => 'Can I customize the components?',
          'body' => 'Absolutely! PressX is built with customization in mind. All components are modular and can be styled using Tailwind CSS. You can easily modify existing components or create new ones to match your needs.',
        ],
      ],
    ],
    [
      '_type' => 'text',
      'eyebrow' => 'About PressX',
      'title' => 'Modern WordPress Development',
      'body' => '<p>PressX combines the power of WordPress with modern frontend technologies, ' .
        'offering a seamless headless CMS experience. Build faster, more performant websites ' .
        'with our innovative platform.</p>',
      'text_layout' => 'centered',
      'link_title' => 'Get Started',
      'link_url' => '/get-started',
      'link2_title' => 'Learn More',
      'link2_url' => '/about',
    ],
    [
      '_type' => 'carousel',
      'title' => 'Featured Capabilities',
      'items' => [
        [
          'media' => $image_url,
          'title' => 'Modern Development',
          'summary' => 'Build with Next.js and TypeScript for a modern development experience.',
        ],
        [
          'media' => $image_url,
          'title' => 'Headless WordPress',
          'summary' => 'Leverage WordPress as a headless CMS with our optimized integration.',
        ],
        [
          'media' => $image_url,
          'title' => 'Component Library',
          'summary' => 'Extensive library of pre-built components to accelerate development.',
        ],
      ],
    ],
    [
      '_type' => 'card_group',
      'title' => 'Why Choose PressX?',
      'cards' => [
        [
          'type' => 'stat',
          'heading' => 'Lightning Fast',
          'body' => 'Optimized performance with server-side rendering and static generation',
          'icon' => 'zap',
        ],
        [
          'type' => 'stat',
          'heading' => 'Fully Customizable',
          'body' => 'Modular components and flexible layouts for any design need',
          'icon' => 'settings',
        ],
        [
          'type' => 'stat',
          'heading' => 'Modern Stack',
          'body' => 'Built with Next.js, TypeScript, and Tailwind CSS',
          'icon' => 'rocket',
        ],
      ],
    ],
    [
      '_type' => 'card_group',
      'title' => 'Latest Features',
      'cards' => [
        [
          'type' => 'custom',
          'media' => $image_url,
          'mediaLink' => '/features/headless',
          'heading' => 'Headless WordPress',
          'heading_url' => '/features/headless',
          'summaryText' => 'Leverage WordPress as a headless CMS with our modern Next.js frontend',
          'tags' => [
            ['tag' => 'GraphQL'],
            ['tag' => 'WordPress'],
            ['tag' => 'Next.js'],
          ],
          'link_title' => 'Learn More',
          'link_url' => '/features/headless',
        ],
        [
          'type' => 'custom',
          'media' => $image_url,
          'mediaLink' => '/features/components',
          'heading' => 'Component Library',
          'heading_url' => '/features/components',
          'summaryText' => 'Extensive collection of pre-built components for rapid development',
          'tags' => [
            ['tag' => 'UI'],
            ['tag' => 'Components'],
            ['tag' => 'Tailwind'],
          ],
          'link_title' => 'View Components',
          'link_url' => '/features/components',
        ],
      ],
    ],
    [
      '_type' => 'media',
      'title' => 'Featured Media',
      'media' => $image_url,
    ],
    [
      '_type' => 'gallery',
      'title' => 'Image Gallery',
      'summary' => 'A collection of images showcasing our platform features.',
      'media_items' => [
        [
          'media' => $image_url,
          'alt' => 'Platform screenshot 1',
        ],
        [
          'media' => $image_url,
          'alt' => 'Platform screenshot 2',
        ],
        [
          'media' => $image_url,
          'alt' => 'Platform screenshot 3',
        ],
        [
          'media' => $image_url,
          'alt' => 'Platform screenshot 4',
        ],
      ],
    ],
    [
      '_type' => 'side_by_side',
      'eyebrow' => 'Developer Experience',
      'layout' => 'image_right',
      'title' => 'Built for Modern Development',
      'summary' => 'PressX combines the best of WordPress with modern development practices, offering a seamless experience for developers and content creators alike.',
      'media' => $image_url,
      'link_title' => 'Get Started',
      'link_url' => '/get-started',
      'features' => [
        [
          '_type' => 'bullet',
          'text' => 'TypeScript and Next.js support out of the box',
          'icon' => 'code',
        ],
        [
          '_type' => 'bullet',
          'text' => 'GraphQL API for efficient data fetching',
          'icon' => 'database',
        ],
        [
          '_type' => 'bullet',
          'text' => 'Modern development workflow with hot reloading',
          'icon' => 'refresh-cw',
        ],
      ],
    ],
    [
      '_type' => 'side_by_side',
      'eyebrow' => 'Performance Metrics',
      'layout' => 'image_left',
      'title' => 'Built for Speed',
      'summary' => 'Experience lightning-fast performance with our optimized architecture and modern build tools.',
      'media' => $image_url,
      'link_title' => 'Learn More',
      'link_url' => '/performance',
      'features' => [
        [
          '_type' => 'stat',
          'title' => 'Performance Score',
          'summary' => '95+',
          'icon' => 'trending-up',
        ],
        [
          '_type' => 'stat',
          'title' => 'Build Time',
          'summary' => '< 10s',
          'icon' => 'timer',
        ],
      ],
    ],
    [
      '_type' => 'embed',
      'title' => 'Watch Our Tutorial',
      'script' => '<iframe src="https://www.youtube.com/embed/71EZb94AS1k" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="width:100%; aspect-ratio:16/9;"></iframe>',
      'caption' => 'Learn how to get started with PressX in this quick tutorial video.',
      'max_width' => '800px',
    ],
    [
      '_type' => 'pricing',
      'eyebrow' => 'Choose Your Plan',
      'title' => 'Compare Our Options',
      'summary' => 'Select the best option for your needs',
      'includes_label' => 'Includes',
      'cards' => [
        [
          'eyebrow' => 'PressX CMS',
          'title' => 'Free',
          'monthly_label' => '',
          'features' => [
            ['text' => 'Full access to open source features'],
            ['text' => 'Community support'],
            ['text' => 'Documentation'],
          ],
          'cta_text' => 'Get Started',
          'cta_link' => '#',
        ],
        [
          'eyebrow' => 'Technical Discovery',
          'title' => '$5,000',
          'monthly_label' => '',
          'features' => [
            ['text' => 'Comprehensive needs assessment'],
            ['text' => 'Custom solution design'],
            ['text' => 'Implementation roadmap'],
          ],
          'cta_text' => 'Book Discovery',
          'cta_link' => '#',
        ],
        [
          'eyebrow' => 'Full Project Build',
          'title' => 'Contact',
          'monthly_label' => '',
          'features' => [
            ['text' => 'End-to-end project management'],
            ['text' => 'Custom development'],
            ['text' => 'Ongoing support'],
          ],
          'cta_text' => 'Contact Sales',
          'cta_link' => '#',
        ],
      ],
    ],
    [
      '_type' => 'newsletter',
      'title' => 'Stay Updated with PressX',
      'summary' => 'Subscribe to our newsletter to receive the latest updates, ' .
        'tips, and best practices for building modern WordPress sites with PressX.',
    ],
    [
      '_type' => 'quote',
      'quote' => 'PressX has transformed how we build and manage our WordPress sites. ' .
        'The modern development experience and component-driven approach have ' .
        'significantly improved our workflow.',
      'author' => 'Sarah Johnson',
      'job_title' => 'Lead Developer at TechCorp',
      'media' => $image_url,
    ],
    [
      '_type' => 'side_by_side',
      'eyebrow' => 'Features',
      'layout' => 'image_right',
      'title' => 'Modern Development Experience',
      'summary' => 'Build fast, scalable websites with modern development tools ' .
        'and workflows.',
      'link_title' => 'Learn More',
      'link_url' => '#features',
      'media' => $image_url,
      'features' => [
        [
          'text' => 'Next.js and React for modern frontend development',
        ],
        [
          'text' => 'WordPress as a headless CMS',
        ],
        [
          'text' => 'GraphQL API for efficient data fetching',
        ],
        [
          'text' => 'Tailwind CSS for rapid styling',
        ],
      ],
    ],
  ];

  // Update meta values in Carbon Fields format.
  if (function_exists('carbon_set_post_meta')) {
    carbon_set_post_meta($page_id, 'sections', $sections);
    WP_CLI::log("Added Carbon Fields sections to the landing page.");
  }
  else {
    WP_CLI::warning("Carbon Fields not available. Sections not added.");
  }

  // Get the post slug.
  $post = get_post($page_id);
  $slug = $post->post_name;

  WP_CLI::success("Landing page created successfully!");
  WP_CLI::log("ID: {$page_id}");
  WP_CLI::log("Slug: {$slug}");
  WP_CLI::log("View your page at:");
  WP_CLI::log("http://pressx.ddev.site/landing/{$slug}");
  WP_CLI::log("http://pressx.ddev.site:3333/{$slug} (Next.js)");

  return TRUE;
}
