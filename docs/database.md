# Aurora POS - Database Design (Phase 3)

> **Status:** For approval before implementation  
> **Stack:** Laravel 13 + MySQL 8 / MariaDB 10.3+, PHP 8.3+  
> **Rules:** single DB, `store_id` on all store-owned tables, money as `decimal(15,4)`, quantities `decimal(12,3)`, financial records never hard-deleted, stock changes only via `stock_movements`, DB transactions + row locking for sales/purchases/payments.

## 1. Design Principles

- **Multi-store isolation** - Every transactional record belongs to exactly one store via `store_id`. Global scopes enforce isolation; Super Admin can opt-out explicitly.
- **Immutable audit trail** - `stock_movements` is append-only. Financial records use status/cancel/reverse workflows.
- **Referential integrity** - Foreign keys use `ON DELETE RESTRICT` on financial/procurement links. Catalog can use `ON DELETE SET NULL` where sensible.
- **Per-store uniqueness** - SKUs unique per store, barcodes globally unique, invoice numbers unique per (store,type).
- **Money precision** - All monetary values `decimal(15,4)`. Round to 2dp at display edges.
- **Audit metadata** - `created_by`, `updated_by`, `approved_by`, `created_at`, `updated_at` where appropriate.

## 2. Core (Auth, Stores, RBAC, Settings)

### `stores`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | Auto-increment |
| code | VARCHAR(10) UNIQUE | Short code used in invoice prefixes (e.g., DHK, CTG) |
| name | VARCHAR(150) | Store name |
| phone | VARCHAR(20) NULL | Contact |
| email | VARCHAR(150) NULL | Contact |
| address | TEXT NULL | Full address |
| city | VARCHAR(100) NULL | City |
| state | VARCHAR(100) NULL | State/Province |
| country | VARCHAR(100) NULL | Country |
| zip_code | VARCHAR(20) NULL | Postal code |
| is_active | BOOLEAN DEFAULT true | Only active stores can transact |
| currency_code | CHAR(3) DEFAULT 'USD' | Per-store currency (configurable) |
| vat_percent | DECIMAL(5,2) DEFAULT 0.00 | Per-store VAT % (configurable) |
| receipt_paper_size | ENUM('80mm','58mm','dl') DEFAULT '80mm' | Browser print size (DL 110×220mm) |
| receipt_header | TEXT NULL | Custom receipt header |
| receipt_footer | TEXT NULL | Custom receipt footer |
| settings | JSON NULL | Additional per-store settings |
| created_at/updated_at | TIMESTAMPS | |
| deleted_at | SOFT DELETES NULL | Soft delete allowed for catalog context; Super Admin can manage |

### `users` (extends Laravel default)
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| name | VARCHAR(150) | |
| email | VARCHAR(150) UNIQUE | |
| email_verified_at | TIMESTAMP NULL | |
| password | VARCHAR(255) | |
| phone | VARCHAR(20) NULL | |
| is_active | BOOLEAN DEFAULT true | |
| last_login_at | TIMESTAMP NULL | |
| remember_token | VARCHAR(100) NULL | |
| created_at/updated_at | TIMESTAMPS | |
| deleted_at | SOFT DELETES NULL | |

### `user_store` (pivot)
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| user_id | FK users(id) ON DELETE CASCADE | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| is_default | BOOLEAN DEFAULT false | Default store on login |
| created_at/updated_at | TIMESTAMPS | |
UNIQUE(user_id, store_id)

### Roles/Permissions (spatie/laravel-permission)
Use `roles`, `permissions`, `model_has_permissions`, `model_has_roles`, `role_has_permissions` with `team_id = store_id` for store-scoped roles. Super Admin bypasses team scoping.

### `settings`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| store_id | FK stores(id) ON DELETE CASCADE NULL | NULL = global setting |
| key | VARCHAR(100) | |
| value | TEXT NULL | |
| type | VARCHAR(20) DEFAULT 'string' | string/json/boolean/integer/decimal |
| created_at/updated_at | TIMESTAMPS | |
UNIQUE(store_id, key)

### `invoice_sequences`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| type | ENUM('sale','purchase','payroll') | |
| prefix | VARCHAR(10) | e.g., INV-DHK, PUR-DHK |
| current_number | BIGINT UNSIGNED DEFAULT 0 | Incremented under lock |
| padding | TINYINT DEFAULT 6 | Zero-padding |
| created_at/updated_at | TIMESTAMPS | |
UNIQUE(store_id, type)

### `activity_log` (spatie/laravel-activitylog)
Standard schema for audit trail.

## 3. Catalog & Inventory

### `categories`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| store_id | FK stores(id) ON DELETE CASCADE NULL | NULL = global category |
| parent_id | FK categories(id) ON DELETE SET NULL | Self-referencing |
| name | VARCHAR(150) | |
| slug | VARCHAR(150) | |
| description | TEXT NULL | |
| is_active | BOOLEAN DEFAULT true | |
| sort_order | INT DEFAULT 0 | |
| created_at/updated_at | TIMESTAMPS | |
| deleted_at | SOFT DELETES NULL | |
UNIQUE(store_id, slug)

### `brands`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| store_id | FK stores(id) ON DELETE CASCADE NULL | |
| name | VARCHAR(150) | |
| slug | VARCHAR(150) | |
| is_active | BOOLEAN DEFAULT true | |
| created_at/updated_at | TIMESTAMPS | |
| deleted_at | SOFT DELETES NULL | |
UNIQUE(store_id, slug)

### `units`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| store_id | FK stores(id) ON DELETE CASCADE NULL | |
| name | VARCHAR(50) | e.g., Piece, Box, Kg, Liter |
| short_name | VARCHAR(10) | e.g., pcs, box, kg, ltr |
| is_active | BOOLEAN DEFAULT true | |
| created_at/updated_at | TIMESTAMPS | |

### `products`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| category_id | FK categories(id) ON DELETE SET NULL | |
| brand_id | FK brands(id) ON DELETE SET NULL | |
| unit_id | FK units(id) ON DELETE SET NULL | |
| name | VARCHAR(200) | |
| slug | VARCHAR(200) | |
| sku | VARCHAR(100) | |
| description | TEXT NULL | |
| purchase_price | DECIMAL(15,4) DEFAULT 0.0000 | Base purchase price |
| selling_price | DECIMAL(15,4) DEFAULT 0.0000 | Retail price |
| wholesale_price | DECIMAL(15,4) DEFAULT 0.0000 | Wholesale price |
| min_stock_alert | DECIMAL(12,3) DEFAULT 0.000 | Low stock threshold |
| track_inventory | BOOLEAN DEFAULT true | |
| is_active | BOOLEAN DEFAULT true | |
| has_variants | BOOLEAN DEFAULT false | |
| tax_rate | DECIMAL(5,2) DEFAULT 0.00 | Per-product tax override |
| created_by | FK users(id) ON DELETE SET NULL | |
| updated_by | FK users(id) ON DELETE SET NULL | |
| created_at/updated_at | TIMESTAMPS | |
| deleted_at | SOFT DELETES NULL | |
UNIQUE(store_id, sku)  
INDEX(store_id, category_id, is_active)

### `product_variants`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| product_id | FK products(id) ON DELETE CASCADE | |
| name | VARCHAR(150) | e.g., Size L / Color Red |
| sku | VARCHAR(100) | Variant SKU |
| barcode | VARCHAR(100) UNIQUE | Global unique barcode |
| purchase_price | DECIMAL(15,4) DEFAULT 0.0000 | Override |
| selling_price | DECIMAL(15,4) DEFAULT 0.0000 | Override |
| wholesale_price | DECIMAL(15,4) DEFAULT 0.0000 | Override |
| is_active | BOOLEAN DEFAULT true | |
| created_at/updated_at | TIMESTAMPS | |
INDEX(product_id)

### `product_images`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| product_id | FK products(id) ON DELETE CASCADE | |
| variant_id | FK product_variants(id) ON DELETE CASCADE NULL | |
| path | VARCHAR(255) | |
| is_primary | BOOLEAN DEFAULT false | |
| sort_order | INT DEFAULT 0 | |
| created_at/updated_at | TIMESTAMPS | |

### `stocks`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| product_id | FK products(id) ON DELETE CASCADE | |
| variant_id | FK product_variants(id) ON DELETE CASCADE NULL | |
| quantity | DECIMAL(12,3) DEFAULT 0.000 | Current stock |
| reserved_quantity | DECIMAL(12,3) DEFAULT 0.000 | Held sales |
| created_at/updated_at | TIMESTAMPS | |
UNIQUE(store_id, product_id, variant_id)  
INDEX(store_id, product_id)

### `stock_movements` (append-only)
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| product_id | FK products(id) ON DELETE CASCADE | |
| variant_id | FK product_variants(id) ON DELETE CASCADE NULL | |
| type | ENUM('purchase_receive','sale','sale_return','purchase_return','adjustment_in','adjustment_out','transfer_in','transfer_out','damage','expiry') | |
| quantity | DECIMAL(12,3) | Signed: +in, -out |
| balance_after | DECIMAL(12,3) | Balance after movement |
| reference_type | VARCHAR(50) NULL | App\Model class |
| reference_id | BIGINT UNSIGNED NULL | FK to source document |
| note | TEXT NULL | Reason/details |
| unit_cost | DECIMAL(15,4) NULL | Cost at movement time |
| created_by | FK users(id) ON DELETE SET NULL | |
| created_at | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | No updated_at |
INDEX(store_id, product_id, created_at)  
INDEX(reference_type, reference_id)

### `stock_transfers`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| from_store_id | FK stores(id) ON DELETE RESTRICT | |
| to_store_id | FK stores(id) ON DELETE RESTRICT | |
| transfer_no | VARCHAR(50) UNIQUE | |
| status | ENUM('pending','in_transit','received','cancelled') DEFAULT 'pending' | |
| note | TEXT NULL | |
| transferred_by | FK users(id) ON DELETE SET NULL | |
| received_by | FK users(id) ON DELETE SET NULL | |
| transferred_at | TIMESTAMP NULL | |
| received_at | TIMESTAMP NULL | |
| created_at/updated_at | TIMESTAMPS | |
| created_by/updated_by/approved_by | FK users(id) ON DELETE SET NULL | |

### `stock_transfer_items`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| transfer_id | FK stock_transfers(id) ON DELETE CASCADE | |
| product_id | FK products(id) ON DELETE RESTRICT | |
| variant_id | FK product_variants(id) ON DELETE RESTRICT NULL | |
| quantity | DECIMAL(12,3) | |
| received_quantity | DECIMAL(12,3) DEFAULT 0.000 | |
| created_at/updated_at | TIMESTAMPS | |

## 4. Suppliers & Purchases

### `suppliers`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| name | VARCHAR(200) | |
| company_name | VARCHAR(200) NULL | |
| email | VARCHAR(150) NULL | |
| phone | VARCHAR(20) | |
| address | TEXT NULL | |
| city | VARCHAR(100) NULL | |
| opening_balance | DECIMAL(15,4) DEFAULT 0.0000 | |
| current_balance | DECIMAL(15,4) DEFAULT 0.0000 | |
| is_active | BOOLEAN DEFAULT true | |
| notes | TEXT NULL | |
| created_by | FK users(id) ON DELETE SET NULL | |
| updated_by | FK users(id) ON DELETE SET NULL | |
| created_at/updated_at | TIMESTAMPS | |
| deleted_at | SOFT DELETES NULL | |
INDEX(store_id, is_active)

### `purchases`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| supplier_id | FK suppliers(id) ON DELETE RESTRICT | |
| purchase_no | VARCHAR(50) | Per-store unique |
| reference_no | VARCHAR(50) NULL | Supplier invoice ref |
| status | ENUM('draft','pending','partially_received','received','cancelled') DEFAULT 'draft' | |
| purchase_date | DATE | |
| expected_date | DATE NULL | |
| subtotal | DECIMAL(15,4) DEFAULT 0.0000 | |
| discount_type | ENUM('fixed','percent') NULL | |
| discount_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| tax_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| shipping_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| total_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| paid_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| due_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| note | TEXT NULL | |
| created_by | FK users(id) ON DELETE SET NULL | |
| updated_by | FK users(id) ON DELETE SET NULL | |
| approved_by | FK users(id) ON DELETE SET NULL | |
| approved_at | TIMESTAMP NULL | |
| received_at | TIMESTAMP NULL | |
| created_at/updated_at | TIMESTAMPS | |
UNIQUE(store_id, purchase_no)  
INDEX(store_id, supplier_id, status, purchase_date)

### `purchase_items`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| purchase_id | FK purchases(id) ON DELETE CASCADE | |
| product_id | FK products(id) ON DELETE RESTRICT | |
| variant_id | FK product_variants(id) ON DELETE RESTRICT NULL | |
| quantity | DECIMAL(12,3) | Ordered |
| quantity_received | DECIMAL(12,3) DEFAULT 0.000 | Received so far |
| unit_cost | DECIMAL(15,4) | Purchase price |
| discount_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| tax_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| line_total | DECIMAL(15,4) | qty_received * unit_cost - discount + tax |
| created_at/updated_at | TIMESTAMPS | |

### `purchase_payments`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| purchase_id | FK purchases(id) ON DELETE RESTRICT | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| payment_no | VARCHAR(50) | |
| amount | DECIMAL(15,4) | |
| payment_method | ENUM('cash','card','mobile_money','bank_transfer','cheque','other') | |
| reference_no | VARCHAR(100) NULL | |
| paid_at | TIMESTAMP | |
| note | TEXT NULL | |
| created_by | FK users(id) ON DELETE SET NULL | |
| created_at/updated_at | TIMESTAMPS | |
UNIQUE(store_id, payment_no)  
INDEX(purchase_id)

### `purchase_returns`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| purchase_id | FK purchases(id) ON DELETE RESTRICT | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| return_no | VARCHAR(50) | |
| status | ENUM('pending','approved','completed','cancelled') DEFAULT 'pending' | |
| return_date | DATE | |
| subtotal | DECIMAL(15,4) DEFAULT 0.0000 | |
| tax_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| total_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| note | TEXT NULL | |
| created_by/updated_by/approved_by | FK users(id) ON DELETE SET NULL | |
| created_at/updated_at | TIMESTAMPS | |
UNIQUE(store_id, return_no)

### `purchase_return_items`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| return_id | FK purchase_returns(id) ON DELETE CASCADE | |
| product_id | FK products(id) ON DELETE RESTRICT | |
| variant_id | FK product_variants(id) ON DELETE RESTRICT NULL | |
| quantity | DECIMAL(12,3) | |
| unit_cost | DECIMAL(15,4) | |
| line_total | DECIMAL(15,4) | |
| created_at/updated_at | TIMESTAMPS | |

### `supplier_ledgers`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| supplier_id | FK suppliers(id) ON DELETE RESTRICT | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| type | ENUM('opening','purchase','payment','return','adjustment') | |
| reference_type | VARCHAR(50) NULL | |
| reference_id | BIGINT UNSIGNED NULL | |
| debit | DECIMAL(15,4) DEFAULT 0.0000 | Amount supplier owes / charged |
| credit | DECIMAL(15,4) DEFAULT 0.0000 | Amount paid / credited |
| balance | DECIMAL(15,4) DEFAULT 0.0000 | Running balance |
| note | TEXT NULL | |
| created_by | FK users(id) ON DELETE SET NULL | |
| created_at | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | |
INDEX(supplier_id, store_id, created_at)

## 5. POS & Sales

### `customers`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| name | VARCHAR(150) | |
| phone | VARCHAR(20) | |
| email | VARCHAR(150) NULL | |
| address | TEXT NULL | |
| tax_number | VARCHAR(50) NULL | |
| opening_balance | DECIMAL(15,4) DEFAULT 0.0000 | |
| current_balance | DECIMAL(15,4) DEFAULT 0.0000 | |
| is_active | BOOLEAN DEFAULT true | |
| created_by/updated_by | FK users(id) ON DELETE SET NULL | |
| created_at/updated_at | TIMESTAMPS | |
| deleted_at | SOFT DELETES NULL | |
UNIQUE(store_id, phone)  
INDEX(store_id, is_active)

### `sales`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| customer_id | FK customers(id) ON DELETE SET NULL | |
| user_id | FK users(id) ON DELETE RESTRICT | Cashier |
| invoice_no | VARCHAR(50) | |
| status | ENUM('completed','held','cancelled','returned') DEFAULT 'completed' | |
| sale_date | DATETIME | |
| subtotal | DECIMAL(15,4) DEFAULT 0.0000 | |
| discount_type | ENUM('fixed','percent') NULL | |
| discount_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| tax_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| shipping_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| total_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| paid_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| due_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| change_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| note | TEXT NULL | |
| hold_reference | VARCHAR(50) NULL | For held sales |
| cancelled_at | TIMESTAMP NULL | |
| cancelled_by | FK users(id) ON DELETE SET NULL | |
| cancel_reason | TEXT NULL | |
| created_at/updated_at | TIMESTAMPS | |
UNIQUE(store_id, invoice_no)  
INDEX(store_id, user_id, sale_date)  
INDEX(store_id, customer_id, sale_date)

### `sale_items`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| sale_id | FK sales(id) ON DELETE CASCADE | |
| product_id | FK products(id) ON DELETE RESTRICT | |
| variant_id | FK product_variants(id) ON DELETE RESTRICT NULL | |
| quantity | DECIMAL(12,3) | |
| unit_price | DECIMAL(15,4) | Price at sale time |
| discount_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| tax_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| line_total | DECIMAL(15,4) | |
| created_at/updated_at | TIMESTAMPS | |

### `payments` (polymorphic)
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| payable_type | VARCHAR(50) | sale / purchase |
| payable_id | BIGINT UNSIGNED | |
| payment_no | VARCHAR(50) | |
| amount | DECIMAL(15,4) | |
| payment_method | ENUM('cash','card','mobile_money','bank_transfer','cheque','split','other') | |
| reference_no | VARCHAR(100) NULL | Card/Mobile ref |
| paid_at | TIMESTAMP | |
| note | TEXT NULL | |
| created_by | FK users(id) ON DELETE SET NULL | |
| created_at/updated_at | TIMESTAMPS | |
UNIQUE(store_id, payment_no)  
INDEX(payable_type, payable_id)  
INDEX(store_id, paid_at)

### `sale_returns`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| sale_id | FK sales(id) ON DELETE RESTRICT | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| return_no | VARCHAR(50) | |
| status | ENUM('pending','approved','completed','cancelled') DEFAULT 'pending' | |
| return_date | DATE | |
| subtotal | DECIMAL(15,4) DEFAULT 0.0000 | |
| tax_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| total_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| refund_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| refund_method | ENUM('cash','card','mobile_money','bank_transfer','store_credit') NULL | |
| note | TEXT NULL | |
| created_by/updated_by/approved_by | FK users(id) ON DELETE SET NULL | |
| created_at/updated_at | TIMESTAMPS | |
UNIQUE(store_id, return_no)

### `sale_return_items`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| return_id | FK sale_returns(id) ON DELETE CASCADE | |
| product_id | FK products(id) ON DELETE RESTRICT | |
| variant_id | FK product_variants(id) ON DELETE RESTRICT NULL | |
| quantity | DECIMAL(12,3) | |
| unit_price | DECIMAL(15,4) | |
| line_total | DECIMAL(15,4) | |
| created_at/updated_at | TIMESTAMPS | |

### `held_sales`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| user_id | FK users(id) ON DELETE CASCADE | |
| hold_no | VARCHAR(50) | |
| cart_data | JSON | Full cart snapshot |
| customer_id | FK customers(id) ON DELETE SET NULL | |
| note | TEXT NULL | |
| held_at | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | |
| created_at/updated_at | TIMESTAMPS | |
INDEX(store_id, user_id)

### `customer_ledgers`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| customer_id | FK customers(id) ON DELETE RESTRICT | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| type | ENUM('opening','sale','payment','return','adjustment') | |
| reference_type | VARCHAR(50) NULL | |
| reference_id | BIGINT UNSIGNED NULL | |
| debit | DECIMAL(15,4) DEFAULT 0.0000 | |
| credit | DECIMAL(15,4) DEFAULT 0.0000 | |
| balance | DECIMAL(15,4) DEFAULT 0.0000 | |
| note | TEXT NULL | |
| created_by | FK users(id) ON DELETE SET NULL | |
| created_at | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | |
INDEX(customer_id, store_id, created_at)

## 6. HR, Attendance, Payroll

### `employees`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| user_id | FK users(id) ON DELETE SET NULL | Link to login account |
| store_id | FK stores(id) ON DELETE CASCADE | Primary store |
| employee_code | VARCHAR(50) | Per-store unique |
| first_name | VARCHAR(100) | |
| last_name | VARCHAR(100) | |
| email | VARCHAR(150) NULL | |
| phone | VARCHAR(20) | |
| date_of_birth | DATE NULL | |
| gender | ENUM('male','female','other') NULL | |
| address | TEXT NULL | |
| joining_date | DATE | |
| designation | VARCHAR(100) | |
| employment_status | ENUM('active','inactive','terminated','resigned') DEFAULT 'active' | |
| salary_type | ENUM('monthly','daily','hourly') DEFAULT 'monthly' | |
| basic_salary | DECIMAL(15,4) DEFAULT 0.0000 | |
| bank_name | VARCHAR(100) NULL | |
| bank_account_no | VARCHAR(50) NULL | |
| emergency_contact | VARCHAR(20) NULL | |
| documents | JSON NULL | |
| created_by/updated_by | FK users(id) ON DELETE SET NULL | |
| created_at/updated_at | TIMESTAMPS | |
| deleted_at | SOFT DELETES NULL | |
UNIQUE(store_id, employee_code)  
INDEX(store_id, employment_status)

### `shifts`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| name | VARCHAR(100) | e.g., Morning, Evening, Night |
| start_time | TIME | |
| end_time | TIME | |
| break_minutes | INT DEFAULT 0 | |
| is_active | BOOLEAN DEFAULT true | |
| created_at/updated_at | TIMESTAMPS | |
UNIQUE(store_id, name)

### `employee_shifts`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| employee_id | FK employees(id) ON DELETE CASCADE | |
| shift_id | FK shifts(id) ON DELETE RESTRICT | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| date | DATE | |
| created_by | FK users(id) ON DELETE SET NULL | |
| created_at/updated_at | TIMESTAMPS | |
UNIQUE(employee_id, date)  
INDEX(store_id, date)

### `attendances`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| employee_id | FK employees(id) ON DELETE CASCADE | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| date | DATE | |
| shift_id | FK shifts(id) ON DELETE SET NULL | |
| check_in | TIME NULL | |
| check_out | TIME NULL | |
| check_in_ip | VARCHAR(45) NULL | |
| check_out_ip | VARCHAR(45) NULL | |
| status | ENUM('present','absent','late','half_day','leave') DEFAULT 'present' | |
| late_minutes | INT DEFAULT 0 | |
| early_leave_minutes | INT DEFAULT 0 | |
| overtime_minutes | INT DEFAULT 0 | |
| working_minutes | INT DEFAULT 0 | |
| note | TEXT NULL | |
| created_by/updated_by | FK users(id) ON DELETE SET NULL | |
| created_at/updated_at | TIMESTAMPS | |
UNIQUE(employee_id, date)  
INDEX(store_id, date)

### `leaves`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| employee_id | FK employees(id) ON DELETE CASCADE | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| leave_type | ENUM('casual','sick','annual','unpaid','other') | |
| start_date | DATE | |
| end_date | DATE | |
| days | DECIMAL(4,1) | |
| reason | TEXT | |
| status | ENUM('pending','approved','rejected','cancelled') DEFAULT 'pending' | |
| approved_by | FK users(id) ON DELETE SET NULL | |
| approved_at | TIMESTAMP NULL | |
| rejection_reason | TEXT NULL | |
| created_at/updated_at | TIMESTAMPS | |
INDEX(store_id, employee_id, status, start_date)

### `payrolls`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| payroll_no | VARCHAR(50) | |
| month | TINYINT | 1-12 |
| year | SMALLINT | |
| status | ENUM('draft','approved','paid','cancelled') DEFAULT 'draft' | |
| total_employees | INT DEFAULT 0 | |
| total_basic | DECIMAL(15,4) DEFAULT 0.0000 | |
| total_overtime | DECIMAL(15,4) DEFAULT 0.0000 | |
| total_bonus | DECIMAL(15,4) DEFAULT 0.0000 | |
| total_allowance | DECIMAL(15,4) DEFAULT 0.0000 | |
| total_deduction | DECIMAL(15,4) DEFAULT 0.0000 | |
| total_advance | DECIMAL(15,4) DEFAULT 0.0000 | |
| total_net | DECIMAL(15,4) DEFAULT 0.0000 | |
| note | TEXT NULL | |
| created_by | FK users(id) ON DELETE SET NULL | |
| updated_by | FK users(id) ON DELETE SET NULL | |
| approved_by | FK users(id) ON DELETE SET NULL | |
| approved_at | TIMESTAMP NULL | |
| created_at/updated_at | TIMESTAMPS | |
UNIQUE(store_id, month, year)  
UNIQUE(store_id, payroll_no)

### `payroll_items`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| payroll_id | FK payrolls(id) ON DELETE CASCADE | |
| employee_id | FK employees(id) ON DELETE RESTRICT | |
| basic_salary | DECIMAL(15,4) | |
| working_days | INT DEFAULT 0 | |
| present_days | INT DEFAULT 0 | |
| leave_days | DECIMAL(4,1) DEFAULT 0.0 | |
| absent_days | INT DEFAULT 0 | |
| overtime_hours | DECIMAL(6,2) DEFAULT 0.00 | |
| overtime_amount | DECIMAL(15,4) DEFAULT 0.0000 | |
| bonus | DECIMAL(15,4) DEFAULT 0.0000 | |
| allowance | DECIMAL(15,4) DEFAULT 0.0000 | |
| deduction | DECIMAL(15,4) DEFAULT 0.0000 | |
| advance | DECIMAL(15,4) DEFAULT 0.0000 | |
| other_adjustments | DECIMAL(15,4) DEFAULT 0.0000 | |
| net_salary | DECIMAL(15,4) | |
| note | TEXT NULL | |
| created_at/updated_at | TIMESTAMPS | |
UNIQUE(payroll_id, employee_id)

### `salary_payments`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| payroll_item_id | FK payroll_items(id) ON DELETE RESTRICT | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| payment_no | VARCHAR(50) | |
| amount | DECIMAL(15,4) | |
| payment_method | ENUM('cash','card','mobile_money','bank_transfer','cheque','other') | |
| reference_no | VARCHAR(100) NULL | |
| paid_at | TIMESTAMP | |
| note | TEXT NULL | |
| created_by | FK users(id) ON DELETE SET NULL | |
| created_at/updated_at | TIMESTAMPS | |
UNIQUE(store_id, payment_no)

### `salary_adjustments` (advances)
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| employee_id | FK employees(id) ON DELETE CASCADE | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| type | ENUM('advance','bonus','deduction','adjustment') | |
| amount | DECIMAL(15,4) | |
| reason | TEXT | |
| status | ENUM('pending','approved','paid','rejected') DEFAULT 'pending' | |
| approved_by | FK users(id) ON DELETE SET NULL | |
| approved_at | TIMESTAMP NULL | |
| paid_at | TIMESTAMP NULL | |
| created_by | FK users(id) ON DELETE SET NULL | |
| created_at/updated_at | TIMESTAMPS | |
INDEX(store_id, employee_id, status)

## 7. Expenses

### `expense_categories`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| store_id | FK stores(id) ON DELETE CASCADE NULL | NULL = global |
| name | VARCHAR(150) | |
| description | TEXT NULL | |
| is_active | BOOLEAN DEFAULT true | |
| created_at/updated_at | TIMESTAMPS | |
UNIQUE(store_id, name)

### `expenses`
| Column | Type | Notes |
| --- | --- | --- |
| id | BIGINT UNSIGNED PK | |
| store_id | FK stores(id) ON DELETE CASCADE | |
| expense_category_id | FK expense_categories(id) ON DELETE RESTRICT | |
| expense_no | VARCHAR(50) | |
| amount | DECIMAL(15,4) | |
| expense_date | DATE | |
| payment_method | ENUM('cash','card','mobile_money','bank_transfer','cheque','other') | |
| reference_no | VARCHAR(100) NULL | |
| vendor | VARCHAR(150) NULL | |
| description | TEXT | |
| receipt_path | VARCHAR(255) NULL | Attachment |
| note | TEXT NULL | |
| created_by/updated_by | FK users(id) ON DELETE SET NULL | |
| created_at/updated_at | TIMESTAMPS | |
UNIQUE(store_id, expense_no)  
INDEX(store_id, expense_category_id, expense_date)

## 8. Relationships Summary

- Store has many Users through `user_store`, Products, Sales, Purchases, Expenses, Employees
- Product has variants, images, stock per store, movements
- Sale has items, payments (polymorphic), returns; affects stock via movements
- Purchase has items, payments, returns; affects stock on receive
- Employee has shifts, attendance, leaves, payroll items, adjustments
- Payroll has items and salary payments
- All financial documents generate ledger entries

## 9. Migration Order

1. Core: stores, users (modify), user_store, roles/permissions (spatie), settings, invoice_sequences, activity_log
2. Catalog: categories, brands, units, products, product_variants, product_images
3. Inventory: stocks, stock_movements, stock_transfers, stock_transfer_items
4. Procurement: suppliers, purchases, purchase_items, purchase_payments, purchase_returns, purchase_return_items, supplier_ledgers
5. Sales: customers, sales, sale_items, payments, sale_returns, sale_return_items, held_sales, customer_ledgers
6. HR: employees, shifts, employee_shifts, attendances, leaves, payrolls, payroll_items, salary_payments, salary_adjustments
7. Expenses: expense_categories, expenses

## 10. Seeders Plan

- `RolesAndPermissionsSeeder` - Super Admin, Admin, Manager, Cashier, Employee + core permissions
- `SuperAdminSeeder` - Create Super Admin user (prompt/change credentials)
- `StoreSeeder` - Demo store (configurable currency USD, VAT per-store)
- `UnitSeeder`, `CategorySeeder` - Common units/categories

## 11. Notes for Implementation

- Use global scopes for `store_id` filtering on store-owned models
- Create `StoreContext` service + middleware to resolve active store
- `StockService` must only mutate stock and write `stock_movements` inside `DB::transaction()` with `lockForUpdate()`
- Guard sales/purchases/payments with transactions + row locks
- Invoice numbers via `InvoiceService` using locked `invoice_sequences`
- Prevent deletion of financial records - use status changes/reversals
- Spatie teams set to store_id for RBAC scoping

**Ready for approval to generate migrations, models, and seeders.**
