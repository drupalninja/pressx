# PressX WP-CLI Commands

This directory contains WP-CLI commands for PressX. These commands were migrated from standalone scripts in the `scripts/` directory to be properly integrated as WP-CLI commands.

## Available Commands

All commands are available under the `wp pressx` namespace:

- `wp pressx create-pages` - Creates Privacy Policy and Terms of Use pages
- `wp pressx create-home` - Creates the home page
- `wp pressx create-pricing` - Creates the pricing page
- `wp pressx create-features` - Creates the features page
- `wp pressx create-resources` - Creates the resources page
- `wp pressx create-landing` - Creates the landing page
- `wp pressx create-ai-landing` - Creates the AI landing page
- `wp pressx create-get-started` - Creates the get started page
- `wp pressx create-articles` - Creates the articles page
- `wp pressx create-contact` - Creates the contact page
- `wp pressx create-menu` - Creates the main menu
- `wp pressx create-footer-menu` - Creates the footer menu

## Options

All commands support the following options:

- `--force` - Force recreation of content even if it already exists

## Examples

```bash
# Create pages
wp pressx create-pages

# Force recreation of pages
wp pressx create-pages --force

# Create all content
wp pressx create-pages && wp pressx create-home && wp pressx create-pricing && wp pressx create-features && wp pressx create-resources && wp pressx create-landing && wp pressx create-ai-landing && wp pressx create-get-started && wp pressx create-articles && wp pressx create-contact && wp pressx create-menu && wp pressx create-footer-menu
```

## Migration Process

The migration process involved:

1. Creating a WP-CLI command class in `includes/cli/wp-cli.php`
2. Creating a directory structure for command files in `includes/cli/commands/`
3. Moving and adapting each script to be a function that can be called by the WP-CLI command
4. Moving shared includes to `includes/cli/includes/`
5. Moving images to `includes/cli/images/`
6. Updating paths in the scripts to use the new directory structure
7. Adding WP-CLI command registration to `pressx-core.php`

## Next Steps

To complete the migration:

1. Test each command to ensure it works as expected
2. Remove the original scripts from the `scripts/` directory once all commands are working
3. Update any documentation or references to the scripts to use the new WP-CLI commands
