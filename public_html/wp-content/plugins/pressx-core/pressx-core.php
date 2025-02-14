<?php
/**
 * Plugin Name: PressX Core
 * Description: Core functionality for PressX headless WordPress setup
 * Version: 1.0.0
 * Author: PressX
 * @file
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once '/var/www/html/vendor/autoload.php';

use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

// Initialize Carbon Fields
add_action('after_setup_theme', function() {
    Carbon_Fields::boot();
});

// Register Landing Page post type
add_action('init', function() {
    register_post_type('landing', [
        'labels' => [
            'name' => 'Landing Pages',
            'singular_name' => 'Landing Page',
        ],
        'public' => true,
        'show_in_graphql' => true,
        'graphql_single_name' => 'landing',
        'graphql_plural_name' => 'landings',
        'supports' => ['title', 'editor', 'custom-fields'],
        'has_archive' => true,
        'show_in_rest' => true,
    ]);
});

// Register Carbon Fields
add_action('carbon_fields_register_fields', function() {
    use_block_editor_for_post_type('landing');

    Container::make('post_meta', 'Landing Page Sections')
        ->where('post_type', '=', 'landing')
        ->add_fields([
            Field::make('complex', 'sections')
                ->add_fields([
                    Field::make('text', 'type')
                        ->set_default_value('hero'),
                    Field::make('text', 'title')
                        ->set_required(true),
                    Field::make('textarea', 'description'),
                    Field::make('image', 'background_image'),
                    Field::make('text', 'cta_text'),
                    Field::make('text', 'cta_link'),
                ])
        ]);
});

// Register GraphQL fields
add_action('graphql_register_types', function() {
    register_graphql_field('Landing', 'sections', [
        'type' => ['list_of' => 'LandingSection'],
        'description' => 'Sections of the landing page',
        'resolve' => function($post) {
            $sections = carbon_get_post_meta($post->ID, 'sections');
            return array_map(function($section) {
                return [
                    'type' => $section['type'],
                    'title' => $section['title'],
                    'description' => $section['description'],
                    'backgroundImage' => wp_get_attachment_url($section['background_image']),
                    'ctaText' => $section['cta_text'],
                    'ctaLink' => $section['cta_link'],
                ];
            }, $sections ?: []);
        }
    ]);

    register_graphql_object_type('LandingSection', [
        'fields' => [
            'type' => ['type' => 'String'],
            'title' => ['type' => 'String'],
            'description' => ['type' => 'String'],
            'backgroundImage' => ['type' => 'String'],
            'ctaText' => ['type' => 'String'],
            'ctaLink' => ['type' => 'String'],
        ]
    ]);
});

// Modify preview links for Next.js
add_filter('preview_post_link', function($preview_link, $post) {
    if (!$post) return $preview_link;

    $frontend_url = defined('PRESSX_FRONTEND_URL') ? PRESSX_FRONTEND_URL : 'http://localhost:3333';
    $preview_secret = defined('WORDPRESS_PREVIEW_SECRET') ? WORDPRESS_PREVIEW_SECRET : 'pressx_preview_secret';

    return sprintf(
        '%s/api/preview?secret=%s&id=%d',
        untrailingslashit($frontend_url),
        $preview_secret,
        $post->ID
    );
}, 10, 2);

// Modify permalink for landing pages
add_filter('post_type_link', function($post_link, $post) {
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

// Modify view post link for landing pages
add_filter('post_link', function($url, $post) {
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

// Override get_sample_permalink for landing pages in admin
add_filter('get_sample_permalink', function($permalink, $post_id, $title, $name, $post) {
    if (!$post || $post->post_type !== 'landing') {
        return $permalink;
    }

    $frontend_url = defined('PRESSX_FRONTEND_URL') ? PRESSX_FRONTEND_URL : 'http://localhost:3333';
    $post_name = $name ? $name : $post->post_name;

    return [
        sprintf('%s/landing/%%pagename%%', untrailingslashit($frontend_url)),
        $post_name
    ];
}, 10, 5);
