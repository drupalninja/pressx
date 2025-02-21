<?php

/**
 * @file
 * PressX Core.
 *
 * Core functionality for PressX headless WordPress setup.
 *
 * Plugin Name: PressX Core
 * Plugin URI: https://github.com/your-username/pressx
 * Description: Core functionality for PressX headless WordPress setup.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://github.com/your-username
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: pressx-core.
 */

/**
 * @file
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
  require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/vendor/autoload.php';

  // Define Carbon Fields URL.
  if (!defined('Carbon_Fields\\URL')) {
    define('Carbon_Fields\\URL', plugins_url('vendor/carbon-fields', __FILE__));
  }

  // Boot Carbon Fields
  Carbon_Fields::boot();

  // Disable default WordPress image sizes.
  add_filter('intermediate_image_sizes', '__return_empty_array');
  add_filter('big_image_size_threshold', '__return_false');
});

// Register Carbon Fields assets.
add_action('admin_enqueue_scripts', function () {
  $vendor_url = plugins_url('vendor/carbon-fields', __FILE__);
  wp_enqueue_style('carbon-fields-core', $vendor_url . '/build/classic/core.min.css');
  wp_enqueue_style('carbon-fields-metaboxes', $vendor_url . '/build/classic/metaboxes.min.css');
  wp_enqueue_script('carbon-fields-vendor', $vendor_url . '/build/classic/vendor.min.js', ['jquery'], null, true);
  wp_enqueue_script('carbon-fields-core', $vendor_url . '/build/classic/core.min.js', ['carbon-fields-vendor'], null, true);
  wp_enqueue_script('carbon-fields-metaboxes', $vendor_url . '/build/classic/metaboxes.min.js', ['carbon-fields-core'], null, true);
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
    'supports' => ['title'],
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
            ->set_help_text('The main heading for the hero section. Use **text** for bold text.'),
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
        ])
        ->add_fields('accordion', [
          Field::make('text', 'title')
            ->set_help_text('The title for the accordion section.'),
          Field::make('complex', 'items')
            ->set_layout('tabbed-horizontal')
            ->setup_labels([
              'plural_name' => 'Items',
              'singular_name' => 'Item',
            ])
            ->add_fields([
              Field::make('text', 'title')
                ->set_required(TRUE)
                ->set_help_text('The title for this accordion item.'),
              Field::make('rich_text', 'body')
                ->set_required(TRUE)
                ->set_help_text('The content for this accordion item.'),
              Field::make('text', 'link_title')
                ->set_help_text('Optional link text for this item.'),
              Field::make('text', 'link_url')
                ->set_help_text('Optional link URL for this item.'),
            ]),
        ])
        ->add_fields('card_group', [
          Field::make('text', 'title')
            ->set_help_text('The title for the card group section.'),
          Field::make('complex', 'cards')
            ->set_layout('tabbed-horizontal')
            ->setup_labels([
              'plural_name' => 'Cards',
              'singular_name' => 'Card',
            ])
            ->add_fields([
              Field::make('select', 'type')
                ->set_options([
                  'stat' => 'Stat Card',
                  'custom' => 'Custom Card',
                ])
                ->set_required(TRUE)
                ->set_help_text('Select the type of card.'),
              Field::make('image', 'media')
                ->set_value_type('url')
                ->set_help_text('Optional media for the card.'),
              Field::make('text', 'media_link')
                ->set_help_text('Optional link for the media (custom cards only).'),
              Field::make('text', 'heading')
                ->set_required(TRUE)
                ->set_help_text('The heading for the card.'),
              Field::make('text', 'heading_url')
                ->set_help_text('Optional URL for the heading (custom cards only).'),
              Field::make('text', 'body')
                ->set_help_text('The body text for stat cards.'),
              Field::make('text', 'summary_text')
                ->set_help_text('The summary text for custom cards.'),
              Field::make('complex', 'tags')
                ->set_layout('tabbed-vertical')
                ->add_fields([
                  Field::make('text', 'tag')
                    ->set_required(TRUE)
                    ->set_help_text('A tag for the card.'),
                ])
                ->set_help_text('Tags for custom cards.'),
              Field::make('text', 'icon')
                ->set_help_text('Optional icon name for stat cards (e.g., "rocket", "zap", "star").'),
              Field::make('text', 'link_title')
                ->set_help_text('Optional link text for the card.'),
              Field::make('text', 'link_url')
                ->set_help_text('Optional link URL for the card.'),
            ]),
        ])
        ->add_fields('logo_collection', [
          Field::make('text', 'title')
            ->set_help_text('The title for the logo collection section.'),
          Field::make('media_gallery', 'logos')
            ->set_type(['image'])
            ->set_help_text('Add collection of logos.')
        ])
        ->add_fields('carousel', [
          Field::make('text', 'title')
            ->set_help_text('The title for the carousel section.'),
          Field::make('complex', 'items')
            ->set_layout('tabbed-horizontal')
            ->setup_labels([
              'plural_name' => 'Items',
              'singular_name' => 'Item',
            ])
            ->add_fields([
              Field::make('image', 'media')
                ->set_value_type('url')
                ->set_required(TRUE)
                ->set_help_text('The image for this item.'),
              Field::make('text', 'title')
                ->set_required(TRUE)
                ->set_help_text('The title for this item.'),
              Field::make('text', 'summary')
                ->set_help_text('The summary text for this item.'),
            ]),
        ])
        ->add_fields('embed', [
          Field::make('text', 'title')
            ->set_help_text('The title for the embed section.'),
          Field::make('text', 'embed_url')
            ->set_required(TRUE)
            ->set_help_text('The URL to embed (e.g., YouTube, Twitter, etc.).'),
          Field::make('text', 'caption')
            ->set_help_text('Optional caption text for the embedded content.'),
          Field::make('text', 'max_width')
            ->set_help_text('Optional maximum width for the embed (e.g., 800px, 100%).'),
        ])
        ->add_fields('media', [
          Field::make('text', 'title')
            ->set_help_text('Optional title for the media section.'),
          Field::make('image', 'media')
            ->set_value_type('url')
            ->set_required(TRUE)
            ->set_help_text('The media to display in this section.'),
        ])
        ->add_fields('gallery', [
          Field::make('text', 'title')
            ->set_help_text('The title for the gallery section.'),
          Field::make('rich_text', 'summary')
            ->set_help_text('Optional summary text for the gallery.'),
          Field::make('complex', 'media_items')
            ->set_layout('tabbed-horizontal')
            ->setup_labels([
              'plural_name' => 'Media Items',
              'singular_name' => 'Media Item',
            ])
            ->add_fields([
              Field::make('image', 'media')
                ->set_value_type('url')
                ->set_required(TRUE)
                ->set_help_text('Image for the gallery.'),
              Field::make('text', 'alt')
                ->set_help_text('Alt text for the image.')
            ])
        ]),
    ]);
});

// Helper function to consistently resolve media fields
function resolve_media_field($media_url) {
  if (empty($media_url)) {
    return NULL;
  }

  $attachment_id = attachment_url_to_postid($media_url);
  if (!$attachment_id) {
    // If we can't get an attachment ID but have a URL, return just the URL
    return [
      'sourceUrl' => $media_url
    ];
  }

  $metadata = wp_get_attachment_metadata($attachment_id);
  return [
    'sourceUrl' => wp_get_attachment_url($attachment_id),
    'width' => (int) ($metadata['width'] ?? 0),
    'height' => (int) ($metadata['height'] ?? 0),
    'mimeType' => get_post_mime_type($attachment_id)
  ];
}

// Register GraphQL fields.
add_action('graphql_register_types', function () {
  // Register CarouselItem type
  register_graphql_object_type('CarouselItem', [
    'fields' => [
      'media' => [
        'type' => 'Media',
        'description' => 'Media for the carousel item',
      ],
      'title' => [
        'type' => 'String',
        'description' => 'Title of the carousel item',
      ],
      'summary' => [
        'type' => 'String',
        'description' => 'Summary text for the carousel item',
      ],
    ],
  ]);

  register_graphql_object_type('GalleryMediaItem', [
    'fields' => [
      'media' => ['type' => 'Media'],
      'alt' => ['type' => 'String'],
    ],
  ]);

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

        // Convert markdown bold to HTML
        $heading = $section['heading'] ?? '';
        $heading = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $heading);

        // Process accordion items if this is an accordion section
        $accordion_items = [];
        if ($section['_type'] === 'accordion' && !empty($section['items'])) {
          $accordion_items = array_map(function ($item) {
            return [
              'title' => $item['title'] ?? '',
              'body' => [
                'value' => $item['body'] ?? '',
              ],
              'link' => !empty($item['link_url']) ? [
                'url' => $item['link_url'],
                'title' => $item['link_title'] ?? '',
              ] : NULL,
            ];
          }, $section['items']);
        }

        // Process carousel items if this is a carousel section
        $carousel_items = [];
        if ($section['_type'] === 'carousel' && !empty($section['items'])) {
          $carousel_items = array_map(function ($item) {
            return [
              'media' => resolve_media_field($item['media']),
              'title' => $item['title'] ?? '',
              'summary' => $item['summary'] ?? '',
            ];
          }, $section['items']);
        }

        // Process cards if this is a card group section
        $cards = [];
        if ($section['_type'] === 'card_group' && !empty($section['cards'])) {
          $cards = array_map(function ($card) {
            // Process tags
            $tags = [];
            if (!empty($card['tags'])) {
              $tags = array_map(function ($tag_item) {
                return $tag_item['tag'] ?? '';
              }, $card['tags']);
            }

            return [
              'type' => $card['type'] ?? 'custom',
              'media' => resolve_media_field($card['media']),
              'mediaLink' => $card['media_link'] ?? '',
              'heading' => [
                'title' => $card['heading'] ?? '',
                'url' => $card['type'] === 'custom' ? ($card['heading_url'] ?? '') : NULL,
              ],
              'body' => $card['body'] ?? '',
              'summaryText' => $card['summary_text'] ?? '',
              'tags' => $tags,
              'icon' => $card['icon'] ?? '',
              'link' => !empty($card['link_url']) ? [
                'url' => $card['link_url'],
                'title' => $card['link_title'] ?? '',
              ] : NULL,
            ];
          }, $section['cards']);
        }

        // Process gallery media items if this is a gallery section
        $gallery_media_items = [];
        if ($section['_type'] === 'gallery' && !empty($section['media_items'])) {
          $gallery_media_items = array_map(function ($item) {
            return [
              'media' => resolve_media_field($item['media']),
              'alt' => $item['alt'] ?? '',
            ];
          }, $section['media_items']);
        }

        $type = $section['_type'] ?? 'hero';

        $base = [
          'type' => $type,
          'title' => $section['title'] ?? '',
        ];

        switch ($type) {
          case 'logo_collection':
            return array_merge($base, [
              'type' => 'logo_collection', // Explicitly set the type
              'logos' => array_map(function ($logo_id) {
                $attachment = get_post($logo_id);
                $media = resolve_media_field(wp_get_attachment_url($logo_id));
                $media['alt'] = get_post_meta($logo_id, '_wp_attachment_image_alt', true) ?: $attachment->post_title;
                return $media;
              }, $section['logos'] ?: []),
            ]);

          case 'hero':
            return array_merge($base, [
              'heroLayout' => $section['hero_layout'] ?? 'image_top',
              'heading' => $heading,
              'summary' => $section['summary'] ?? '',
              'media' => resolve_media_field($section['media']),
              'link' => $link,
              'link2' => $link2,
            ]);

          case 'accordion':
            return array_merge($base, [
              'accordionItems' => $accordion_items,
            ]);

          case 'carousel':
            return array_merge($base, [
              'carouselItems' => $carousel_items,
            ]);

          case 'card_group':
            return array_merge($base, [
              'cards' => $cards,
            ]);

          case 'embed':
            return array_merge($base, [
              'embedUrl' => $section['embed_url'] ?? '',
              'caption' => $section['caption'] ?? '',
              'maxWidth' => $section['max_width'] ?? '',
            ]);

          case 'media':
            return array_merge($base, [
              'media' => resolve_media_field($section['media'])
            ]);

          case 'gallery':
            return array_merge($base, [
              'summary' => $section['summary'] ?? '',
              'mediaItems' => !empty($section['media_items']) ? array_map(function ($item) {
                return [
                  'media' => resolve_media_field($item['media']),
                  'alt' => $item['alt'] ?? '',
                ];
              }, $section['media_items']) : [],
            ]);

          default:
            return $base;
        }
      }, $sections ?: []);
    },
  ]);

  register_graphql_object_type('AccordionItem', [
    'fields' => [
      'title' => ['type' => 'String'],
      'body' => ['type' => 'AccordionBody'],
      'link' => ['type' => 'Link'],
    ],
  ]);

  register_graphql_object_type('AccordionBody', [
    'fields' => [
      'value' => ['type' => 'String'],
    ],
  ]);

  register_graphql_object_type('Link', [
    'fields' => [
      'url' => ['type' => 'String'],
      'title' => ['type' => 'String'],
    ],
  ]);

  register_graphql_object_type('ImageSize', [
    'fields' => [
      'name' => ['type' => 'String'],
      'sourceUrl' => ['type' => 'String'],
      'width' => ['type' => 'Int'],
      'height' => ['type' => 'Int'],
    ],
  ]);

  register_graphql_object_type('Media', [
    'fields' => [
      'sourceUrl' => ['type' => 'String'],
      'width' => ['type' => 'Int'],
      'height' => ['type' => 'Int'],
      'alt' => ['type' => 'String'],
    ],
  ]);

  register_graphql_object_type('Card', [
    'fields' => [
      'type' => ['type' => 'String'],
      'media' => ['type' => 'Media'],
      'mediaLink' => ['type' => 'String'],
      'heading' => ['type' => 'CardHeading'],
      'body' => ['type' => 'String'],
      'summaryText' => ['type' => 'String'],
      'tags' => ['type' => ['list_of' => 'String']],
      'icon' => ['type' => 'String'],
      'link' => ['type' => 'Link'],
    ],
  ]);

  register_graphql_object_type('CardHeading', [
    'fields' => [
      'title' => ['type' => ['non_null' => 'String']],
      'url' => ['type' => 'String'],
    ],
  ]);

  register_graphql_object_type('LandingSection', [
    'fields' => [
      'type' => ['type' => 'String'],
      'title' => ['type' => 'String'],
      // Hero fields
      'heroLayout' => ['type' => 'String'],
      'heading' => ['type' => 'String'],
      'summary' => ['type' => 'String'],
      'media' => ['type' => 'Media'],
      'link' => ['type' => 'Link'],
      'link2' => ['type' => 'Link'],
      // Accordion fields
      'accordionItems' => [
        'type' => ['list_of' => 'AccordionItem'],
        'description' => 'Items for accordion section',
      ],
      // Carousel fields
      'carouselItems' => [
        'type' => ['list_of' => 'CarouselItem'],
        'description' => 'Items for carousel section',
      ],
      // Card group fields
      'cards' => [
        'type' => ['list_of' => 'Card'],
        'description' => 'Cards for card group section',
      ],
      // Embed fields
      'embedUrl' => ['type' => 'String'],
      'caption' => ['type' => 'String'],
      'maxWidth' => ['type' => 'String'],
      // Gallery fields
      'mediaItems' => [
        'type' => ['list_of' => 'GalleryMediaItem'],
        'description' => 'Media items for gallery section',
      ],
      // Logo collection fields
      'logos' => [
        'type' => ['list_of' => 'Media'],
        'description' => 'Logos for logo collection section',
      ],
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
