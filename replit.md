# SD Colours Photobook Lab

## Project Overview

A PHP + PostgreSQL web application for SD Colours Photobook Lab — a professional photo printing lab in Rourkela, India. The site serves both a public-facing marketing website and a full e-commerce/order management system for photographers and admins.

## Tech Stack

- **Backend**: PHP 8.2 (built-in development server)
- **Database**: PostgreSQL (Replit managed, via PDO pgsql)
- **Frontend**: HTML5, CSS3, Tailwind CSS (CDN), Vanilla JavaScript
- **Server**: `php -S 0.0.0.0:5000`

## Project Structure

```
/
├── includes/           # Shared PHP includes
│   ├── db.php          # PDO database connection
│   ├── auth.php        # Session/authentication helpers
│   ├── header.php      # Common HTML header + nav
│   └── footer.php      # Common HTML footer
├── admin/              # Admin-only panel
│   ├── index.php       # Admin dashboard
│   ├── orders.php      # View & update all orders
│   ├── products.php    # CRUD for products
│   └── photographers.php # Approve/reject photographer accounts
├── photographer/       # Photographer portal (login required)
│   ├── index.php       # Photographer dashboard
│   ├── shop.php        # Browse products & add to cart
│   ├── cart.php        # Shopping cart
│   ├── checkout.php    # Place order
│   └── orders.php      # My order history
├── index.php           # Public homepage
├── products.php        # Public products catalog
├── pricing.php         # Public pricing page
├── gallery.php         # Public gallery
├── about.php           # About page
├── contact.php         # Contact page
├── login.php           # Login form
├── register.php        # Photographer registration
├── logout.php          # Logout
├── style.css           # Main stylesheet
└── images/             # Logo, monogram assets
    └── combos/         # 19 combo product photos (web-accessible)
```

## Database Schema

- **users** — id, name, email, password_hash, role (admin/photographer), phone, studio_name, city, status (pending/approved/rejected), created_at
- **products** — id, name, category, description, price, price_alt, sizes (JSON), features (JSON), tag, image, active, sort_order, created_at
- **orders** — id, photographer_id (FK), status, total, notes, admin_notes, created_at, updated_at
- **order_items** — id, order_id (FK), product_id (FK), product_name, size, quantity, unit_price, notes

## User Roles

1. **Admin** — Full access to dashboard, orders, products, photographer management
   - Default login: `admin@sdcolours.com` / `admin123`
2. **Photographer** — Must register and be approved by admin before they can browse & order
3. **Public** — Anyone can view the public site (products, pricing, gallery, etc.)

## Key Features

- Photographer registration with admin approval workflow
- Product catalog with categories: combo pads, albums, LED frames, wall acrylic
- Shopping cart (session-based) with size selection
- Order placement and tracking
- Admin can update order status (pending → processing → shipped → delivered)
- Admin can add/edit/hide/delete products
- WhatsApp integration for non-registered users

## REST API

A complete JSON REST API is available at `/api/*` for use by the Flutter photographer mobile app and the admin desktop app.

- **Docs**: `GET /api/docs` — interactive HTML documentation page
- **Auth**: Bearer token (30-day expiry) obtained via `POST /api/auth/login`
- **Token storage**: `api_tokens` table — id, user_id, token, expires_at, created_at
- **CORS**: fully enabled (all origins)

### API Endpoints Summary

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| POST | /api/auth/login | public | Login, returns Bearer token |
| POST | /api/auth/register | public | Register photographer (starts as pending) |
| GET | /api/auth/me | any | Get own profile |
| PATCH | /api/auth/me | any | Update profile / change password |
| POST | /api/auth/logout | any | Revoke current token |
| GET | /api/products | public | List active products |
| GET | /api/products/{id} | public | Single product detail |
| GET | /api/photographer/dashboard | photographer | Stats + recent orders |
| GET | /api/photographer/orders | photographer | All own orders |
| GET | /api/photographer/orders/{id} | photographer | Order detail with items |
| POST | /api/photographer/orders | photographer | Place new order |
| GET | /api/admin/dashboard | admin | Lab overview stats |
| GET | /api/admin/orders | admin | All orders (filter by status/search) |
| GET | /api/admin/orders/{id} | admin | Full order detail |
| PATCH | /api/admin/orders/{id} | admin | Update status / admin_notes |
| GET | /api/admin/photographers | admin | List photographers |
| PATCH | /api/admin/photographers/{id} | admin | Approve/reject photographer |
| GET | /api/admin/products | admin | All products (incl. inactive) |
| POST | /api/admin/products | admin | Create product |
| PUT | /api/admin/products/{id} | admin | Replace product |
| PATCH | /api/admin/products/{id}/toggle | admin | Toggle active/inactive |
| DELETE | /api/admin/products/{id} | admin | Delete product |

## Workflow

- **Start application**: `php -S 0.0.0.0:5000 router.php` on port 5000
- `router.php` routes `/api/*` to `api/index.php`; all other requests served normally
