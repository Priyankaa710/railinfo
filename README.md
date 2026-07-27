# RailInfo — Train Schedule Checker & PNR Status Tracker Portal

A GovTech-style CodeIgniter 4 portal for checking train schedules and
tracking PNR status, with MySQL-backed caching for fast, reliable
results even when the upstream railway API is slow or down.

---

## 1. Tech Stack

| Layer        | Technology                                   |
|--------------|-----------------------------------------------|
| Frontend     | HTML5, Bootstrap 5, CSS3, vanilla JS           |
| Backend      | CodeIgniter 4 (PHP 8.1+), MVC architecture     |
| Database     | MySQL 5.7+ / MariaDB 10.3+                     |
| HTTP Client  | CodeIgniter `CURLRequest` (external rail API)  |
| Deployment   | Hostinger shared hosting (or any LAMP host)    |

---

## 2. Project Structure

```
railinfo/
├── app/
│   ├── Config/
│   │   ├── App.php            Site-wide settings (baseURL, timezone…)
│   │   ├── Database.php       DB connection defaults (overridden by .env)
│   │   ├── Paths.php          Bridges public/index.php to /vendor/system
│   │   ├── RailApi.php        External railway API base URL / key / TTL
│   │   └── Routes.php         All application routes
│   ├── Controllers/
│   │   ├── BaseController.php
│   │   ├── Home.php           Landing page, About, 404 handler
│   │   ├── TrainController.php   Schedule search, results, autocomplete
│   │   └── PNRController.php     PNR tracking + JSON API endpoint
│   ├── Libraries/
│   │   └── RailApiClient.php  HTTP Client wrapper for the live rail API
│   ├── Models/
│   │   ├── StationModel.php       Query Builder autocomplete search
│   │   ├── TrainModel.php
│   │   ├── ScheduleModel.php      Cache-first schedule lookups
│   │   ├── PnrCacheModel.php      Cache-first PNR lookups
│   │   └── TravelHistoryModel.php Durable history (MySQL + session)
│   └── Views/
│       ├── templates/header.php, footer.php
│       ├── home.php, about.php
│       ├── trains/search.php, results.php, show.php
│       ├── pnr/search.php, result.php
│       └── errors/custom404.php
├── public/                    Web root — point your domain here
│   ├── index.php              CI4 front controller
│   ├── .htaccess              Pretty URL rewriting
│   └── assets/css/style.css, assets/js/app.js
├── database/schema.sql        Full schema + seed data
├── writable/                  Logs, cache, sessions (must be writable)
├── composer.json
├── env                        Copy to ".env" and fill in real credentials
└── .htaccess                  Root redirect into /public (shared hosting)
```

---

## 3. Database Tables

- **stations** — master list of stations, used by the autocomplete search (`StationModel::search()` via CI4 Query Builder).
- **trains** — master list of trains and their base timings/running days.
- **schedules** — cached, date-specific schedule + seat/fare snapshot. `ScheduleModel::isFresh()` decides when to refresh from the live API.
- **pnr_cache** — cached PNR lookups, refreshed via `PnrCacheModel::isFresh()`.
- **travel_history** — durable log of searches per visitor (session ID), in addition to the fast copy kept in the CI4 session (`$session->get('travel_history')`).

Import `database/schema.sql` into your MySQL database — it creates all
five tables and seeds a handful of stations/trains/schedules so the
site works immediately.

---

## 4. Local Installation

1. **Requirements:** PHP 8.1+, Composer, MySQL 5.7+, the `intl` and `mbstring` PHP extensions.
2. Unzip the project, then from the project root:
   ```bash
   composer install
   ```
   This downloads the CodeIgniter 4 framework itself (the `system/`
   directory lives inside `vendor/codeigniter4/framework/system` — it
   is intentionally **not** bundled in the zip, the same way a real
   CI4 repository keeps `vendor/` out of version control).
3. Copy the environment template and edit it:
   ```bash
   cp env .env
   ```
   Fill in your MySQL credentials and, if you have one, your external
   railway API base URL/key under the `railapi.*` keys.
4. Create the database and import the schema:
   ```bash
   mysql -u root -p -e "CREATE DATABASE railinfo CHARACTER SET utf8mb4"
   mysql -u root -p railinfo < database/schema.sql
   ```
5. Serve the app:
   ```bash
   php spark serve
   ```
   Visit `http://localhost:8080`.

> **No external API yet?** That's fine — `RailApiClient` fails
> gracefully (catches every exception and returns `null`), so the app
> runs entirely off the MySQL cache and seed data until you plug in a
> real provider.

---

## 5. Deploying to Hostinger (shared hosting)

1. **Create the MySQL database** in hPanel → Databases → MySQL Databases. Note the database name, username, and password (Hostinger prefixes them, e.g. `u123456789_railinfo`).
2. **Import the schema:** hPanel → phpMyAdmin → select your database → Import → choose `database/schema.sql`.
3. **Run `composer install` locally first** (Hostinger shared hosting typically has no SSH/Composer access on lower plans), then upload the **entire project including the generated `vendor/` folder**.
4. **Upload via FTP/File Manager** to your hosting account root (e.g. `public_html/` or a subdomain folder).
5. **Set the .env file:**
   - Rename `env` to `.env` (use FTP or hPanel File Manager with "show hidden files" enabled).
   - Set `CI_ENVIRONMENT = production`.
   - Set `app.baseURL` to your live domain, e.g. `https://railinfo.example.com/`.
   - Fill in the MySQL credentials Hostinger gave you.
6. **Point the domain at `public/`:**
   - If your Hostinger plan lets you set the document root, point it directly at the `public/` folder — this is the most secure option.
   - If not, keep the project as uploaded; the root `.htaccess` included in this project transparently rewrites all requests into `public/`, so `public_html/` can be the project root itself.
7. **Permissions:** ensure `writable/` (and its subfolders) are writable by the web server, typically `755` or `775` on shared hosting.
8. **Test:** visit your domain — you should see the RailInfo home page. Try a schedule search and a PNR lookup (PNR `2451098213` after adding a matching row, or any PNR once your live API is connected) to confirm the database connection.

---

## 6. Key Functional Notes

- **Station autocomplete** — `GET /trains/station-suggest?term=...` calls `StationModel::search()`, which builds a ranked `LIKE` query with CI4's Query Builder and returns JSON consumed by `public/assets/js/app.js`.
- **Cache-first schedule search** — `TrainController::results()` checks `ScheduleModel::isFresh()`; if the cached rows are stale (default TTL: 15 minutes, configurable in `app/Config/RailApi.php`), it calls the live API via `RailApiClient::fetchSchedule()` and persists the response back into `schedules`/`trains` before rendering.
- **Cache-first PNR tracking** — same pattern via `PnrCacheModel` and `RailApiClient::fetchPnrStatus()`.
- **Travel history** — every search is pushed into the CI4 session (`travel_history`, capped at 8 entries, shown on the home page) **and** logged durably to the `travel_history` MySQL table via `TravelHistoryModel`.
- **Responsive schedule table** — `trains/results.php` renders an article/card layout on mobile and a full `<table>` on desktop (`d-lg-none` / `d-none d-lg-block`), and the table itself sits in a horizontally scrollable wrapper (`.ri-table-wrap`) as a second line of defence on in-between breakpoints.

---

## 7. Connecting a Real Railway API

`app/Libraries/RailApiClient.php` is intentionally provider-agnostic —
point `railapi.baseURL` / `railapi.key` in `.env` at whichever
schedule/PNR data provider you have access to, and adjust the two
`fetch*()` methods' request/response shape to match that provider's
actual API contract. Everything downstream (caching, display,
history) already works against the normalized array shape documented
in the method doc-blocks.

---

## 8. Disclaimer

RailInfo is a template/demo GovTech portal. It ships with **seed data
only** — no live connection to any official railway system is
included. Wire up `RailApiClient` to a licensed data provider before
using this in production, and always direct end users to verify
critical travel details against the official railway enquiry system.
