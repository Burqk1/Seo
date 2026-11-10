# Müşteri Destek & SEO Raporlama Paneli

A small admin panel written in plain PHP, without a framework. It has three parts: a ticket module where logged-in users can open support tickets and update their status, a reporting module that imports Lighthouse JSON reports and runs SEO analysis on URLs, and a dashboard that summarizes this data with a Chart.js chart. Data is stored in MySQL, and report files are kept as JSON under `storage/reports/`. The UI is in Turkish.

## Features

- **Authentication**: Registration / login with email and password (BCRYPT via `password_hash`). Passwords must be at least 6 characters. Every POST form checks a session-bound CSRF token (the `_csrf` field or the `X-CSRF` header).
- **Support tickets** (`/tickets`): Opening a new ticket with a subject and description, listing all tickets, and updating status (`new`, `in_progress`, `resolved`).
- **SEO reports** (`/reports`):
  - **Manual upload**: A Lighthouse JSON file is uploaded; the `categories.seo`, `performance` and `accessibility` scores are read.
  - **URL analysis**: For a single URL, Lighthouse (PageSpeed Insights v5) and a custom SEO audit of the page HTML run together.
  - **Bulk analysis**: Up to 10 URLs, with a 2-second wait between each.
  - **Demo reports** (`/reports/demo`): Creates three sample reports.
  - **Report detail**: The stored JSON content is shown in a modal window.
- **SEO audit** (`app/Services/SeoAnalyzer.php`): Checks `title` (presence, length), meta description, H1/heading structure, `alt` text on images, empty links, `viewport`, `charset` and `<html lang>`. The score starts at 100 and points are deducted for each issue (critical −20, error −15, warning −10, info −5).
- **Dashboard** (`/`): Total and open ticket counts, tickets resolved today, tickets opened in the last 7 days, report count, reports in the last 30 days, average SEO score, the latest report's performance score, and a Performance / SEO / Accessibility chart of the last 10 reports.
- Light/dark theme toggle and mobile menu (`public/js/app.js`, `public/css/styles.css`).

## Technology

- PHP (7.4 or later, since typed properties and arrow functions are used), PDO
- MySQL (`utf8mb4`)
- Chart.js (via the jsDelivr CDN)
- Vanilla JavaScript and CSS
- Google PageSpeed Insights API v5 (optional; see below)

## Project structure

```
app/
├── Auth.php                    # Session, CSRF, login/registration
├── Db.php                      # PDO wrapper
├── Controllers/
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── TicketController.php
│   └── ReportController.php
├── Services/
│   ├── LighthouseService.php   # PageSpeed API call / demo report generation, JSON saving
│   └── SeoAnalyzer.php         # HTML-based SEO audit
└── Views/                      # layout, login, tickets, reports templates
public/
├── index.php                   # Front controller and router
├── css/styles.css
└── js/app.js
storage/reports/                # Uploaded / generated JSON reports
schema.sql                      # users, tickets, seo_reports tables
```

## Requirements

- PHP 7.4+ with the `pdo_mysql` and `dom` extensions
- `allow_url_fopen` must be enabled for URL analysis (pages are fetched with `file_get_contents`)
- MySQL or MariaDB
- The web server must be able to write to the `storage/reports/` folder

## Setup

1. Create a database named `mds` in MySQL and import `schema.sql`:
   ```bash
   mysql -u root -e "CREATE DATABASE mds CHARACTER SET utf8mb4"
   mysql -u root mds < schema.sql
   ```
2. Edit the database connection details in the `$config` array in `public/index.php`. Default: `mysql:host=localhost;dbname=mds;charset=utf8mb4`, user `root`, empty password.
3. Set the web server's document root to `public/`. All requests are routed through `public/index.php`, so nonexistent paths must fall through to this file. There is no `.htaccess` in the repository. PHP's built-in server is enough for development:
   ```bash
   php -S localhost:8000 -t public
   ```
4. Create an account on the `/login` page and log in.
5. On the `/reports` screen, upload a sample JSON (for example `storage/reports/sample_lighthouse.json`) or use the "Demo Raporlar Oluştur" ("Create Demo Reports") button.

## Routes

| Path | Method | Description |
|---|---|---|
| `/login`, `/register` | GET / POST | Login and registration |
| `/logout` | POST | Logout |
| `/` | GET | Dashboard |
| `/tickets` | GET | Ticket list and form |
| `/tickets/store`, `/tickets/update` | POST | Add ticket / update status |
| `/reports` | GET | Reports screen |
| `/reports/upload` | POST | Lighthouse JSON upload (`json` file field, `label`) |
| `/reports/analyze-url` | POST | Single URL analysis (JSON response) |
| `/reports/analyze-bulk` | POST | Bulk analysis, one URL per line in the `urls` field (JSON response) |
| `/reports/demo` | GET | Demo report generation |
| `/reports/api/detail?id=` | GET | Report record + JSON content |
| `/api/reports/last10` | GET | Last 10 reports for the chart |

All pages and API endpoints except `/login`, `/register` and `/logout` require a session; if not logged in, the user is redirected to `/login`.

## Important: Lighthouse scores are currently simulated

`LighthouseService` can take an API key, but `ReportController` creates the service without one (`new LighthouseService()`). When the key is empty, a placeholder value (`AIzaSyDemoKeyForProject123`) is assigned and the service returns a fake report derived from the `crc32` hash of the URL instead of calling the real API. As a result, the performance, SEO and accessibility scores in URL and bulk analysis are not real measurements. For real PageSpeed results, a valid Google API key must be passed in the `ReportController` constructor:

```php
$this->lighthouse = new LighthouseService('GERCEK_API_ANAHTARI');
```

Similarly, for URLs containing `demo` or `test`, and when the page can't be fetched, `SeoAnalyzer` analyzes a generated sample HTML instead of returning an error.

## Known gaps

- The content of uploaded JSON files is not validated; the file is saved directly under `storage/reports/`.
- The repository has no license file; no license is specified.
