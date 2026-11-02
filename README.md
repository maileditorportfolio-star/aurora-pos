# Aurora POS

**Aurora POS** is an open-source, multi-store Point of Sale (POS) & business management
system. One installation runs one business with many stores — each store has its own
products, stock, employees, suppliers, sales, expenses and reports, while a Super Admin
sees and controls everything across all stores.

- **Repo:** https://github.com/maileditorportfolio-star/aurora-pos
- **License:** MIT — free to install, use, modify and distribute (see [LICENSE](LICENSE))

## Features

- **Multi-store management** — create / activate / deactivate stores; only active stores
  can transact; every record belongs to exactly one store
- **RBAC** — Super Admin, Admin, Manager, Cashier, Employee + custom roles and
  permissions, assignable per store
- **Catalog & inventory** — categories, brands, units, product variants, barcodes/SKUs,
  retail & wholesale prices, per-store stock, full stock-movement history, transfers,
  returns, damage/expiry tracking
- **Suppliers & purchases** — supplier ledger, purchase orders, partial receiving,
  partial payments, purchase returns, invoices
- **POS & sales** — barcode scanning, fast search, cart with discounts/taxes, cash /
  card / mobile-money / split payments, held sales, returns & refunds, printable
  receipts (browser print, DL / 80mm / 58mm paper sizes)
- **Employees** — profiles, shifts & schedules, check-in/out attendance, overtime, leaves
- **Payroll** — monthly payroll from attendance + salary structure, bonuses, deductions,
  advances, approval flow, printable salary slips
- **Expenses & reports** — expense tracking, dashboards and daily → yearly sales,
  purchase, stock, ledger, payroll and store-comparison reports
- **Per-store settings** — currency (default USD, changeable), VAT %, receipt header

## Tech stack

| Layer | Technology |
| --- | --- |
| Backend | Laravel 13 (latest stable), PHP 8.3+ (developed on 8.5) |
| Frontend | Inertia.js + React 19 + TypeScript + Tailwind CSS 4 + Vite |
| Database | MySQL 8+ or MariaDB 10.3+ |
| Auth & RBAC | Laravel auth + spatie/laravel-permission (store-scoped) |
| Tests | Pest 4 |

## Requirements

- PHP **8.3+** with extensions: `bcmath curl dom fileinfo gd intl mbstring openssl pdo_mysql xml zip`
- Composer 2.x
- Node.js **20+** and npm
- MySQL 8+ or MariaDB 10.3+ running locally (or use Docker — see below)

## Installation

```bash
# 1. Clone
git clone https://github.com/maileditorportfolio-star/aurora-pos.git
cd aurora-pos

# 2. Install dependencies
composer install
npm install --ignore-scripts
npm run build

# 3. Environment
cp .env.example .env
# then edit .env: set APP_NAME and your DB_* credentials

# 4. Create the database (MySQL example)
# CREATE DATABASE aurora_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 5. App key + migrate (+ seed demo data)
php artisan key:generate
php artisan migrate --seed

# 6. Run
composer run dev
# → http://localhost:8000  (artisan serve + queue + logs + Vite)
```

For production: `npm run build`, `php artisan config:cache route:cache view:cache`,
point a web server (Nginx/Apache) at `public/`, and set `APP_ENV=production` with a real
`APP_KEY`. See [docs/deployment.md](docs/deployment.md) (Phase 7).

### Docker

`docker compose up` (coming with Phase 7) will start MySQL + the app in one command.

## First login

Register the first account at `/register` — the first registered user becomes the
Super Admin (see `database/seeders/DatabaseSeeder.php`). Change demo credentials
before deploying anywhere public.

## Project structure

```
app/            Models, Controllers, Services, Actions, Middleware
resources/js/   React + Inertia pages, components, layouts (TypeScript)
routes/         web.php, auth.php, settings.php, console.php
database/       migrations, factories, seeders
tests/          Pest feature + unit tests
docs/           architecture, database design, deployment (phase docs)
product.md      product definition & business rules
```

## Testing

```bash
php artisan test          # full suite (uses in-memory SQLite, no MySQL needed)
./vendor/bin/pint         # code style
npm run lint              # frontend lint
```

## Business rules enforced by the system

- Only **active** stores can process sales, purchases and payments
- Users only access their **authorized** stores (enforced on routes, controllers and UI)
- Inventory never changes without a **stock movement** record
- Sales / purchases / payments run in **database transactions** with row locking —
  no overselling
- Financial records are **never hard-deleted** — they are cancelled or reversed
- Money is stored as `decimal`, never floats; invoice numbers are unique per store
- Important actions are written to the **audit log**

## Roadmap

Phase-by-phase build log lives in `docs/` — current phase: Phase 2 (scaffold).
Next: Phase 3 database design → Phase 4 auth & RBAC → Phase 5 core modules
(stores → catalog → suppliers → inventory → purchases → POS → HR → payroll →
expenses → reports).

## Contributing

Contributions are welcome! See [CONTRIBUTING.md](CONTRIBUTING.md) (Phase 7).
Please report security issues privately — see [SECURITY.md](SECURITY.md) (Phase 7).

## License

MIT License — see [LICENSE](LICENSE). Free for commercial and personal use.
