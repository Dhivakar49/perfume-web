# 🚀 Deployment Guide - InfinityFree Hosting

## Step-by-Step Deployment Process

### Method 1: FTP Upload (Recommended)

#### 1. Download FileZilla (FTP Client)
- Download: https://filezilla-project.org/
- Install and open FileZilla

#### 2. Get Your FTP Credentials
- Login to InfinityFree Control Panel
- Go to **Accounts** → **Your Account**
- Find FTP details:
  - **FTP Hostname**: (e.g., ftpupload.net)
  - **FTP Username**: (e.g., epiz_xxxxx)
  - **FTP Password**: Your password

#### 3. Connect via FileZilla
- Open FileZilla
- Enter your FTP credentials:
  - Host: `ftp://ftpupload.net` (or your FTP hostname)
  - Username: Your FTP username
  - Password: Your FTP password
  - Port: `21`
- Click **Quickconnect**

#### 4. Upload Files
- On the **left side**: Navigate to your local project folder
- On the **right side**: Navigate to `/htdocs/` folder
- **Select all files** from your local project
- **Drag and drop** to the `/htdocs/` folder
- Wait for upload to complete (may take 5-10 minutes)

---

### Method 2: File Manager (InfinityFree Dashboard)

#### 1. Compress Your Project
- Right-click your project folder
- Select **Send to** → **Compressed (zipped) folder**
- Name it: `perfume-store.zip`

#### 2. Upload via File Manager
- Login to InfinityFree Control Panel
- Go to **File Manager**
- Navigate to `/htdocs/` folder
- Click **Upload** button
- Select your `perfume-store.zip` file
- Wait for upload to complete

#### 3. Extract Files
- Right-click on `perfume-store.zip`
- Select **Extract**
- Delete the zip file after extraction

---

### Method 3: Git Deployment (Advanced)

If your hosting supports Git:

```bash
# On your hosting terminal
cd htdocs
git clone https://github.com/Dhivakar49/perfume-web.git .
```

---

## 📝 Important Configuration Changes

### 1. Update Database Configuration

Edit `config/database.php`:

```php
private $host = 'sql123.infinityfree.com'; // Your MySQL hostname
private $db_name = 'epiz_xxxxx_perfume'; // Your database name
private $username = 'epiz_xxxxx'; // Your MySQL username
private $password = 'your_password'; // Your MySQL password
```

Edit `config/db.php`:

```php
define('DB_HOST', 'sql123.infinityfree.com');
define('DB_USER', 'epiz_xxxxx');
define('DB_PASS', 'your_password');
define('DB_NAME', 'epiz_xxxxx_perfume');
```

### 2. Update Site URL

Edit `config/config.php`:

```php
define('SITE_URL', 'https://yoursite.infinityfreeapp.com');
```

### 3. Create Database

- Go to InfinityFree Control Panel
- Click **MySQL Databases**
- Create new database: `perfume_store`
- Note down the database name, username, and password

### 4. Import Database Schema

**Option A: phpMyAdmin**
- Go to InfinityFree Control Panel
- Click **phpMyAdmin**
- Select your database
- Click **Import** tab
- Choose `database/complete_schema.sql`
- Click **Go**

**Option B: File Manager**
- Upload `database/complete_schema.sql` to your hosting
- Use phpMyAdmin to import it

---

## 🔧 Post-Deployment Setup

### 1. Set File Permissions
- `/assets/images/products/` → 755
- `/assets/uploads/` → 755 (if exists)
- All PHP files → 644

### 2. Test Your Site
Visit your site URL:
- Homepage: `https://yoursite.infinityfreeapp.com`
- Admin: `https://yoursite.infinityfreeapp.com/admin/login.php`

### 3. Add Products
- Visit: `https://yoursite.infinityfreeapp.com/admin/setup-products.php`
- This will add sample products

### 4. Fix Images
- Visit: `https://yoursite.infinityfreeapp.com/admin/reset-and-fix.php`
- This will link images to products

---

## 📂 Files to Upload

### Essential Files:
```
✅ All PHP files (*.php)
✅ /admin/ folder
✅ /api/ folder
✅ /assets/ folder (CSS, JS, images)
✅ /config/ folder
✅ /database/ folder
✅ /includes/ folder
✅ /js/ folder
✅ README.md
```

### Files to EXCLUDE:
```
❌ /docs/ folder (optional)
❌ *.bat files (Windows only)
❌ *.md files (except README.md)
❌ .git/ folder (if using FTP)
```

---

## 🐛 Common Issues & Solutions

### Issue 1: Database Connection Error
**Solution:**
- Check database credentials in `config/database.php` and `config/db.php`
- Make sure database is created in InfinityFree panel
- Verify MySQL hostname (usually `sql123.infinityfree.com`)

### Issue 2: Images Not Loading
**Solution:**
- Check file permissions (755 for folders, 644 for files)
- Verify images are uploaded to `/htdocs/assets/images/products/`
- Run `admin/reset-and-fix.php` to fix image paths

### Issue 3: 404 Errors
**Solution:**
- Make sure all files are in `/htdocs/` folder, not in a subfolder
- Check `.htaccess` file exists
- Verify file names are correct (case-sensitive)

### Issue 4: Session Issues
**Solution:**
- Check PHP version (should be 7.4+)
- Verify session folder has write permissions
- Clear browser cookies

### Issue 5: Slow Loading
**Solution:**
- InfinityFree has resource limits
- Optimize images (compress them)
- Enable caching in `.htaccess`

---

## 🔐 Security Checklist

Before going live:

- [ ] Change admin password in `admin/login.php`
- [ ] Update database credentials
- [ ] Set `display_errors = 0` in production
- [ ] Remove test files from `/admin/` folder
- [ ] Add `.htaccess` for security
- [ ] Enable HTTPS (free SSL in InfinityFree)

---

## 📞 InfinityFree Support

- **Control Panel**: https://infinityfree.net/
- **Forum**: https://forum.infinityfree.net/
- **Knowledge Base**: https://infinityfree.net/support

---

## ✅ Deployment Checklist

- [ ] Upload all files via FTP/File Manager
- [ ] Update database configuration
- [ ] Create database in InfinityFree
- [ ] Import database schema
- [ ] Update SITE_URL in config
- [ ] Set file permissions
- [ ] Test homepage
- [ ] Test admin login
- [ ] Add products
- [ ] Fix images
- [ ] Test user registration/login
- [ ] Test cart and checkout

---

## 🎉 Your Site is Live!

Once deployed, your site will be accessible at:
- **Main Site**: `https://yoursite.infinityfreeapp.com`
- **Admin Panel**: `https://yoursite.infinityfreeapp.com/admin/login.php`

**Admin Credentials:**
- Username: `admin`
- Password: `admin123` (change this!)

---

**Need Help?** Check the InfinityFree forum or contact their support!
