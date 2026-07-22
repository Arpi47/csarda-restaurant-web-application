# Csárda – Multilingual Restaurant Web Application

**Author:** Árpád Perna
**GitHub:** https://github.com/Arpi47

A full-stack multilingual restaurant web application built with Laravel and React. The application provides a modern restaurant website with online reservations, user authentication, Google OAuth integration, reCAPTCHA protection, dynamic menu and gallery management, and a dedicated administration system.

---

# Table of Contents

* [English](#english)
* [Magyar](#magyar)
* [Srpski](#srpski)

---

# English

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

## 15. Running the Application

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

## 16. Accessing the Application

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

## 17. Optional: Access from Other Devices with Tailscale

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

## 18. Security and Environment Files

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

## 19. Project Structure

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

## 20. Author

**Árpád Perna**

GitHub:

https://github.com/Arpi47

This project was designed and developed as an individual full-stack web development project.

---

# Magyar

## 1. A projektről

A Csárda egy többnyelvű, full-stack éttermi webalkalmazás, amely egyéni szoftverfejlesztési projektként készült.

Az alkalmazás részei:

* Laravel backend
* React frontend Vite használatával
* MySQL adatbázis
* Felhasználói hitelesítés
* Google OAuth bejelentkezés
* Google-fiók összekapcsolása
* Google reCAPTCHA védelem
* Elfelejtett jelszó és jelszó-visszaállítás
* Regisztrációs e-mail-cím megerősítése
* Online asztalfoglalás
* Dinamikus étlap
* Éttermi galéria
* Többnyelvű tartalom
* Felhasználói profilkezelés
* Adminisztrációs felület
* Super Admin funkciók
* Foglalások kezelése
* Laravel időzített feladatok

Támogatott nyelvek:

* Angol
* Magyar
* Szerb latin
* Szerb cirill

---

## 2. Technológiák

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

### Külső szolgáltatások

* Google OAuth
* Google reCAPTCHA v3

---

## 3. Rendszerkövetelmények

A projekt telepítéséhez szükséges:

* PHP 8.2 vagy újabb
* Composer
* Node.js és npm
* MySQL
* Git

### macOS

macOS rendszeren használható például a MAMP helyi PHP és MySQL környezetként.

Ajánlott:

* MAMP
* Composer
* Node.js
* npm
* Git

### Windows

Használható például:

* XAMPP vagy MAMP for Windows
* Composer
* Node.js
* npm
* Git

### Linux

PHP és MySQL a Linux disztribúció csomagkezelőjével telepíthető.

Ubuntu esetén például:

```bash
sudo apt update
sudo apt install php php-cli php-mysql php-mbstring php-xml php-curl php-zip mysql-server unzip
```

A Composer és Node.js telepítését az adott Linux disztribúció hivatalos útmutatója szerint kell elvégezni.

---

## 4. Repository klónozása

```bash
git clone https://github.com/Arpi47/csarda-restaurant-web-application.git
```

Lépj be a projekt könyvtárába:

```bash
cd csarda-restaurant-web-application
```

---

## 5. Backend telepítése

PHP függőségek telepítése:

```bash
composer install
```

Másold át az `.env.example` fájlt `.env` néven:

```bash
cp .env.example .env
```

Windows esetén, ha a `cp` parancs nem érhető el, manuálisan másold az `.env.example` fájlt `.env` néven.

Generáld az alkalmazáskulcsot:

```bash
php artisan key:generate
```

---

## 6. Adatbázis konfigurációja

Az alkalmazás MySQL adatbázist használ.

Hozz létre egy:

```text
csarda
```

nevű adatbázist.

Ezután állítsd be a `.env` fájlban az adatbázis adatait.

Például:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csarda
DB_USERNAME=root
DB_PASSWORD=
```

### macOS és MAMP

A MAMP alapértelmezett MySQL portja gyakran:

```text
8889
```

Ebben az esetben:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=csarda
DB_USERNAME=root
DB_PASSWORD=root
```

Ha a saját MySQL telepítésed más portot vagy jelszót használ, módosítsd a `.env` fájlt.

---

## 7. Környezeti változók konfigurációja

A repository tartalmazza az:

```text
.env.example
```

fájlt.

Másold át:

```text
.env
```

néven.

A teljes működéshez a következő értékeket kell beállítani:

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

---

## 8. Frontend környezeti változók

Lépj be:

```bash
cd frontend
```

Másold az:

```text
frontend/.env.example
```

fájlt:

```text
frontend/.env
```

néven.

Az alapértelmezett konfiguráció:

```env
VITE_API_URL=http://localhost:8000/api
VITE_BACKEND_URL=http://localhost:8000
VITE_ASSET_URL=http://localhost:8000
VITE_STORAGE_URL=http://localhost:8000/storage
VITE_RECAPTCHA_SITE_KEY=
```

A reCAPTCHA site key hozzáadása:

```env
VITE_RECAPTCHA_SITE_KEY=YOUR_RECAPTCHA_SITE_KEY
```

Ezután:

```bash
cd ..
```

---

## 9. Google reCAPTCHA konfiguráció

Az alkalmazás Google reCAPTCHA v3-at használ.

A reCAPTCHA szükséges többek között:

* Felhasználói regisztrációhoz
* Asztalfoglaláshoz
* Jelszó-visszaállítási kérelemhez

A Google reCAPTCHA adminisztrációs felületén hozz létre új kulcsot.

Válaszd:

```text
reCAPTCHA v3
```

Helyi teszteléshez add hozzá:

```text
localhost
127.0.0.1
```

A backend `.env` fájljában:

```env
RECAPTCHA_SECRET_KEY=YOUR_SECRET_KEY
RECAPTCHA_SITE_KEY=YOUR_SITE_KEY
```

A `frontend/.env` fájlban:

```env
VITE_RECAPTCHA_SITE_KEY=YOUR_SITE_KEY
```

A site key a frontendhez, a secret key a Laravel backendhez szükséges.

> **Fontos:** A `RECAPTCHA_SECRET_KEY` titkos kulcsot soha ne töltsd fel GitHubra.

Ha Tailscale vagy más külső cím használatával teszteled az alkalmazást, előfordulhat, hogy a használt címet külön hozzá kell adni a reCAPTCHA konfigurációhoz.

---

## 10. Google OAuth konfiguráció

Az alkalmazás támogatja:

* Google bejelentkezést
* Google-fiók összekapcsolását a felhasználói profilból

Hozz létre Google OAuth hitelesítő adatokat egy Web Application számára.

A következő redirect URI-kat kell megadni:

```text
http://localhost:8000/auth/google/callback
```

és:

```text
http://localhost:8000/auth/google/link/callback
```

A `.env` fájlban:

```env
GOOGLE_CLIENT_ID=YOUR_CLIENT_ID
GOOGLE_CLIENT_SECRET=YOUR_CLIENT_SECRET
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
GOOGLE_LINK_REDIRECT_URI=http://localhost:8000/auth/google/link/callback
```

A frontend címe:

```env
FRONTEND_URL=http://localhost:5173
```

> **Fontos:** A `GOOGLE_CLIENT_SECRET` titkos érték, ezért soha ne töltsd fel GitHubra.

---

## 11. Frontend függőségek telepítése

```bash
cd frontend
npm install
```

Majd:

```bash
cd ..
```

---

## 12. Adatbázis és seederek

Migrációk futtatása:

```bash
php artisan migrate
```

Seederek futtatása:

```bash
php artisan db:seed
```

A `DatabaseSeeder` a következő seedereket futtatja:

* `SuperAdminSeeder`
* `AdminSeeder`
* `UserSeeder`
* `CategorySeeder`
* `MenuSeeder`
* `GalleryImageSeeder`

A galéria képei innen kerülnek beolvasásra:

```text
database/seeders/gallery/
```

A képek:

```text
gallery_1.png
gallery_2.png
gallery_3.png
gallery_4.png
```

automatikusan a következő helyre kerülnek:

```text
public/images/gallery/
```

---

## 13. Előre létrehozott tesztfelhasználók

### Normál felhasználó

```text
Email: user1@example.com
Jelszó: password123
```

### Második normál felhasználó

```text
Email: user2@example.com
Jelszó: password123
```

### Adminisztrátor

```text
Email: admin@example.com
Jelszó: strongpassword
```

### Super Admin

```text
Email: superadmin@gmail.com
Jelszó: superadmin123
```

A hitelesítő adatok a következő seederekben találhatók:

```text
database/seeders/UserSeeder.php
database/seeders/AdminSeeder.php
database/seeders/SuperAdminSeeder.php
```

> **Biztonsági megjegyzés:** Ezek a fiókok kizárólag helyi fejlesztéshez és teszteléshez használhatók. Éles környezetben meg kell változtatni a jelszavakat.

---

## 14. E-mail konfiguráció helyi teszteléshez

Az alapértelmezett konfiguráció a Laravel `log` mail driverét használja:

```env
MAIL_MAILER=log
```

Helyi teszteléshez nincs szükség külső SMTP szerverre.

Az alkalmazás többek között az alábbi funkciókhoz generál e-maileket:

* Regisztrációs e-mail-cím megerősítése
* Elfelejtett jelszó / jelszó-visszaállítás
* Egyéb e-mailt generáló funkciók

A generált e-mailek ide kerülnek:

```text
storage/logs/laravel.log
```

> **Fontos:** Az alapértelmezett helyi konfigurációban az e-mailek nem kerülnek ténylegesen elküldésre a felhasználó e-mail-címére. A generált e-mail tartalma és a benne található linkek a `storage/logs/laravel.log` fájlban találhatók.

### Regisztráció megerősítése

Regisztráció után nyisd meg:

```text
storage/logs/laravel.log
```

Keresd meg a legutóbb generált e-mailt, majd másold ki belőle a regisztráció megerősítéséhez szükséges linket.

Nyisd meg a linket a böngészőben.

### Elfelejtett jelszó

A jelszó-visszaállítási űrlap elküldése után nyisd meg:

```text
storage/logs/laravel.log
```

Keresd meg a generált jelszó-visszaállító e-mailt, majd másold ki a benne található linket a böngészőbe.

Ugyanez a folyamat vonatkozik minden más olyan funkcióra, amely e-mailt generál.

---

## 15. Az alkalmazás futtatása

Az alkalmazás futtatásához három terminál szükséges.

### 1. terminál – Laravel backend

A projekt gyökeréből:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Backend:

```text
http://localhost:8000
```

### 2. terminál – React frontend

```bash
cd frontend
npm run dev -- --host
```

Frontend:

```text
http://localhost:5173
```

### 3. terminál – Laravel Scheduler

A projekt gyökeréből:

```bash
php artisan schedule:work
```

Az időzített feladatok helyi futtatásához a schedulernek futnia kell.

---

## 16. Az alkalmazás elérése

A React frontend:

```text
http://localhost:5173
```

A Laravel backend:

```text
http://localhost:8000
```

A frontend a Laravel backend API-ján keresztül kommunikál.

---

## 17. Opcionális: elérés más eszközről Tailscale használatával

A Tailscale segítségével az alkalmazás elérhetővé tehető egy másik, ugyanahhoz a Tailscale hálózathoz csatlakoztatott eszközről.

Telepítsd a Tailscale-t:

* a Laravel és React alkalmazást futtató számítógépre,
* valamint a teszteléshez használt telefonra vagy más eszközre.

Jelentkezz be ugyanabba a Tailscale-fiókba mindkét eszközön.

Indítsd a Laravel backendet:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Indítsd a React frontendet:

```bash
npm run dev -- --host
```

Keresd meg a számítógép Tailscale IP-címét.

Ez általában például ilyen:

```text
100.x.x.x
```

A frontend elérhető:

```text
http://100.x.x.x:5173
```

A backend:

```text
http://100.x.x.x:8000
```

A konfigurációt hozzá kell igazítani a Tailscale IP-címhez.

Például:

```env
FRONTEND_URL=http://100.x.x.x:5173
```

Továbbá:

```env
CORS_ALLOWED_ORIGINS=http://100.x.x.x:5173
```

és:

```env
SANCTUM_STATEFUL_DOMAINS=localhost:5173,localhost,127.0.0.1:5173,127.0.0.1,100.x.x.x:5173,100.x.x.x
```

Google OAuth és reCAPTCHA használata esetén a Tailscale címet ezek konfigurációjában is szükség lehet hozzáadni.

---

## 18. Biztonság és környezeti fájlok

A következő fájlokat nem szabad GitHubra feltölteni:

```text
.env
frontend/.env
```

A következő fájlok biztonságosan feltölthetők:

```text
.env.example
frontend/.env.example
```

Soha ne tölts fel GitHubra:

* Google OAuth Client Secret
* reCAPTCHA Secret Key
* Adatbázis-jelszavak
* Éles API kulcsok
* Egyéb titkos hitelesítési adatokat

A `.gitignore` kizárja a titkos környezeti fájlokat és a generált függőségeket.

---

## 19. Projektstruktúra

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

## 20. Szerző

**Perna Árpád**

GitHub:

https://github.com/Arpi47

A projekt egyéni full-stack webfejlesztési projektként került megtervezésre és megvalósításra.

---

# Srpski

## 1. O projektu

Csárda je višejezička full-stack web aplikacija za restoran, razvijena kao samostalni softverski projekat.

Aplikacija sadrži:

* Laravel backend
* React frontend uz Vite
* MySQL bazu podataka
* Autentifikaciju korisnika
* Google OAuth prijavljivanje
* Povezivanje Google naloga
* Google reCAPTCHA zaštitu
* Resetovanje zaboravljene lozinke
* Potvrdu e-mail adrese nakon registracije
* Online rezervacije stolova
* Dinamički meni restorana
* Galeriju restorana
* Višejezični sadržaj
* Upravljanje korisničkim profilom
* Administrativni panel
* Funkcionalnosti Super Admin naloga
* Upravljanje rezervacijama
* Zakazane Laravel zadatke

Podržani jezici:

* Engleski
* Mađarski
* Srpski latinica
* Srpski ćirilica

---

## 2. Tehnologije

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

### Spoljne usluge

* Google OAuth
* Google reCAPTCHA v3

---

## 3. Sistemski zahtevi

Potrebni su:

* PHP 8.2 ili noviji
* Composer
* Node.js i npm
* MySQL
* Git

### macOS

Na macOS-u može se koristiti MAMP kao lokalno PHP i MySQL okruženje.

Preporučeno:

* MAMP
* Composer
* Node.js
* npm
* Git

### Windows

Mogu se koristiti:

* XAMPP ili MAMP for Windows
* Composer
* Node.js
* npm
* Git

### Linux

PHP i MySQL mogu se instalirati pomoću package managera odgovarajuće Linux distribucije.

Na primer, na Ubuntu sistemu:

```bash
sudo apt update
sudo apt install php php-cli php-mysql php-mbstring php-xml php-curl php-zip mysql-server unzip
```

Composer i Node.js treba instalirati prema zvaničnim uputstvima za korišćenu Linux distribuciju.

---

## 4. Kloniranje repozitorijuma

```bash
git clone https://github.com/Arpi47/csarda-restaurant-web-application.git
```

Uđite u direktorijum projekta:

```bash
cd csarda-restaurant-web-application
```

---

## 5. Instalacija backenda

Instalirajte PHP zavisnosti:

```bash
composer install
```

Kopirajte:

```text
.env.example
```

u:

```text
.env
```

Na primer:

```bash
cp .env.example .env
```

Na Windows sistemu, ukoliko komanda `cp` nije dostupna, ručno kopirajte `.env.example` i preimenujte ga u `.env`.

Generišite Laravel application key:

```bash
php artisan key:generate
```

---

## 6. Konfiguracija baze podataka

Aplikacija koristi MySQL.

Kreirajte bazu podataka:

```text
csarda
```

Zatim podesite `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csarda
DB_USERNAME=root
DB_PASSWORD=
```

### macOS i MAMP

MAMP često koristi MySQL port:

```text
8889
```

U tom slučaju:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=csarda
DB_USERNAME=root
DB_PASSWORD=root
```

Ako lokalna MySQL instalacija koristi drugačiji port ili lozinku, potrebno je prilagoditi `.env`.

---

## 7. Konfiguracija environment promenljivih

Repository sadrži:

```text
.env.example
```

Kopirajte ga u:

```text
.env
```

Za potpunu funkcionalnost potrebno je podesiti:

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

Vrednosti zavise od lokalnog okruženja.

---

## 8. Frontend environment konfiguracija

Uđite u frontend:

```bash
cd frontend
```

Kopirajte:

```text
frontend/.env.example
```

u:

```text
frontend/.env
```

Podrazumevana konfiguracija:

```env
VITE_API_URL=http://localhost:8000/api
VITE_BACKEND_URL=http://localhost:8000
VITE_ASSET_URL=http://localhost:8000
VITE_STORAGE_URL=http://localhost:8000/storage
VITE_RECAPTCHA_SITE_KEY=
```

Dodajte reCAPTCHA site key:

```env
VITE_RECAPTCHA_SITE_KEY=YOUR_RECAPTCHA_SITE_KEY
```

Zatim:

```bash
cd ..
```

---

## 9. Google reCAPTCHA konfiguracija

Aplikacija koristi Google reCAPTCHA v3.

reCAPTCHA je potrebna za funkcionalnosti kao što su:

* Registracija korisnika
* Rezervacija stola
* Zahtev za resetovanje lozinke

Kreirajte novi reCAPTCHA ključ u Google reCAPTCHA administracionoj konzoli.

Izaberite:

```text
reCAPTCHA v3
```

Za lokalno testiranje dodajte:

```text
localhost
127.0.0.1
```

U backend `.env`:

```env
RECAPTCHA_SECRET_KEY=YOUR_SECRET_KEY
RECAPTCHA_SITE_KEY=YOUR_SITE_KEY
```

U `frontend/.env`:

```env
VITE_RECAPTCHA_SITE_KEY=YOUR_SITE_KEY
```

Site key koristi frontend, dok secret key koristi Laravel backend.

> **Važno:** `RECAPTCHA_SECRET_KEY` nikada ne treba javno objavljivati niti postavljati na GitHub.

Ako se aplikacija testira preko Tailscale-a ili druge adrese, korišćena adresa možda mora biti dodata u reCAPTCHA konfiguraciju.

---

## 10. Google OAuth konfiguracija

Aplikacija podržava:

* Prijavljivanje pomoću Google naloga
* Povezivanje Google naloga sa korisničkim profilom

Potrebno je kreirati Google OAuth kredencijale za Web Application.

Dodajte sledeće redirect URI adrese:

```text
http://localhost:8000/auth/google/callback
```

i:

```text
http://localhost:8000/auth/google/link/callback
```

U `.env`:

```env
GOOGLE_CLIENT_ID=YOUR_CLIENT_ID
GOOGLE_CLIENT_SECRET=YOUR_CLIENT_SECRET
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
GOOGLE_LINK_REDIRECT_URI=http://localhost:8000/auth/google/link/callback
```

Frontend adresa:

```env
FRONTEND_URL=http://localhost:5173
```

> **Važno:** `GOOGLE_CLIENT_SECRET` je tajna vrednost i nikada ne treba da bude postavljena na GitHub.

---

## 11. Instalacija frontend zavisnosti

```bash
cd frontend
npm install
```

Zatim:

```bash
cd ..
```

---

## 12. Baza podataka i seederi

Pokrenite migracije:

```bash
php artisan migrate
```

Pokrenite seedere:

```bash
php artisan db:seed
```

`DatabaseSeeder` pokreće:

* `SuperAdminSeeder`
* `AdminSeeder`
* `UserSeeder`
* `CategorySeeder`
* `MenuSeeder`
* `GalleryImageSeeder`

Galerijske slike se nalaze u:

```text
database/seeders/gallery/
```

Uključene slike:

```text
gallery_1.png
gallery_2.png
gallery_3.png
gallery_4.png
```

Seeder ih automatski kopira u:

```text
public/images/gallery/
```

---

## 13. Testni korisnički nalozi

### Običan korisnik

```text
Email: user1@example.com
Lozinka: password123
```

### Drugi običan korisnik

```text
Email: user2@example.com
Lozinka: password123
```

### Administrator

```text
Email: admin@example.com
Lozinka: strongpassword
```

### Super Admin

```text
Email: superadmin@gmail.com
Lozinka: superadmin123
```

Podaci se nalaze u:

```text
database/seeders/UserSeeder.php
database/seeders/AdminSeeder.php
database/seeders/SuperAdminSeeder.php
```

> **Bezbednosna napomena:** Ovi nalozi namenjeni su isključivo lokalnom razvoju i testiranju. Lozinke treba promeniti pre korišćenja aplikacije u produkciji.

---

## 14. Konfiguracija e-pošte za lokalno testiranje

Podrazumevana konfiguracija koristi Laravel `log` mail driver:

```env
MAIL_MAILER=log
```

Za lokalno testiranje nije potreban eksterni SMTP server.

Aplikacija generiše e-poruke za funkcionalnosti kao što su:

* Potvrda e-mail adrese nakon registracije
* Resetovanje zaboravljene lozinke
* Druge funkcionalnosti koje generišu e-poruke

Generisane e-poruke se upisuju u:

```text
storage/logs/laravel.log
```

> **Važno:** U podrazumevanoj lokalnoj konfiguraciji e-poruke se ne šalju stvarno na e-mail adresu korisnika. Umesto toga, sadržaj generisane e-poruke i linkovi upisuju se u `storage/logs/laravel.log`.

### Potvrda registracije

Nakon registracije otvorite:

```text
storage/logs/laravel.log
```

Pronađite poslednju generisanu e-poruku i kopirajte link za potvrdu registracije.

Otvorite link u internet pregledaču.

### Resetovanje lozinke

Nakon slanja zahteva za resetovanje lozinke otvorite:

```text
storage/logs/laravel.log
```

Pronađite generisanu e-poruku za resetovanje lozinke i kopirajte link u internet pregledač.

Isti postupak važi i za druge funkcionalnosti koje generišu e-poruke.

---

## 15. Pokretanje aplikacije

Za pokretanje aplikacije potrebna su tri terminala.

### Terminal 1 – Laravel backend

Iz glavnog direktorijuma projekta:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Backend:

```text
http://localhost:8000
```

### Terminal 2 – React frontend

```bash
cd frontend
npm run dev -- --host
```

Frontend:

```text
http://localhost:5173
```

### Terminal 3 – Laravel Scheduler

Iz glavnog direktorijuma projekta:

```bash
php artisan schedule:work
```

Scheduler mora ostati pokrenut kako bi zakazani zadaci mogli da se izvršavaju tokom lokalnog razvoja.

---

## 16. Pristup aplikaciji

React frontend:

```text
http://localhost:5173
```

Laravel backend:

```text
http://localhost:8000
```

Frontend komunicira sa Laravel backendom preko API-ja.

---

## 17. Opciono: pristup preko Tailscale-a

Tailscale omogućava pristup lokalno pokrenutoj aplikaciji sa drugog uređaja koji je povezan na istu Tailscale mrežu.

Instalirajte Tailscale na:

* računar na kojem se aplikacija pokreće,
* telefon ili drugi uređaj koji se koristi za testiranje.

Prijavite se na isti Tailscale nalog na oba uređaja.

Pokrenite Laravel:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Pokrenite frontend:

```bash
npm run dev -- --host
```

Pronađite Tailscale IP adresu računara.

Primer:

```text
100.x.x.x
```

Frontend:

```text
http://100.x.x.x:5173
```

Backend:

```text
http://100.x.x.x:8000
```

Konfiguracija treba da bude prilagođena Tailscale IP adresi.

Na primer:

```env
FRONTEND_URL=http://100.x.x.x:5173
```

Takođe:

```env
CORS_ALLOWED_ORIGINS=http://100.x.x.x:5173
```

i:

```env
SANCTUM_STATEFUL_DOMAINS=localhost:5173,localhost,127.0.0.1:5173,127.0.0.1,100.x.x.x:5173,100.x.x.x
```

Kod korišćenja Google OAuth-a i reCAPTCHA-e, Tailscale adresa možda mora biti dodatno podešena i u njihovim konfiguracijama.

---

## 18. Bezbednost i environment fajlovi

Sledeći fajlovi ne smeju biti postavljeni na GitHub:

```text
.env
frontend/.env
```

Sledeći fajlovi treba da budu deo repository-ja:

```text
.env.example
frontend/.env.example
```

Nikada ne postavljajte na GitHub:

* Google OAuth Client Secret
* reCAPTCHA Secret Key
* Lozinke baze podataka
* Produkcione API ključeve
* Druge tajne pristupne podatke

`.gitignore` je podešen tako da isključuje osetljive environment fajlove i generisane zavisnosti.

---

## 19. Struktura projekta

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

## 20. Autor

**Árpád Perna**

GitHub:

https://github.com/Arpi47

Projekat je samostalno osmišljen i razvijen kao full-stack web development projekat.

