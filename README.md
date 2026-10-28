# Inventory Management System

A web app for tracking stock of materials. You organise materials into categories, set an opening balance for each one, and record inward (stock in) and outward (stock out) movements. The app keeps a running current balance for every material.

## Features

- **Login and registration:** each user signs in with email and password. All inventory pages are behind authentication.
- **Dashboard:** counts of categories, materials, total transactions and today's transactions, the 6 latest stock movements, and quick links into each module.
- **Categories:** add, edit and delete material categories. Category names are unique. A category can't be deleted while materials still use it. The seeder creates five defaults: Raw Material, Finish Goods, Spares, Machines and Others.
- **Materials:** add, edit and delete materials, each with a category and an opening balance (up to 2 decimal places). A material name must be unique within its category. Deleted materials are soft-deleted, so their history is kept.
- **Inward / outward entries:** pick a category, then a material from that category, a date and a quantity. Positive quantities are stock in and negative quantities are stock out.
- **Manage stock:** a table of every active material showing its opening balance and current balance (opening balance + all inward/outward entries).
- **Fast tables:** server-side DataTables with search, sorting and paging on every list.
- **Validation:** form request classes with clear error messages on every form, shown as SweetAlert pop-ups.

## Tech stack

**Backend:** PHP 8.3, Laravel 13, Eloquent ORM, SQLite by default (MySQL supported)

**Frontend:** Blade templates, Bootstrap 5, jQuery, DataTables, SweetAlert2, Flatpickr (date picker), Bootstrap Icons

**Architecture:** thin controllers backed by a service layer (`app/Services`), form request validation (`app/Http/Requests`), and AJAX endpoints for create, edit, delete and list actions

**Deployment:** includes a `Procfile` and `nixpacks.toml` for platforms such as Railway

## Project structure

```
inventory-management-system/
├── app/
│   ├── Http/Controllers/     # Auth, Category, Material, InwardOutward
│   ├── Http/Requests/        # Validation rules and messages
│   ├── Models/               # Category, Material, InwardOutward, User
│   └── Services/             # Business logic for each module
├── database/
│   ├── migrations/           # categories, materials, inward_outward tables
│   └── seeders/              # Default categories
├── resources/views/
│   ├── auth/                 # Login and register pages
│   ├── categories/           # Category list and modal
│   ├── materials/            # Material list, manage stock page, modal
│   ├── inward_outward/       # Stock movement list and modal
│   └── layouts/              # Master layout, header, sidebar, footer
└── routes/web.php
```

## Database

| Table | Main columns |
| --- | --- |
| `categories` | `name` (unique) |
| `materials` | `category_id`, `name` (unique per category), `opening_balance`, `deleted_at` (soft delete) |
| `inward_outward` | `material_id`, `date`, `quantity` (positive = inward, negative = outward) |

## Running it locally

### Prerequisites
- PHP 8.3+ and Composer

### Setup

```bash
git clone https://github.com/Shweta173/inventory-management-system.git
cd inventory-management-system
composer install
cp .env.example .env
php artisan key:generate
```

Create the SQLite database, run the migrations and seed the default categories:

```bash
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

Open `http://localhost:8000`, register an account, and log in.

### Using MySQL and a local domain

The assessment was tested with MySQL/MariaDB at `http://inventory.test/`. To run it that way:

1. Create a MySQL database (for example in phpMyAdmin).
2. Set the database values in `.env`:

   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=<database_name>
   DB_USERNAME=<database_username>
   DB_PASSWORD=<database_password>
   ```

3. Run `php artisan migrate --seed`.
4. Point a local virtual host for `inventory.test` at the project's `public/` folder (Laragon, Valet or Herd can do this for you).

## Testing checklist

| Area | What to check |
| --- | --- |
| Authentication | Register validates name (letters only), email format and password length (8+). A wrong password shows "These credentials do not match." Logging out asks for confirmation. Visiting `/dashboard` while logged out redirects to `/login`. |
| Dashboard | Time-based greeting with the user's name, four stat cards, and the 6 latest transactions ("No transactions yet." when empty). |
| Categories | Add/edit in a right-side offcanvas panel that stays open on validation errors. Success toasts, and delete asks for confirmation. |
| Materials | Duplicate names in the same category are rejected. Deleting a material hides it but keeps the row with `deleted_at` set. Live search and a reload button refresh the table without reloading the page. |
| Inward / Outward | Choosing a category loads only its materials. Positive quantity = inward, negative = outward. Flatpickr date picker, and every required field is validated. |
| Manage Materials | Current balance = opening balance + all transactions (e.g. 100 + 50 − 30 = 120). Negative balances are shown in red. |
| Tables | First/Prev/page numbers/Next/Last pagination, 10/25/50/100 rows per page, one search box per page. Offcanvas panels only close with the X button. |

To check soft-deleted materials directly in the database:

```sql
SELECT * FROM materials WHERE deleted_at IS NOT NULL;
```

## Author

**Shweta Kamble** · [GitHub](https://github.com/Shweta173)
