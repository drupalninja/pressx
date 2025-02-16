<?php
/**
 * Plugin Name: PressX Core
 * Plugin URI: https://github.com/your-username/pressx
 * Description: Core functionality for PressX headless WordPress setup.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://github.com/your-username
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: pressx-core
 */

/**
 * @file
 * PressX Core.
 *
 * Core functionality for PressX headless WordPress setup.
 */

if (!defined('ABSPATH')) {
  exit;
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Carbon_Fields;

// Initialize Carbon Fields.
add_action('after_setup_theme', function () {
  require_once '/var/www/html/vendor/autoload.php';

  // Define Carbon Fields URL.
  if (!defined('Carbon_Fields\URL')) {
    define('Carbon_Fields\URL', site_url('vendor/htmlburger/carbon-fields'));
  }

  Carbon_Fields::boot();

  // Add custom image sizes based on Drupal configuration.
  add_image_size('hero-s', 640, 360, TRUE);
  add_image_size('hero-sx2', 1280, 720, TRUE);
  add_image_size('hero-m', 960, 540, TRUE);
  add_image_size('hero-mx2', 1920, 1080, TRUE);
  add_image_size('hero-l', 1280, 720, TRUE);
  add_image_size('hero-lx2', 2560, 1440, TRUE);

  // 16:9 aspect ratio sizes.
  add_image_size('16:9-xlarge', 1920, 1080, TRUE);
  add_image_size('16:9-large2x', 2560, 1440, TRUE);
  add_image_size('16:9-large', 1280, 720, TRUE);
  add_image_size('16:9-medium', 960, 540, TRUE);
  add_image_size('16:9-small', 640, 360, TRUE);
});

// Register Carbon Fields assets.
add_action('admin_enqueue_scripts', function () {
  wp_enqueue_style('carbon-fields-core', site_url('vendor/htmlburger/carbon-fields/build/classic/core.css'));
  wp_enqueue_style('carbon-fields-metaboxes', site_url('vendor/htmlburger/carbon-fields/build/classic/metaboxes.css'));
  wp_enqueue_script('carbon-fields-vendor', site_url('vendor/htmlburger/carbon-fields/build/classic/vendor.js'), ['jquery'], NULL, TRUE);
  wp_enqueue_script('carbon-fields-core', site_url('vendor/htmlburger/carbon-fields/build/classic/core.js'), ['carbon-fields-vendor'], NULL, TRUE);
  wp_enqueue_script('carbon-fields-metaboxes', site_url('vendor/htmlburger/carbon-fields/build/classic/metaboxes.js'), ['carbon-fields-core'], NULL, TRUE);
});

// Register Landing Page post type.
add_action('init', function () {
  register_post_type('landing', [
    'labels' => [
      'name' => 'Landing Pages',
      'singular_name' => 'Landing Page',
    ],
    'public' => TRUE,
    'show_in_graphql' => TRUE,
    'graphql_single_name' => 'landing',
    'graphql_plural_name' => 'landings',
    'supports' => ['title', 'custom-fields'],
    'has_archive' => TRUE,
    'show_in_rest' => TRUE,
    'rewrite' => [
      'slug' => 'landing',
    ],
    'show_in_nav_menus' => TRUE,
    'publicly_queryable' => TRUE,
  ]);
});

// Initialize Carbon Fields.
add_action('carbon_fields_loaded', function () {
  Container::make('post_meta', 'Landing Page Sections')
    ->where('post_type', '=', 'landing')
    ->add_fields([
      Field::make('complex', 'sections')
        ->set_layout('tabbed-vertical')
        ->setup_labels([
          'plural_name' => 'Sections',
          'singular_name' => 'Section',
        ])
        ->add_fields('hero', [
          Field::make('select', 'hero_layout')
            ->set_options([
              'image_top' => 'Image Top',
              'image_bottom' => 'Image Bottom',
              'image_bottom_split' => 'Image Bottom Split',
            ])
            ->set_default_value('image_top')
            ->set_required(TRUE)
            ->set_help_text('Select the layout for this hero section.'),
          Field::make('text', 'heading')
            ->set_help_text('The main heading for the hero section.'),
          Field::make('rich_text', 'summary')
            ->set_help_text('Provide the teaser summary for the hero.'),
          Field::make('image', 'media')
            ->set_value_type('url')
            ->set_help_text('Featured media item for the hero.'),
          Field::make('text', 'link_title')
            ->set_help_text('The text for the primary call-to-action link.'),
          Field::make('text', 'link_url')
            ->set_help_text('The URL for the primary call-to-action link.'),
          Field::make('text', 'link2_title')
            ->set_help_text('The text for the secondary optional link.'),
          Field::make('text', 'link2_url')
            ->set_help_text('The URL for the secondary optional link.'),
        ]),
    ]);
});

// Register GraphQL fields.
add_action('graphql_register_types', function () {
  register_graphql_field('Landing', 'sections', [
    'type' => ['list_of' => 'LandingSection'],
    'description' => 'Sections of the landing page.',
    'resolve' => function ($post) {
      $sections = carbon_get_post_meta($post->ID, 'sections');
      return array_map(function ($section) {
        $link = [
          'url' => $section['link_url'] ?? '',
          'title' => $section['link_title'] ?? '',
        ];
        $link2 = [
          'url' => $section['link2_url'] ?? '',
          'title' => $section['link2_title'] ?? '',
        ];

        return [
          'type' => $section['_type'] ?? 'hero',
          'heroLayout' => $section['hero_layout'] ?? 'image_top',
          'heading' => $section['heading'] ?? '',
          'summary' => $section['summary'] ?? '',
          'media' => $section['media'] ?? '',
          'link' => $link,
          'link2' => $link2,
        ];
      }, $sections ?: []);
    },
  ]);

  register_graphql_object_type('Link', [
    'fields' => [
      'url' => ['type' => 'String'],
      'title' => ['type' => 'String'],
    ],
  ]);

  register_graphql_object_type('LandingSection', [
    'fields' => [
      'type' => ['type' => 'String'],
      'heroLayout' => ['type' => 'String'],
      'heading' => ['type' => 'String'],
      'summary' => ['type' => 'String'],
      'media' => ['type' => 'String'],
      'link' => ['type' => 'Link'],
      'link2' => ['type' => 'Link'],
    ],
  ]);
});

// Register navigation menus.
add_action('init', function () {
  register_nav_menus([
    'primary' => 'Primary Navigation',
    'footer' => 'Footer Navigation',
  ]);
});

// Modify preview links for Next.js.
add_filter('preview_post_link', function ($preview_link, $post) {
  if (!$post) {
    return $preview_link;
  }

  $frontend_url = defined('PRESSX_FRONTEND_URL') ? PRESSX_FRONTEND_URL : 'http://localhost:3333';
  $preview_secret = defined('WORDPRESS_PREVIEW_SECRET') ? WORDPRESS_PREVIEW_SECRET : 'pressx_preview_secret';

  return sprintf(
    '%s/api/preview?secret=%s&id=%d',
    untrailingslashit($frontend_url),
    $preview_secret,
    $post->ID
  );
}, 10, 2);

// Modify permalink for landing pages.
add_filter('post_type_link', function ($post_link, $post) {
  if ($post->post_type !== 'landing') {
    return $post_link;
  }

  $frontend_url = defined('PRESSX_FRONTEND_URL') ? PRESSX_FRONTEND_URL : 'http://localhost:3333';
  return sprintf(
    '%s/landing/%s',
    untrailingslashit($frontend_url),
    $post->post_name
  );
}, 10, 2);

// Modify view post link for landing pages.
add_filter('post_link', function ($url, $post) {
  if ($post->post_type !== 'landing') {
    return $url;
  }

  $frontend_url = defined('PRESSX_FRONTEND_URL') ? PRESSX_FRONTEND_URL : 'http://localhost:3333';
  return sprintf(
    '%s/landing/%s',
    untrailingslashit($frontend_url),
    $post->post_name
  );
}, 10, 2);

// Override get_sample_permalink for landing pages in admin.
add_filter('get_sample_permalink', function ($permalink, $post_id, $title, $name, $post) {
  if (!$post || $post->post_type !== 'landing') {
    return $permalink;
  }

  $frontend_url = defined('PRESSX_FRONTEND_URL') ? PRESSX_FRONTEND_URL : 'http://localhost:3333';
  $post_name = $name ? $name : $post->post_name;

  return [
    sprintf('%s/landing/%%pagename%%', untrailingslashit($frontend_url)),
    $post_name,
  ];
}, 10, 5);
