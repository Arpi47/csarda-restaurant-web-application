# Csárda Frontend

The `frontend` directory contains the public-facing React application of the Csárda restaurant web application.

It provides the modern, responsive interface used by restaurant guests and communicates with the Laravel backend through API requests.

The Laravel backend and administration panel are maintained separately from this React frontend.

---

## Overview

The Csárda frontend is built with **React** and **Vite** and provides the user-facing part of the application.

The frontend is responsible for:

- Rendering the public website
- Navigation between pages
- User authentication interfaces
- User profile management
- Restaurant menu presentation
- Gallery presentation
- Online reservation interface
- Multilingual content
- Light and dark theme support
- Responsive desktop and mobile layouts
- Page transitions and animations
- Communication with the Laravel backend API

The frontend does not directly access the database. All database operations and business logic are handled by the Laravel backend.

```text
User
 │
 ▼
React Frontend
 │
 │ HTTP / API Requests
 ▼
Laravel Backend
 │
 ▼
Database
```

---

# Technology Stack

The frontend is built using the following technologies:

- **React** – UI development
- **Vite** – development server and production build tool
- **JavaScript** – application logic
- **Tailwind CSS** – responsive styling and utility classes
- **React Router** – client-side routing
- **Framer Motion** – animations and page transitions
- **Lucide React** – interface icons
- **REST API** – communication with the Laravel backend

---

# Requirements

Before running the frontend, make sure the following are installed:

- Node.js
- npm

The recommended Node.js version should match the version used by the project during development.

You can check the installed versions with:

```bash
node -v
npm -v
```

---

# Installation

From the project root, enter the frontend directory:

```bash
cd frontend
```

Install the required dependencies:

```bash
npm install
```

After installation, the frontend is ready for development.

---

# Environment Configuration

The React frontend uses environment variables for configuration that may differ between development and production environments.

Create a `.env` file inside the `frontend` directory if one does not already exist.

Example:

```env
VITE_API_URL=http://127.0.0.1:8000
```

The exact environment variable names must match those used by the current frontend API configuration.

The API URL must point to the Laravel backend that provides the required API endpoints.

For example:

```text
React Frontend
http://localhost:5173
        │
        │ API requests
        ▼
Laravel Backend
http://127.0.0.1:8000
```

### Important

The frontend and backend URLs must be configured consistently.

Avoid unnecessarily mixing:

```text
localhost
```

and:

```text
127.0.0.1
```

during local development.

Although both addresses normally point to the same local computer, browsers treat them as different hosts. This can affect:

- Authentication
- Cookies
- Laravel Sanctum
- CORS
- API requests

For local development, it is recommended to use a consistent hostname throughout the configuration.

---

# Running the Development Server

Start the Vite development server from the `frontend` directory:

```bash
npm run dev
```

The frontend will normally be available at:

```text
http://localhost:5173
```

The Laravel backend must also be running for API-dependent functionality to work correctly.

Start the Laravel backend from the project root in a separate terminal:

```bash
php artisan serve
```

The backend will normally be available at:

```text
http://127.0.0.1:8000
```

A complete local development environment therefore consists of two running services:

```text
┌──────────────────────────────┐
│ React + Vite                 │
│ localhost:5173               │
│                              │
│ Public Website               │
└──────────────┬───────────────┘
               │
               │ REST API
               ▼
┌──────────────────────────────┐
│ Laravel                      │
│ 127.0.0.1:8000               │
│                              │
│ API + Authentication         │
│ Business Logic               │
│ Administration               │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│ MySQL / MariaDB              │
└──────────────────────────────┘
```

---

# Production Build

To create an optimized production build:

```bash
npm run build
```

The generated production files are placed in the Vite build output directory.

The production build should be deployed according to the hosting architecture of the complete Csárda application.

The production frontend must be configured to communicate with the production Laravel backend.

---

# Previewing the Production Build

After creating a production build, it can be locally previewed with:

```bash
npm run preview
```

This allows the production build to be tested before deployment.

---

# Project Structure

The main frontend structure is organized as follows:

```text
frontend/
│
├── public/
│   └── ...
│
├── src/
│   ├── api/
│   │   ├── client.js
│   │   └── ...
│   │
│   ├── components/
│   │   ├── common/
│   │   ├── layout/
│   │   └── ...
│   │
│   ├── contexts/
│   │   ├── AuthContext.jsx
│   │   ├── LanguageContext.jsx
│   │   └── ...
│   │
│   ├── layouts/
│   │   └── MainLayout.jsx
│   │
│   ├── pages/
│   │   ├── Home.jsx
│   │   ├── Menu.jsx
│   │   ├── Gallery.jsx
│   │   ├── About.jsx
│   │   ├── Contact.jsx
│   │   ├── Login.jsx
│   │   ├── Profile.jsx
│   │   ├── Reservation.jsx
│   │   └── ...
│   │
│   ├── App.jsx
│   ├── AppRoutes.jsx
│   ├── main.jsx
│   └── index.css
│
├── .env
├── package.json
├── package-lock.json
├── vite.config.js
└── README.md
```

The exact structure may change as the application evolves.

---

# Main Application Areas

## Pages

The `pages` directory contains the main user-facing application pages.

Examples include:

- Home
- Menu
- Gallery
- About
- Contact
- Login
- User Profile
- Reservation

Pages are responsible for composing the appropriate components and presenting the application's main content.

---

## Components

Reusable UI elements are stored in the `components` directory.

Examples include:

- Navigation
- Footer
- Page headers
- Theme switcher
- Scroll-to-top button
- Other reusable interface elements

The goal is to avoid duplicating the same UI logic across multiple pages.

---

## Layouts

The main application layout defines common elements shared across pages.

The main layout can include:

- Navigation
- Main content area
- Footer
- Global interface elements

This allows individual pages to focus on their own content instead of repeatedly defining the complete website structure.

---

## Contexts

React Context is used for application-wide state and functionality.

Examples include:

### Authentication Context

Responsible for managing authentication-related frontend state and user information.

### Language Context

Responsible for:

- Current language selection
- Translated interface text
- Language switching

Supported languages include:

- English
- Hungarian
- Serbian Latin
- Serbian Cyrillic

### Theme

The frontend supports light and dark visual themes.

The theme can be controlled by the application's theme system and user preference.

---

# API Communication

The React frontend communicates with the Laravel backend through API requests.

The API client is located in:

```text
src/api/
```

The frontend uses the Laravel backend for operations such as:

- Authentication
- User data
- Menu data
- Menu categories
- Gallery data
- Reservations
- Profile management
- Other dynamic content

The frontend should not contain database credentials or directly connect to the database.

All sensitive operations must be processed and validated by the Laravel backend.

---

# Authentication

Authentication is handled by the Laravel backend.

The React frontend provides the user interface for:

- Registration
- Login
- Logout
- Authentication state
- Profile management

Laravel Sanctum is used as part of the authentication architecture.

Correct configuration of the following is required:

- API URL
- CORS
- Sanctum stateful domains
- Session configuration
- Cookies
- Credentials

Authentication issues during local development are often caused by inconsistent hostnames such as:

```text
localhost
```

versus:

```text
127.0.0.1
```

The frontend and Laravel backend configuration should therefore use consistent URLs.

---

# Multilingual Support

The application supports four language variants:

```text
English
Hungarian
Serbian Latin
Serbian Cyrillic
```

The language system is handled on the frontend through the language context and translation resources.

The selected language affects the user interface and localized content presented to the user.

Where content is stored in the database, the frontend receives the appropriate localized data from the Laravel API.

---

# Responsive Design

The frontend is designed to work across different screen sizes.

## Desktop

On desktop and laptop screens, the interface provides:

- Wide content layouts
- Full navigation
- Multi-column sections where appropriate
- Larger visual elements
- Mouse and keyboard interaction

## Mobile

On mobile devices, the layout automatically adapts to smaller screens.

The interface uses:

- Responsive layouts
- Stacked content
- Mobile-friendly navigation
- Touch-friendly buttons
- Flexible images
- Responsive typography
- Mobile-friendly forms

The goal is to provide a consistent user experience regardless of whether the website is accessed from a desktop computer, laptop, tablet, or smartphone.

---

# Animations and Visual Effects

The frontend uses **Framer Motion** for animations and transitions.

Animations are used for features such as:

- Page transitions
- Element entrance animations
- Gallery interactions
- Visual feedback
- Parallax effects

Animations should enhance the user experience without preventing users from accessing or using the application's functionality.

---

# Styling

The frontend uses **Tailwind CSS** for styling.

The project includes:

- Responsive utility classes
- Custom theme variables
- Light and dark theme support
- Responsive breakpoints
- Mobile-specific layout adjustments

The design is implemented with a responsive-first approach where components adapt to different viewport sizes.

---

# Icons

The project uses **Lucide React** for interface icons.

Icons are used throughout the application for actions and navigation elements while maintaining a consistent visual style.

---

# Troubleshooting

## Frontend cannot connect to the backend

Check:

1. Is Laravel running?
2. Is the API URL correct?
3. Is the Laravel backend accessible?
4. Is CORS configured correctly?
5. Is the frontend using the correct hostname?
6. Are `localhost` and `127.0.0.1` being mixed?

---

## Authentication does not work

Check:

- Laravel Sanctum configuration
- CORS configuration
- Stateful domains
- Session configuration
- Cookie settings
- Frontend API URL
- Backend URL

Also make sure the frontend and backend use consistent hostnames.

---

## Images are not displayed

Check:

- The image URL returned by the API
- Laravel storage configuration
- `php artisan storage:link`
- Public file permissions
- The configured backend asset URL
- Browser cache
- Whether the image is accessible directly from the browser

If the website works in one browser but not another, test the browser cache and privacy/shield settings as well.

---

## Changes are not visible

During development:

1. Restart the Vite development server if necessary.
2. Check browser cache.
3. Verify that the correct `.env` file is being used.
4. Restart Vite after changing environment variables.

Vite environment variables are loaded when the development server starts, so changing `.env` usually requires restarting the Vite server.

---

# Development Notes

The frontend is part of the larger Csárda full-stack application.

The complete application consists of:

```text
Csárda
│
├── Laravel Backend
│   ├── REST API
│   ├── Authentication
│   ├── Business Logic
│   ├── Database
│   └── Admin Panel
│
└── React Frontend
    ├── Public Website
    ├── User Interface
    ├── Authentication UI
    ├── Reservations
    ├── Menu
    ├── Gallery
    └── Responsive Design
```

For complete project installation instructions, backend configuration, database setup, authentication configuration, security considerations, and production deployment, refer to the main project documentation:

[`../README.md`](../README.md)

---

# Author

**Árpád Perna**

GitHub: **Arpi47**
