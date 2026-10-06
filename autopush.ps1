# Auto Push Script untuk Yayasan CBIM
param(
    [string]$Pesan = ""
)

Write-Host "========================================================" -ForegroundColor Cyan
Write-Host "       YAYASAN CBIM - AUTO PUSH KE GITHUB & VERCEL     " -ForegroundColor Cyan
Write-Host "========================================================" -ForegroundColor Cyan
Write-Host ""

git status -s

if ([string]::IsNullOrWhiteSpace($Pesan)) {
    $inputPesan = Read-Host "Masukkan pesan commit (tekan Enter untuk default)"
    if ([string]::IsNullOrWhiteSpace($inputPesan)) {
        $Pesan = "Update otomatis: " + (Get-Date -Format "yyyy-MM-dd HH:mm:ss")
    } else {
        $Pesan = $inputPesan
    }
}

Write-Host "`n[1/3] Menambahkan berkas..." -ForegroundColor Yellow
git add .

Write-Host "[2/3] Menyimpan commit: '$Pesan'..." -ForegroundColor Yellow
git commit -m "$Pesan"

Write-Host "[3/3] Mendorong ke GitHub (branch main)..." -ForegroundColor Yellow
git push origin main

if ($LASTEXITCODE -eq 0) {
    Write-Host "`n========================================================" -ForegroundColor Green
    Write-Host " BERHASIL!" -ForegroundColor Green
    Write-Host " Perubahan telah terdorong ke GitHub." -ForegroundColor Green
    Write-Host " Vercel akan otomatis mendeteksi dan melakukan deploy!" -ForegroundColor Green
    Write-Host "========================================================" -ForegroundColor Green
} else {
    Write-Host "`n========================================================" -ForegroundColor Red
    Write-Host " [PERHATIAN] Push gagal!" -ForegroundColor Red
    Write-Host " Pastikan remote repository GitHub sudah ditambahkan:" -ForegroundColor Red
    Write-Host " git remote add origin https://github.com/<USERNAME>/<REPO>.git" -ForegroundColor Red
    Write-Host "========================================================" -ForegroundColor Red
}
