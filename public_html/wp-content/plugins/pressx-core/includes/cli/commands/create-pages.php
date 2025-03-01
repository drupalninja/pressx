<?php

/**
 * @file
 * Script to create Privacy Policy and Terms of Use pages.
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Creates Privacy Policy and Terms of Use pages.
 *
 * @param bool $force
 *   Whether to force recreation of pages even if they already exist.
 */
function pressx_create_pages($force = FALSE) {
  // Include the image handler.
  require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'image-handler.php';

  // Get the image ID using the helper function.
  $image_path = plugin_dir_path(dirname(dirname(__FILE__))) . 'images/card.png';
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
          <li>Object to processing of your data</li>
        </ul>

        <h3>Third-Party Disclosure</h3>
        <p>We do not sell, trade, or otherwise transfer your personally identifiable information to outside parties except as described in this Privacy Policy.</p>

        <h3>Cookies</h3>
        <p>We use cookies to enhance your experience, analyze site usage, and assist in our marketing efforts. You can control cookies through your browser settings.</p>

        <h3>Updates to This Policy</h3>
        <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.</p>

        <h3>Contact Us</h3>
        <p>If you have any questions about this Privacy Policy, please contact us at privacy@pressx.com.</p>',
      'meta' => [
        '_wp_page_template' => 'default',
        '_next_page_template' => 'privacy',
      ],
      'featured_image' => $image_id,
    ],
    [
      'title' => 'Terms of Use',
      'slug' => 'terms',
      'content' => '<h2>Acceptance of Terms</h2>
        <p>By accessing and using PressX, you accept and agree to be bound by the terms and provisions of this agreement.</p>

        <h3>Use License</h3>
        <p>Permission is granted to temporarily use PressX for personal, non-commercial transitory viewing only.</p>
        <p>This is the grant of a license, not a transfer of title, and under this license you may not:</p>
        <ul>
          <li>Modify or copy the materials</li>
          <li>Use the materials for any commercial purpose</li>
          <li>Attempt to decompile or reverse engineer any software contained on PressX</li>
          <li>Remove any copyright or other proprietary notations from the materials</li>
          <li>Transfer the materials to another person or "mirror" the materials on any other server</li>
        </ul>

        <h3>Disclaimer</h3>
        <p>The materials on PressX are provided on an \'as is\' basis. PressX makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</p>

        <h3>Limitations</h3>
        <p>In no event shall PressX or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on PressX, even if PressX or a PressX authorized representative has been notified orally or in writing of the possibility of such damage.</p>

        <h3>Accuracy of Materials</h3>
        <p>The materials appearing on PressX could include technical, typographical, or photographic errors. PressX does not warrant that any of the materials on its website are accurate, complete or current. PressX may make changes to the materials contained on its website at any time without notice.</p>

        <h3>Links</h3>
        <p>PressX has not reviewed all of the sites linked to its website and is not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by PressX of the site. Use of any such linked website is at the user\'s own risk.</p>

        <h3>Modifications</h3>
        <p>PressX may revise these terms of service for its website at any time without notice. By using this website you are agreeing to be bound by the then current version of these terms of service.</p>

        <h3>Governing Law</h3>
        <p>These terms and conditions are governed by and construed in accordance with the laws and you irrevocably submit to the exclusive jurisdiction of the courts in that location.</p>',
      'meta' => [
        '_wp_page_template' => 'default',
        '_next_page_template' => 'terms',
      ],
      'featured_image' => $image_id,
    ],
  ];

  // Remove default WordPress pages if force is true.
  if ($force) {
    WP_CLI::log("Removing default WordPress pages...");
    $default_pages = get_pages([
      'post_status' => ['publish', 'draft'],
    ]);

    $removed_count = 0;
    foreach ($default_pages as $default_page) {
      // Skip pages we're about to create (in case script is run multiple times).
      $skip = FALSE;
      foreach ($pages as $page_data) {
        if (isset($page_data['slug']) && $default_page->post_name === $page_data['slug']) {
          $skip = TRUE;
          break;
        }
      }

      if (!$skip) {
        // Force delete, bypass trash.
        wp_delete_post($default_page->ID, TRUE);
        WP_CLI::log("Deleted page: {$default_page->post_title} (ID: {$default_page->ID})");
        $removed_count++;
      }
    }

    if ($removed_count > 0) {
      WP_CLI::success("{$removed_count} default pages removed successfully.");
    }
    else {
      WP_CLI::log("No default pages found to remove.");
    }
  }

  // Create or update each page.
  foreach ($pages as $page_data) {
    // Check if the page already exists.
    $existing_page = get_page_by_path($page_data['slug']);

    if ($existing_page && !$force) {
      WP_CLI::log("Page '{$page_data['title']}' already exists. Skipping.");
      continue;
    }

    // Prepare the page data.
    $page_args = [
      'post_title' => $page_data['title'],
      'post_name' => $page_data['slug'],
      'post_content' => $page_data['content'],
      'post_status' => 'publish',
      'post_type' => 'page',
    ];

    // If the page exists and force is true, update it.
    if ($existing_page && $force) {
      $page_args['ID'] = $existing_page->ID;
      $page_id = wp_update_post($page_args);
      WP_CLI::log("Updated page: {$page_data['title']}");
    }
    else {
      // Otherwise, create a new page.
      $page_id = wp_insert_post($page_args);
      WP_CLI::log("Created page: {$page_data['title']}");
    }

    // Set the featured image if provided.
    if (!empty($page_data['featured_image']) && $page_id) {
      set_post_thumbnail($page_id, $page_data['featured_image']);
    }

    // Set meta fields if provided.
    if (!empty($page_data['meta']) && $page_id) {
      foreach ($page_data['meta'] as $meta_key => $meta_value) {
        update_post_meta($page_id, $meta_key, $meta_value);
      }
    }
  }

  return TRUE;
}
