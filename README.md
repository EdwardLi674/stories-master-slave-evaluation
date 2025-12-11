# Project Name

This project is a full-stack application with **React (Vite)** as the frontend, **Laravel** as the backend, and **MySQL** as the database. The database is configured with **master-slave replication**, and Laravel migrations are used to create and manage database tables.
The backend uses a master-slave database setup. **The core functionality** of this project is to replicate tables from the master to the slave database.

---

## Table of Contents

- [Prerequisites](#prerequisites)
- [Installation](#installation)

  - [Frontend (React + Vite)](#frontend-react--vite)
  - [Backend (Laravel)](#backend-laravel)

- [Database Setup](#database-setup)
- [Running the Application](#running-the-application)
- [Additional Notes](#additional-notes)
- [License](#license)

---

## Prerequisites

Before starting, ensure you have the following installed:

- Node.js >= 18
- npm or yarn
- PHP >= 8.1
- Composer
- MySQL
- Git

---

## Installation

### Frontend (React + Vite)

1. Navigate to the frontend folder:

```bash
cd frontend
```

2. Install dependencies:

```bash
npm install
# or
yarn install
```

3. Start the development server:

```bash
npm run dev
# or
yarn dev
```

The React application will be available at [http://localhost:5173](http://localhost:5173).

---

### Backend (Laravel)

1. Navigate to the backend folder:

```bash
cd backend
```

2. Install PHP dependencies:

```bash
composer install
```

---

## Database Setup

1. Create the master and slave databases in MySQL.

2. Run Laravel migrations to create the `story` table and other necessary tables:

```bash
php artisan migrate
```

---

## Running the Application

1. Start the Laravel backend server:

```bash
php artisan serve
```

The backend will be available at [http://127.0.0.1:8000](http://127.0.0.1:8000).

2. Start the React frontend (if not already running):

```bash
cd frontend
npm run dev
```

Access the full application at the frontend URL.

---

## Additional Notes

- For production, configure a web server (Nginx/Apache) and build the frontend using:

```bash
npm run build
```

- To refresh the database and rerun migrations:

```bash
php artisan migrate:fresh
```

- To seed the database (if seeders are available):

```bash
php artisan db:seed
```

- Ensure MySQL master-slave replication is properly configured for your environment.

- If using Vite + Laravel during development, you may configure a proxy in `vite.config.js` to avoid CORS issues:

```js
export default defineConfig({
  server: {
    proxy: {
      "/api": "http://127.0.0.1:8000",
    },
  },
});
```

---

## License

This project is licensed under the MIT License.
