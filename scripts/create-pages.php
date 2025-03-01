<?php

/**
 * @file
 * Script to create Privacy Policy and Terms of Use pages.
 */

// Include the image handler.
require_once __DIR__ . '/includes/image-handler.php';

// Get the image ID using the helper function.
$image_path = __DIR__ . '/images/card.png';
$image_id = pressx_ensure_image($image_path);

// Sample page data with Next.js integration.
$pages = [
  [
    'title' => 'Privacy Policy',
    'slug' => 'privacy',
    'content' => '<h2>Information We Collect</h2>
      <p>At PressX, we are committed to protecting your privacy. This Privacy Policy outlines how we collect, use, and safeguard your personal information.</p>

      <h3>Types of Information Collected</h3>
      <ul>
        <li>Personal identification information</li>
        <li>Usage data and analytics</li>
        <li>Cookies and tracking technologies</li>
      </ul>

      <h3>How We Use Your Information</h3>
      <ul>
        <li>To provide and maintain our service</li>
        <li>To notify you about changes to our service</li>
        <li>To allow you to participate in interactive features</li>
        <li>To provide customer support</li>
      </ul>

      <h3>Data Protection</h3>
      <p>We implement a variety of security measures to maintain the safety of your personal information:</p>
      <ul>
        <li>Encryption of sensitive data</li>
        <li>Regular security audits</li>
        <li>Restricted access to personal information</li>
      </ul>

      <h3>Your Rights</h3>
      <p>You have the right to:</p>
      <ul>
        <li>Access your personal data</li>
        <li>Correct inaccurate information</li>
        <li>Request deletion of your data</li>
        <li>Opt-out of marketing communications</li>
      </ul>

      <p><em>Last updated: ' . date('F j, Y') . '</em></p>',
    'metadata' => [
      'description' => 'PressX Privacy Policy - How we collect, use, and protect your personal information',
      'keywords' => ['Privacy', 'Data Protection', 'Legal'],
    ],
  ],
  [
    'title' => 'Terms of Use',
    'slug' => 'terms',
    'content' => '<h2>Acceptance of Terms</h2>
      <p>By accessing and using the PressX platform, you agree to be bound by these Terms of Use. Please read them carefully.</p>

      <h3>Use of Service</h3>
      <ul>
        <li>You must be at least 13 years old to use our service</li>
        <li>You agree to use the platform for lawful purposes only</li>
        <li>You are responsible for maintaining the confidentiality of your account</li>
      </ul>

      <h3>Intellectual Property</h3>
      <p>All content, features, and functionality are and will remain the exclusive property of PressX:</p>
      <ul>
        <li>Trademarks and logos are protected</li>
        <li>User-generated content remains the property of its creators</li>
        <li>Unauthorized use of our intellectual property is prohibited</li>
      </ul>

      <h3>Limitation of Liability</h3>
      <p>PressX is provided "as is" and "as available" without any warranties:</p>
      <ul>
        <li>We are not liable for any direct, indirect, incidental damages</li>
        <li>We do not guarantee uninterrupted or error-free service</li>
        <li>Users use the platform at their own risk</li>
      </ul>

      <h3>Modifications to Terms</h3>
      <p>We reserve the right to modify these terms at any time. Continued use of the platform constitutes acceptance of updated terms.</p>

      <h3>Governing Law</h3>
      <p>These terms are governed by the laws of the jurisdiction in which PressX is registered.</p>

      <p><em>Last updated: ' . date('F j, Y') . '</em></p>',
    'metadata' => [
      'description' => 'PressX Terms of Use - Legal agreement for using our platform',
      'keywords' => ['Terms', 'Legal', 'Agreement'],
    ],
  ],
];

// Get the admin user.
$admin = get_user_by('login', 'admin');
if (!$admin) {
  echo "Error: Admin user not found.\n";
  exit(1);
}

// Remove default WordPress pages.
echo "Removing default WordPress pages...\n";
$default_pages = get_pages([
  'post_status' => ['publish', 'draft'],
]);

$removed_count = 0;
foreach ($default_pages as $default_page) {
  // Skip pages we're about to create (in case script is run multiple times).
  $skip = FALSE;
  foreach ($pages as $page) {
    if (isset($page['slug']) && $default_page->post_name === $page['slug']) {
      $skip = TRUE;
      break;
    }
  }

  if (!$skip) {
    // Force delete, bypass trash.
    wp_delete_post($default_page->ID, TRUE);
    echo "Deleted page: {$default_page->post_title} (ID: {$default_page->ID})\n";
    $removed_count++;
  }
}

if ($removed_count > 0) {
  echo "{$removed_count} default pages removed successfully.\n\n";
}
else {
  echo "No default pages found to remove.\n\n";
}

// Create the pages.
foreach ($pages as $page) {
  // Prepare page data with admin author.
  $page_data = [
    'post_title' => $page['title'],
    'post_content' => $page['content'],
    'post_status' => 'publish',
    'post_type' => 'page',
    'post_author' => $admin->ID,
  ];

  // Add slug if specified.
  if (isset($page['slug'])) {
    $page_data['post_name'] = $page['slug'];
  }

  // Insert the page.
  $page_id = wp_insert_post($page_data);

  if (is_wp_error($page_id)) {
    echo "Error creating page '{$page['title']}': " . $page_id->get_error_message() . "\n";
    continue;
  }

  // Set featured image if we have one.
  if ($image_id) {
    set_post_thumbnail($page_id, $image_id);
  }

  // Add metadata if specified.
  if (isset($page['metadata'])) {
    update_post_meta($page_id, '_page_metadata', $page['metadata']);
  }

  // Get the page URL.
  $page_url = get_permalink($page_id);

  echo "Created page: {$page['title']} (ID: {$page_id})\n";
  echo "URL: {$page_url}\n";
}

echo "\nPages created successfully! 🎉\n";
echo "View your pages at:\n";
echo "http://pressx.ddev.site/privacy\n";
echo "http://pressx.ddev.site/terms\n";
echo "http://pressx.ddev.site:3333/privacy\n";
echo "http://pressx.ddev.site:3333/terms\n";
