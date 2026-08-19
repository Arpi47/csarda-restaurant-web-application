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
- PHP 8.5.7 or a compatible PHP 8.5 installation
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

The database configuration differs between operating systems because the local MySQL port is different in the documented development environments.

## Windows 11

For the Windows 11 development environment, the project can be used with XAMPP.

Required:

- XAMPP
- Apache is optional when using `php artisan serve`
- MySQL
- MySQL port `3306`
- PHP
- Composer
- Node.js and npm
- Git

## Linux

For Linux development, a standard PHP, Composer, Node.js/npm, and MySQL/MariaDB installation can be used.

Required:

- PHP
- Composer
- Node.js and npm
- MySQL or MariaDB
- MySQL/MariaDB port `3306`
- Git

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

## Google Services

The following Google services are required only when the corresponding application features are used:

- Google Calendar API
- Google Cloud Service Account
- Google reCAPTCHA v3
- Google OAuth 2.0, if Google login is enabled

Google Calendar synchronization specifically requires a Google Cloud project, the Google Calendar API, a Service Account, and access to the configured calendars.

---

# Installation

## 1. Clone the Repository

Clone the repository:

```bash
git clone https://github.com/Arpi47/csarda.git
```

Enter the project directory:

```bash
cd csarda
```

---

## 2. Configure the Laravel Backend

Install the PHP dependencies:

```bash
composer install
```

Create the Laravel environment file from the example:

### macOS / Linux

```bash
cp .env.example .env
```

### Windows PowerShell

```powershell
Copy-Item .env.example .env
```

Generate the Laravel application key:

```bash
php artisan key:generate
```

This command generates the `APP_KEY` required by Laravel for application encryption.

The `.env` file will then contain a generated value similar to:

```env
APP_KEY=base64:...
```

Do not copy an `APP_KEY` from another installation. Every separate environment should have its own generated application key.

The `.env` file must not be committed to GitHub.

---

## 3. Configure the Database

Create an empty MySQL or MariaDB database named `csarda`, or use another database name and update `DB_DATABASE` accordingly.

The database configuration depends on the operating system.

### Windows 11

When using XAMPP, MySQL normally runs on port `3306`.

Example:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csarda
DB_USERNAME=root
DB_PASSWORD=
```

The exact username and password depend on the local XAMPP configuration.

### Linux

A standard MySQL or MariaDB installation normally uses port `3306`.

Example:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csarda
DB_USERNAME=root
DB_PASSWORD=
```

The exact credentials depend on the local Linux MySQL/MariaDB configuration.

### macOS with MAMP

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

### Database Port Summary

| Operating system | Local environment | MySQL/MariaDB port |
| ---------------- | ----------------- | -----------------: |
| Windows 11       | XAMPP             |             `3306` |
| Linux            | MySQL/MariaDB     |             `3306` |
| macOS            | MAMP              |             `8889` |

After configuring the database, run:

```bash
php artisan migrate
```

If seed data is required and the project contains the corresponding seeders:

```bash
php artisan db:seed
```

or:

```bash
php artisan migrate --seed
```

Do not use destructive commands such as `php artisan migrate:fresh` unless the development database is intentionally being reset.

---

## 4. Configure Laravel Storage

Create the public storage link:

```bash
php artisan storage:link
```

This allows files stored through Laravel's public storage system to be served by the application.

The server must also have appropriate permissions for Laravel's writable storage directories.

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

The React frontend communicates with the Laravel backend through the API URL.

For local development, configure the Vite API URL as:

```env
VITE_API_URL=http://localhost:8000
```

The Laravel backend therefore uses:

```text
http://localhost:8000
```

and the Vite development server uses:

```text
http://localhost:5173
```

The frontend API URL must not be changed to:

```text
http://127.0.0.1:8000
```

for the normal local setup documented here.

Return to the project root:

```bash
cd ..
```

---

## 6. Start the Laravel Backend

From the Laravel project root:

```bash
php artisan serve
```

The backend is normally available at:

```text
http://localhost:8000
```

---

## 7. Start the React/Vite Frontend

In a second terminal:

```bash
cd frontend
npm run dev
```

The frontend is normally available at:

```text
http://localhost:5173
```

Both the Laravel backend and the React/Vite frontend must be running for the complete development application to work.

Typical local development setup:

### Terminal 1

```bash
php artisan serve
```

### Terminal 2

```bash
cd frontend
npm run dev
```

Optional Laravel scheduler:

### Terminal 3

```bash
php artisan schedule:work
```

The scheduler is only necessary when scheduled Laravel tasks are required during development.

---

## 8. Build the Frontend for Production

For a production frontend build:

```bash
cd frontend
npm run build
```

The resulting production assets must then be deployed according to the project's production hosting configuration.

---

## 9. Configure Google Calendar Integration

Google Calendar synchronization requires a Google Cloud project.

### 9.1 Create or Select a Google Cloud Project

Create a Google Cloud project or use an existing project for the application.

### 9.2 Enable the Google Calendar API

In Google Cloud Console, enable:

```text
Google Calendar API
```

### 9.3 Create a Service Account

Create a Google Cloud Service Account for server-side calendar access.

Create and download its JSON credentials file.

Create the following directory in the Laravel project if it does not already exist:

```text
storage/app/google/
```

Place the credentials file at:

```text
storage/app/google/calendar-service-account.json
```

The credentials file contains sensitive information and must never be committed to GitHub.

### 9.4 Share the Calendars with the Service Account

The Google Calendars used by the application must be shared with the Service Account email address with sufficient permission for reading calendar events.

The application currently supports separate calendar configuration for:

- General restaurant opening-hours events
- Serbian holidays
- Hungarian holidays

### 9.5 Configure the Environment Variables

Add the required values to `.env`:

```env
GOOGLE_CALENDAR_ID=
GOOGLE_SERBIAN_HOLIDAYS_CALENDAR_ID=
GOOGLE_HUNGARIAN_HOLIDAYS_CALENDAR_ID=
GOOGLE_CALENDAR_CREDENTIALS=storage/app/google/calendar-service-account.json
```

The Google Calendar configuration is located in:

```text
config/google-calendar.php
```

After changing Google-related `.env` values:

```bash
php artisan config:clear
```

---

## 10. Synchronize Serbian and Hungarian Holidays

The project provides Artisan commands for synchronizing holiday data from Google Calendar.

Synchronize Serbian holidays:

```bash
php artisan google-calendar:sync-serbian-holidays
```

Synchronize Hungarian holidays:

```bash
php artisan google-calendar:sync-hungarian-holidays
```

The commands retrieve the configured Google Calendar events and synchronize the relevant holiday information with the application's database.

If scheduled synchronization is configured for the application, the Laravel scheduler can be started during development with:

```bash
php artisan schedule:work
```

---

## 11. Configure Google reCAPTCHA v3

The reservation system uses Google reCAPTCHA v3.

A reCAPTCHA site must be created/configured for the domain where the application is used.

The required site key and secret key must be added to `.env` using the environment variable names expected by the project.

For example, if the application configuration uses these names:

```env
RECAPTCHA_SITE_KEY=
RECAPTCHA_SECRET_KEY=
```

the corresponding Google reCAPTCHA credentials must be entered there.

The exact variable names must match the application's existing configuration.

For local development, the local hostname used by the application must be permitted by the reCAPTCHA configuration.

For production, the production domain must be configured in Google reCAPTCHA.

After changing reCAPTCHA configuration:

```bash
php artisan config:clear
```

Without valid reCAPTCHA configuration, reservation requests may fail validation.

---

## 12. Configure Google OAuth

If Google login is enabled, configure Google OAuth 2.0 in Google Cloud.

Create an OAuth 2.0 Client ID and configure the redirect URI used by the Laravel application.

The required values are stored in `.env` using the environment variable names expected by the project's Google authentication configuration.

Typical values are:

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=
```

The exact variable names and redirect URI must match the current Laravel authentication implementation.

For local development, use the same hostname consistently.

If the application runs at:

```text
http://localhost:8000
```

the Google OAuth redirect URI should also use `localhost`, rather than switching to `127.0.0.1`.

After changing Google OAuth configuration:

```bash
php artisan config:clear
```

Never commit Google OAuth client secrets to GitHub.

---

## 13. Configure CORS and Laravel Sanctum

The React frontend and Laravel backend run on different ports during local development.

The documented local URLs are:

```text
Frontend:
http://localhost:5173

Backend:
http://localhost:8000
```

CORS must allow the frontend origin.

Laravel Sanctum must also be configured consistently with the frontend/backend hostnames and session settings.

Avoid unnecessarily mixing:

```text
localhost
```

and:

```text
127.0.0.1
```

for the application URLs.

For this project, use:

```text
http://localhost:5173
```

for the React frontend and:

```text
http://localhost:8000
```

for the Laravel backend.

`127.0.0.1` may still be used by individual infrastructure services such as:

```env
MEMCACHED_HOST=127.0.0.1
REDIS_HOST=127.0.0.1
```

This does not change the application URLs described above.

---

# Environment Configuration

The `.env` file contains environment-specific configuration for Laravel and must not be committed to GitHub.

The exact `.env` file may contain additional variables depending on the installed version of the project. The values below show the important configuration areas relevant to local installation.

## Application

```env
APP_NAME=Csarda
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
```

Generate the application key with:

```bash
php artisan key:generate
```

After generation, `APP_KEY` will contain a generated value:

```env
APP_KEY=base64:...
```

Do not manually invent the key and do not copy the key from another environment.

## Database — Windows 11

For XAMPP:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csarda
DB_USERNAME=root
DB_PASSWORD=
```

## Database — Linux

For a standard MySQL/MariaDB installation:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csarda
DB_USERNAME=root
DB_PASSWORD=
```

## Database — macOS with MAMP

For the default MAMP MySQL configuration:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=8889
DB_DATABASE=csarda
DB_USERNAME=root
DB_PASSWORD=root
```

If the MAMP MySQL credentials have been changed, use the actual configured values.

## React/Vite API URL

The React frontend must point to the Laravel backend:

```env
VITE_API_URL=http://localhost:8000
```

The frontend itself runs on:

```text
http://localhost:5173
```

## Redis and Memcached

If Redis or Memcached are configured for local development, their service-specific host settings may use `127.0.0.1`, for example:

```env
REDIS_HOST=127.0.0.1
MEMCACHED_HOST=127.0.0.1
```

These settings are independent of the Laravel application URL and the React API URL.

The project does not use `127.0.0.1:8000` as its normal Laravel API address.

## Google Calendar

```env
GOOGLE_CALENDAR_ID=
GOOGLE_SERBIAN_HOLIDAYS_CALENDAR_ID=
GOOGLE_HUNGARIAN_HOLIDAYS_CALENDAR_ID=
GOOGLE_CALENDAR_CREDENTIALS=storage/app/google/calendar-service-account.json
```

## Google reCAPTCHA

Use the environment variable names defined by the application's current reCAPTCHA configuration.

For example:

```env
RECAPTCHA_SITE_KEY=
RECAPTCHA_SECRET_KEY=
```

## Google OAuth

If Google login is enabled, configure the variables expected by the project's Google authentication configuration.

Typical values include:

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=
```

## Configuration Cache

After changing `.env` values, clear the Laravel configuration cache:

```bash
php artisan config:clear
```

If Laravel configuration has previously been cached and changes are not being detected, it may also be necessary to rebuild the configuration cache for the target environment.

---

# Important Installation Notes

## 1. Keep the Application URLs Consistent

The documented local application URLs are:

```text
React/Vite:
http://localhost:5173

Laravel:
http://localhost:8000
```

Do not switch between `localhost` and `127.0.0.1` unnecessarily.

This is particularly important for:

- Laravel Sanctum
- Session cookies
- CORS
- Google OAuth redirect URIs
- Frontend API requests

## 2. `127.0.0.1` Is Still Valid for Local Services

The fact that the application uses `localhost` does not mean that `127.0.0.1` must be removed from every `.env` variable.

For example, the following can legitimately use `127.0.0.1`:

```env
REDIS_HOST=127.0.0.1
MEMCACHED_HOST=127.0.0.1
```

The database host may also use `127.0.0.1` in the Windows/Linux configurations documented above.

The important distinction is that the Laravel application/API URL is:

```text
http://localhost:8000
```

and the Vite API URL is:

```text
http://localhost:8000
```

not:

```text
http://127.0.0.1:8000
```

## 3. MySQL Ports Differ by Operating System

Do not copy the macOS database configuration to Windows/Linux or vice versa.

Use:

```text
Windows 11 / XAMPP: 3306
Linux:              3306
macOS / MAMP:       8889
```

For macOS/MAMP:

```env
DB_HOST=localhost
DB_PORT=8889
```

For Windows/XAMPP and Linux:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
```

The actual port should always match the MySQL/MariaDB server configuration.

## 4. The Laravel Application Key Is Required

A new installation must generate its own application key:

```bash
php artisan key:generate
```

If `APP_KEY` is missing, Laravel will not be correctly configured for normal operation.

Never commit a real production `.env` file or expose its `APP_KEY`.

## 5. Do Not Commit Sensitive Credentials

Never commit any of the following to GitHub:

- `.env`
- Google Service Account JSON files
- Google OAuth client secrets
- reCAPTCHA secret keys
- Database passwords
- Mail passwords
- API secrets
- Other private credentials

The Google Service Account file should remain outside version control:

```text
storage/app/google/calendar-service-account.json
```

## 6. Clear Configuration After `.env` Changes

After changing environment variables:

```bash
php artisan config:clear
```

If necessary, clear other cached Laravel data during development:

```bash
php artisan cache:clear
```

Do not blindly use production cache commands during development unless the effect is understood.

## 7. Storage Permissions

Laravel must be able to write to its required storage directories.

After installation:

```bash
php artisan storage:link
```

If uploaded images or other files are not displayed correctly, check the storage link and filesystem permissions.

## 8. CORS and Sanctum Must Match the Frontend

The frontend origin is:

```text
http://localhost:5173
```

The backend origin is:

```text
http://localhost:8000
```

CORS and Sanctum configuration must allow the frontend to communicate with the backend.

Authentication problems can occur when one part of the configuration uses `localhost` while another uses `127.0.0.1`.

## 9. Google Calendar Access Must Be Granted

Creating the Service Account is not sufficient by itself.

The configured Google Calendars must also be shared with the Service Account email address.

If the calendar is not accessible by the Service Account, the synchronization commands cannot retrieve its events.

## 10. Google Services Must Be Configured Before Their Features Are Used

The following features require their respective Google configuration:

| Feature                                | Required Google configuration                           |
| -------------------------------------- | ------------------------------------------------------- |
| Holiday synchronization                | Google Calendar API + Service Account + Calendar access |
| Opening-hours calendar synchronization | Google Calendar API + Service Account + Calendar access |
| Reservation protection                 | Google reCAPTCHA v3                                     |
| Google login                           | Google OAuth 2.0                                        |

Features that depend on an unconfigured Google service may fail even when the rest of the application is installed correctly.

## 11. Do Not Use Destructive Database Commands Accidentally

The following command deletes existing database tables before recreating them:

```bash
php artisan migrate:fresh
```

Use it only when intentionally resetting a development database.

For a normal new installation:

```bash
php artisan migrate
```

is sufficient.

## 12. Development and Production Settings Must Differ

For local development:

```env
APP_ENV=local
APP_DEBUG=true
```

For production:

```env
APP_ENV=production
APP_DEBUG=false
```

Debug mode must be disabled in production.

Production should also use:

- HTTPS
- Secure environment variables
- A production database
- Restricted CORS origins
- Correct Sanctum configuration
- Production reCAPTCHA credentials
- Production Google OAuth redirect URIs
- Secure Google Service Account credentials
- Appropriate filesystem permissions

## 13. Final Local Installation Check

Before testing the application, verify:

- [ ] PHP is installed and available from the terminal
- [ ] Composer is installed
- [ ] Node.js and npm are installed
- [ ] MySQL/MariaDB is running
- [ ] The correct database port is configured
- [ ] The `csarda` database exists
- [ ] `.env` exists
- [ ] `APP_KEY` was generated
- [ ] Laravel dependencies were installed
- [ ] Database migrations were executed
- [ ] `php artisan storage:link` was executed
- [ ] Frontend dependencies were installed
- [ ] `VITE_API_URL=http://localhost:8000` is configured
- [ ] Laravel is running at `http://localhost:8000`
- [ ] Vite is running at `http://localhost:5173`
- [ ] CORS allows `http://localhost:5173`
- [ ] Sanctum uses the correct local host configuration
- [ ] Google Calendar API is enabled if synchronization is required
- [ ] Google Service Account credentials are installed if synchronization is required
- [ ] Required Google Calendars are shared with the Service Account
- [ ] Google Calendar IDs are configured
- [ ] reCAPTCHA is configured if reservations are being tested
- [ ] Google OAuth is configured if Google login is being tested
- [ ] Sensitive credentials are not committed to GitHub

Once the required configuration is complete, start the application with:

```bash
php artisan serve
```

and in another terminal:

```bash
cd frontend
npm run dev
```

The application should then be available at:

```text
http://localhost:5173
```

with the Laravel backend/API available at:

```text
http://localhost:8000
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
