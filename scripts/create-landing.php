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
    '_type' => 'accordion',
    'title' => 'Frequently Asked Questions',
    'items' => [
      [
        'title' => 'What makes PressX different?',
        'body' => 'PressX combines the power of WordPress with modern frontend technologies, offering a seamless headless CMS experience. You get the best of both worlds: WordPress\'s robust content management and Next.js\'s superior performance.',
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
