# WAVE FM — Community Radio Station CMS
**Version:** 1.0.0 | **PHP:** 7.4+ | **MySQL:** 5.7+ / MariaDB 10.3+

---

## Project Structure

```
wavefm/
├── app/
│   ├── controllers/
│   │   ├── BaseController.php
│   │   ├── PublicControllers.php   ← Home, News, Shows, Presenters, Requests, About
│   │   └── AdminControllers.php    ← Auth + all CRUD admin controllers
│   ├── models/
│   │   ├── BaseModel.php
│   │   └── Models.php              ← All models in one file
│   ├── helpers/
│   │   ├── Logger.php
│   │   ├── Router.php
│   │   └── Security.php
│   └── views/
│       ├── main_layout.php         ← Public site layout
│       ├── admin_layout.php        ← Admin panel layout
│       ├── home/index.php
│       ├── news/index.php
│       ├── news/article.php
│       ├── shows/index.php
│       ├── shows/detail.php
│       ├── presenters/index.php
│       ├── presenters/profile.php
│       ├── requests/index.php
│       ├── about/index.php
│       ├── admin/login.php
│       ├── admin/dashboard.php
│       ├── admin/news/
│       ├── admin/presenters/
│       ├── admin/shows/
│       ├── admin/podcasts/
│       ├── admin/requests/
│       ├── admin/users/
│       ├── admin/settings/
│       └── errors/404.php, 500.php
├── config/
│   ├── config.php                  ← Main config — edit this first!
│   └── Database.php                ← PDO singleton
├── database/
│   └── wavefm.sql                  ← Full schema + seed data
├── logs/                           ← Auto-created, writable by Apache
├── public/
│   ├── .htaccess
│   ├── index.php                   ← Front controller / entry point
│   ├── assets/
│   │   ├── css/style.css
│   │   ├── css/admin.css
│   │   ├── js/app.js
│   │   └── images/uploads/         ← Image uploads go here
│   └── uploads/                    ← Audio file uploads
└── .htaccess                       ← Root redirect to public/
```

---

## XAMPP Setup — Step by Step

### 1. Copy files to XAMPP

```
Copy the entire `wavefm/` folder to:
  C:\xampp\htdocs\wavefm\          (Windows)
  /opt/lampp/htdocs/wavefm/        (Linux)
  /Applications/XAMPP/htdocs/wavefm/ (macOS)
```

### 2. Enable Apache mod_rewrite

**Windows XAMPP:**
1. Open XAMPP Control Panel → Apache → Config → `httpd.conf`
2. Find `#LoadModule rewrite_module modules/mod_rewrite.so`
3. Remove the `#` to uncomment it
4. Find `AllowOverride None` (inside the `<Directory "htdocs">` block)
5. Change it to `AllowOverride All`
6. Save, restart Apache

**Linux XAMPP:**
```bash
sudo a2enmod rewrite
sudo service apache2 restart
```

### 3. Create the database

1. Start Apache and MySQL in XAMPP Control Panel
2. Open browser → `http://localhost/phpmyadmin`
3. Click **"New"** in the left sidebar
4. Name the database **`wavefm`**, charset: `utf8mb4_unicode_ci`, click **Create**
5. Select `wavefm` database → click **Import** tab
6. Browse for `wavefm/database/wavefm.sql` → click **Go**

### 4. Configure the application

Open `wavefm/config/config.php` and edit these lines:

```php
// If running at http://localhost/wavefm/public/
define('BASE_URL', 'http://localhost/wavefm/public/');

// Database credentials (default XAMPP: root / no password)
define('DB_HOST', 'localhost');
define('DB_NAME', 'wavefm');
define('DB_USER', 'root');
define('DB_PASS', '');        // Leave empty for default XAMPP
```

### 5. Set folder permissions (Linux/macOS only)

```bash
chmod -R 755 wavefm/
chmod -R 777 wavefm/logs/
chmod -R 777 wavefm/public/assets/images/uploads/
chmod -R 777 wavefm/public/uploads/
```

### 6. Access the site

| URL | Description |
|-----|-------------|
| `http://localhost/wavefm/public/` | Main website |
| `http://localhost/wavefm/public/admin/login` | Admin panel |

**Default admin credentials:**
- Username: `admin`
- Password: `Admin@123`

> ⚠️ **Change the password immediately** after first login via Admin → Users → Edit.

---

## Optional: Virtual Host Setup (cleaner URLs)

Add to `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    ServerName wavefm.local
    DocumentRoot "C:/xampp/htdocs/wavefm/public"
    <Directory "C:/xampp/htdocs/wavefm/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Add to `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1   wavefm.local
```

Then update `config.php`:
```php
define('BASE_URL', 'http://wavefm.local/');
```

---

## Admin Panel Features

| Section | Capabilities |
|---------|-------------|
| **Dashboard** | Stats overview, recent activity log |
| **News** | Full CRUD, categories, image upload, status, featured flag |
| **Presenters** | Full CRUD, photo upload, social links, sort order |
| **Shows** | Full CRUD, link to presenter & genre |
| **Podcasts** | Upload audio files (MP3/OGG/WAV), link to show |
| **Requests** | View all requests, change status (pending/approved/played/rejected), delete |
| **Users** | Full CRUD (superadmin only), role-based access control |
| **Settings** | Edit all site content: name, tagline, contact, stream URL, social links, about text |

---

## Security Features

- **Bcrypt** password hashing (cost factor 12)
- **CSRF tokens** on all POST forms
- **Prepared statements** — no raw SQL with user input anywhere
- **Input sanitisation** via `Security::sanitize()` on all user input
- **Rate-limited login** — locks out after 5 failed attempts for 15 minutes
- **Session regeneration** on login, periodic rotation
- **Role-based access control** — Editor / Admin / Super Admin
- **File upload validation** — MIME type check via `finfo`, size limit, safe filename
- **Security headers** — X-Frame-Options, X-Content-Type-Options, X-XSS-Protection
- **Activity logging** — all admin actions logged to `activity_log` table
- **Error logging** — errors written to `logs/YYYY-MM-error.log`
- **`noindex` on admin** — robots meta tag prevents search engine indexing

---

## Live Stream Integration

To connect your Icecast / Shoutcast stream:

1. Go to **Admin → Settings → Streaming**
2. Set **Stream URL** to your stream endpoint:
   - Icecast: `http://yourserver:8000/stream`
   - Shoutcast: `http://yourserver:8000/;stream`
3. The HTML5 `<audio>` player in the navbar and hero will use this URL

For **now-playing metadata**, you can poll your stream's status JSON endpoint and update `#miniTitle` and `#npSong` via JavaScript in `public/assets/js/app.js`.

---

## Production Deployment Checklist

- [ ] Change `ENVIRONMENT` to `'production'` in `config.php`
- [ ] Change `DB_USER` and `DB_PASS` to a dedicated DB user (not root)
- [ ] Enable HTTPS and set `session.cookie_secure = 1`
- [ ] Set strong `SESSION_NAME` value
- [ ] Change default admin password
- [ ] Set `logs/` directory writable but not web-accessible (it's above `public/`)
- [ ] Remove `database/wavefm.sql` from production server
- [ ] Configure real stream URL
- [ ] Set up `cron` for log rotation if needed

---

## Troubleshooting

| Problem | Solution |
|---------|---------|
| Blank page | Check `logs/` for PHP errors; enable `display_errors` in `config.php` dev mode |
| 404 on all routes | `mod_rewrite` not enabled, or `AllowOverride All` not set |
| DB connection error | Check `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` in `config.php` |
| Upload fails | Check `public/assets/images/uploads/` and `public/uploads/` are writable |
| Session issues | Check `session.save_path` is writable on your server |
| Admin login loops | Clear browser cookies; check `SESSION_NAME` in config |

---

## Developer Notes

- **MVC Pattern**: Controllers handle request logic, Models handle DB queries, Views handle output
- **No framework dependency** — pure PHP, Bootstrap 5, Font Awesome 6
- **Single entry point** — all requests route through `public/index.php`
- **Router** — lightweight custom router with named URL parameters (`/:id`, `/:slug`)
- **All queries use PDO prepared statements** — SQL injection is not possible
- **CSS custom properties** — design tokens in `:root` for easy theming

---

*WAVE FM CMS — Built for community radio. Ready for XAMPP, production-deployable.*
