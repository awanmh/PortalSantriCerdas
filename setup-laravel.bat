@echo off
title Laravel 12 Setup - PortalSantriCerdas
color 0a

echo ==========================================
echo    Laravel 12 Setup - PortalSantriCerdas
echo ==========================================
echo.

:: 1. Masuk ke folder project
cd /d %~dp0

:: 2. Install dependency backend
echo [1/6] Installing PHP dependencies (composer)...
composer install
if %errorlevel% neq 0 (
    echo ❌ Composer install gagal! Pastikan Composer sudah terinstall.
    pause
    exit /b
)

:: 3. Install dependency frontend
echo [2/6] Installing Node.js dependencies (npm)...
npm install
if %errorlevel% neq 0 (
    echo ❌ NPM install gagal! Pastikan Node.js sudah terinstall.
    pause
    exit /b
)

:: 4. Copy .env jika belum ada
if not exist ".env" (
    echo [3/6] Copying .env file...
    copy .env.example .env
)

:: 5. Generate Laravel APP_KEY
echo [4/6] Generating Laravel APP_KEY...
php artisan key:generate

:: 6. Jalankan migrasi database
echo [5/6] Running migrations...
php artisan migrate

:: 7. Jalankan server Laravel
echo [6/6] Starting Laravel development server...
start cmd /k "php artisan serve"

:: 8. Jalankan Vite (npm run dev)
echo Starting Vite frontend (npm run dev)...
start cmd /k "npm run dev"

echo ==========================================
echo ✅ Laravel setup selesai! 
echo Backend: http://127.0.0.1:8000
echo Frontend: http://localhost:5173
echo ==========================================
pause
