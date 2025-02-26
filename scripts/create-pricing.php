<?php

/**
 * @file
 * Script to create a PressX pricing landing page based on the provided design.
 */

// Include the image handler.
require_once __DIR__ . '/includes/image-handler.php';

// Get the image ID using the helper function.
$image_path = __DIR__ . '/images/card.png';
$image_id = pressx_ensure_image($image_path);

// Create a new landing page.
$post_data = [
  'post_title'    => 'PressX Pricing',
  'post_status'   => 'publish',
  'post_type'     => 'landing',
  'post_name'     => 'pricing',
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
    'hero_layout' => 'image_bottom',
    'heading' => 'Empower Your Content with PressX Today',
    'summary' => "Discover the power of a decoupled CMS that adapts to your needs. With PressX, you can create, manage, and scale your content effortlessly.",
    'media' => $image_url,
    'link_title' => 'Get Started',
    'link_url' => '#get-started',
    'link2_title' => 'Learn More',
    'link2_url' => '#learn-more',
  ],
  [
    '_type' => 'side_by_side',
    'eyebrow' => 'Explore',
    'layout' => 'image_right',
    'title' => 'Unleash Your Content with PressX',
    'summary' => "PressX is a powerful decoupled CMS that streamlines content management and enhances user experience. With its flexible architecture, you can easily integrate and scale your digital projects.",
    'media' => $image_url,
    'features' => [
      [
        '_type' => 'bullet',
        'text' => 'Seamless integration, customizable solutions, and robust performance for all your content needs.',
        'title' => 'Key Features',
      ],
      [
        '_type' => 'bullet',
        'text' => 'Experience unmatched flexibility and control over your content delivery and management.',
        'title' => 'Why Choose',
      ],
    ],
    'link_title' => 'Learn More',
    'link_url' => '#learn-more',
  ],
  [
    '_type' => 'text',
    'eyebrow' => 'Tailored PressX Offerings',
    'title' => 'Unlock the Full Potential of PressX',
    'body' => '<p>Tailor your PressX experience: choose between self-managed and full-service options</p>',
    'text_layout' => 'centered',
  ],
  [
    '_type' => 'pricing',
    'title' => '',
    'includes_label' => 'Includes:',
    'cards' => [
      [
        'eyebrow' => 'PressX CMS Platform',
        'title' => 'Free',
        'monthly_label' => '',
        'features' => [
          ['text' => 'Full access to open source features'],
          ['text' => 'Community support'],
          ['text' => 'Documentation'],
          ['text' => 'AI development features'],
        ],
        'cta_text' => 'Get Started',
        'cta_link' => '#',
      ],
      [
        'eyebrow' => 'Paid Services',
        'title' => 'Contact Us',
        'monthly_label' => '',
        'features' => [
          ['text' => 'Custom development'],
          ['text' => 'Content migration'],
          ['text' => 'Ongoing support'],
          ['text' => 'Consulting services'],
        ],
        'cta_text' => 'Contact Sales',
        'cta_link' => '#',
      ],
    ],
  ],
  [
    '_type' => 'side_by_side',
    'layout' => 'image_left',
    'title' => 'Get Started with PressX',
    'summary' => 'Join us today and unlock the full potential of your content management experience.',
    'media' => $image_url,
    'link_title' => 'Sign Up',
    'link_url' => '#sign-up',
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
echo "http://pressx.ddev.site:3333/{$slug} (Next.js)\n";
