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
```

## Database Schema

- **users** — id, name, email, password_hash, role (admin/photographer), phone, studio_name, city, status (pending/approved/rejected), created_at
- **products** — id, name, category, description, price, price_alt, sizes (JSON), features (JSON), tag, active, sort_order, created_at
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

## Workflow

- **Start application**: `php -S 0.0.0.0:5000` on port 5000
