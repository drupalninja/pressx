<?php

/**
 * @file
 * WP-CLI commands for PressX.
 */

if (!defined('ABSPATH')) {
  exit;
}

if (!defined('WP_CLI') || !WP_CLI) {
  return;
}

/**
 * PressX CLI commands.
 */
class PressX_CLI_Command extends WP_CLI_Command {

  /**
   * Creates pages (Privacy Policy and Terms of Use).
   *
   * ## OPTIONS
   *
   * [--force]
   * : Force recreation of pages even if they already exist.
   *
   * ## EXAMPLES
   *
   * wp pressx create-pages
   * wp pressx create-pages --force
   */
  public function create_pages($args, $assoc_args) {
    $force = isset($assoc_args['force']) ? TRUE : FALSE;

    // Include the script logic.
    require_once plugin_dir_path(__FILE__) . 'commands/create-pages.php';

    // Execute the script with the force parameter.
    pressx_create_pages($force);

    WP_CLI::success('Pages created successfully.');
  }

  /**
   * Creates the home page.
   *
   * ## OPTIONS
   *
   * [--force]
   * : Force recreation of the home page even if it already exists.
   *
   * ## EXAMPLES
   *
   * wp pressx create-home
   * wp pressx create-home --force
   */
  public function create_home($args, $assoc_args) {
    $force = isset($assoc_args['force']) ? TRUE : FALSE;

    // Include the script logic.
    require_once plugin_dir_path(__FILE__) . 'commands/create-home.php';

    // Execute the script with the force parameter.
    pressx_create_home($force);

    WP_CLI::success('Home page created successfully.');
  }

  /**
   * Creates the pricing page.
   *
   * ## OPTIONS
   *
   * [--force]
   * : Force recreation of the pricing page even if it already exists.
   *
   * ## EXAMPLES
   *
   * wp pressx create-pricing
   * wp pressx create-pricing --force
   */
  public function create_pricing($args, $assoc_args) {
    $force = isset($assoc_args['force']) ? TRUE : FALSE;

    // Include the script logic.
    require_once plugin_dir_path(__FILE__) . 'commands/create-pricing.php';

    // Execute the script with the force parameter.
    pressx_create_pricing($force);

    WP_CLI::success('Pricing page created successfully.');
  }

  /**
   * Creates the features page.
   *
   * ## OPTIONS
   *
   * [--force]
   * : Force recreation of the features page even if it already exists.
   *
   * ## EXAMPLES
   *
   * wp pressx create-features
   * wp pressx create-features --force
   */
  public function create_features($args, $assoc_args) {
    $force = isset($assoc_args['force']) ? TRUE : FALSE;

    // Include the script logic.
    require_once plugin_dir_path(__FILE__) . 'commands/create-features.php';

    // Execute the script with the force parameter.
    pressx_create_features($force);

    WP_CLI::success('Features page created successfully.');
  }

  /**
   * Creates the resources page.
   *
   * ## OPTIONS
   *
   * [--force]
   * : Force recreation of the resources page even if it already exists.
   *
   * ## EXAMPLES
   *
   * wp pressx create-resources
   * wp pressx create-resources --force
   */
  public function create_resources($args, $assoc_args) {
    $force = isset($assoc_args['force']) ? TRUE : FALSE;

    // Include the script logic.
    require_once plugin_dir_path(__FILE__) . 'commands/create-resources.php';

    // Execute the script with the force parameter.
    pressx_create_resources($force);

    WP_CLI::success('Resources page created successfully.');
  }

  /**
   * Creates the landing page.
   *
   * ## OPTIONS
   *
   * [--force]
   * : Force recreation of the landing page even if it already exists.
   *
   * ## EXAMPLES
   *
   * wp pressx create-landing
   * wp pressx create-landing --force
   */
  public function create_landing($args, $assoc_args) {
    $force = isset($assoc_args['force']) ? TRUE : FALSE;

    // Include the script logic.
    require_once plugin_dir_path(__FILE__) . 'commands/create-landing.php';

    // Execute the script with the force parameter.
    pressx_create_landing($force);

    WP_CLI::success('Landing page created successfully.');
  }

  /**
   * Creates the AI landing page.
   *
   * ## OPTIONS
   *
   * [<prompt>...]
   * : The prompt to use for generating the AI landing page (e.g., 'coffee shop').
   *
   * ## EXAMPLES
   *
   * wp pressx create-ai-landing
   * wp pressx create-ai-landing "coffee shop"
   * wp pressx create-ai-landing coffee shop
   */
  public function create_ai_landing($args, $assoc_args) {
    // Include the script logic.
    require_once plugin_dir_path(__FILE__) . 'commands/create-ai-landing.php';

    // Get the prompt from the positional arguments
    $options = [];
    if (!empty($args)) {
      $options['prompt'] = implode(' ', $args);
    }

    // Execute the script with options.
    pressx_create_ai_landing($options);

    WP_CLI::success('AI landing page created successfully.');
  }

  /**
   * Creates the get started page.
   *
   * ## OPTIONS
   *
   * [--force]
   * : Force recreation of the get started page even if it already exists.
   *
   * ## EXAMPLES
   *
   * wp pressx create-get-started
   * wp pressx create-get-started --force
   */
  public function create_get_started($args, $assoc_args) {
    $force = isset($assoc_args['force']) ? TRUE : FALSE;

    // Include the script logic.
    require_once plugin_dir_path(__FILE__) . 'commands/create-get-started.php';

    // Execute the script with the force parameter.
    pressx_create_get_started($force);

    WP_CLI::success('Get started page created successfully.');
  }

  /**
   * Creates the articles page.
   *
   * ## OPTIONS
   *
   * [--force]
   * : Force recreation of the articles page even if it already exists.
   *
   * ## EXAMPLES
   *
   * wp pressx create-articles
   * wp pressx create-articles --force
   */
  public function create_articles($args, $assoc_args) {
    $force = isset($assoc_args['force']) ? TRUE : FALSE;

    // Include the script logic.
    require_once plugin_dir_path(__FILE__) . 'commands/create-articles.php';

    // Execute the script with the force parameter.
    pressx_create_articles($force);

    WP_CLI::success('Articles page created successfully.');
  }

  /**
   * Creates the contact page.
   *
   * ## OPTIONS
   *
   * [--force]
   * : Force recreation of the contact page even if it already exists.
   *
   * ## EXAMPLES
   *
   * wp pressx create-contact
   * wp pressx create-contact --force
   */
  public function create_contact($args, $assoc_args) {
    $force = isset($assoc_args['force']) ? TRUE : FALSE;

    // Include the script logic.
    require_once plugin_dir_path(__FILE__) . 'commands/create-contact.php';

    // Execute the script with the force parameter.
    pressx_create_contact($force);

    WP_CLI::success('Contact page created successfully.');
  }

  /**
   * Creates the main menu.
   *
   * ## OPTIONS
   *
   * [--force]
   * : Force recreation of the menu even if it already exists.
   *
   * ## EXAMPLES
   *
   * wp pressx create-menu
   * wp pressx create-menu --force
   */
  public function create_menu($args, $assoc_args) {
    $force = isset($assoc_args['force']) ? TRUE : FALSE;

    // Include the script logic.
    require_once plugin_dir_path(__FILE__) . 'commands/create-menu.php';

    // Execute the script with the force parameter.
    pressx_create_menu($force);

    WP_CLI::success('Menu created successfully.');
  }

  /**
   * Creates the footer menu.
   *
   * ## OPTIONS
   *
   * [--force]
   * : Force recreation of the footer menu even if it already exists.
   *
   * ## EXAMPLES
   *
   * wp pressx create-footer-menu
   * wp pressx create-footer-menu --force
   */
  public function create_footer_menu($args, $assoc_args) {
    $force = isset($assoc_args['force']) ? TRUE : FALSE;

    // Include the script logic.
    require_once plugin_dir_path(__FILE__) . 'commands/create-footer-menu.php';

    // Execute the script with the force parameter.
    pressx_create_footer_menu($force);

    WP_CLI::success('Footer menu created successfully.');
  }

  /**
   * Tests the Pexels API integration.
   *
   * ## OPTIONS
   *
   * [<query>]
   * : The search query to test with. If not provided, default test queries will be used.
   *
   * [--count=<number>]
   * : The number of images to fetch for gallery test. Default: 4.
   *
   * ## EXAMPLES
   *
   * wp pressx test-pexels
   * wp pressx test-pexels "coffee shop"
   * wp pressx test-pexels "mountain landscape" --count=6
   */
  public function test_pexels($args, $assoc_args) {
    $query = isset($args[0]) ? $args[0] : '';
    $count = isset($assoc_args['count']) ? intval($assoc_args['count']) : 4;

    // Include the script logic.
    require_once plugin_dir_path(__FILE__) . 'commands/test-pexels.php';

    // Execute the script with the parameters.
    pressx_test_pexels($query, $count);

    WP_CLI::success('Pexels API test completed.');
  }
}

// Register the commands
WP_CLI::add_command('pressx', 'PressX_CLI_Command');
