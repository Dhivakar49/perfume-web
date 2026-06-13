# 🚀 InfinityFree Hosting - Quick Setup Guide

## What You Need to Change Before Uploading

### 1️⃣ Database Configuration (MOST IMPORTANT!)

**File: `config/database.php`**

Change these lines (around line 11-14):

```php
// FROM (Local):
private $host = 'localhost';
private $db_name = 'perfume_store';
private $username = 'root';
private $password = 'dhivakar7890';

// TO (InfinityFree):
private $host = 'sql123.infinityfree.com';  // Get from InfinityFree MySQL panel
private $db_name = 'epiz_xxxxx_perfume';    // Your database name from InfinityFree
private $username = 'epiz_xxxxx';            // Your MySQL username from InfinityFree
private $password = 'your_mysql_password';   // Your MySQL password from InfinityFree
```

### 2️⃣ Get Your InfinityFree Credentials

Login to InfinityFree Control Panel and note down:

#### MySQL Database Info:
- Go to **MySQL Databases** section
- Note: **MySQL Hostname** (e.g., sql123.infinityfree.com)
- Note: **Database Name** (e.g., epiz_xxxxx_perfume)
- Note: **Username** (e.g., epiz_xxxxx)
- Note: **Password** (the one you set)

#### FTP Info:
- Go to **FTP Accounts** section
- Note: **FTP Hostname** (e.g., ftpupload.net)
- Note: **FTP Username** (e.g., epiz_xxxxx)
- Note: **FTP Password** (your password)

---

## 📤 Upload Steps

### Option 1: Using FileZilla (Recommended)

1. **Download FileZilla**: https://filezilla-project.org/
2. **Connect to FTP**:
   - Host: `ftpupload.net` (your FTP hostname)
   - Username: Your FTP username
   - Password: Your FTP password
   - Port: 21
3. **Upload Files**:
   - Left side: Your local project folder
   - Right side: Navigate to `/htdocs/` folder
   - Drag ALL files from left to right
   - Wait for upload (5-10 minutes)

### Option 2: Using Control Panel File Manager

1. **Zip your project** on your computer
2. **Login to InfinityFree** → **File Manager**
3. **Navigate to** `/htdocs/` folder
4. **Upload** the zip file
5. **Right-click** zip → **Extract**
6. **Delete** the zip file

---

## 🗄️ Database Setup

### Step 1: Create Database
1. InfinityFree Control Panel → **MySQL Databases**
2. Click **Create Database**
3. Name it: `perfume_store` (or any name)
4. Note down the full database name (e.g., `epiz_xxxxx_perfume`)

### Step 2: Import Database
1. InfinityFree Control Panel → **phpMyAdmin**
2. Select your database from left sidebar
3. Click **Import** tab
4. Click **Choose File** → Select `database/complete_schema.sql`
5. Click **Go** button at bottom
6. Wait for "Import successful" message

---

## ✅ After Upload Checklist

1. **Test Homepage**: `https://yoursite.infinityfreeapp.com`
2. **Test Admin Login**: `https://yoursite.infinityfreeapp.com/admin/login.php`
   - Username: `admin`
   - Password: `admin123`
3. **Add Products**: Go to `admin/setup-products.php` to add sample products
4. **Fix Images**: Go to `admin/reset-and-fix.php` to link images

---

## 🔧 If You Get Errors

### "Database Connection Failed"
- Check database credentials in `config/database.php`
- Make sure database is created in InfinityFree
- Verify MySQL hostname is correct

### "Images Not Loading"
- Run: `https://yoursite.infinityfreeapp.com/admin/reset-and-fix.php`
- Check images are uploaded to `/htdocs/assets/images/products/`

### "404 Not Found"
- Make sure files are in `/htdocs/` NOT in a subfolder
- Check file names (case-sensitive on Linux servers)

---

## 🎯 Summary - Only 2 Changes Needed!

1. **Edit `config/database.php`** with InfinityFree MySQL credentials
2. **Upload all files** to `/htdocs/` folder via FTP

That's it! 🎉

---

## 📱 Your Site URLs After Hosting

- **Homepage**: `https://yoursite.infinityfreeapp.com`
- **Shop**: `https://yoursite.infinityfreeapp.com/products.php`
- **Admin Panel**: `https://yoursite.infinityfreeapp.com/admin/login.php`
- **Cart**: `https://yoursite.infinityfreeapp.com/cart.php`

---

**Note**: Replace `yoursite` with your actual InfinityFree subdomain name.
