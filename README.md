# PressX - Headless WordPress Setup

PressX is a modern headless WordPress setup that combines the power of WordPress as a backend CMS with Next.js for the frontend, all containerized using DDEV for consistent development environments.

## 🚀 Quick Start

1. **Prerequisites**
   - [DDEV](https://ddev.readthedocs.io/en/stable/)
   - [Docker](https://www.docker.com/)
   - [Composer](https://getcomposer.org/)
   - [Node.js](https://nodejs.org/) (for Next.js frontend)

2. **Installation**
   ```bash
   # Clone the repository
   git clone [your-repo-url]
   cd pressx

   # Start DDEV
   ddev start

   # Install WordPress and dependencies
   ddev install

   # Or install with preview mode enabled
   ddev install --preview

   # Install Next.js dependencies
   cd nextjs
   npm install
   ```

   The `ddev install` command will:
   - Set up the WordPress installation
   - Install and activate required plugins (pressx-core, classic-editor, wp-graphql)
   - Configure permalink structure
   - Create sample content (landing pages, blog posts)
   - Set up navigation menus
   - Generate admin credentials

   Using the `--preview` flag will additionally:
   - Configure the Next.js environment for preview mode
   - Set up the necessary environment variables
   - Enable real-time content previewing from WordPress to Next.js

3. **Running the Application**
   ```bash
   # Start WordPress backend
   ddev start

   # Start Next.js frontend (in a separate terminal)
   cd nextjs
   npm run dev

   # Or use the DDEV command if you installed with preview mode
   ddev nextjs
   ```

## 🏗️ Project Structure

```
pressx/
├── public_html/              # WordPress installation
│   └── wp-content/
│       └── plugins/
│           └── pressx-core/  # Custom plugin for headless functionality
├── nextjs/                   # Next.js frontend application
│   └── src/
│       ├── components/       # Reusable UI components
│       │   └── sections/     # Page section components
│       ├── pages/            # Next.js pages
│       └── lib/              # Utility functions and API clients
├── drupal-config/            # Drupal configuration reference files
├── scripts/                  # Helper scripts for content creation
├── vendor/                   # Composer dependencies
└── .ddev/                    # DDEV configuration
```

## 🔧 Development

### WordPress Backend

- **Local URL**: https://pressx.ddev.site
- **Admin URL**: https://pressx.ddev.site/wp-admin
- **GraphQL Endpoint**: https://pressx.ddev.site/graphql

The WordPress backend uses several key components:
- Carbon Fields for custom fields
- WPGraphQL for the GraphQL API
- Custom post types and fields defined in the pressx-core plugin
- Classic Editor for content management

### Next.js Frontend

- **Development URL**: http://pressx.ddev.site:3333 or http://localhost:3333
- **Development**: `cd nextjs && npm run dev`
- **Build**: `cd nextjs && npm run build`
- **Start**: `cd nextjs && npm start`

### Preview Mode

PressX supports a preview mode that allows you to see content changes in real-time before publishing:

1. **Enabling Preview Mode**:
   - Install with preview mode: `ddev install --preview`
   - Or toggle it on/off anytime: `ddev toggle-preview on` or `ddev toggle-preview off`
   - Or manually configure by setting `NEXT_PUBLIC_PREVIEW_MODE=true` in `nextjs/.env.local`

2. **Using Preview Mode**:
   - Edit content in WordPress
   - Click the "Preview" button to see changes in the Next.js frontend
   - Preview URLs follow the pattern: `http://localhost:3333/preview/[id]`

3. **Admin Bar**:
   - When in preview mode, an admin bar appears at the top of all pages
   - Shows the current page type and post ID for easy reference
   - Provides quick links to edit the current content in WordPress
   - Includes a home button for easy navigation
   - Can be hidden/shown with a toggle button
   - Responsive design works on all device sizes
   - Automatically adjusts the page layout to prevent overlap with content

4. **Preview Mode Environment Variables**:
   ```
   NEXT_PUBLIC_PREVIEW_MODE=true
   WORDPRESS_PREVIEW_SECRET=pressx_preview_secret
   WORDPRESS_PREVIEW_USERNAME=admin
   WORDPRESS_PREVIEW_PASSWORD=your_password_here
   ```

   > **IMPORTANT**: You must set `WORDPRESS_PREVIEW_USERNAME` and `WORDPRESS_PREVIEW_PASSWORD` to valid WordPress credentials with appropriate permissions. These credentials are used to authenticate with the WordPress GraphQL API to access unpublished content. Never commit these credentials to version control.

5. **JWT Authentication**:
   - The preview mode uses JWT authentication to securely access unpublished content
   - JWT tokens are automatically retrieved and stored in cookies
   - The GraphQL client uses these tokens when making requests to the WordPress API
   - For security, tokens expire after a short period and are automatically refreshed

## 🛠️ Key Features

1. **Headless WordPress**
   - Decoupled architecture
   - GraphQL API using WPGraphQL
   - Custom post types and fields

2. **Modern Frontend**
   - Next.js for server-side rendering
   - TypeScript support
   - Component-based architecture
   - Tailwind CSS for styling

3. **Development Environment**
   - DDEV for containerization
   - Automated setup process
   - Consistent development environment

4. **Content Sections**
   - Flexible page builder with multiple section types
   - Component-based design for easy customization
   - Responsive layouts for all devices

5. **Sample Content**
   - Pre-configured landing pages (Home, Features, Pricing, Resources, Get Started, Contact)
   - Sample blog posts
   - Navigation menus (Primary and Footer)

6. **Preview Mode**
   - Real-time content previewing
   - Secure preview links
   - Seamless WordPress to Next.js integration

## 📦 Available Section Types

PressX includes a variety of section types for building flexible landing pages:

1. **Hero** - Feature prominent content with various layout options
2. **Text** - Simple text sections with optional links
3. **Accordion** - Expandable content sections for FAQs or detailed information
4. **Card Group** - Display content in card format with various card types
5. **Carousel** - Scrollable content with images and text
6. **Embed** - Embed external content with support for rich media scripts
7. **Gallery** - Display multiple images in a grid layout
8. **Logo Collection** - Showcase partner or client logos
9. **Media** - Display images or videos with optional captions
10. **Newsletter** - Email signup form
11. **Pricing** - Display pricing options in a structured format
12. **Quote** - Highlight testimonials or important quotes
13. **Side by Side** - Two-column layout with text and media
14. **Recent Posts** - Display recent blog posts

## 🔄 Working with Section Types

### Embed Section

The Embed section allows you to include external content like videos, maps, or other third-party widgets. Recent updates have improved this section to support full HTML/script embeds rather than just URLs.

**Key Features:**
- Support for rich script embeds (iframes, JavaScript widgets, etc.)
- Optional title and caption
- Configurable maximum width
- Responsive design

**Example Usage:**
```php
[
  '_type' => 'embed',
  'title' => 'Watch Our Tutorial',
  'script' => '<iframe src="https://www.youtube.com/embed/VIDEO_ID" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="width:100%; aspect-ratio:16/9;"></iframe>',
  'caption' => 'Learn how to get started with PressX in this quick tutorial.',
  'max_width' => '800px',
]
```

## 📦 Dependencies

### WordPress (Composer)
- `htmlburger/carbon-fields`: Custom fields framework
- `wp-graphql`: GraphQL API for WordPress
- `classic-editor`: Traditional WordPress editing experience
- Other WordPress plugins managed via Composer

### Frontend (npm)
- Next.js
- TypeScript
- Tailwind CSS
- Shadcn UI components
- Other frontend dependencies managed via npm

## 🔄 Common Workflows

### Adding Custom Fields
1. Define fields in `pressx-core/pressx-core.php`
2. Update GraphQL schema if needed
3. Update TypeScript types in the frontend

### Adding New Post Types
1. Register post type in `pressx-core/pressx-core.php`
2. Add custom fields if needed
3. Update GraphQL schema
4. Create corresponding frontend components

### Creating Content
1. Use the WordPress admin interface to create content
2. Alternatively, use the helper scripts in the `scripts/` directory:
   ```bash
   ddev exec php scripts/create-landing.php
   ddev exec php scripts/create-contact.php
   ddev exec php scripts/create-get-started.php
   ```

## 🚨 Troubleshooting

### Common Issues

1. **Carbon Fields UI Not Loading**
   - Ensure Carbon Fields assets are properly copied to the plugin directory
   - Check browser console for JavaScript errors
   - Verify WordPress admin scripts are loading

2. **GraphQL Errors**
   - Verify WPGraphQL plugin is activated
   - Check field registration in the schema
   - Validate query structure
   - Test queries in the GraphQL IDE at https://pressx.ddev.site/graphql

3. **Next.js Development Server Issues**
   - Ensure Node.js version is compatible
   - Check for TypeScript errors
   - Verify GraphQL queries match the schema

4. **Preview Mode Authentication Issues**
   - If you're having trouble with preview mode authentication:
     - Run `ddev toggle-preview on` to reset the admin password and update environment variables
     - Check that the WPGraphQL JWT Authentication plugin is active
     - Verify that your `.env.local` file contains the correct credentials
     - Clear your browser cookies and try again
   - The `toggle-preview` script automatically:
     - Sets a secure password for the admin user
     - Updates the `.env.local` file with the correct credentials
     - Activates the necessary plugins for JWT authentication

5. **DDEV Configuration Issues**
   - Run `ddev describe` to check the current configuration
   - Ensure ports 80, 443, and 3333 are available
   - Check DDEV logs with `ddev logs`

## 📝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## 📄 License

PressX is dual-licensed:

- WordPress-specific code (plugins, themes, PHP files) is licensed under [GPL-2.0](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html) or later
- Next.js frontend code is licensed under the [MIT License](https://opensource.org/licenses/MIT)

See the [LICENSE](LICENSE) file for details.
