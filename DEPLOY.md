# 🚀 SHASEA — Panduan Deploy Railway + Cloudflare

## Arsitektur

```
Pengunjung → Cloudflare (CDN + SSL + Domain) → Railway (PHP 8.2 + Apache + MySQL)
```

---

## ✅ STEP 1 — Upload ke GitHub

### 1.1 Buat repo baru
- Buka github.com → New repository
- Nama: `shasea-website`
- Private ✅ (rekomendasi)
- Klik **Create repository**

### 1.2 Upload project
```bash
# Di folder shasea (hasil extract ZIP ini)
git init
git add .
git commit -m "Initial commit - Shasea website"
git branch -M main
git remote add origin https://github.com/USERNAME/shasea-website.git
git push -u origin main
```

> Atau bisa drag & drop file langsung di GitHub website

---

## ✅ STEP 2 — Deploy ke Railway

### 2.1 Buat akun Railway
- Buka **railway.app**
- Sign up dengan GitHub (gratis, $5 credit/bulan)

### 2.2 Buat project baru
1. Klik **New Project**
2. Pilih **Deploy from GitHub repo**
3. Pilih repo `shasea-website`
4. Railway otomatis detect `Dockerfile` → klik **Deploy Now**
5. Tunggu build selesai (~2-3 menit) ✅

### 2.3 Tambah MySQL database
1. Di project Railway → klik **+ New**
2. Pilih **Database** → **Add MySQL**
3. Railway otomatis buat MySQL instance

### 2.4 Set Environment Variables
Di Railway → klik service PHP → tab **Variables** → tambah:

```
DB_HOST      = [copy dari MySQL service: MYSQLHOST]
DB_PORT      = [copy dari MySQL service: MYSQLPORT]
DB_NAME      = [copy dari MySQL service: MYSQLDATABASE]
DB_USER      = [copy dari MySQL service: MYSQLUSER]
DB_PASS      = [copy dari MySQL service: MYSQLPASSWORD]
APP_URL      = https://shasea.yourdomain.com
APP_ENV      = production
```

> **Tips:** Di Railway, klik MySQL service → tab Variables → lihat semua credentials

### 2.5 Import Database
1. Di Railway → klik MySQL service → tab **Data**
2. Klik **Connect** → copy connection string
3. Buka **TablePlus** atau **DBeaver** (gratis)
4. Connect ke MySQL Railway dengan credentials tadi
5. Jalankan isi file `database.sql` (copy paste atau import file)

Atau pakai Railway CLI:
```bash
# Install Railway CLI
npm install -g @railway/cli

# Login
railway login

# Connect ke MySQL Railway dan import
railway run mysql -h $MYSQLHOST -u $MYSQLUSER -p$MYSQLPASSWORD $MYSQLDATABASE < database.sql
```

---

## ✅ STEP 3 — Hubungkan Domain ke Cloudflare

### 3.1 Daftarkan domain ke Cloudflare
1. Buka **cloudflare.com** → Sign up gratis
2. Klik **Add a Site** → masukkan domain kamu (contoh: `shasea.id`)
3. Pilih plan **Free** → klik **Continue**
4. Cloudflare scan DNS existing → klik **Continue**
5. Cloudflare kasih 2 nameserver, contoh:
   ```
   ada.ns.cloudflare.com
   bob.ns.cloudflare.com
   ```
6. Buka registrar domain kamu (Niagahoster/IDCloudHost/GoDaddy)
7. Ganti nameserver ke punya Cloudflare
8. Tunggu propagasi 5-30 menit

### 3.2 Set custom domain di Railway
1. Railway → PHP service → tab **Settings**
2. Scroll ke **Domains** → klik **+ Custom Domain**
3. Masukkan domain: `shasea.id` atau `www.shasea.id`
4. Railway kasih target CNAME, contoh:
   ```
   roundhouse.proxy.rlwy.net
   ```

### 3.3 Tambah DNS Record di Cloudflare
1. Cloudflare Dashboard → **DNS** → **Add record**

```
┌──────────┬───────┬─────────────────────────────────┬─────────┐
│ Type     │ Name  │ Target                          │ Proxy   │
├──────────┼───────┼─────────────────────────────────┼─────────┤
│ CNAME    │ @     │ roundhouse.proxy.rlwy.net       │ 🟠 ON   │
│ CNAME    │ www   │ roundhouse.proxy.rlwy.net       │ 🟠 ON   │
└──────────┴───────┴─────────────────────────────────┴─────────┘
```

> Proxy **ON** (orange cloud) = traffic lewat Cloudflare CDN ✅

### 3.4 SSL Settings di Cloudflare
1. **SSL/TLS** → Mode: **Full** (bukan Flexible)
2. **SSL/TLS** → **Edge Certificates** → Always Use HTTPS: **ON**
3. **SSL/TLS** → **Edge Certificates** → HSTS: **Enable**

---

## ✅ STEP 4 — Optimasi Cloudflare (Bonus)

### Speed
```
Speed → Optimization:
  ✅ Auto Minify: HTML, CSS, JavaScript
  ✅ Brotli: ON
  ✅ Rocket Loader: ON
```

### Caching
```
Caching → Configuration:
  Browser Cache TTL: 1 month
  
Caching → Cache Rules → Create rule:
  If: hostname = shasea.id AND
      URI path matches regex: \.(css|js|jpg|png|webp|svg|ico|woff2)$
  Then: Cache Everything, Edge TTL: 1 month
```

### Security
```
Security → Settings:
  Security Level: Medium
  Bot Fight Mode: ON
  
WAF → Managed Rules:
  Cloudflare Managed Ruleset: ON (blok attack otomatis)
```

### Performance extras
```
Network:
  HTTP/3 (QUIC): ON
  0-RTT Connection Resumption: ON
  WebSockets: ON (jika dibutuhkan)
```

---

## ✅ STEP 5 — Ganti Admin Password

Setelah semua jalan, **WAJIB** ganti password admin default!

1. Login ke `https://shasea.id/admin/`
   - Username: `superadmin`
   - Password: `shasea2025`

2. Atau update langsung via MySQL:
```sql
UPDATE admin_users 
SET password = '$2y$12$HASH_BARU' 
WHERE username = 'superadmin';
```

Generate hash baru:
```php
<?php echo password_hash('PASSWORD_BARU_KAMU', PASSWORD_BCRYPT, ['cost'=>12]); ?>
```

---

## 🎯 Checklist Final

```
□ GitHub repo uploaded
□ Railway deploy sukses (tidak ada error di Logs)
□ MySQL Railway running
□ database.sql berhasil diimport
□ Environment variables ter-set di Railway
□ Domain terhubung ke Cloudflare
□ DNS record CNAME ter-set
□ SSL Full mode aktif
□ Always HTTPS aktif
□ Auto Minify aktif
□ Website bisa diakses via https://shasea.id
□ Admin login bisa diakses
□ Password admin sudah diganti
□ Coba tambah produk dari admin
□ Coba akses katalog
□ Coba klik produk (cek analytics tracking)
```

---

## 🔥 Hasil Akhir

```
https://shasea.id          → Homepage
https://shasea.id/catalog  → Katalog produk
https://shasea.id/admin    → Admin Dashboard
```

**Performa yang didapat:**
- ⚡ Cloudflare CDN global (200+ lokasi)
- 🔒 SSL gratis + auto-renew
- 🛡️ DDoS protection otomatis
- 🚀 Gzip/Brotli compression
- 📦 Static asset caching (1 bulan)
- 🌍 HTTP/3 support

**Biaya:**
| Layanan | Harga |
|---------|-------|
| Railway | Gratis ($5/bln credit) |
| Cloudflare | Gratis |
| Domain .id | ~Rp 150rb/tahun |
| **Total** | **~Rp 150rb/tahun** |

---

*Shasea — Elegant Muslim Fashion | Deployed with ❤️*
