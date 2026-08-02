# RailInfo

This repository contains the **RailInfo** application — a train schedule checker and PNR status tracker portal, built with cache-first lookups so results stay fast and reliable even when the upstream railway API is slow or unavailable.

**Live Demo:** [https://railinfo-priyanka.ct.ws/](https://railinfo-priyanka.ct.ws/)

## Project Structure

- `app/` - CodeIgniter 4 application code (Controllers, Models, Views, Config, Libraries).
- `public/` - Web root. Contains the front controller (`index.php`), `.htaccess`, and static assets (CSS/JS).
- `database/` - `schema.sql` with full table definitions and seed data.
- `writable/` - Logs, cache, and session storage (must be writable by the server).

## Setup Instructions

### Backend (CodeIgniter 4 / PHP)

1. Navigate to the project folder:
   ```
   cd railinfo
   ```
2. Install dependencies:
   ```
   composer install
   ```
3. Copy `env` (if present) to `.env` and configure values like `database.default.hostname`, `database.default.database`, `database.default.username`, `database.default.password`, and `railapi.baseURL` / `railapi.key`.
4. Create the database and import the schema:
   ```
   mysql -u root -p -e "CREATE DATABASE railinfo CHARACTER SET utf8mb4"
   mysql -u root -p railinfo < database/schema.sql
   ```
5. Run the server:
   ```
   php spark serve
   ```
6. The app will be available at `http://localhost:8080`.

### Frontend

The frontend is not a separate application — it's server-rendered directly by CodeIgniter using PHP views (`app/Views/`) styled with Bootstrap 5 and vanilla JS (`public/assets/`). No separate install or build step is required; it runs automatically with the backend server above.

## Building for Production

- No separate frontend build step is needed since views are rendered server-side.
- Set `CI_ENVIRONMENT = production` in `.env` before deploying.
- Ensure `writable/` and its subfolders have proper write permissions (typically `755` or `775`).

## Deployment

RailInfo is deployed on Hostinger shared hosting and is live at **[https://railinfo-priyanka.ct.ws/](https://railinfo-priyanka.ct.ws/)**.

General deployment steps:

1. Create the MySQL database via your hosting control panel and note the credentials.
2. Import `database/schema.sql` via phpMyAdmin or the MySQL CLI.
3. Run `composer install` locally, then upload the full project (including the generated `vendor/` folder) via FTP/File Manager.
4. Rename `env` to `.env`, set `CI_ENVIRONMENT = production`, set `app.baseURL` to your live domain, and fill in your database credentials.
5. Point the domain's document root at `public/` (or rely on the included root `.htaccess` to rewrite requests into `public/` on shared hosting).
6. Verify by visiting your domain and testing a schedule search and a PNR lookup.

## Contributing

1. Fork the repository.
2. Create a feature branch: `git checkout -b feature-name`.
3. Make your changes and commit them with clear messages.
4. Push to your fork and create a pull request.

## License

This project is open source, licensed under the MIT License.
