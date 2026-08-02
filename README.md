# Csárda – Multilingual Restaurant Web Application

**Author:** Árpád Perna
**GitHub:** Arpi47

A full-stack multilingual restaurant web application built with **Laravel** and **React**. The project provides a modern, responsive website for restaurant guests and a separate administration panel for managing the website's content, users, reservations, and other system data.

The application is designed to work on **desktop computers, laptops, tablets, and mobile devices**, with a responsive interface adapted to different screen sizes and input methods.

---

## Table of Contents

- [Overview](#overview)
- [Main Features](#main-features)
- [User Features](#user-features)
- [Admin Features](#admin-features)
- [Technology Stack](#technology-stack)
- [Project Architecture](#project-architecture)
- [Project Structure](#project-structure)
- [Requirements](#requirements)
- [Installation](#installation)

    - [1. Clone the Repository](#1-clone-the-repository)
    - [2. Configure the Laravel Backend](#2-configure-the-laravel-backend)
    - [3. Configure the Database](#3-configure-the-database)
    - [4. Install Backend Dependencies](#4-install-backend-dependencies)
    - [5. Configure the React Frontend](#5-configure-the-react-frontend)
    - [6. Install Frontend Dependencies](#6-install-frontend-dependencies)
    - [7. Build or Start the Frontend](#7-build-or-start-the-frontend)
    - [8. Start the Laravel Backend](#8-start-the-laravel-backend)

- [Environment Configuration](#environment-configuration)
- [Important Installation Notes](#important-installation-notes)
- [Desktop and Mobile Experience](#desktop-and-mobile-experience)
- [Security](#security)
- [Development Workflow](#development-workflow)
- [Production Deployment](#production-deployment)
- [Future Improvements](#future-improvements)
- [License](#license)

---

## Overview

Csárda is a full-stack restaurant web application consisting of two main parts:

1. **Public React frontend** – the website used by restaurant guests.
2. **Laravel administration panel** – a protected backend used by administrators to manage the system.

The frontend and backend communicate through APIs. The Laravel backend is responsible for business logic, authentication, validation, database access, and API endpoints, while the React frontend provides the modern user-facing interface.

The application uses a database-driven architecture, meaning that important content such as menu items, categories, gallery images, reservations, and user data can be managed dynamically instead of being permanently hard-coded into the website.

The project supports multiple languages:

- English
- Hungarian
- Serbian Latin
- Serbian Cyrillic

The application is responsive and adapts its layout and interaction elements according to the user's device and screen size.

---

# Main Features

- Multilingual user interface
- English, Hungarian, Serbian Latin, and Serbian Cyrillic support
- Responsive desktop and mobile design
- Light and dark theme support
- User registration and authentication
- User profile management
- Profile image upload
- Account deletion request and cancellation
- Dynamic restaurant menu
- Menu categories
- Image gallery
- Gallery image preview and modal view
- Online table reservations
- Reservation validation and business rules
- Google reCAPTCHA v3 integration
- Protected administration panel
- User and administrator management
- Reservation management
- Menu and category management
- Gallery management
- Contact and website settings management
- Admin activity logging
- Role and permission-based administrative functionality
- Automatic adaptation to different screen sizes
- Animated page transitions and visual effects

---

# User Features

The public website is designed for restaurant guests and visitors.

Users can:

- Browse the restaurant website
- View restaurant information
- Browse the dynamically loaded menu
- View menu categories and items
- Open and browse the image gallery
- View gallery images in a larger format
- Select their preferred language
- Switch between light and dark themes
- Register a user account
- Log in securely
- Manage their personal profile
- Request account deletion
- Cancel an account deletion request when permitted
- Make online table reservations
- Receive validation feedback when reservation data is invalid
- Use the website from desktop and mobile devices

Reservation requests are validated both on the frontend and backend. The backend also applies business rules such as minimum reservation dates, opening hours, guest limits, and blocked disposable email domains.

---

# Admin Features

The administration panel is a protected part of the Laravel application and is intended for authorized administrators.

Depending on the administrator's role and permissions, the admin panel can be used to manage:

### Users

- View registered users
- Search and filter users
- Monitor account status
- Suspend or manage user accounts
- View authentication-related information

### Administrators

- Invite and manage administrator accounts
- Manage administrator profiles
- Upload administrator profile images
- Apply role-based access restrictions
- Protect sensitive actions using administrator permissions
- Support super administrator functionality

### Reservations

- View incoming reservations
- Review reservation details
- Manage reservation statuses
- Handle reservation-related administrative tasks

### Menu

- Create menu items
- Edit menu items
- Delete menu items
- Manage menu categories
- Manage multilingual menu content
- Manage menu prices and images

### Gallery

- Add gallery images
- Edit gallery content
- Remove gallery images
- Manage the content displayed on the public website

### Website and Contact Settings

- Manage configurable contact information
- Manage website-related settings
- Keep frequently changing information outside the frontend source code

### Activity Logging

Administrative actions can be recorded using an activity logging system, making it easier to monitor important actions performed inside the administration panel.

---

# Technology Stack

## Backend

- **PHP 8.5.7**
- **Laravel 12.44.0**
- Laravel Sanctum
- REST API
- Eloquent ORM
- Laravel Migrations
- Laravel Blade
- MySQL / MariaDB

## Frontend

- React
- Vite
- JavaScript
- Tailwind CSS
- Framer Motion
- React Router
- Lucide React

## Security and Protection

- Laravel authentication
- Laravel Sanctum
- CSRF protection
- Server-side validation
- Google reCAPTCHA v3
- Role and permission checks
- Protected administrative routes
- Disposable email domain blocking
- Administrative activity logging

## Development Tools

- Git
- GitHub
- Composer
- npm
- Node.js
- VS Code
- Local development server

---

# Project Architecture

The application follows a separated frontend/backend architecture.

```text
                    ┌─────────────────────────┐
                    │       User Device       │
                    │ Desktop / Mobile / Web  │
                    └────────────┬────────────┘
                                 │
                                 │
                                 ▼
                    ┌─────────────────────────┐
                    │      React Frontend     │
                    │        Vite + JS        │
                    │                         │
                    │  Public Website / UI    │
                    └────────────┬────────────┘
                                 │
                            REST API
                                 │
                                 ▼
                    ┌─────────────────────────┐
                    │    Laravel Backend      │
                    │                         │
                    │ Business Logic          │
                    │ Authentication          │
                    │ Validation              │
                    │ API Endpoints           │
                    │ Admin Panel             │
                    └────────────┬────────────┘
                                 │
                                 ▼
                    ┌─────────────────────────┐
                    │       Database          │
                    │     MySQL / MariaDB     │
                    └─────────────────────────┘
```

The **React frontend** handles the public-facing user experience.

The **Laravel backend** handles:

- Business logic
- Authentication
- Authorization
- API requests
- Validation
- Database operations
- Reservation processing
- Administrative functionality

The **Laravel Blade administration panel** provides a protected interface for managing the application.

---

# Project Structure

The project is divided into a Laravel backend and a React frontend.

```text
csarda/
│
├── app/
│   ├── Console/
│   ├── Helpers/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   ├── Models/
│   └── ...
│
├── bootstrap/
│
├── config/
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
│
├── public/
│   ├── admins/
│   ├── gallery/
│   └── ...
│
├── resources/
│   └── views/
│       └── admin/
│
├── routes/
│   ├── api.php
│   └── web.php
│
├── frontend/
│   ├── src/
│   │   ├── api/
│   │   ├── components/
│   │   ├── contexts/
│   │   ├── layouts/
│   │   ├── pages/
│   │   └── ...
│   ├── public/
│   ├── package.json
│   └── vite.config.js
│
├── .env.example
├── composer.json
├── package.json
└── README.md
```

The exact structure may evolve as new features are added.

---

# Requirements

Before installing the project, make sure the following software is available:

- PHP 8.5 or compatible supported version
- Composer
- Node.js
- npm
- MySQL or MariaDB
- Git
- A web server or Laravel development server

For local development, the project can be run using Laravel's development server and Vite's development server.

The project can also be hosted on a production web server with PHP and a supported database.

---

# Installation

## 1. Clone the Repository

Clone the repository from GitHub:

```bash
git clone https://github.com/Arpi47/csarda.git
```

Enter the project directory:

```bash
cd csarda
```

If the repository uses a different name or URL, replace the command with the correct repository address.

---

## 2. Configure the Laravel Backend

Install the PHP dependencies:

```bash
composer install
```

Create the environment configuration file:

```bash
cp .env.example .env
```

Generate the Laravel application key:

```bash
php artisan key:generate
```

The `.env` file contains environment-specific configuration and must not be committed to GitHub.

---

## 3. Configure the Database

Create an empty MySQL or MariaDB database for the project.

Then update the database configuration in `.env`.

Example:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csarda
DB_USERNAME=root
DB_PASSWORD=
```

The exact values depend on the local or production database configuration.

Run the database migrations:

```bash
php artisan migrate
```

If the project contains seeders and sample data, they can be executed with:

```bash
php artisan db:seed
```

or:

```bash
php artisan migrate --seed
```

**Important:** Running migrations with destructive options such as `migrate:fresh` deletes existing database tables and data. This should only be used when intentionally resetting a development database.

---

## 4. Install Backend Dependencies

If the project has already been cloned, Laravel dependencies should be installed with:

```bash
composer install
```

For development environments, the following may also be useful:

```bash
php artisan storage:link
```

This creates the symbolic link required for files stored through Laravel's public storage system to be accessible from the browser.

---

## 5. Configure the React Frontend

Move into the frontend directory:

```bash
cd frontend
```

Install the JavaScript dependencies:

```bash
npm install
```

Create the frontend environment configuration if required:

```bash
cp .env.example .env
```

The frontend environment variables must point to the correct Laravel backend/API address.

For example:

```env
VITE_API_URL=http://127.0.0.1:8000
```

The exact variable names depend on the current frontend configuration.

After configuring the frontend, return to the project root:

```bash
cd ..
```

---

## 6. Install Frontend Dependencies

If the frontend dependencies have not yet been installed:

```bash
cd frontend
npm install
```

The main frontend dependencies include React, Vite, Tailwind CSS, React Router, Framer Motion, and Lucide React.

---

## 7. Build or Start the Frontend

For local development:

```bash
npm run dev
```

This starts the Vite development server.

The frontend is normally available at:

```text
http://localhost:5173
```

For a production build:

```bash
npm run build
```

The production build should then be deployed according to the hosting environment and the project's current deployment configuration.

---

## 8. Start the Laravel Backend

From the Laravel project root:

```bash
php artisan serve
```

The Laravel development server is normally available at:

```text
http://127.0.0.1:8000
```

During local development, the React frontend and Laravel backend therefore run as separate services:

```text
React / Vite
http://localhost:5173

        │
        │ API requests
        ▼

Laravel
http://127.0.0.1:8000
```

Both services must be running for the complete application to work correctly in development.

---

# Environment Configuration

The `.env` file is one of the most important parts of the installation.

It typically contains configuration for:

- Application URL
- Application environment
- Application key
- Database connection
- Frontend/backend URLs
- Session configuration
- Sanctum configuration
- CORS configuration
- Google reCAPTCHA
- Mail services
- File storage
- Other environment-specific settings

A typical Laravel configuration may contain values similar to:

```env
APP_NAME=Csarda
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
```

Database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csarda
DB_USERNAME=root
DB_PASSWORD=
```

The exact configuration must match the local environment.

**Never commit `.env` files or private API keys, passwords, application secrets, or other sensitive credentials to GitHub.**

---

# Important Installation Notes

Several configuration areas require special attention when installing the project on a new machine.

## 1. Frontend and Backend URLs

The React frontend must communicate with the correct Laravel backend URL.

If the backend runs on:

```text
http://127.0.0.1:8000
```

the frontend API configuration must point to that address.

Using an incorrect API URL can cause:

- Failed API requests
- Missing menu items
- Missing gallery images
- Authentication errors
- Reservation failures

---

## 2. CORS Configuration

Because the React frontend and Laravel backend may run on different ports during development, CORS configuration must allow the frontend origin.

For example:

```text
http://localhost:5173
```

and, when required:

```text
http://127.0.0.1:5173
```

The exact allowed origins should match the actual frontend URL.

In production, only trusted domains should be allowed.

---

## 3. Laravel Sanctum

Authentication between the React frontend and Laravel backend depends on correct Sanctum configuration.

The following must be configured consistently:

- Frontend URL
- Backend URL
- Stateful domains
- Session configuration
- CORS settings
- Cookies and credentials

A mismatch between `localhost` and `127.0.0.1` can cause authentication problems because browsers treat them as different hosts.

For example:

```text
localhost
```

and:

```text
127.0.0.1
```

should not be mixed unnecessarily during development.

It is recommended to use one consistent hostname whenever possible.

---

## 4. Google reCAPTCHA

The reservation system uses Google reCAPTCHA v3.

The required site key and secret key must be configured through environment variables.

The frontend and backend must use the correct keys for the configured domain.

When deploying to a new domain, the domain must also be registered in the Google reCAPTCHA configuration.

Without correct reCAPTCHA configuration, reservation requests may fail.

---

## 5. File Storage and Permissions

The application uses uploaded files such as:

- User profile images
- Administrator profile images
- Gallery images
- Menu images

The server must have permission to write to the appropriate storage directories.

When Laravel's public storage system is used, run:

```bash
php artisan storage:link
```

On production servers, make sure the web server has appropriate read/write permissions.

Avoid giving unnecessarily broad filesystem permissions.

---

## 6. Database Migrations

After installing the project on a new environment, migrations must be executed:

```bash
php artisan migrate
```

If the database schema changes in a future version of the project, new migrations must also be executed.

Never use destructive migration commands on a production database unless a complete backup has been created and the consequences are fully understood.

---

## 7. Environment Variables

The project depends on environment-specific configuration.

Before starting the application, check:

- `.env`
- Frontend environment variables
- Database credentials
- API URLs
- Sanctum configuration
- CORS configuration
- reCAPTCHA credentials
- Mail configuration
- File storage configuration

A missing or incorrect environment variable can cause the application to partially work while specific features fail.

---

## 8. Development vs Production

Development and production environments should not use identical settings.

For development:

```env
APP_ENV=local
APP_DEBUG=true
```

For production:

```env
APP_ENV=production
APP_DEBUG=false
```

Debug mode should be disabled in production to prevent sensitive application information from being exposed.

---

# Desktop and Mobile Experience

The application is responsive and designed for multiple device types.

## Desktop

On desktop computers and laptops, the application provides:

- Wide content layouts
- Full navigation
- Larger image presentation
- Multi-column content where appropriate
- Mouse and keyboard-friendly interaction
- Full administration panel layout

The administration interface is primarily designed for desktop use because administrators typically work with larger amounts of data and multiple management functions.

---

## Mobile

On mobile devices, the public website automatically adapts to smaller screens.

The interface uses:

- Responsive layouts
- Stacked content sections
- Mobile-friendly navigation
- Larger touch targets
- Flexible images
- Responsive typography
- Mobile-friendly buttons and forms

The goal is to make the public website comfortable to use with touch input while preserving the functionality available on desktop devices.

The administration panel is primarily optimized for larger screens and desktop-style management workflows.

---

# Security

Security is considered at multiple levels of the application.

The project uses:

- Laravel authentication mechanisms
- Laravel Sanctum
- CSRF protection
- Server-side validation
- Client-side validation where appropriate
- Protected API endpoints
- Role-based administrative access
- Super administrator restrictions
- Google reCAPTCHA v3
- Disposable email domain blocking
- Secure environment variables
- Activity logging for important administrative operations

Authentication and authorization are handled by the backend. The frontend should never be considered a trusted security boundary by itself.

All important permissions and sensitive operations must be verified server-side.

---

# Development Workflow

A typical local development workflow is:

### Terminal 1 – Laravel Backend

```bash
php artisan serve
```

### Terminal 2 – React Frontend

```bash
cd frontend
npm run dev
```

The developer can then access the React website through the Vite development server while the Laravel backend processes API requests.

When changes are made to the frontend, Vite provides a fast development experience with automatic updates.

Backend changes are handled by Laravel and PHP.

---

# Production Deployment

A production deployment should be performed on a server that supports:

- PHP
- Composer
- MySQL or MariaDB
- Node.js/npm for building the frontend, if required
- A web server such as Apache or Nginx
- HTTPS

A typical production deployment includes:

1. Uploading or cloning the project
2. Installing Composer dependencies
3. Installing frontend dependencies
4. Building the React frontend
5. Creating and configuring the production `.env`
6. Configuring the production database
7. Running database migrations
8. Configuring storage and file permissions
9. Creating the Laravel storage link
10. Configuring CORS and Sanctum
11. Configuring reCAPTCHA for the production domain
12. Disabling Laravel debug mode
13. Configuring HTTPS
14. Configuring the web server
15. Testing authentication, reservations, uploads, and API communication

Before performing migrations or major updates on a production system, a database backup should always be created.

---

# Future Improvements

Possible future improvements include:

- Online payment integration
- Email notifications for reservations
- Automated reservation confirmation emails
- More advanced reservation management
- Additional administrator roles and permissions
- Improved reporting and statistics
- Automated deployment
- Docker-based development and deployment
- Automated testing and CI/CD
- Progressive Web App functionality
- Further mobile optimization

---

# License

The author reserves the right to use, modify, license, sell, commercialize, deploy, or otherwise distribute this project and its future versions as a commercial product.

Any person or organization wishing to use this project, or a substantial portion of its source code, for commercial purposes must obtain prior written permission or a separate commercial license from the author.

For commercial licensing, production deployment, redistribution, or other uses not explicitly permitted by the license, please contact the author.

For full terms and conditions, please refer to the [LICENSE](LICENSE) file.

---

# Author

**Árpád Perna**

GitHub: **Arpi47**

Csárda is a full-stack multilingual restaurant web application demonstrating modern web development practices with Laravel, React, REST APIs, database-driven content management, authentication, responsive design, and administrative tools.
