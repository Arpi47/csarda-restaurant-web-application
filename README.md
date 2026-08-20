# Csárda – Multilingual Restaurant Web Application

**Author:** Árpád Perna
**GitHub:** Arpi47

A full-stack multilingual restaurant web application built with **Laravel** and **React**. The project provides a modern, responsive website for restaurant guests and a separate administration panel for managing the website's content, users, reservations, opening hours, holidays, application download links, QR codes, and other system data.

The application is designed to work on **desktop computers, laptops, tablets, and mobile devices**, with a responsive interface adapted to different screen sizes and input methods.

The public website is primarily designed for restaurant guests, while the Laravel Blade administration panel provides authorized administrators with centralized control over the application's content and operational settings.

---

## Table of Contents

- [Overview](#overview)
- [Main Features](#main-features)
- [User Features](#user-features)
- [Admin Features](#admin-features)
- [Opening Hours and Holiday Management](#opening-hours-and-holiday-management)
- [Google Calendar Integration](#google-calendar-integration)
- [QR Code and Application Download Management](#qr-code-and-application-download-management)
- [Technology Stack](#technology-stack)
- [Project Architecture](#project-architecture)
- [Project Structure](#project-structure)
- [Requirements](#requirements)
- [Installation](#installation)

    - [1. Clone the Repository](#1-clone-the-repository)
    - [2. Configure the Laravel Backend](#2-configure-the-laravel-backend)
    - [3. Configure the Database](#3-configure-the-database)
    - [4. Configure Laravel Storage](#4-configure-laravel-storage)
    - [5. Configure the React Frontend](#5-configure-the-react-frontend)
    - [6. Start the Laravel Backend](#6-start-the-laravel-backend)
    - [7. Start the React/Vite Frontend](#7-start-the-react/vite-frontend)
    - [8. Build the Frontend for Production](#8-build-the-frontend-for-production)
    - [9. Configure Google Calendar Integration](#9-configure-google-calendar-integration)
    - [10. Synchronize Serbian and Hungarian Holidays](#10-synchronize-serbian-and-hungarian-holidays)
    - [11. Configure Google reCAPTCHA v3](#11-configure-google-recaptcha-v3)
    - [12. Configure Google OAuth](#12-configure-google-oauth)
    - [13. Configure CORS and Laravel Sanctum](#10-configure-cors-and-laravel-sanctum)

- [Environment Configuration](#environment-configuration)
- [Important Installation Notes](#important-installation-notes)
- [Desktop and Mobile Experience](#desktop-and-mobile-experience)
- [Security](#security)
- [Development Workflow](#development-workflow)
- [Production Deployment](#production-deployment)
- [Future Improvements](#future-improvements)
- [License](#license)
- [Author](#author)

---

# Overview

Csárda is a full-stack restaurant web application consisting of two main parts:

1. **Public React frontend** – the website used by restaurant guests.
2. **Laravel administration panel** – a protected backend used by authorized administrators to manage the system.

The frontend and backend communicate through APIs. The Laravel backend is responsible for business logic, authentication, authorization, validation, database access, and API endpoints, while the React frontend provides the modern user-facing interface.

The administration panel is implemented using **Laravel Blade** and provides centralized management of the application's operational and content-related features.

The application uses a database-driven architecture, meaning that important content such as menu items, categories, gallery images, reservations, users, opening hours, special opening hours, and holidays can be managed dynamically instead of being permanently hard-coded into the website.

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
- Responsive support down to 320 px screen width
- Light and dark theme support
- Manual theme switching for public users
- Automatic time-based theme switching in the admin panel
- User registration and authentication
- User profile management
- Account deletion request and cancellation
- Dynamic restaurant menu
- Menu categories
- Image gallery
- Gallery image preview and modal view
- Online table reservations
- Reservation validation and business rules
- Google reCAPTCHA v3 integration
- Restaurant opening hours management
- Kitchen opening hours management
- Special opening hours management
- Serbian holiday management
- Hungarian holiday management
- Google Calendar holiday synchronization
- Protected administration panel
- User and administrator management
- Reservation management
- Menu and category management
- Gallery management
- Contact and website settings management
- Application download link management
- QR code generation for application downloads
- QR code generation for the restaurant menu
- Automatic frontend display of application download QR codes
- Admin activity logging
- Role and permission-based administrative functionality
- Kitchen-aware mobile call button
- Kitchen-aware ordering availability in the navigation bar
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
- Switch between light and dark themes manually
- Register a user account
- Log in securely
- Manage their personal profile
- Request account deletion
- Cancel an account deletion request when permitted
- Make online table reservations
- Receive validation feedback when reservation data is invalid
- Use the website from desktop and mobile devices

The public website supports manual light and dark theme switching. The automatic time-based theme functionality is available only within the administration panel and is not applied to the public user interface.

Reservation requests are validated both on the frontend and backend. The backend also applies business rules such as minimum reservation dates, opening hours, guest limits, and blocked disposable email domains.

---

# Admin Features

The administration panel is a protected part of the Laravel application and is intended for authorized administrators.

Depending on the administrator's role and permissions, the admin panel can be used to manage:

## Users

- View registered users
- Search and filter users
- Monitor account status
- Suspend or manage user accounts
- View authentication-related information

## Administrators

- Invite and manage administrator accounts
- Manage administrator profiles
- Upload administrator profile images
- Apply role-based access restrictions
- Protect sensitive actions using administrator permissions
- Support super administrator functionality

## Reservations

- View incoming reservations
- Review reservation details
- Manage reservation statuses
- Handle reservation-related administrative tasks
- Apply reservation-related business rules

## Menu

- Create menu items
- Edit menu items
- Delete menu items
- Manage menu categories
- Manage multilingual menu content
- Manage menu prices and images
- Generate a QR code pointing to the public restaurant menu

The menu QR code functionality can be used as a foundation for a future QR-based digital menu system. For example, printed menus on restaurant tables could eventually be replaced or supplemented by QR codes that guests scan with their mobile devices to open the restaurant's online menu.

## Gallery

- Add gallery images
- Edit gallery content
- Remove gallery images
- Manage the content displayed on the public website

## Website and Contact Settings

- Manage configurable contact information
- Manage website-related settings
- Keep frequently changing information outside the frontend source code

## Application Download Management

Administrators can manage the download links for the restaurant's mobile applications.

The system supports application links for:

- Google Play Store
- Apple App Store

The administrator can provide the corresponding store URLs through the administration panel.

The React frontend then automatically displays the appropriate application download QR code in the relevant location. The QR code points directly to the configured application download destination.

This allows the administrator to update the application download destination without modifying the React frontend source code.

## QR Code Generation

The administration panel currently supports QR code generation for:

1. **Google Play Store application download links**
2. **Apple App Store application download links**
3. **The public restaurant menu page**

Application download QR codes are automatically displayed by the React frontend in the appropriate location after the corresponding store links have been configured.

The menu QR code can be generated for potential future use as a digital restaurant menu. The intended use is that guests could scan a QR code placed on a restaurant table and immediately access the Csárda menu through their mobile device.

## Opening Hours

The administration panel provides centralized access to the application's different opening-hours management areas.

The main opening-hours page acts as a navigation hub for the different opening-hours management functions rather than directly containing all opening-hours management functionality.

Administrators can access separate management areas for:

- Regular opening hours
- Special opening hours
- Serbian holidays
- Hungarian holidays

Restaurant and kitchen opening hours are managed separately.

## Activity Logging

Administrative actions can be recorded using an activity logging system, making it easier to monitor important actions performed inside the administration panel.

---

# Opening Hours and Holiday Management

The application contains a more advanced opening-hours management system that separates the operating schedule of the **restaurant** and the **kitchen**.

## Restaurant Opening Hours

Administrators can configure:

- Whether the restaurant is active
- Restaurant opening time
- Restaurant closing time
- Last reservation time

The system validates the configured times to prevent invalid opening-hour combinations.

The last reservation time cannot be configured later than the permitted limit before restaurant closing.

## Kitchen Opening Hours

The kitchen has its own independent schedule.

Administrators can configure:

- Whether the kitchen is active
- Kitchen opening time
- Kitchen closing time
- Last order time

The kitchen's operating state is used by the public React frontend to determine whether food orders are currently available.

For example, if the kitchen is closed or the last order time has already passed:

- The mobile call/order-related functionality is disabled where appropriate.
- The ordering option displayed in the navigation bar reflects the current kitchen availability.
- Users are prevented from being directed toward an ordering workflow when the kitchen is no longer accepting orders.

This ensures that the public interface reflects the actual operational state of the kitchen.

## Special Opening Hours

Administrators can configure special opening hours for individual dates.

Special opening hours can be used to override the normal opening-hours schedule for specific days.

This is useful for situations such as:

- Special events
- Temporary schedule changes
- Seasonal operating hours
- Exceptional opening days
- Exceptional closing days

## Serbian Holidays

Serbian public holidays are imported from a configured Google Calendar and stored in the application's database.

Administrators can manage the imported Serbian holidays and configure separate restaurant and kitchen operating schedules for individual holiday dates.

This makes it possible to define holiday-specific opening hours without modifying the regular weekly schedule.

## Hungarian Holidays

Hungarian public holidays are handled in the same way.

Hungarian holidays are imported from a configured Google Calendar and stored in the application's database.

Administrators can configure separate restaurant and kitchen opening hours for Hungarian holiday dates.

## Holiday and Special Opening Priority

The system supports special opening-hour configurations that can override the normal operating schedule for a specific date.

This allows the restaurant to define an exceptional schedule even when a day is also affected by an imported Serbian or Hungarian holiday.

The resulting opening-hours logic can therefore take into account:

- Regular weekly opening hours
- Holiday-specific opening hours
- Special opening hours

This provides administrators with a centralized way to manage exceptional operating schedules.

---

# Google Calendar Integration

The application integrates with the **Google Calendar API** to synchronize calendar-based information.

The current integration supports:

- Restaurant opening-hours calendar events
- Serbian holiday calendar events
- Hungarian holiday calendar events

The Google Calendar integration uses a Google Service Account and the Google Calendar API in read-only mode.

The application configuration is located in:

```text
config/google-calendar.php
```

The configuration supports the following environment variables:

```env
GOOGLE_CALENDAR_ID=
GOOGLE_SERBIAN_HOLIDAYS_CALENDAR_ID=
GOOGLE_HUNGARIAN_HOLIDAYS_CALENDAR_ID=
GOOGLE_CALENDAR_CREDENTIALS=
```

The credentials path defaults to:

```text
storage/app/google/calendar-service-account.json
```

The Service Account credentials file must not be committed to GitHub.

The configured Google Calendars must be accessible to the Service Account used by the application.

The application uses separate calendar IDs for:

- General restaurant opening-hours events
- Serbian holidays
- Hungarian holidays

The holiday synchronization services retrieve events from the configured calendars and synchronize them with the corresponding database tables.

---

# QR Code and Application Download Management

The administration panel allows administrators to configure mobile application download destinations.

The supported platforms are:

- Google Play Store
- Apple App Store

The administrator enters the appropriate store URL through the administration panel.

The React frontend then uses the configured URLs to display the corresponding QR codes automatically.

The QR codes are generated from the configured application download URLs, meaning that the QR code itself does not need to be manually uploaded as an image.

The administration panel also provides QR code generation for the public restaurant menu page.

This functionality is intended to support a future digital menu workflow where QR codes could be placed on restaurant tables instead of, or alongside, traditional printed menus.

A guest could scan the QR code using a smartphone and be redirected directly to the Csárda menu page.

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
- Google Calendar API

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
- Protected environment configuration
- Google Calendar Service Account authentication

## Development Tools

- Git
- GitHub
- Composer
- npm
- Node.js
- VS Code
- Local development server
- MAMP for local development

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
                    │ Google Calendar API     │
                    └────────────┬────────────┘
                                 │
                   ┌─────────────┴─────────────┐
                   │                           │
                   ▼                           ▼
        ┌─────────────────────┐     ┌─────────────────────┐
        │      Database       │     │   Google Calendar   │
        │   MySQL / MariaDB   │     │         API         │
        └─────────────────────┘     └─────────────────────┘
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
- Opening-hours logic
- Holiday synchronization
- QR code configuration
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
│   ├── Services/
│   └── ...
│
├── bootstrap/
│
├── config/
│   └── google-calendar.php
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
│   ├── console.php
│   └── web.php
│
├── frontend/
│   ├── src/
│   │   ├── api/
│   │   ├── components/
│   │   ├── contexts/
│   │   ├── layouts/
│   │   ├── locales/
│   │   ├── pages/
│   │   └── ...
│   ├── public/
│   ├── package.json
│   └── vite.config.js
│
├── storage/
│   └── app/
│       └── google/
│           └── calendar-service-account.json
│
├── .env.example
├── composer.json
├── package.json
└── README.md
```

The exact structure may evolve as new features are added.

---

# Requirements

Before installing the project, make sure the following software and services are available.

## General Requirements

- Git 2.x or newer
- PHP 8.5.7 or a compatible PHP installation that satisfies the project's Composer dependencies
- Composer 2.x or newer
- Node.js 22.x LTS or newer
- npm 10.x or newer
- MySQL 8.0 or newer, or MariaDB 10.6 or newer
- A modern web browser with JavaScript enabled
- Internet access for installing Composer/npm packages and using Google services

The project can be developed locally on:

- Windows 11
- Linux
- macOS

The exact installation commands depend on the operating system, Linux distribution, installed PHP version, and whether MySQL or MariaDB is used.

### Important Linux Note

The Linux commands in this guide include examples for Ubuntu/Debian-based distributions because they use the `apt` package manager.

Other Linux distributions use different package managers:

- Ubuntu/Debian: `apt`
- Fedora/RHEL: `dnf`
- Arch Linux: `pacman`
- openSUSE: `zypper`

Package names may also differ between distributions.

Do not blindly copy Ubuntu/Debian package commands to another Linux distribution. Use the equivalent package manager and package names provided by your distribution.

---

## Windows 11

For the Windows 11 development environment, the project can be used with:

- XAMPP
- WAMP64

Required:

- XAMPP or WAMP64
- Apache is optional when using `php artisan serve`
- MySQL or MariaDB
- MySQL/MariaDB port `3306`
- PHP
- Composer
- Node.js and npm
- Git

### Important Windows Installation Note

The installation order on Windows 11 is important.

The project has been tested using the installation sequence documented in this guide. Installing dependencies before PHP is correctly configured, running Laravel commands before the required dependencies are installed, or configuring the database in the wrong order may result in multiple errors that then have to be fixed individually.

For the most reliable installation, follow the Windows installation steps in the documented order.

The examples below use XAMPP paths where applicable. If WAMP64 is used instead, use the corresponding PHP and database paths provided by that installation.

---

## Linux

For Linux development, a standard PHP, Composer, Node.js/npm, and MySQL or MariaDB installation can be used.

Required:

- PHP
- Required PHP extensions, including XML, DOM, and MySQL/MariaDB support
- Composer
- Node.js and npm
- MySQL or MariaDB
- MySQL/MariaDB port `3306`
- Git

### PHP Version and Extension Packages

The exact PHP extension package names depend on the installed PHP version.

For example, on an Ubuntu/Debian system using PHP 8.3:

```bash
sudo apt update
sudo apt install php8.3-xml php8.3-mysql -y
```

The XML package provides the XML-related functionality required by Composer dependencies. The exact package layout may differ depending on the PHP version and Linux distribution.

On systems using another PHP version, replace `8.3` with the installed version where appropriate.

For example:

```bash
sudo apt install php8.5-xml php8.5-mysql -y
```

if PHP 8.5 packages are available and PHP 8.5 is the version being used by the project.

On other Linux distributions, install the equivalent PHP XML and MySQL/MariaDB extensions using the distribution's package manager.

Examples:

### Fedora/RHEL

```bash
sudo dnf install php-xml php-mysqlnd
```

### Arch Linux

```bash
sudo pacman -S php
```

Then enable the required PHP extensions according to the Arch Linux PHP configuration.

### openSUSE

```bash
sudo zypper install php8-xmlreader php8-mysql
```

Package names can vary depending on the exact distribution and repository configuration.

---

## macOS

For the macOS development environment, MAMP can be used for the local MySQL server.

Required:

- MAMP
- MySQL port `8889`
- PHP
- Composer
- Node.js and npm
- Git

The Laravel and Vite development servers are started separately and therefore do not require Apache from MAMP for normal local development.

---

## Google Services

The following Google services are required only when the corresponding application features are used:

- Google Calendar API
- Google Cloud Service Account
- Google reCAPTCHA v3
- Google OAuth 2.0, if Google login is enabled

Google Calendar synchronization specifically requires a Google Cloud project, the Google Calendar API, a Service Account, and access to the configured calendars.

---

# Installation

## Windows 11 Installation

### Important: Follow the Installation Order

The order of the following Windows installation steps should be followed as documented.

---

### Step 1. Install XAMPP or WAMP64

Download and install either XAMPP or WAMP64.

The installation must provide:

- PHP
- MySQL or MariaDB

Apache is optional because the Laravel development server is started separately using:

```powershell
php artisan serve
```

For a standard XAMPP installation, PHP is normally located at:

```text
C:\xampp\php
```

---

### Step 2. Add PHP to the Windows PATH Environment Variable

PHP must be available globally from the terminal before Composer and Laravel commands are used.

For XAMPP, the default PHP directory is:

```text
C:\xampp\php
```

Copy this path.

Then add it to the Windows PATH environment variable:

1. Press the Windows key.
2. Search for `env`.
3. Open **Edit the system environment variables** or the corresponding Environment Variables settings.
4. Click **Environment Variables...**.
5. Under **System variables**, find and select `Path`.
6. Click **Edit...**.
7. Click **New**.
8. Add the PHP path:

```text
C:\xampp\php
```

9. Click **OK** in all open windows to save the changes.
10. Close and reopen PowerShell, Command Prompt, or the VS Code terminal.

Verify that PHP is available:

```powershell
php -v
```

The terminal should display the installed PHP version.

If `php` is not recognized as a command, verify that the correct PHP directory was added to the PATH variable and restart the terminal.

---

### Step 3. Clone the Repository

Open PowerShell or another terminal.

Clone the repository:

```powershell
git clone https://github.com/Arpi47/csarda.git
```

Enter the project directory:

```powershell
cd csarda
```

---

### Step 4. Install Composer

Download and install Composer for Windows.

During installation, make sure Composer uses the PHP installation provided by XAMPP or WAMP64.

For XAMPP, this is normally:

```text
C:\xampp\php\php.exe
```

After installation, close and reopen the terminal.

Verify Composer:

```powershell
composer --version
```

---

### Step 5. Install the Laravel PHP Dependencies

From the `csarda` project root, install the PHP dependencies:

```powershell
composer install
```

If the local PHP installation does not satisfy the platform requirements and the installation cannot currently be corrected, a temporary local development workaround is:

```powershell
composer install --ignore-platform-reqs
```

Normally, `composer install` should be used for a fresh installation because the project contains a `composer.lock` file and this installs the dependency versions recorded for the project.

If a complete dependency update is intentionally required, `composer update` may be used, but it can update dependencies and modify `composer.lock`.

Do not use `--ignore-platform-reqs` as a normal production installation method.

---

### Step 6. Create and Configure the Laravel Environment File

Create the `.env` file:

```powershell
Copy-Item .env.example .env
```

Generate the Laravel application key:

```powershell
php artisan key:generate
```

The `.env` file must not be committed to GitHub.

For a standard XAMPP MySQL installation, the database configuration is normally:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csarda
DB_USERNAME=root
DB_PASSWORD=
```

The exact database credentials depend on the XAMPP or WAMP64 configuration.

---

### Step 7. Install Node.js

Download and install Node.js.

Use a supported LTS version where possible.

After installation, close and reopen the terminal.

Verify Node.js:

```powershell
node -v
```

Verify npm:

```powershell
npm -v
```

---

### Step 8. Configure the PowerShell Execution Policy

PowerShell may block locally installed npm scripts.

If required, run:

```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

Confirm the change if PowerShell asks for confirmation.

This setting applies to the current Windows user.

---

### Step 9. Configure the Database and Run Migrations

Create the `csarda` database using phpMyAdmin or the MySQL/MariaDB command line.

Make sure that the database configuration in `.env` is correct.

Then execute:

```powershell
php artisan migrate --seed
```

This creates the required database tables and runs the project's database seeders.

Do not use:

```powershell
php artisan migrate:fresh
```

unless the development database is intentionally being deleted and recreated.

---

### Step 10. Configure Laravel Storage

Create the public storage link:

```powershell
php artisan storage:link
```

This creates the symbolic link required for publicly accessible Laravel storage files.

---

### Step 11. Start XAMPP or WAMP64

Open XAMPP or WAMP64.

Start the database server:

- Start **MySQL** when using XAMPP.
- Start the corresponding MySQL or MariaDB service when using WAMP64.

The database server must be running before the application is started.

Apache is not required when the Laravel backend is started with:

```powershell
php artisan serve
```

---

### Step 12. Install the React Frontend Dependencies

Move into the frontend directory:

```powershell
cd frontend
```

Install the JavaScript dependencies:

```powershell
npm install
```

Return to the project root:

```powershell
cd ..
```

---

### Step 13. Configure the React/Vite API URL

Configure the frontend environment so that the React application communicates with the Laravel API.

The required API URL is:

```env
VITE_API_URL=http://localhost:8000/api
```

The `/api` suffix is required because the React frontend communicates with the Laravel API routes.

The local application URLs are:

```text
React/Vite:
http://localhost:5173

Laravel application:
http://localhost:8000

Laravel API:
http://localhost:8000/api
```

For the documented local setup, do not unnecessarily replace `localhost` with `127.0.0.1` in the application URLs.

---

### Step 14. Start the Application

The complete development environment uses multiple terminals.

#### Terminal 1 — Laravel Backend

Open a terminal in:

```text
csarda/
```

Run:

```powershell
php artisan serve
```

The Laravel backend should be available at:

```text
http://localhost:8000
```

#### Terminal 2 — React/Vite Frontend

Open a second terminal.

Move into:

```text
csarda/frontend/
```

Run:

```powershell
npm run dev
```

The React/Vite frontend should normally be available at:

```text
http://localhost:5173
```

#### Terminal 3 — Laravel Scheduler

Open a third terminal in:

```text
csarda/
```

Run:

```powershell
php artisan schedule:work
```

The scheduler is required when scheduled Laravel tasks are used during development.

---

## Linux and macOS Installation

The following steps apply to Linux and macOS.

### 1. Clone the Repository

Clone the repository:

```bash
git clone https://github.com/Arpi47/csarda.git
```

Enter the project directory:

```bash
cd csarda
```

---

### 2. Configure the Laravel Backend

#### Linux PHP Extensions

Before installing the Composer dependencies on Linux, make sure that the required PHP extensions are installed.

For Ubuntu/Debian using PHP 8.3:

```bash
sudo apt update
sudo apt install php8.3-xml php8.3-mysql -y
```

If another PHP version is installed, use the corresponding package names.

For example, with PHP 8.5:

```bash
sudo apt update
sudo apt install php8.5-xml php8.5-mysql -y
```

For Fedora/RHEL, Arch Linux, openSUSE, or another Linux distribution, install the equivalent XML and MySQL/MariaDB PHP extensions using that distribution's package manager.

After the required PHP extensions are available, install the PHP dependencies:

```bash
composer install
```

#### Composer Platform Requirement Problems

If `composer install` reports a platform requirement error, first verify the installed PHP version and required PHP extensions.

Check the PHP version:

```bash
php -v
```

Check the installed extensions:

```bash
php -m
```

The preferred solution is to install a compatible PHP version and all required PHP extensions.

As a temporary local development workaround, Composer platform requirements can be ignored:

```bash
composer install --ignore-platform-reqs
```

This should not be the preferred solution for production or long-term development.

#### Create the Laravel Environment File

##### macOS / Linux

```bash
cp .env.example .env
```

Generate the Laravel application key:

```bash
php artisan key:generate
```

The `.env` file will then contain a generated value similar to:

```env
APP_KEY=base64:...
```

Do not copy an `APP_KEY` from another installation.

Every separate environment should have its own generated application key.

The `.env` file must not be committed to GitHub.

---

### 3. Configure the Database

Create an empty MySQL or MariaDB database named `csarda`, or use another database name and update `DB_DATABASE` accordingly.

The database configuration depends on the operating system and database server.

#### Linux

A standard MySQL or MariaDB installation normally uses port `3306`.

The `mysql` Laravel database driver is used for both MySQL and MariaDB:

```env
DB_CONNECTION=mysql
```

#### Ubuntu/Debian with MariaDB

The following example applies to an Ubuntu/Debian-based system using MariaDB.

Start the MariaDB service:

```bash
sudo systemctl start mariadb
```

Check its status:

```bash
sudo systemctl status mariadb
```

Restart the service if necessary:

```bash
sudo systemctl restart mariadb
```

Enable MariaDB to start automatically after system boot:

```bash
sudo systemctl enable mariadb
```

Open the MariaDB shell:

```bash
sudo mariadb
```

Create the application database:

```sql
CREATE DATABASE IF NOT EXISTS csarda;
```

For a dedicated application database user, create the user and grant access:

```sql
CREATE USER IF NOT EXISTS 'csarda_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON csarda.* TO 'csarda_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Then configure Laravel:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csarda
DB_USERNAME=csarda_user
DB_PASSWORD=your_secure_password
```

Using a dedicated database user is recommended instead of using the MariaDB or MySQL `root` account for the application.

#### MariaDB Root Authentication

Some Linux MariaDB installations configure the `root` user with socket-based authentication.

The MariaDB root authentication method can be changed, for example:

```sql
ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD ('your_root_password');
```

However, changing the root authentication method is not required when using a dedicated application database user.

#### MySQL on Linux

If MySQL is used instead of MariaDB, service names and administration commands may differ depending on the Linux distribution.

For example, on Ubuntu/Debian, MySQL commonly uses:

```bash
sudo systemctl start mysql
sudo systemctl status mysql
sudo systemctl restart mysql
sudo systemctl enable mysql
```

Open the MySQL shell:

```bash
sudo mysql
```

Then create the database and application user:

```sql
CREATE DATABASE IF NOT EXISTS csarda;
CREATE USER IF NOT EXISTS 'csarda_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON csarda.* TO 'csarda_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### Other Linux Distributions

The package manager and service name may differ.

##### Fedora/RHEL

MariaDB is commonly managed with:

```bash
sudo systemctl start mariadb
sudo systemctl enable mariadb
```

Packages are typically installed with `dnf`.

##### Arch Linux

MariaDB is commonly managed through `systemctl`, but installation and initial database setup follow Arch-specific procedures.

Packages are installed with:

```bash
sudo pacman -S mariadb
```

##### openSUSE

Packages are commonly installed with:

```bash
sudo zypper install mariadb
```

The database service may then be managed with `systemctl`.

Always check the documentation for the exact Linux distribution and database server version being used.

#### macOS with MAMP

MAMP uses MySQL port `8889` by default.

Example:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=8889
DB_DATABASE=csarda
DB_USERNAME=root
DB_PASSWORD=root
```

The default MAMP credentials are commonly `root` / `root`, unless they have been changed.

#### Database Port Summary

| Operating system | Local environment | MySQL/MariaDB port |
| ---------------- | ----------------- | -----------------: |
| Windows 11       | XAMPP/WAMP64      |             `3306` |
| Linux            | MySQL/MariaDB     |             `3306` |
| macOS            | MAMP              |             `8889` |

After configuring the database, run:

```bash
php artisan migrate
```

If seed data is required:

```bash
php artisan db:seed
```

or:

```bash
php artisan migrate --seed
```

Do not use destructive commands such as `php artisan migrate:fresh` unless the development database is intentionally being reset.

---

### 4. Configure Laravel Storage

Create the public storage link:

```bash
php artisan storage:link
```

This allows files stored through Laravel's public storage system to be served by the application.

---

### 5. Configure the React Frontend

Move into the frontend directory:

```bash
cd frontend
```

Install the JavaScript dependencies:

```bash
npm install
```

The React frontend communicates with the Laravel backend through the API URL.

For local development, configure:

```env
VITE_API_URL=http://localhost:8000/api
```

The Laravel backend base URL is:

```text
http://localhost:8000
```

The Laravel API base URL is:

```text
http://localhost:8000/api
```

The Vite development server uses:

```text
http://localhost:5173
```

Return to the project root:

```bash
cd ..
```

---

### 6. Start the Laravel Backend

From the Laravel project root:

```bash
php artisan serve
```

The backend is normally available at:

```text
http://localhost:8000
```

---

### 7. Start the React/Vite Frontend

In a second terminal:

```bash
cd frontend
npm run dev
```

The frontend is normally available at:

```text
http://localhost:5173
```

---

### 8. Start the Laravel Scheduler

Optional scheduler terminal:

```bash
php artisan schedule:work
```

The scheduler is necessary when scheduled Laravel tasks are required during development.

---

# Environment Configuration

## React/Vite API URL

The React frontend must point to the Laravel API:

```env
VITE_API_URL=http://localhost:8000/api
```

The Laravel application itself runs at:

```text
http://localhost:8000
```

The frontend runs at:

```text
http://localhost:5173
```

The React application communicates with Laravel through:

```text
http://localhost:8000/api
```

---

# Important Installation Notes

## 1. Linux Commands Depend on the Distribution

The Linux examples in this README primarily use Ubuntu/Debian commands.

For Ubuntu/Debian:

```bash
sudo apt install package-name
```

For Fedora/RHEL:

```bash
sudo dnf install package-name
```

For Arch Linux:

```bash
sudo pacman -S package-name
```

For openSUSE:

```bash
sudo zypper install package-name
```

The exact package names may differ.

---

## 2. MariaDB and MySQL Are Both Supported

Laravel uses:

```env
DB_CONNECTION=mysql
```

for both MySQL and MariaDB.

On Linux, the main differences may include:

- package names
- service names
- default authentication methods
- initial database configuration
- administrator commands

MariaDB service examples commonly use:

```bash
sudo systemctl start mariadb
```

MySQL service examples commonly use:

```bash
sudo systemctl start mysql
```

The exact service name should be verified on the target system.

---

## 3. Prefer a Dedicated Database User

For Linux installations, using a dedicated database user is recommended.

Example:

```sql
CREATE USER IF NOT EXISTS 'csarda_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON csarda.* TO 'csarda_user'@'localhost';
FLUSH PRIVILEGES;
```

Laravel:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csarda
DB_USERNAME=csarda_user
DB_PASSWORD=your_secure_password
```

This avoids unnecessary changes to the MySQL or MariaDB `root` authentication configuration.

---

## 4. Keep the Application URLs Consistent

The documented local URLs are:

```text
React/Vite:
http://localhost:5173

Laravel application:
http://localhost:8000

Laravel API:
http://localhost:8000/api
```

Do not switch between `localhost` and `127.0.0.1` unnecessarily.

This is particularly important for:

- Laravel Sanctum
- Session cookies
- CORS
- Google OAuth redirect URIs
- Frontend API requests

---

## 5. Composer Platform Requirement Problems

If `composer install` fails because of missing PHP extensions or an incompatible PHP version, first fix the PHP installation.

Check:

```bash
php -v
php -m
```

Install the missing extensions and use a PHP version compatible with the project's Composer dependencies.

Only as a temporary local development workaround:

```bash
composer install --ignore-platform-reqs
```

Do not rely on `--ignore-platform-reqs` as a normal production installation method.

---

## 6. MySQL Ports Differ by Operating System

Use:

```text
Windows 11 / XAMPP / WAMP64:  3306
Linux:                        3306
macOS / MAMP:                 8889
```

The actual port should always match the database server configuration.

---

## 7. Final Local Installation Check

Before testing the application, verify:

- [ ] PHP is installed and available from the terminal.
- [ ] The installed PHP version is compatible with the project's Composer dependencies.
- [ ] Required PHP extensions, including XML and MySQL/MariaDB support, are installed.
- [ ] Composer is installed.
- [ ] Node.js and npm are installed.
- [ ] MySQL or MariaDB is installed and running.
- [ ] The correct database port is configured.
- [ ] The `csarda` database exists.
- [ ] A database user with access to `csarda` exists.
- [ ] `.env` exists.
- [ ] `APP_KEY` was generated.
- [ ] Laravel dependencies were installed.
- [ ] Database migrations and required seeders were executed.
- [ ] `php artisan storage:link` was executed.
- [ ] Frontend dependencies were installed.
- [ ] `VITE_API_URL=http://localhost:8000/api` is configured.
- [ ] Laravel is running at `http://localhost:8000`.
- [ ] The Laravel API is available under `http://localhost:8000/api`.
- [ ] Vite is running at `http://localhost:5173`.
- [ ] CORS allows `http://localhost:5173`.
- [ ] Sanctum uses the correct local host configuration.
- [ ] Google Calendar API is enabled if synchronization is required.
- [ ] Google Service Account credentials are installed if synchronization is required.
- [ ] Required Google Calendars are shared with the Service Account.
- [ ] Google Calendar IDs are configured.
- [ ] reCAPTCHA is configured if reservations are being tested.
- [ ] Google OAuth is configured if Google login is being tested.
- [ ] Sensitive credentials are not committed to GitHub.

Once the required configuration is complete, start the application with:

```bash
php artisan serve
```

and in another terminal:

```bash
cd frontend
npm run dev
```

If scheduled tasks are required, start the scheduler in a third terminal:

```bash
php artisan schedule:work
```

The application should then be available at:

```text
http://localhost:5173
```

with the Laravel backend available at:

```text
http://localhost:8000
```

and the Laravel API available at:

```text
http://localhost:8000/api
```

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

The administration panel also includes a time-based automatic theme mechanism, while public users can manually select their preferred theme.

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

The public frontend is designed to provide reliable responsive behavior down to approximately **320 px screen width**.

Screen widths below 320 px are not considered a primary supported target, and layout issues or visual overflow may occur on extremely narrow displays.

The administration panel is primarily optimized for larger screens and desktop-style management workflows.

## Kitchen-Aware Mobile Interaction

The mobile interface also takes the current kitchen operating status into account.

The mobile call/order-related button is disabled when:

- The kitchen is not active
- The kitchen is currently closed
- The last order time has already passed

The ordering option displayed in the navigation bar also checks the kitchen's current availability.

This prevents users from being directed toward ordering functionality when the kitchen is no longer accepting orders.

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
- Protected Google Calendar credentials

Authentication and authorization are handled by the backend. The frontend should never be considered a trusted security boundary by itself.

All important permissions and sensitive operations must be verified server-side.

Google Service Account credentials and other secrets must be kept outside version control.

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

Optional Laravel scheduler:

### Terminal 3 – Laravel Backend

```bash
php artisan schedule:work
```

The scheduler is only necessary when scheduled Laravel tasks are required during development.

---

If Google Calendar synchronization is required, the holiday synchronization commands can be executed manually:

```bash
php artisan google-calendar:sync-serbian-holidays
php artisan google-calendar:sync-hungarian-holidays
```

The local startup workflow can also be configured to execute these synchronization commands automatically.

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
12. Configuring Google Calendar API access
13. Creating and securely storing Google Service Account credentials
14. Sharing the required Google Calendars with the Service Account
15. Configuring the Google Calendar IDs
16. Synchronizing Serbian and Hungarian holidays
17. Disabling Laravel debug mode
18. Configuring HTTPS
19. Configuring the web server
20. Testing authentication, reservations, uploads, opening hours, holiday synchronization, QR codes, and API communication

Before performing migrations or major updates on a production system, a database backup should always be created.

---

# Future Improvements

Possible future improvements include:

- Full QR-based digital menu deployment on restaurant tables
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
- Additional restaurant operational integrations

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

Csárda is a full-stack multilingual restaurant web application demonstrating modern web development practices with Laravel, React, REST APIs, database-driven content management, authentication, responsive design, opening-hours management, Google Calendar integration, QR code generation, and administrative tools.
