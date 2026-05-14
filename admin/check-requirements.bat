@echo off
echo ========================================
echo   System Requirements Check
echo ========================================
echo.

echo [1/3] Checking PHP...
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [X] PHP NOT FOUND
    echo     Install: choco install php
    echo     Or download: https://windows.php.net/download/
) else (
    echo [OK] PHP is installed
    php -v | findstr /C:"PHP"
)
echo.

echo [2/3] Checking MySQL...
php -r "try { new PDO('mysql:host=localhost', 'root', 'dhivakar7890'); echo '[OK] MySQL is running and accessible'; } catch(Exception $e) { echo '[X] MySQL NOT ACCESSIBLE - ' . $e->getMessage(); }" 2>nul
if %errorlevel% neq 0 (
    echo [X] MySQL connection failed
    echo     XAMPP users: Start MySQL from XAMPP Control Panel
    echo     Others: Run 'net start MySQL' as administrator
)
echo.

echo [3/3] Checking required PHP extensions...
php -m | findstr /C:"mysqli" >nul 2>&1
if %errorlevel% neq 0 (
    echo [X] mysqli extension NOT found
) else (
    echo [OK] mysqli extension found
)

php -m | findstr /C:"pdo_mysql" >nul 2>&1
if %errorlevel% neq 0 (
    echo [X] pdo_mysql extension NOT found
) else (
    echo [OK] pdo_mysql extension found
)

php -m | findstr /C:"session" >nul 2>&1
if %errorlevel% neq 0 (
    echo [X] session extension NOT found
) else (
    echo [OK] session extension found
)
echo.

echo ========================================
echo   Check Complete
echo ========================================
echo.
echo If all checks passed, run: setup.bat
echo.
pause
