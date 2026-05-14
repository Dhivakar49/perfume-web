@echo off
echo ========================================
echo   XAMPP Status Check
echo ========================================
echo.

echo [1/3] Checking XAMPP Installation...
if exist "C:\xampp\php\php.exe" (
    echo [OK] XAMPP PHP found at C:\xampp\php\php.exe
    C:\xampp\php\php.exe -v | findstr /C:"PHP"
) else (
    echo [X] XAMPP PHP not found
    echo     Install XAMPP from: https://www.apachefriends.org/
)
echo.

echo [2/3] Checking MySQL...
if exist "C:\xampp\mysql\bin\mysql.exe" (
    echo [OK] XAMPP MySQL found
    
    REM Check if MySQL is running
    tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
    if "%ERRORLEVEL%"=="0" (
        echo [OK] MySQL is RUNNING
    ) else (
        echo [!] MySQL is NOT RUNNING
        echo     Start MySQL from XAMPP Control Panel
    )
) else (
    echo [X] XAMPP MySQL not found
)
echo.

echo [3/3] Checking Apache...
tasklist /FI "IMAGENAME eq httpd.exe" 2>NUL | find /I /N "httpd.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] Apache is RUNNING
) else (
    echo [!] Apache is NOT RUNNING (optional for this project)
)
echo.

echo ========================================
echo   What to do next:
echo ========================================
echo.
echo 1. If MySQL is not running:
echo    - Open XAMPP Control Panel
echo    - Click START next to MySQL
echo.
echo 2. To run the project:
echo    - Double-click: run-server.bat
echo    - Visit: http://localhost:8000
echo.
pause
