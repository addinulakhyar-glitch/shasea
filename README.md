# SHASEA — Muslim Fashion Website
**Full-stack PHP/MySQL dynamic website + Admin Dashboard**

---

## 🗂 Struktur Project

```
shasea/
├── config/
│   └── database.php          # DB config & helpers
├── includes/
│   ├── header.php            # HTML head
│   ├── navbar.php            # Navbar + cart sidebar + modals
│   └── footer.php            # Footer + scripts
├── assets/
│   ├── css/
│   │   ├── main.css          # Design system & global styles
│   │   ├── home.css          # Homepage styles
│   │   ├── catalog.css       # Catalog & product detail styles
│   │   └── pages.css         # About, contact, checkout styles
│   ├── js/
│   │   ├── main.js           # Global JS (navbar, cart, toast)
│   │   ├── tracking.js       # Click tracking system
│   │   └── catalog.js        # Catalog filter/search/sort
│   └── images/               # ← Taruh gambar produk di sini
│       ├── logo.png           # ← Logo Shasea
│       ├── banner-1.jpg
│       ├── banner-2.jpg
│       ├── brand-story.jpg
│       └── products/
│           └── ...            # Gambar produk
├── api/
│   ├── products.php          # Products JSON API
│   ├── cart.php              # Cart API (session-based)
│   ├── track-click.php       # Click tracking endpoint
│   ├── contact.php           # Contact form API
│   └── newsletter.php        # Newsletter subscribe API
├── admin/
│   ├── login.php             # Admin login
│   ├── logout.php            # Admin logout
│   ├── dashboard.php         # Overview dashboard
│   ├── products.php          # ✦ Products CRUD
│   ├── categories.php        # ✦ Categories CRUD
│   ├── orders.php            # ✦ Order management
│   ├── banners.php           # ✦ Banner/slider management
│   ├── content.php           # ✦ CMS content editor
│   ├── contacts.php          # ✦ Messages inbox
│   ├── analytics.php         # ✦ Shopee-style analytics
│   ├── auth.php              # Auth middleware
│   ├── assets/
│   │   ├── css/dashboard.css # Admin design system
│   │   └── js/dashboard.js  # Admin JS utilities
│   ├── includes/
│   │   └── sidebar.php       # Admin sidebar nav
│   └── api/
│       ├── analytics.php     # Analytics data API
│       ├── products-crud.php # Products CRUD API
│       └── orders-crud.php   # Orders CRUD API
├── pages/                    # Static info pages
├── index.php                 # Homepage
├── catalog.php               # Product catalog
├── product-detail.php        # Product detail
├── about.php                 # About Shasea
├── contact.php               # Contact page
├── checkout.php              # Checkout page
└── database.sql              # Database schema + seed data
```

---

## ⚡ Cara Install

### 1. Requirements
- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.4+
- Apache / Nginx dengan mod_rewrite

### 2. Setup Database
```sql
-- Import database.sql ke MySQL
mysql -u root -p < database.sql
-- atau via phpMyAdmin: import file database.sql
```

### 3. Konfigurasi
Edit file `config/database.php`:
```php
define('DB_HOST',  'localhost');
define('DB_NAME',  'shasea_db');
define('DB_USER',  'root');       // username MySQL kamu
define('DB_PASS',  '');           // password MySQL kamu
define('BASE_URL', 'http://localhost/shasea'); // sesuaikan URL
```

### 4. Upload Gambar
Taruh gambar di folder `assets/images/`:
- `logo.png` — Logo Shasea (transparent PNG)
- `banner-1.jpg`, `banner-2.jpg` — Hero banner (1920×700px)
- `brand-story.jpg` — Foto brand story
- `products/` — Gambar produk (3:4 ratio, min 600×800px)
- `categories/` — Gambar kategori

### 5. Akses Website
- **Frontend**: `http://localhost/shasea/`
- **Admin**: `http://localhost/shasea/admin/login.php`

### 6. Login Admin
```
Username : superadmin
Password : shasea2025
```
> ⚠️ **Segera ganti password** setelah login pertama!

---

## 🎨 Design System

| Token | Value |
|-------|-------|
| Background | `#0d0b09` |
| Surface | `#1c1916` |
| Gold Primary | `#c4a882` |
| Gold Light | `#d4bf9f` |
| Text Primary | `#f0e8dc` |
| Font Display | Cinzel |
| Font Heading | Cormorant Garamond |
| Font Body | Jost |

---

## ✨ Fitur Utama

### 🖥 Website Frontend
- Homepage dengan hero slider, featured products, collections bento
- Katalog produk dengan filter sidebar (kategori, harga, ukuran)
- Search & sort produk (AJAX, no page reload)
- Product detail dengan gallery, size/color selector, add to cart
- Cart sidebar (session-based)
- Checkout dengan pilihan pembayaran & kurir
- About, Contact, halaman informasi
- Newsletter subscribe
- WhatsApp float button
- Scroll reveal animations
- Fully responsive (mobile-first)

### 🛠 Admin Dashboard
- **Dashboard Overview** — Revenue, orders, click stats, charts
- **Manajemen Produk** — CRUD lengkap dengan bulk actions, inline edit
- **Manajemen Kategori** — CRUD dengan toggle aktif/nonaktif
- **Manajemen Pesanan** — List orders, update status, detail modal, WA quick contact
- **Banner/Slider** — CRUD dengan drag & drop reorder, live preview
- **Konten Situs** — CMS editor untuk semua teks website
- **Pesan Masuk** — Inbox kontak, mark read/replied, WA reply

### 📊 Analytics (Phase 3)
- Revenue & order chart (line chart per hari)
- Click traffic chart
- **Top Produk Diklik** (7/14/30/90 hari)
- **Sumber klik** (catalog, homepage, search, related, direct) — donut chart
- **Order funnel** (masuk → dikonfirmasi → dikirim → selesai)
- **Click heatmap** per hari & jam (7 hari)
- Revenue per kategori
- **Realtime klik** (60 menit terakhir, auto-refresh 30s)
- Tabel performa produk (klik, konversi, revenue)

---

## 🔧 Kustomisasi

### Tambah produk baru via admin:
1. Login ke `/admin/`
2. Klik menu **Produk** → **Tambah Produk**
3. Isi nama, harga, stok, ukuran, gambar
4. Klik **Simpan**

### Update teks website:
1. Login ke `/admin/`
2. Klik menu **Konten Situs**
3. Edit teks sesuai kebutuhan
4. Klik **Simpan Semua** (atau Ctrl+S)

### Ganti banner homepage:
1. Login ke `/admin/`
2. Klik menu **Banner/Slider**
3. Edit atau tambah banner baru
4. Drag untuk atur urutan

---

## 📱 Cara Order via WhatsApp
Pengunjung bisa langsung order via WA dengan tombol yang ada di:
- Setiap halaman (float button)
- Product detail
- Cart sidebar
- Setelah checkout

---

*Made with ♥ for Shasea — Elegant Muslim Fashion*
