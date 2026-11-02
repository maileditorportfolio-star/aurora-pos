# Aurora POS — what this product actually is

**Aurora POS** is a multi-store Point of Sale & business management system for retail
businesses that run more than one outlet. One installation = one business, many stores.

- **Repo:** https://github.com/maileditorportfolio-star/aurora-pos
- **License:** MIT — public, anyone can install, run and use it freely

## One-liner

A fast, secure, multi-store POS where a super admin runs the whole business — stores,
people, stock, money — while each store's team just sells, buys and reports.

## Who it's for

- Retail owners with 2+ shops (fashion, electronics, grocery, pharmacy, etc.)
- Store managers and cashiers who need a POS that works on day one
- Accountants who need clean ledgers, audit trails and P&L reports

## What it does (core modules)

1. **Multi-store management** — super admin creates/activates/deactivates stores; only
   active stores can transact; every record belongs to exactly one store.
2. **RBAC** — Super Admin, Admin, Manager, Cashier, Employee + custom roles/permissions,
   with store-specific assignments. Enforced on routes, controllers and UI.
3. **Catalog & inventory** — categories, brands, units, product variants, barcodes/SKUs,
   multi-price (retail/wholesale), images, low-stock alerts, per-store stock levels, full
   stock movement history, inter-store transfers, returns, damage/expiry tracking.
4. **Suppliers & purchases** — supplier profiles with opening balance and ledger, purchase
   orders (draft → received), partial receiving, returns, full/partial payments, invoices.
5. **POS & sales** — barcode scanning, fast product search, cart with qty/discount/tax,
   cash/card/mobile-money/split payments, held sales, returns/refunds, printable receipts,
   unique invoice numbers, automatic stock deduction inside DB transactions.
6. **Employees** — profiles, documents, shifts & weekly/monthly schedules, check-in/out
   attendance, late/early/overtime tracking, leave management.
7. **Payroll** — monthly payroll generation from attendance + salary structure, overtime,
   bonuses, allowances, deductions, advances, approval flow, salary slips.
8. **Expenses** — categories, store expenses, attachments, included in P&L.
9. **Dashboard & reports** — super-admin cross-store dashboard, store dashboard, sales /
   purchase / stock / stock-movement / supplier-ledger / attendance / payroll / expense /
   store-comparison reports, daily → yearly ranges.

## Tech stack (decided 2026-11-02, locked in Phase 2)

| Layer | Choice |
| --- | --- |
| Backend | Laravel 13 (13.39.0 verified), PHP 8.3+ (dev machine runs 8.5.12) |
| Database | MySQL 8+ / MariaDB 10.3+ (single DB, multi-store via `store_id` scoping) |
| Frontend | Inertia.js 3 + React 19 + TypeScript + Tailwind CSS 4 + Vite 6 |
| RBAC | spatie/laravel-permission (teams = stores) |
| Audit | spatie/laravel-activitylog |
| PDF invoices | barryvdh/laravel-dompdf |
| Receipts | browser print, per-store paper size (DL 110×220mm / 80mm / 58mm) |
| Per-store settings | currency (default USD, changeable), VAT % (configurable) |
| Queues | database driver (Redis later), scheduler for alerts/payroll |

## Rules the product never breaks

- Only **active** stores can process sales/purchases/payments.
- Users only see **their authorized** stores.
- Inventory never changes without a **stock movement record**.
- Sales/purchases/payments run in **DB transactions** with row locking — no overselling.
- Financial records are **never hard-deleted** — cancelled/reversed/returned instead.
- Money in `decimal(15,4)` columns; unique invoice numbers per store.
- Every important action lands in the **audit log** with created/updated/approved by.

## Out of scope (for now)

- Per-store separate databases (single DB + scoping instead)
- E-commerce storefront / online orders (separate product: aurora-ecommerce)
- Offline PWA POS, multi-currency, multi-language — future phases
