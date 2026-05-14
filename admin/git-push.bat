@echo off
echo ========================================
echo   Git Push Helper
echo ========================================
echo.

cd ..

echo [1/4] Checking git status...
git status
echo.

echo [2/4] Adding all changes...
git add .
echo.

echo [3/4] Creating commit...
set /p commit_message="Enter commit message: "
git commit -m "%commit_message%"
echo.

echo [4/4] Pushing to GitHub...
git push origin main
echo.

echo ========================================
echo   Push Complete!
echo ========================================
echo.
pause
