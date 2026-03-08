# PHP-App-MVC (PHP 8.2)

Saf PHP + Composer ile mini MVC iskeleti.

## Ozellikler

- `nikic/fast-route` ile routing
- `vlucas/phpdotenv` ile `.env` yonetimi
- MariaDB baglantisi (PDO)
- Session tabanli auth (login/logout)
- RBAC (roles/permissions) + `Admin` rolu super-admin bypass
- Tum POST isteklerinde CSRF korumasi (form + AJAX header)
- Admin prefix: `/admin/*`
- DataTables server-side endpoint: `POST /admin/customers/dt`

## Proje Yapisi

- `public/index.php`
- `bootstrap/app.php`
- `config/database.php`
- `app/Core/{Db.php,Auth.php,Csrf.php,View.php}`
- `app/Controllers/{AuthController.php,Admin/CustomerController.php}`
- `app/Views/layouts/{auth.php,admin.php}`
- `app/Views/auth/login.php`
- `app/Views/admin/customers/index.php`
- `database/schema.sql`
- `database/seed.sql`
- `.env.example`

## Kurulum

1. Bagimliliklari yukleyin:

```bash
composer install
```

2. Ortam dosyasini olusturun:

```bash
cp .env.example .env
```

Windows icin alternatif:

```powershell
Copy-Item .env.example .env
```

3. `.env` icindeki DB ayarlarini guncelleyin.

4. Veritabani semasi ve seed calistirin:

```sql
SOURCE database/schema.sql;
SOURCE database/seed.sql;
```

## Varsayilan Super Admin

- Email: `admin@example.com`
- Sifre: `admin123`
- Rol: `Admin` (tum izinleri bypass eder)

## Front-End Vendor Dosyalari

CDN kullanilmiyor. Asagidaki vendor dosyalari lokal olarak `public/assets/vendor/` altina yerlestirildi:

- `public/assets/vendor/bootstrap/...`
- `public/assets/vendor/jquery/...`
- `public/assets/vendor/datatables/...`
- `public/assets/vendor/select2/...`
- `public/assets/vendor/sweetalert2/...`

Detay liste ve yollar: `public/assets/vendor/README.md`

## Calistirma

Web server document root `public/` olmali.

PHP built-in server:

```bash
php -S localhost:8000 -t public
```

## Ornek URL'ler

- `GET /login`
- `POST /login`
- `POST /logout`
- `GET /admin/customers`
- `POST /admin/customers/dt`

## Notlar

- `/admin/*` route'lari login ister.
- `/admin/customers` ve `/admin/customers/dt` icin gerekli permission: `customers.view`
- DataTables endpoint'te order/sort whitelist ile guvenli hale getirildi.

## Front-end Asset Notu

- Vendor JS/CSS dosyalari repoda lokal olarak mevcuttur (`public/assets/vendor/`).
- Guncelleme yapmak isterseniz ayni dosya yollarini koruyarak daha yeni surumlerle degistirebilirsiniz.
