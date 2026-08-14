@echo off
setlocal
cd /d "%~dp0"

echo [1/3] Building assets (npm run build)...
call npm run build
if errorlevel 1 (
    echo.
    echo [ERROR] Build gagal. Tekan tombol apa saja untuk keluar.
    pause
    exit /b 1
)

echo.
echo [2/3] Menjalankan php artisan serve di port 8000...
start "Laravel Serve" cmd /k "cd /d "%~dp0" && php artisan serve --host=0.0.0.0 --port=8000"

echo [3/3] Membuka tunnel ngrok ke http://localhost:8000...
start "Ngrok Tunnel" cmd /k "cd /d "%~dp0" && ngrok http 8000"

echo.
echo Selesai. Cek window "Laravel Serve" dan "Ngrok Tunnel".
echo Tutup window Ngrok untuk menghentikan tunnel.
pause
