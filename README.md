# Csárda – Multilingual Restaurant Web Application

**Author:** Árpád Perna  
**GitHub:** https://github.com/Arpi47

A full-stack multilingual restaurant web application built with Laravel and React. The application provides a modern restaurant website with online reservations, user authentication, Google OAuth integration, reCAPTCHA protection, dynamic menu and gallery management, app download links with automatically generated QR codes, and a dedicated administration system.

---

# Table of Contents

* [1. About the Project](#1-about-the-project)
* [2. Technologies](#2-technologies)
* [3. System Requirements](#3-system-requirements)
* [4. Clone the Repository](#4-clone-the-repository)
* [5. Backend Installation](#5-backend-installation)
* [6. Database Configuration](#6-database-configuration)
* [7. Environment Configuration](#7-environment-configuration)
* [8. Frontend Environment Configuration](#8-frontend-environment-configuration)
* [9. Google reCAPTCHA Configuration](#9-google-recaptcha-configuration)
* [10. Google OAuth Configuration](#10-google-oauth-configuration)
* [11. Install Frontend Dependencies](#11-install-frontend-dependencies)
* [12. Database Migration and Seeders](#12-database-migration-and-seeders)
* [13. Seeded Test Accounts](#13-seeded-test-accounts)
* [14. Email Configuration for Local Testing](#14-email-configuration-for-local-testing)
* [15. App Download Links and Dynamic QR Codes](#15-app-download-links-and-dynamic-qr-codes)
* [16. Running the Application](#16-running-the-application)
* [17. Accessing the Application](#17-accessing-the-application)
* [18. Optional: Access from Other Devices with Tailscale](#18-optional-access-from-other-devices-with-tailscale)
* [19. Security and Environment Files](#19-security-and-environment-files)
* [20. Project Structure](#20-project-structure)
* [21. Author](#21-author)
* [License and Usage](#license-and-usage)

---

## 1. About the Project

Csárda is a multilingual full-stack restaurant web application developed as an individual software development project.

The application consists of:

* A Laravel backend
* A React frontend powered by Vite
* A MySQL database
* User authentication
* Google OAuth authentication
* Google account linking
* Google reCAPTCHA protection
* Password reset functionality
* Registration email verification
* Online table reservations
* Dynamic restaurant menu
* Restaurant gallery
* Multilingual content
* User profile management
* Admin panel
* Super administrator functionality
* Reservation management
* Dynamic restaurant contact information
* Dynamic mobile application download links
* Automatically generated QR codes for mobile application download links
* Scheduled Laravel tasks

Supported languages:

* English
* Hungarian
* Serbian Latin
* Serbian Cyrillic

---

## 2. Technologies

### Backend

* PHP 8.2+
* Laravel 12
* Laravel Sanctum
* Laravel Socialite
* MySQL
* Composer

### Frontend

* React
* React Router
* Vite
* Tailwind CSS
* Framer Motion
* Axios
* Lucide React
* React Icons

### External Services

* Google OAuth
* Google reCAPTCHA v3

---

## 3. System Requirements

Before installing the project, make sure the following software is installed.

### All Operating Systems

* PHP 8.2 or newer
* Composer
* Node.js and npm
* MySQL
* Git

### macOS

For macOS, a local PHP and MySQL environment such as MAMP can be used.

Recommended:

* MAMP
* Composer
* Node.js
* npm
* Git

### Windows

Recommended options include:

* XAMPP or MAMP for Windows
* Composer
* Node.js
* npm
* Git

Make sure that PHP, Composer, Node.js and npm are available from the terminal.

### Linux

You can install PHP and MySQL using your distribution's package manager.

For example, on Ubuntu:

```bash
sudo apt update
sudo apt install php php-cli php-mysql php-mbstring php-xml php-curl php-zip mysql-server unzip
```

Install Composer and Node.js according to the official installation instructions for your Linux distribution.

---

## 4. Clone the Repository

Clone the project:

```bash
git clone https://github.com/Arpi47/csarda-restaurant-web-application.git
```

Enter the project directory:

```bash
cd csarda-restaurant-web-application
```

---

## 5. Backend Installation

Install PHP dependencies:

```bash
composer install
```

Create the Laravel environment file:

```bash
cp .env.example .env
```

On Windows, if `cp` is not available, manually copy:

```text
.env.example
```

to:

```text
.env
```

Generate the Laravel application key:

```bash
php artisan key:generate
```

---

## 6. Database Configuration

The project uses MySQL.

Create a MySQL database named:

```text
csarda
```

Then configure the database connection in `.env`.

Example:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csarda
DB_USERNAME=root
DB_PASSWORD=
```

### macOS with MAMP

The default MAMP MySQL configuration commonly uses port `8889`.

Example:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=csarda
DB_USERNAME=root
DB_PASSWORD=root
```

If your local MySQL installation uses different credentials or a different port, adjust the `.env` values accordingly.

---

## 7. Environment Configuration

The repository contains an `.env.example` file.

Copy it to:

```text
.env
```

The following values must be configured for the complete application functionality:

```env
APP_NAME=Csarda
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csarda
DB_USERNAME=root
DB_PASSWORD=

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
GOOGLE_LINK_REDIRECT_URI=http://localhost:8000/auth/google/link/callback

RECAPTCHA_SITE_KEY=
RECAPTCHA_SECRET_KEY=

CORS_ALLOWED_ORIGINS=http://localhost:5173,http://127.0.0.1:5173

FRONTEND_URL=http://localhost:5173

SANCTUM_STATEFUL_DOMAINS=localhost:5173,localhost,127.0.0.1:5173,127.0.0.1
```

The exact values may vary depending on your local environment.

---

## 8. Frontend Environment Configuration

Enter the frontend directory:

```bash
cd frontend
```

Create the frontend environment file by copying:

```text
frontend/.env.example
```

to:

```text
frontend/.env
```

The default configuration is:

```env
VITE_API_URL=http://localhost:8000/api
VITE_BACKEND_URL=http://localhost:8000
VITE_ASSET_URL=http://localhost:8000
VITE_STORAGE_URL=http://localhost:8000/storage
VITE_RECAPTCHA_SITE_KEY=
```

Add your Google reCAPTCHA site key:

```env
VITE_RECAPTCHA_SITE_KEY=YOUR_RECAPTCHA_SITE_KEY
```

Return to the project root:

```bash
cd ..
```

---

## 9. Google reCAPTCHA Configuration

The application uses Google reCAPTCHA v3.

A valid reCAPTCHA configuration is required for features protected by reCAPTCHA, including:

* User registration
* Reservation functionality
* Password reset requests

### Step 1 – Create a reCAPTCHA key

Visit the Google reCAPTCHA administration console and create a new reCAPTCHA key.

Select:

```text
reCAPTCHA v3
```

Add the domains where the application will be tested.

For local development, use:

```text
localhost
127.0.0.1
```

If you are testing the application through another hostname or IP address, that address may also need to be added to the reCAPTCHA configuration.

### Step 2 – Configure the backend

Add the secret key to:

```env
RECAPTCHA_SECRET_KEY=YOUR_SECRET_KEY
```

Add the site key to:

```env
RECAPTCHA_SITE_KEY=YOUR_SITE_KEY
```

### Step 3 – Configure the React frontend

In:

```text
frontend/.env
```

add:

```env
VITE_RECAPTCHA_SITE_KEY=YOUR_SITE_KEY
```

The reCAPTCHA site key is used by the frontend.

The reCAPTCHA secret key is used by the Laravel backend.

> **Security warning:** Never publish or commit `RECAPTCHA_SECRET_KEY` to GitHub.

---

## 10. Google OAuth Configuration

The application supports:

* Google login
* Google account linking from the user profile

A Google OAuth application must be created through the Google Cloud Console.

Create OAuth credentials for a Web Application.

Configure the following local redirect URIs:

```text
http://localhost:8000/auth/google/callback
```

and:

```text
http://localhost:8000/auth/google/link/callback
```

Add the credentials to `.env`:

```env
GOOGLE_CLIENT_ID=YOUR_CLIENT_ID
GOOGLE_CLIENT_SECRET=YOUR_CLIENT_SECRET
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
GOOGLE_LINK_REDIRECT_URI=http://localhost:8000/auth/google/link/callback
```

The frontend URL must also be configured:

```env
FRONTEND_URL=http://localhost:5173
```

> **Security warning:** Never publish or commit `GOOGLE_CLIENT_SECRET` to GitHub.

The Google OAuth flow requires both the Laravel backend and React frontend to be running.

---

## 11. Install Frontend Dependencies

Install the React frontend dependencies:

```bash
cd frontend
npm install
```

Return to the project root:

```bash
cd ..
```

---

## 12. Database Migration and Seeders

Run the database migrations:

```bash
php artisan migrate
```

Run the database seeders:

```bash
php artisan db:seed
```

The main `DatabaseSeeder` runs:

* `SuperAdminSeeder`
* `AdminSeeder`
* `UserSeeder`
* `CategorySeeder`
* `MenuSeeder`
* `GalleryImageSeeder`

The gallery seeder automatically copies the gallery images from:

```text
database/seeders/gallery/
```

to:

```text
public/images/gallery/
```

The included gallery seed images are:

```text
gallery_1.png
gallery_2.png
gallery_3.png
gallery_4.png
```

---

## 13. Seeded Test Accounts

The project includes predefined test accounts.

### Regular User

```text
Email: user1@example.com
Password: password123
```

### Second Regular User

```text
Email: user2@example.com
Password: password123
```

### Administrator

```text
Email: admin@example.com
Password: strongpassword
```

### Super Administrator

```text
Email: superadmin@gmail.com
Password: superadmin123
```

These credentials are defined in the following seeders:

```text
database/seeders/UserSeeder.php
database/seeders/AdminSeeder.php
database/seeders/SuperAdminSeeder.php
```

> **Security note:** These credentials are intended only for local development and testing. They must be changed before using the application in a production environment.

---

## 14. Email Configuration for Local Testing

The default local configuration uses Laravel's `log` mail driver.

```env
MAIL_MAILER=log
```

No external SMTP server or email service is required for local testing.

The application generates emails for features such as:

* Registration email verification
* Password reset
* Other email-based application functionality

Generated emails are written to:

```text
storage/logs/laravel.log
```

> **Important:** In the default local configuration, emails are not actually delivered to the user's email address. Instead, the generated email content and links are written to `storage/logs/laravel.log`.

### Registration verification

After registering a new account, open:

```text
storage/logs/laravel.log
```

Find the latest generated email and copy the registration verification link from the email content.

Open the link in your browser to verify the account.

### Password reset

When testing the forgotten-password functionality, submit the user's email address through the password reset form.

Then open:

```text
storage/logs/laravel.log
```

Find the generated password reset email and copy the reset link into your browser.

The same procedure applies to other application features that generate emails.

---

## 15. App Download Links and Dynamic QR Codes

The application includes a dedicated app download section on the user-facing website.

The mobile application download links are managed dynamically through the administration system instead of being hard-coded directly into the React frontend.

The administrator can configure the application download URLs, such as:

* Google Play Store URL
* Apple App Store URL

The configured URLs are stored in the backend and are retrieved by the React frontend through the API.

### Dynamic QR code generation

A QR code is automatically generated from each configured application download URL.

For example:

```text
Google Play Store URL
        ↓
Backend application settings
        ↓
API response
        ↓
React frontend
        ↓
QR code generated from the current URL
```

The same process applies to the Apple App Store URL.

This means that the QR code does not need to be manually regenerated or replaced when an administrator changes the application download URL.

### Updating an application URL

When an administrator changes an application download URL in the admin panel:

1. The new URL is saved in the backend.
2. The frontend retrieves the updated value through the API.
3. The QR code is generated from the new URL.
4. The user-facing app download section displays the updated QR code and download link.

Therefore, the QR code always corresponds to the current URL stored in the administration system.

### Important implementation principle

The application download URLs should not be hard-coded in the React components.

Instead, the frontend should retrieve the current values from the backend API.

This provides the following advantages:

* Administrators can update app links without modifying frontend source code.
* QR codes automatically reflect the current URLs.
* The React application does not need to be rebuilt every time an app store URL changes.
* The same configuration can be used across the public website and other frontend components.
* The admin panel provides a centralized location for managing application download links.

### Example

If the administrator changes:

```text
https://example.com/old-app-link
```

to:

```text
https://example.com/new-app-link
```

the frontend will use the new URL when it retrieves the application settings from the backend.

The QR code generated by the frontend will therefore point to:

```text
https://example.com/new-app-link
```

instead of the old URL.

### Recommended configuration flow

The complete data flow is:

```text
Admin Panel
    ↓
Application Settings
    ↓
Laravel Backend
    ↓
API Endpoint
    ↓
React Frontend
    ↓
Current App Store URL
    ↓
Dynamic QR Code
```

This architecture ensures that application download links and QR codes remain synchronized with the values managed by the administrator.

> **Note:** The exact API endpoint and database field names may vary depending on the current implementation. The important requirement is that the public React frontend retrieves the current application download URLs from the Laravel backend rather than storing fixed URLs directly in the frontend source code.

---

## 16. Running the Application

The application requires three terminal sessions.

### Terminal 1 – Laravel Backend

From the project root:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

The Laravel backend will be available at:

```text
http://localhost:8000
```

### Terminal 2 – React Frontend

Open a second terminal.

From the project root:

```bash
cd frontend
npm run dev -- --host
```

The React frontend will normally be available at:

```text
http://localhost:5173
```

### Terminal 3 – Laravel Scheduler

Open a third terminal.

From the project root:

```bash
php artisan schedule:work
```

The scheduler must remain running for scheduled application tasks to execute during local development.

---

## 17. Accessing the Application

Open the React frontend:

```text
http://localhost:5173
```

The Laravel backend runs separately at:

```text
http://localhost:8000
```

The React frontend communicates with the Laravel backend through the configured API endpoints.

---

## 18. Optional: Access from Other Devices with Tailscale

Tailscale can be used to access the locally running application from another device connected to the same Tailscale network.

Install Tailscale on:

* The computer running the application
* The mobile phone or other device used for testing

Sign in to the same Tailscale account on both devices.

Start the Laravel backend with:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Start the frontend with:

```bash
npm run dev -- --host
```

Find the Tailscale IP address of the computer.

It will usually look similar to:

```text
100.x.x.x
```

The frontend can then be accessed from another Tailscale-connected device using:

```text
http://100.x.x.x:5173
```

The Laravel backend will be available at:

```text
http://100.x.x.x:8000
```

When using a Tailscale IP instead of `localhost`, update the environment configuration accordingly.

For example:

```env
FRONTEND_URL=http://100.x.x.x:5173
```

You may also need to update:

```env
CORS_ALLOWED_ORIGINS=http://100.x.x.x:5173
```

and:

```env
SANCTUM_STATEFUL_DOMAINS=localhost:5173,localhost,127.0.0.1:5173,127.0.0.1,100.x.x.x:5173,100.x.x.x
```

The Google OAuth redirect URIs and reCAPTCHA configuration may also need to be adjusted when testing through a Tailscale address.

---

## 19. Security and Environment Files

The following files contain environment-specific configuration and must not be committed to GitHub:

```text
.env
frontend/.env
```

The following example files are safe to commit:

```text
.env.example
frontend/.env.example
```

Never commit:

* Google OAuth client secrets
* reCAPTCHA secret keys
* Database passwords
* Production API keys
* Other private credentials

The `.gitignore` file is configured to exclude sensitive environment files and generated dependencies.

---

## 20. Project Structure

A simplified project structure:

```text
csarda/
├── app/
├── bootstrap/
├── config/
├── database/
│   └── seeders/
│       └── gallery/
├── frontend/
│   ├── src/
│   ├── package.json
│   └── .env.example
├── public/
├── resources/
├── routes/
├── storage/
├── .env.example
├── composer.json
├── package.json
└── README.md
```

---

## 21. Author

**Árpád Perna**

GitHub:

https://github.com/Arpi47

This project was designed and developed as an individual full-stack web development project.

---

# License and Usage

This project is publicly available for portfolio, educational, demonstration, and technical evaluation purposes.

The source code and original project materials are the intellectual property of the author, Árpád Perna, unless otherwise stated for individual third-party components or assets.

## Permitted Use

You are permitted to:

- view and inspect the source code;
- download the project;
- install and run the project locally;
- test and evaluate the project;
- study the source code for educational purposes;
- use the project as a portfolio or technical reference;
- modify the project locally for personal learning and testing purposes.

## Restrictions

Without prior written permission from the author, you may not:

- use the project or its source code for commercial purposes;
- sell, license, rent, or otherwise monetize the project or substantial portions of its source code;
- redistribute the project or substantial portions of its source code as a commercial product;
- deploy the project, or a substantially similar derivative work, as a commercial production website or service;
- rebrand or repackage the project, or substantial portions of it, for commercial distribution;
- present the project or substantial portions of its source code as your own original work;
- remove or modify copyright, ownership, or licensing notices for the purpose of misrepresenting ownership.

Recruiters, employers, clients, developers, students, and other interested persons may freely view, download, install, and locally test the project for the purpose of evaluating its functionality, architecture, implementation, and technical quality.

Such evaluation does not grant permission to commercially use, redistribute, or resell the project.

## Commercial Licensing

The author reserves the right to use, modify, license, sell, commercialize, deploy, or otherwise distribute this project and its future versions as a commercial product.

Any person or organization wishing to use this project, or a substantial portion of its source code, for commercial purposes must obtain prior written permission or a separate commercial license from the author.

For commercial licensing, production deployment, redistribution, or other uses not explicitly permitted by the license, please contact the author.

For full terms and conditions, please refer to the [LICENSE](LICENSE) file.
