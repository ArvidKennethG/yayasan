@echo off
chcp 65001 >nul
echo ========================================================
echo       YAYASAN CBIM - AUTO PUSH KE GITHUB & VERCEL
echo ========================================================
echo.

git status -s
echo.

set /p msg="Masukkan pesan commit (tekan Enter untuk default): "
if "%msg%"=="" (
    for /f "tokens=1-4 delims=/ " %%a in ("%date%") do set mydate=%%a-%%b-%%c
    for /f "tokens=1-2 delims=: " %%a in ("%time%") do set mytime=%%a:%%b
    set msg=Update otomatis: %mydate% %mytime%
)

echo.
echo [1/3] Menambahkan perubahan (git add) ...
git add .

echo [2/3] Menyimpan commit ...
git commit -m "%msg%"

echo [3/3] Mendorong ke GitHub (branch main & master) ...
git push origin main
git push origin main:master

if %errorlevel% equ 0 (
    echo.
    echo ========================================================
    echo  BERHASIL!
    echo  Perubahan telah terdorong ke GitHub.
    echo  Vercel akan otomatis mendeteksi dan melakukan deploy!
    echo ========================================================
) else (
    echo.
    echo ========================================================
    echo  [PERHATIAN] Push gagal!
    echo  Pastikan remote GitHub sudah disetel dengan perintah:
    echo  git remote add origin https://github.com/<USER>/<REPO>.git
    echo ========================================================
)

echo.
pause
