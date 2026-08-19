# Csárda Frontend

The `frontend` directory contains the public-facing React application of the Csárda restaurant web application.

It provides the modern, responsive interface used by restaurant guests and communicates with the Laravel backend through API requests.

The Laravel backend and administration panel are maintained separately from this React frontend.

---

## Overview

The Csárda frontend is built with **React** and **Vite** and provides the public user-facing part of the application.

The frontend is responsible for:

- Rendering the public website
- Navigation between pages
- User authentication interfaces
- User profile management
- Restaurant menu presentation
- Menu availability and access through QR codes
- Gallery presentation
- Online reservation interface
- Multilingual content
- Manual light and dark theme switching
- Responsive desktop and mobile layouts
- Mobile-specific interaction handling
- Page transitions and animations
- Communication with the Laravel backend API
- Displaying application download options and QR codes

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

- Node.js 22.x LTS or newer
- npm 10.x or newer

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
VITE_API_URL=http://localhost:8000
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
http://localhost:8000
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
http://localhost:8000
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
│ localhost:8000               │
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
- Call button
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

The public frontend supports light and dark visual themes.

Users can manually switch between the available themes according to their preference.

The public frontend does **not** automatically switch themes based on the time of day. Automatic time-based theme behavior is a feature of the Laravel administration interface and is not part of the public React frontend.

---

# User Profile

The React frontend provides users with access to their personal profile and account-related functionality.

Users can:

- View their profile information
- Manage available account settings
- Request account deletion
- Cancel an account deletion request when permitted

User profile images are **not uploaded or managed by users**.

Profile image management is handled exclusively through the Laravel administration panel by authorized administrators.

---

# Menu and QR Codes

The public frontend displays the restaurant's dynamically managed menu.

Menu content is provided by the Laravel backend and can include:

- Menu categories
- Menu items
- Multilingual content
- Prices
- Images

The application also supports QR-code-based access to the restaurant menu.

The Laravel administration panel can generate a QR code that points to the public restaurant menu page.

This functionality is intended to support a future restaurant use case where physical printed menus can be replaced or supplemented by QR codes placed on restaurant tables.

Guests can then scan the QR code with a mobile device and open the restaurant's online menu directly.

The QR code is generated and managed through the administration system, while the React frontend provides the destination page.

---

# Application Download and QR Codes

The application supports configurable mobile application download links.

Administrators can provide the appropriate:

- Google Play Store URL
- Apple App Store URL

The configured links are used by the React frontend to display the corresponding application download options.

The frontend can also display QR codes for the configured application download links.

This allows mobile users to scan the appropriate QR code and be directed to the corresponding application store.

The functionality is therefore divided between the two application parts:

```text
Laravel Admin Panel
        │
        │ Configure application store URLs
        ▼
Database
        │
        │ API
        ▼
React Frontend
        │
        ├── Google Play QR Code
        │
        └── Apple App Store QR Code
```

The QR codes are generated automatically on the frontend based on the configured store URLs.

---

# Kitchen Availability and Ordering

The public frontend takes the current kitchen operating status into account when presenting ordering-related actions.

The kitchen's availability is determined by the opening-hours configuration managed through the Laravel administration panel.

The frontend can determine whether:

- The kitchen is currently active
- The kitchen is currently closed
- The last order time has already passed

This status affects ordering-related interface elements.

### Mobile Call Button

On mobile devices, the call button is disabled when:

- The kitchen is currently closed, or
- The last available ordering time has already passed

This prevents users from being encouraged to place an order when the kitchen is no longer accepting orders.

### Navigation Ordering Option

The ordering-related option displayed in the navigation bar also checks the current kitchen availability.

If the kitchen is closed or the last ordering time has already passed, the frontend reflects that state instead of presenting ordering as currently available.

This functionality depends on the opening-hours and kitchen-hours data provided by the Laravel backend.

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
- Contact information
- Opening-hours information
- Kitchen availability
- Application download configuration
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
- Mobile-specific interaction handling

The responsive layout is designed and tested to work reliably down to a viewport width of approximately **320px**.

Viewports below **320px** are not officially supported and may experience layout or positioning issues.

The goal is to provide a consistent user experience across desktop computers, laptops, tablets, and smartphones within the supported viewport range.

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

## Ordering options are unavailable

If the ordering-related UI is disabled or unavailable, check:

1. Whether the kitchen is currently open.
2. Whether the configured last ordering time has already passed.
3. Whether the Laravel backend is returning the correct kitchen opening-hours data.
4. Whether the current day has the correct opening-hours configuration.
5. Whether special opening-hours rules are affecting the current day.

The ordering availability shown by the frontend depends on the data provided by the Laravel backend.

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
    ├── Menu QR Code Destination
    ├── Gallery
    ├── Application Download QR Codes
    ├── Kitchen Availability
    └── Responsive Design
```

For complete project installation instructions, backend configuration, database setup, authentication configuration, security considerations, Google Calendar integration, opening-hours management, and production deployment, refer to the main project documentation:

[`../README.md`](../README.md)

---

# Author

**Árpád Perna**

GitHub: **Arpi47**
