# ResolveHub - Twig/PHP Version

A modern, responsive ticket management system built with Twig templating and PHP.

## 🎨 Theme: Midnight Blue & Gold

- Primary: Deep Blue (#1e40af)
- Secondary: Bright Blue (#3b82f6)
- Accent: Gold (#fbbf24)
- Dark Background: Midnight (#0c1222)

## ✨ Features

✅ Landing page with wavy SVG background and decorative circles
✅ Authentication (Login/Signup) with PHP sessions
✅ Protected dashboard with ticket statistics
✅ Full CRUD ticket management
✅ Form validation (client & server-side)
✅ Session-based data persistence
✅ Responsive design (max-width: 1440px)
✅ Status colors: Green (open), Gold (in progress), Gray (closed)
✅ Toast notifications

## 📋 Prerequisites

- PHP 7.4 or higher
- Composer

## 🚀 Installation

1. Clone/download this folder
2. Navigate to the project directory:
```bash
   cd ticketflow-twig
```
3. Install dependencies:
```bash
   composer install
```

## 🏃 Running the App

Start the PHP built-in server:
```bash
cd public
php -S localhost:8000
```

Or from the root directory:
```bash
php -S localhost:8000 -t public
```

The app will open at [http://localhost:8000](http://localhost:8000)

## 📁 Project Structure
```
ticketflow-twig/
├── public/
│   ├── index.php          # Main router
│   ├── css/
│   │   └── style.css      # All styles
│   └── js/
│       └── app.js         # JavaScript logic
├── views/
│   ├── layout.twig        # Base layout
│   ├── landing.twig       # Home page
│   ├── auth.twig          # Login/Signup
│   ├── dashboard.twig     # Dashboard
│   └── tickets.twig       # Ticket management
├── src/
│   ├── Database.php       # Session-based storage
│   └── Router.php         # Simple router
├── vendor/                # Composer dependencies
├── composer.json
└── README.md
```

## 🎯 Features Breakdown

### Landing Page
- Hero section with wavy SVG
- Decorative circles
- Feature cards
- CTA sections
- Footer

### Authentication
- Login/Signup forms
- Server-side validation
- PHP session management
- Toast notifications

### Dashboard
- Total tickets stat
- Open tickets stat
- In Progress stat
- Resolved tickets stat
- Quick action button

### Ticket Management (CRUD)
- Create new tickets
- View all tickets in card layout
- Edit existing tickets
- Delete with confirmation
- Real-time validation
- Status: open, in_progress, closed
- Priority: low, medium, high

## 🔒 Data Storage

This version uses **PHP sessions** to store:
- User credentials (hashed passwords)
- Authentication tokens
- Ticket data

Data persists during the session but is cleared when:
- Session expires
- User logs out
- Browser is closed (depending on session config)

For production, replace with a real database (MySQL, PostgreSQL, etc.)

## 🌐 Routes

- `/` - Landing page
- `/login` - Login page
- `/signup` - Signup page
- `/dashboard` - Dashboard (protected)
- `/tickets` - Ticket management (protected)
- `/logout` - Logout

## 🛠️ Technologies Used

- PHP 7.4+
- Twig 3.x (Templating)
- Pure CSS (No frameworks)
- Vanilla JavaScript
- PHP Sessions for data storage

## 🎨 Design Consistency

Same layout as React and Vue versions:
- Max-width: 1440px
- Wavy SVG hero
- Decorative circles
- Card-style boxes
- Status colors
- Fully responsive

## 🚀 Deployment

### Option 1: Any PHP Hosting
Upload all files to your hosting and configure the web root to `/public`

### Option 2: Heroku
Add `Procfile`:
```
web: vendor/bin/heroku-php-apache2 public/
```

Deploy:
```bash
git init
git add .
git commit -m "Initial commit"
heroku create
git push heroku main
```

### Option 3: VPS (Ubuntu)
```bash
# Install PHP and Composer
sudo apt update
sudo apt install php php-cli composer

# Clone and setup
git clone your-repo
cd ticketflow-twig
composer install

# Run
php -S 0.0.0.0:8000 -t public
```

## 📝 Notes

- Sessions are stored server-side
- Default session timeout is 24 minutes (configurable in php.ini)
- For production, use HTTPS and secure session cookies
- Consider using a real database for persistent storage

## 🐛 Troubleshooting

**Page Not Found:**
- Make sure you're in the `public/` directory when running PHP server
- Or use: `php -S localhost:8000 -t public` from root

**Composer not found:**
```bash
# Install Composer globally
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
sudo mv composer.phar /usr/local/bin/composer
```

**Session issues:**
- Check that session.save_path is writable
- Verify PHP session extension is enabled

## 📄 License

MIT License - Built for Frontend Wizards Stage 2

## 👨‍💻 Author

Your Name - Frontend Wizards Cohort