<?php

/**
 * @file
 * Script to create the PressX home page.
 */

// Include the image handler.
require_once __DIR__ . '/includes/image-handler.php';

// Get the image ID using the helper function.
$image_path = __DIR__ . '/images/card.png';
$image_id = pressx_ensure_image($image_path);

// First, import all the technology logos.
$logo_paths = [
  'wordpress' => __DIR__ . '/images/wordpress.png',
  'nextjs' => __DIR__ . '/images/nextjs.png',
  'storybook' => __DIR__ . '/images/storybook.png',
  'tailwind' => __DIR__ . '/images/tailwind.png',
  'shadcn' => __DIR__ . '/images/shadcn.png',
  'graphql' => __DIR__ . '/images/graphql.png',
  'react' => __DIR__ . '/images/react.png',
];

$logo_ids = [];

// Import each logo into the media library using the helper function.
foreach ($logo_paths as $name => $logo_path) {
  if (file_exists($logo_path)) {
    // Use the pressx_ensure_image function with a custom title.
    $logo_id = pressx_ensure_image($logo_path, sanitize_file_name($name));
    if ($logo_id) {
      $logo_ids[] = $logo_id;
    }
  }
}

// Create a new landing page.
$post_data = [
  'post_title' => 'PressX - AI-Powered Lightning Fast Development',
  'post_status' => 'publish',
  'post_type' => 'landing',
  'post_name' => 'home',
];

$post_id = wp_insert_post($post_data);

if (is_wp_error($post_id)) {
  echo "Error creating landing page: " . $post_id->get_error_message() . "\n";
  exit(1);
}

// Add sections to match the design.
$image_url = $image_id ? wp_get_attachment_url($image_id) : '';

$sections = [
  [
    '_type' => 'hero',
    'hero_layout' => 'image_top',
    'heading' => '**Empower** Your Web Development with **PressX**',
    'summary' => 'Experience the next-gen platform that seamlessly integrates WordPress\'s powerful CMS with innovative front-end technologies. Elevate your projects with unmatched flexibility and performance.',
    'media' => $image_url,
    'link_title' => 'Start',
    'link_url' => '#get-started',
    'link2_title' => 'Explore',
    'link2_url' => '#features',
  ],
  [
    '_type' => 'side_by_side',
    'eyebrow' => '',
    'layout' => 'image_right',
    'title' => 'Unmatched Advantages of PressX',
    'summary' => 'Elevate Your Web Development with PressX\'s Cutting Edge Features',
    'media' => $image_url,
    'features' => [
      [
        '_type' => 'stat',
        'title' => 'Decoupled Architecture',
        'summary' => 'Harness the power of headless CMS to separate the front and back-end for optimal flexibility.',
        'icon' => 'network',
      ],
      [
        '_type' => 'stat',
        'title' => 'AI Development Tools',
        'summary' => 'Leverage cutting-edge AI assistants and smart code generators to accelerate your development.',
        'icon' => 'bot',
      ],
    ],
  ],
  [
    '_type' => 'card_group',
    'title' => 'Discover the Future of Web Development with PressX\'s Innovative Features',
    'cards' => [
      [
        'type' => 'stat',
        'heading' => 'Unlock the Power of Modern Web Solutions Tailored for You',
        'body' => 'Experience seamless integration and flexibility with our Decoupled Architecture.',
        'icon' => 'code',
      ],
      [
        'type' => 'stat',
        'heading' => 'Experience seamless integration and flexibility with our Decoupled Architecture.',
        'body' => 'Leverage the power of React and Node.js to dramatically improve your website\'s efficiency and speed.',
        'icon' => 'git-branch',
      ],
      [
        'type' => 'stat',
        'heading' => 'Achieve Blazing Fast Performance That Keeps Your Users Engaged and Satisfied',
        'body' => 'Deliver lightning-quick load times and smooth interactions that keep your audience coming back.',
        'icon' => 'zap',
      ],
    ],
  ],
  [
    '_type' => 'logo_collection',
    'title' => 'Trusted Open Source Technology',
    'logos' => $logo_ids,
  ],
  [
    '_type' => 'side_by_side',
    'eyebrow' => '',
    'layout' => 'image_left',
    'title' => 'Elevate Your Web Development Skills with PressX',
    'summary' => 'Join the PressX ecosystem to unlock new possibilities in web development. Our platform combines the best of WordPress\'s robust back-end with cutting-edge front-end technologies, enabling you to create powerful, scalable, and innovative web solutions.',
    'media' => $image_url,
  ],
  [
    '_type' => 'text',
    'title' => 'Accelerate Your Web Development Journey',
    'body' => 'Start building faster, more efficient websites today. Explore PressX\'s capabilities and see how it can transform your development process.',
    'text_layout' => 'buttons-right',
    'link_title' => 'Get Started',
    'link_url' => '/get-started',
    'link2_title' => 'Learn More',
    'link2_url' => '/learn-more',
  ],
  [
    '_type' => 'newsletter',
    'title' => 'Stay Informed with PressX',
    'summary' => 'Get the latest product updates, feature releases, and optimization tips delivered straight to your inbox.',
  ],
  [
    '_type' => 'side_by_side',
    'layout' => 'image_right',
    'title' => 'Discover the Essential Features That Make PressX Stand Out in Web Development',
    'summary' => 'PressX offers a powerful blend of flexibility and performance, enabling seamless integration and unparalleled user experience.',
    'media' => $image_url,
    'features' => [
      [
        '_type' => 'stat',
        'title' => 'Key Features',
        'summary' => 'Explore our innovative tools designed to enhance your web development journey, from AI-assisted coding to seamless React integration.',
      ],
      [
        '_type' => 'stat',
        'title' => 'Accelerate Your Projects',
        'summary' => 'Leverage PressX\'s powerful features to reduce development time and deliver high-performance websites faster than ever.',
      ],
    ],
  ],
];

// Update meta values in Carbon Fields format.
carbon_set_post_meta($post_id, 'sections', $sections);

// Get the post slug.
$post = get_post($post_id);
$slug = $post->post_name;

// Set this page as the homepage.
update_option('show_on_front', 'page');
update_option('page_on_front', $post_id);

echo "\nHome page created successfully! 🎉\n";
echo "----------------------------------------\n";
echo "ID: {$post_id}\n";
echo "Slug: {$slug}\n";
echo "Set as WordPress homepage: ✅\n";
echo "\nView your page at:\n";
echo "http://pressx.ddev.site/\n";
echo "http://pressx.ddev.site:3333/ (Next.js)\n";
