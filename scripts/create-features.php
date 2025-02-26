<?php

/**
 * @file
 * Script to create a test features page with various sections.
 */

// Include the image handler.
require_once __DIR__ . '/includes/image-handler.php';

// Get the image ID using the helper function.
$image_path = __DIR__ . '/images/card.png';
$image_id = pressx_ensure_image($image_path);

// Create a new features page.
$post_data = [
  'post_title' => 'Features',
  'post_status' => 'publish',
  'post_type' => 'landing',
  'post_name' => 'features',
];

$post_id = wp_insert_post($post_data);

if (is_wp_error($post_id)) {
  echo "Error creating feature page: " . $post_id->get_error_message() . "\n";
  exit(1);
}

// Add the hero section.
$image_url = $image_id ? wp_get_attachment_url($image_id) : '';

$sections = [
  [
    '_type' => 'hero',
    'hero_layout' => 'image_bottom',
    'heading' => 'Empower Your Web Development with PressX',
    'summary' => "Discover how PressX combines the flexibility of WordPress with " .
      "modern development tools to create a seamless content management experience " .
      "that empowers both developers and content creators.",
    'media' => $image_url,
    'link_title' => 'Get Started',
    'link_url' => '#primary-cta',
    'link2_title' => 'Learn More',
    'link2_url' => '#secondary-cta',
  ],
  [
    '_type' => 'text',
    'eyebrow' => 'Feature Overview',
    'title' => 'Modern Headless WordPress',
    'body' => '<p>PressX delivers a true headless WordPress experience with a powerful GraphQL API, ' .
      'custom post types, and a modern development workflow. Our platform bridges the gap between ' .
      'traditional WordPress and modern frontend technologies.</p>',
    'text_layout' => 'centered',
    'link_title' => 'Technical Documentation',
    'link_url' => '/docs',
  ],
  [
    '_type' => 'card_group',
    'title' => 'Core Features',
    'cards' => [
      [
        'type' => 'stat',
        'heading' => 'Headless CMS',
        'body' => 'WordPress as a backend with full GraphQL API support',
        'icon' => 'database',
      ],
      [
        'type' => 'stat',
        'heading' => 'Next.js Frontend',
        'body' => 'Modern React-based frontend with SSR and SSG capabilities',
        'icon' => 'code',
      ],
      [
        'type' => 'stat',
        'heading' => 'Component Library',
        'body' => 'Extensive collection of pre-built, customizable components',
        'icon' => 'layers',
      ],
      [
        'type' => 'stat',
        'heading' => 'Developer Tools',
        'body' => 'Integrated tooling for efficient development workflows',
        'icon' => 'tool',
      ],
    ],
  ],
  [
    '_type' => 'sidebyside',
    'eyebrow' => 'Headless CMS',
    'layout' => 'image_right',
    'title' => 'WordPress as a Service',
    'summary' => 'Leverage the power of WordPress as a content management system while delivering content through a modern GraphQL API.',
    'media' => $image_url,
    'link_title' => 'Learn More',
    'link_url' => '/features/headless-cms',
    'features' => [
      [
        '_type' => 'bullet',
        'text' => 'Full GraphQL API with WPGraphQL',
        'icon' => 'database',
      ],
      [
        '_type' => 'bullet',
        'text' => 'Custom post types and fields with Carbon Fields',
        'icon' => 'edit',
      ],
      [
        '_type' => 'bullet',
        'text' => 'Secure authentication and permissions',
        'icon' => 'lock',
      ],
    ],
  ],
  [
    '_type' => 'sidebyside',
    'eyebrow' => 'Frontend Framework',
    'layout' => 'image_left',
    'title' => 'Next.js Powered Frontend',
    'summary' => 'Build lightning-fast websites with Next.js, React, and TypeScript for optimal performance and developer experience.',
    'media' => $image_url,
    'link_title' => 'View Documentation',
    'link_url' => '/features/nextjs',
    'features' => [
      [
        '_type' => 'bullet',
        'text' => 'Server-side rendering for optimal SEO',
        'icon' => 'search',
      ],
      [
        '_type' => 'bullet',
        'text' => 'Static site generation for blazing fast performance',
        'icon' => 'zap',
      ],
      [
        '_type' => 'bullet',
        'text' => 'TypeScript support for type safety',
        'icon' => 'check-circle',
      ],
    ],
  ],
  [
    '_type' => 'carousel',
    'title' => 'Component Library',
    'items' => [
      [
        'media' => $image_url,
        'title' => 'Hero Sections',
        'summary' => 'Multiple hero layouts for impactful page introductions.',
      ],
      [
        'media' => $image_url,
        'title' => 'Content Blocks',
        'summary' => 'Flexible content blocks for diverse page layouts.',
      ],
      [
        'media' => $image_url,
        'title' => 'Interactive Elements',
        'summary' => 'Accordions, tabs, and other interactive components.',
      ],
      [
        'media' => $image_url,
        'title' => 'Media Components',
        'summary' => 'Image galleries, videos, and other media displays.',
      ],
    ],
  ],
  [
    '_type' => 'accordion',
    'title' => 'Technical Specifications',
    'items' => [
      [
        'title' => 'WordPress Requirements',
        'body' => 'PressX requires WordPress 5.9+ and PHP 7.4+. The core plugin includes WPGraphQL and Carbon Fields for extended functionality.',
        'link_title' => 'WordPress Documentation',
        'link_url' => '/docs/wordpress',
      ],
      [
        'title' => 'Frontend Requirements',
        'body' => 'The frontend is built with Next.js 13+, React 18+, and TypeScript. Node.js 16+ is required for development.',
        'link_title' => 'Frontend Documentation',
        'link_url' => '/docs/frontend',
      ],
      [
        'title' => 'Development Environment',
        'body' => 'DDEV is used for local development, providing a consistent environment with Docker containers for WordPress and the database.',
        'link_title' => 'Setup Guide',
        'link_url' => '/docs/setup',
      ],
    ],
  ],
  [
    '_type' => 'gallery',
    'title' => 'Feature Showcase',
    'summary' => 'Visual examples of PressX features and components in action.',
    'media_items' => [
      [
        'media' => $image_url,
        'alt' => 'Headless CMS Dashboard',
      ],
      [
        'media' => $image_url,
        'alt' => 'Next.js Frontend',
      ],
      [
        'media' => $image_url,
        'alt' => 'Component Library',
      ],
      [
        'media' => $image_url,
        'alt' => 'GraphQL API',
      ],
    ],
  ],
  [
    '_type' => 'embed',
    'title' => 'Feature Walkthrough',
    'embed_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
    'caption' => 'Watch this video for a detailed walkthrough of PressX features and capabilities.',
    'max_width' => '800px',
  ],
  [
    '_type' => 'quote',
    'quote' => 'PressX has revolutionized our WordPress development workflow. ' .
      'The headless approach combined with Next.js gives us the best of both worlds: ' .
      'WordPress for content management and modern frontend technologies for performance.',
    'author' => 'Alex Chen',
    'job_title' => 'CTO at Digital Innovations',
    'media' => $image_url,
  ],
  [
    '_type' => 'card_group',
    'title' => 'Use Cases',
    'cards' => [
      [
        'type' => 'custom',
        'media' => $image_url,
        'mediaLink' => '/use-cases/corporate',
        'heading' => 'Corporate Websites',
        'heading_url' => '/use-cases/corporate',
        'summaryText' => 'Build high-performance corporate websites with advanced content management',
        'tags' => [
          ['tag' => 'Enterprise'],
          ['tag' => 'Performance'],
          ['tag' => 'SEO'],
        ],
        'link_title' => 'View Case Study',
        'link_url' => '/use-cases/corporate',
      ],
      [
        'type' => 'custom',
        'media' => $image_url,
        'mediaLink' => '/use-cases/ecommerce',
        'heading' => 'E-commerce',
        'heading_url' => '/use-cases/ecommerce',
        'summaryText' => 'Create fast, conversion-optimized online stores with headless commerce',
        'tags' => [
          ['tag' => 'WooCommerce'],
          ['tag' => 'Headless'],
          ['tag' => 'Performance'],
        ],
        'link_title' => 'View Case Study',
        'link_url' => '/use-cases/ecommerce',
      ],
      [
        'type' => 'custom',
        'media' => $image_url,
        'mediaLink' => '/use-cases/media',
        'heading' => 'Media & Publishing',
        'heading_url' => '/use-cases/media',
        'summaryText' => 'Deliver content-rich media sites with optimal performance',
        'tags' => [
          ['tag' => 'Content'],
          ['tag' => 'Publishing'],
          ['tag' => 'Performance'],
        ],
        'link_title' => 'View Case Study',
        'link_url' => '/use-cases/media',
      ],
    ],
  ],
  [
    '_type' => 'newsletter',
    'title' => 'Stay Updated on PressX Features',
    'summary' => 'Subscribe to our newsletter to receive updates on new features, ' .
      'tutorials, and best practices for building with PressX.',
  ],
];

// Update meta values in Carbon Fields format.
carbon_set_post_meta($post_id, 'sections', $sections);

// Get the post slug.
$post = get_post($post_id);
$slug = $post->post_name;

echo "\nFeatures landing page created successfully! 🎉\n";
echo "----------------------------------------\n";
echo "ID: {$post_id}\n";
echo "Slug: {$slug}\n";
echo "\nView your page at:\n";
echo "http://pressx.ddev.site/landing/{$slug}\n";
echo "http://pressx.ddev.site:3333/landing/{$slug} (Next.js)\n";
