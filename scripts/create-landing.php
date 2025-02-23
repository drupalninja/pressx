<?php

/**
 * @file
 * Script to create a test landing page with a hero section.
 */

// First, ensure the image exists in WordPress media library.
$image_path = __DIR__ . '/images/card.png';
$image_id = NULL;

// Check if image exists locally.
if (file_exists($image_path)) {
  // Import the image into WordPress media library if not already there.
  $upload_dir = wp_upload_dir();
  $filename = basename($image_path);
  $wp_filetype = wp_check_filetype($filename);

  // Prepare the file array.
  $attachment = [
    'post_mime_type' => $wp_filetype['type'],
    'post_title' => sanitize_file_name($filename),
    'post_content' => '',
    'post_status' => 'inherit',
  ];

  // Copy the file to the uploads directory.
  $target_path = $upload_dir['path'] . '/' . $filename;
  if (!file_exists($target_path)) {
    copy($image_path, $target_path);
  }

  // Check if image already exists in media library.
  $existing_attachment = new WP_Query([
    'post_type' => 'attachment',
    'title' => sanitize_file_name($filename),
    'post_status' => 'inherit',
    'posts_per_page' => 1,
  ]);

  if (!$existing_attachment->have_posts()) {
    // Insert the attachment.
    $image_id = wp_insert_attachment($attachment, $target_path);

    // Generate metadata for the attachment.
    $attachment_data = wp_generate_attachment_metadata($image_id, $target_path);
    wp_update_attachment_metadata($image_id, $attachment_data);
  }
  else {
    $image_id = $existing_attachment->posts[0]->ID;
  }
}

// Create a new landing page.
$post_data = [
  'post_title'    => 'Test Landing Page ' . time(),
  'post_status'   => 'publish',
  'post_type'     => 'landing',
  'post_name'     => 'test-landing-page-' . time(),
];

$post_id = wp_insert_post($post_data);

if (is_wp_error($post_id)) {
  echo "Error creating landing page: " . $post_id->get_error_message() . "\n";
  exit(1);
}

// Add the hero section.
$image_url = $image_id ? wp_get_attachment_url($image_id) : '';

$sections = [
  [
    '_type' => 'hero',
    'hero_layout' => 'image_top',
    'heading' => '**Empower** Your Web Development with **PressX**',
    'summary' => "Experience the next-gen platform that seamlessly integrates WordPress's powerful CMS with innovative front-end technologies. Elevate your projects with unmatched flexibility and performance.",
    'media' => $image_url,
    'link_title' => 'Start',
    'link_url' => '#primary-cta',
    'link2_title' => 'Explore',
    'link2_url' => '#secondary-cta',
  ],
  [
    '_type' => 'logo_collection',
    'title' => 'Trusted by Leading Companies',
    'logos' => [$image_id, $image_id, $image_id, $image_id, $image_id, $image_id], // Using the same image multiple times for demo
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
    'modifier' => 'container mx-auto max-w-4xl',
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
    '_type' => 'embed',
    'title' => 'Watch Our Tutorial',
    'embed_url' => 'https://www.youtube.com/embed/71EZb94AS1k',
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
    'summary' => 'Subscribe to our newsletter to receive the latest updates, tips, ' .
      'and best practices for building modern WordPress sites with PressX.',
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
    '_type' => 'sidebyside',
    'eyebrow' => 'Powerful Features',
    'layout' => 'image_right',
    'title' => 'Modern Development Experience',
    'summary' => '<p>PressX combines the power of WordPress with modern frontend technologies, ' .
      'offering a seamless headless CMS experience. Build faster, more maintainable ' .
      'websites with our component-driven approach.</p>',
    'link_title' => 'Learn More',
    'link_url' => '/features',
    'media' => $image_url,
    'modifier' => 'bg-gray-50',
    'features' => [
      ['text' => 'Next.js and TypeScript integration'],
      ['text' => 'GraphQL API with WPGraphQL'],
      ['text' => 'Component-driven development'],
      ['text' => 'Modern tooling and workflows'],
    ],
  ],
];

// Update meta values in Carbon Fields format.
carbon_set_post_meta($post_id, 'sections', $sections);

// Get the post slug.
$post = get_post($post_id);
$slug = $post->post_name;

echo "\nLanding page created successfully! 🎉\n";
echo "----------------------------------------\n";
echo "ID: {$post_id}\n";
echo "Slug: {$slug}\n";
echo "\nView your page at:\n";
echo "http://pressx.ddev.site/landing/{$slug}\n";
echo "http://pressx.ddev.site:3333/landing/{$slug} (Next.js)\n";
