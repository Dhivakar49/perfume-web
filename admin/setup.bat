@echo off
echo ========================================
echo   Perfume Shop - Setup Script
echo ========================================
echo.

REM Check if PHP is installed
echo [1/5] Checking PHP installation...
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] PHP is not installed or not in PATH
    echo.
    echo Please install PHP first:
    echo   Option 1: choco install php
    echo   Option 2: Download from https://windows.php.net/download/
    echo   Option 3: Install XAMPP from https://www.apachefriends.org/
    echo.
    pause
    exit /b 1
)
echo [OK] PHP is installed
php -v
echo.

REM Check if MySQL is running
echo [2/5] Checking MySQL connection...
php -r "try { new PDO('mysql:host=localhost', 'root', 'dhivakar7890'); echo '[OK] MySQL is running'; } catch(Exception $e) { echo '[ERROR] MySQL is not running or not accessible'; exit(1); }"
if %errorlevel% neq 0 (
    echo.
    echo Please start MySQL:
    echo   XAMPP users: Open XAMPP Control Panel and start MySQL
    echo   Others: Run 'net start MySQL' as administrator
    echo.
    pause
    exit /b 1
)
echo.

REM Check if database exists
echo [3/5] Checking database...
php -r "$pdo = new PDO('mysql:host=localhost', 'root', 'dhivakar7890'); $result = $pdo->query('SHOW DATABASES LIKE \"perfume_store\"'); if($result->rowCount() == 0) { echo '[INFO] Database does not exist, will create it'; } else { echo '[OK] Database exists'; }"
echo.

REM Create database if needed
echo [4/5] Setting up database...
php -r "$pdo = new PDO('mysql:host=localhost', 'root', 'dhivakar7890'); $pdo->exec('CREATE DATABASE IF NOT EXISTS perfume_store'); echo '[OK] Database ready';"
echo.

REM Import schema
echo [5/5] Importing database schema...
if exist "database\complete_schema.sql" (
    mysql -u root -pdhivakar7890 perfume_store < database\complete_schema.sql 2>nul
    if %errorlevel% equ 0 (
        echo [OK] Schema imported successfully
    ) else (
        echo [WARNING] Schema import had issues, but continuing...
    )
) else (
    echo [INFO] No schema file found, skipping import
)
echo.

echo ========================================
echo   Setup Complete!
echo ========================================
echo.
echo Starting PHP development server...
echo Server will run at: http://localhost:8000
echo.
echo Press Ctrl+C to stop the server
echo.
echo ========================================
echo.

REM Start PHP server
php -S localhost:8000
