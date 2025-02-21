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

   # Install Next.js dependencies
   cd nextjs
   npm install
   ```

## 🏗️ Project Structure

```
pressx/
├── public_html/              # WordPress installation
│   └── wp-content/
│       └── plugins/
│           └── pressx-core/  # Custom plugin for headless functionality
├── nextjs/                   # Next.js frontend application
├── vendor/                   # Composer dependencies
└── .ddev/                    # DDEV configuration
```

## 🔧 Development

### WordPress Backend

- **Local URL**: https://pressx.ddev.site
- **Admin URL**: https://pressx.ddev.site/wp-admin

The WordPress backend uses several key components:
- Carbon Fields for custom fields
- WPGraphQL for the GraphQL API
- Custom post types and fields defined in the pressx-core plugin

### Next.js Frontend

- **Development**: `cd nextjs && npm run dev`
- **Build**: `cd nextjs && npm run build`
- **Start**: `cd nextjs && npm start`

## 🛠️ Key Features

1. **Headless WordPress**
   - Decoupled architecture
   - GraphQL API using WPGraphQL
   - Custom post types and fields

2. **Modern Frontend**
   - Next.js for server-side rendering
   - TypeScript support
   - Component-based architecture

3. **Development Environment**
   - DDEV for containerization
   - Automated setup process
   - Consistent development environment

## 📦 Dependencies

### WordPress (Composer)
- `htmlburger/carbon-fields`: Custom fields framework
- `wp-graphql`: GraphQL API
- Other WordPress plugins managed via Composer

### Frontend (npm)
- Next.js
- TypeScript
- Other frontend dependencies managed via npm

## 🔄 Common Workflows

### Adding Custom Fields
1. Define fields in `pressx-core/includes/carbon-fields/`
2. Update GraphQL schema if needed
3. Update TypeScript types in the frontend

### Adding New Post Types
1. Register post type in `pressx-core/includes/post-types/`
2. Add custom fields if needed
3. Update GraphQL schema
4. Create corresponding frontend components

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
