# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **PHP portfolio/resume website** with SQLite database integration, designed to be a public-facing application. It features dynamic content management, admin panel, secure authentication, multiple themes, print functionality, and PDF export using wkhtmltopdf.

**⚠️ CRITICAL: This is a PUBLIC-FACING application. Security is mandatory - never compromise on security practices.**

## Git Commit Guidelines

**IMPORTANT: Do NOT add "Co-Authored-By: Claude" or any similar co-author attribution to git commits.** All commits should be attributed solely to the developer making the commit.

## Common Development Commands

### Docker Development (Recommended)
```bash
# Build and run the application
./build.sh build && ./build.sh run

# Development mode with live reload
./build.sh dev

# View logs
./build.sh logs

# Stop application
./build.sh stop

# Run tests in Docker
./build.sh test

# Access container shell
./build.sh shell

# Show all commands
./build.sh help
```

### Traditional Development (Without Docker)
```bash
# Start PHP development server
php -S localhost:8000

# Initialize database (first time)
# Navigate to http://localhost:8000/init_db.php in browser

# Run PHP syntax check
find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \;

# Lint CSS
npx stylelint "css/**/*.css"

# Lint JavaScript
npx eslint js/
```

### CI/CD Pipeline Testing
The GitHub Actions workflow (`.github/workflows/php-workflow.yml`) runs:
- PHP syntax and code quality checks
- CSS/JS linting
- Integration tests with security testing
- Docker build and run tests
- Build artifact creation

## High-Level Architecture

### Core Components

**1. Database Layer (`includes/db_connect.php`)**
- SQLite database stored in `data/resume.db`
- Auto-initializes schema on first connection
- Tables: `profile`, `experience`, `education`, `skills`, `achievements`, `certificates_conferences`, `projects`, `admin_settings`
- Uses PDO with prepared statements for all queries

**2. Admin Authentication System (`admin.php`)**
- Session-based authentication with 30-minute timeout
- Password hashing using bcrypt (password_hash/password_verify)
- CSRF protection with per-session tokens
- Auto-migration from plain text to hashed passwords
- Comprehensive logging of login attempts
- Protected routes require valid session

**3. Theme System (`includes/header.php` + `css/theme-*.css`)**
- 13 built-in themes: light, dark, blue, green, peach, neon, minimal, watercolor, vscode, github, retro, terminal, ubuntu, matrix
- Theme stored in `admin_settings.theme` column
- CSS custom properties (variables) for theme colors
- Live preview in admin panel
- Whitelist validation prevents theme injection

**4. Content Sections (in `includes/`)**
- `profile.php` - Personal information
- `experience.php` - Work history
- `education.php` - Educational background
- `skills.php` - Technical skills with progress bars
- `achievements.php` - Notable achievements
- `projects.php` - Project showcase

**5. PDF Export (`generate-pdf-wk.php`)**
- Uses wkhtmltopdf for HTML-to-PDF conversion
- Applies print.css for optimized layout
- Requires wkhtmltopdf installed on server

### File Structure
```
/
├── index.php              # Main page entry point
├── admin.php              # Admin panel (login + CRUD operations)
├── init_db.php            # Database initialization
├── reset_db.php           # Database reset (admin protected)
├── update_db.php          # Database schema updates
├── check_theme.php        # Theme verification
├── generate-pdf-wk.php    # PDF generation
├── migrate_passwords.php  # Password migration utility
├── includes/
│   ├── db_connect.php     # Database connection + schema
│   ├── populate_db.php    # Default data population
│   ├── header.php         # HTML head + theme loading
│   ├── footer.php         # HTML footer
│   ├── profile.php        # Profile section
│   ├── experience.php     # Experience section
│   ├── education.php      # Education section
│   ├── skills.php         # Skills section
│   ├── achievements.php   # Achievements section
│   └── projects.php       # Projects section
├── css/
│   ├── style.css          # Base styles
│   ├── print.css          # Print-specific styles
│   └── theme-*.css        # Theme files (13 themes)
├── js/
│   └── main.js            # JavaScript functionality
├── assets/
│   └── images/            # Images and assets
├── data/
│   └── resume.db          # SQLite database (auto-created)
├── Dockerfile             # Docker image definition
├── docker-compose.yml     # Docker Compose configuration
├── build.sh               # Docker management script
└── .github/workflows/php-workflow.yml  # CI/CD pipeline
```

## Security Architecture

### Authentication & Authorization
- **Session Management**: 30-minute timeout, session regeneration on login
- **Password Security**: bcrypt hashing with minimum 8 characters
- **CSRF Protection**: Per-session tokens validated on all state-changing operations
- **Access Control**: All admin operations require `$_SESSION['admin_logged_in'] === true`

### Database Security
- **SQL Injection Prevention**: ALL queries use PDO prepared statements with parameter binding
- **Input Validation**: `filter_var()` + `htmlspecialchars()` for all user inputs
- **Output Encoding**: `htmlspecialchars()` on all user-controlled output
- **Database File Protection**: Stored in `data/` directory with proper permissions

### Admin Operations (All Require Authentication)
- `admin.php` - Main admin panel (login, CRUD operations, theme selection)
- `reset_db.php` - Database reset (protected)
- `check_theme.php` - Theme verification (protected)
- `migrate_passwords.php` - Password migration (protected)
- `init_db.php` - Protected after initial database creation

### Security Headers & Validation
- Theme parameter whitelist validation
- Path traversal prevention in file operations
- SQL injection testing in CI/CD
- XSS prevention testing in CI/CD
- Information disclosure prevention (no system paths in errors)

## Key Development Patterns

### Database Operations (MUST USE PREPARED STATEMENTS)
```php
$pdo = getDbConnection();
$stmt = $pdo->prepare("SELECT * FROM profile WHERE id = ?");
$stmt->execute([$id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
```

### Admin Route Protection
```php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    die('Access denied');
}
```

### CSRF Token Validation
```php
if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    http_response_code(403);
    die('Security validation failed');
}
```

### Input Validation & Sanitization
```php
$theme = htmlspecialchars(trim($_POST['theme'] ?? ''), ENT_QUOTES, 'UTF-8');
if (!in_array($theme, ['light', 'dark', 'blue', ...])) {
    die('Invalid theme');
}
```

### Theme Integration
- All new CSS must use CSS custom properties from `:root`
- Test with ALL 13 themes before committing
- Follow existing Bootstrap 5 patterns

## Testing Requirements

### Before Any Commit
1. **Security Testing** (MANDATORY for public-facing app)
   - Verify all admin operations require authentication
   - Test SQL injection prevention on all inputs
   - Test XSS prevention on all user outputs
   - Verify CSRF tokens in all admin forms
   - Test session timeout and logout
   - Verify password hashing (bcrypt)

2. **Functionality Testing**
   - Admin login/logout
   - All CRUD operations (Create, Read, Update, Delete)
   - Theme switching (all 13 themes)
   - Print functionality
   - PDF export (if wkhtmltopdf available)
   - Responsive design on mobile/tablet

3. **Code Quality**
   - PHP syntax check: `php -l <file>`
   - PSR-12 coding standards
   - CSS/JS linting

### CI/CD Pipeline
The workflow automatically runs:
- PHP syntax and code quality checks
- CSS/JS linting
- Integration tests with comprehensive security testing
- Docker build and container tests
- Build artifact creation (on main branch)

## Theme System Details

### Adding a New Theme
1. Create `css/theme-newname.css` with CSS variables
2. Add to whitelist in `admin.php` lines 99 and 111
3. Add to header.php theme loading logic
4. Add to admin panel dropdown
5. Test with ALL existing themes

### Theme CSS Variables
```css
:root {
    --primary-color: #value;
    --secondary-color: #value;
    --accent-color: #value;
    --text-color: #value;
    --light-bg: #value;
    /* Additional variables */
}
```

## Database Schema

### Key Tables
- **profile**: Personal info (name, email, phone, links)
- **experience**: Work history with dates and descriptions
- **education**: Academic background
- **skills**: Skills with categories and proficiency levels
- **achievements**: Certifications and awards
- **projects**: Portfolio projects with tech stack
- **admin_settings**: Username, hashed password, theme preference

### Schema Updates
- Run `update_db.php` to add missing columns
- `db_connect.php` auto-creates tables on first run
- `populate_db.php` adds default data

## Deployment

### Docker (Recommended)
```bash
./build.sh build && ./build.sh run
# Access: http://localhost:8080
```

### Traditional
1. Upload files to PHP-enabled web server
2. Ensure `data/` directory is writable (755)
3. Access `init_db.php` to initialize database
4. Access `admin.php` with default credentials (admin/admin123)
5. **Immediately change default password**

### Production Security Checklist
- [ ] Change default admin password
- [ ] Set up HTTPS
- [ ] Restrict database file access via .htaccess
- [ ] Consider IP restrictions for admin panel
- [ ] Enable rate limiting for login attempts
- [ ] Set up proper logging and monitoring

## Common Issues & Solutions

### Database Not Created
- Ensure `data/` directory exists and is writable
- Check file permissions (755 for directory, 644 for files)

### Theme Not Applying
- Run `update_db.php` to add theme column
- Run `check_theme.php` to verify theme values
- Check that theme CSS file exists

### PDF Export Not Working
- Install wkhtmltopdf: `apt-get install wkhtmltopdf`
- Verify installation: `wkhtmltopdf --version`

### Docker Issues
- Port 8080 already in use: Change in `docker-compose.yml`
- Permission denied: `chmod +x build.sh`
- Container won't start: `./build.sh logs` to debug

## Security Red Flags (NEVER DO THESE)
- ❌ Direct SQL queries without prepared statements
- ❌ Unsanitized user input in database or output
- ❌ Missing authentication on admin operations
- ❌ Exposing error messages with system paths
- ❌ Hardcoded credentials in code
- ❌ Plain text password storage
- ❌ Missing CSRF protection
- ❌ No session timeout

## Important Files to Read
- `.github/copilot-instructions.md` - Detailed security and coding guidelines
- `DOCKER.md` - Comprehensive Docker deployment guide
- `DATABASE_README.md` - Database structure and management
- `CICD.md` - CI/CD pipeline documentation
- `README.md` - General project overview and features

## Quick Reference Commands

```bash
# Development
php -S localhost:8000

# Docker
./build.sh build && ./build.sh run
./build.sh dev
./build.sh logs

# Testing
php -l <file>
find . -name "*.php" -exec php -l {} \;
npx stylelint "css/**/*.css"

# Database
sqlite3 data/resume.db ".tables"
sqlite3 data/resume.db "SELECT * FROM admin_settings;"
```

## Remember
- **This is a PUBLIC-FACING application** - security is not optional
- **Always use prepared statements** - no exceptions
- **Always validate and sanitize** - all user inputs
- **Always check authentication** - before any admin operation
- **Always test thoroughly** - before deploying changes
