<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* ============================================================================
   LOGIN ADMIN TK — Desain mandiri, mirip admin yayasan tapi branding TK.
   ========================================================================== */
$logo    = base_url('assets/templates/media/logos/logo-cbim.png');
$v_css   = @filemtime(FCPATH . 'assets/admin/admin.css') ?: '1';
$warna   = isset($unit_warna) ? $unit_warna : '#e67e22';
$nama    = isset($unit_nama) ? $unit_nama : 'TK & PAUD K Citra Bangsa Mandiri';
$url     = isset($login_url) ? $login_url : base_url('admin-tk/login');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Login — Panel Admin TK CBIM</title>
<link rel="icon" href="<?= $logo; ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0}
html{height:100%}
body{
    min-height:100%;display:flex;align-items:center;justify-content:center;
    font-family:'Inter',system-ui,sans-serif;font-size:15px;color:#1e293b;
    background:linear-gradient(135deg,<?= $warna; ?> 0%,#f39c12 50%,#fef5ec 100%);
    padding:20px;
}
.login-card{
    background:#fff;border-radius:20px;box-shadow:0 25px 60px rgba(0,0,0,.15);
    width:100%;max-width:420px;padding:48px 40px;text-align:center;
    animation:fadeUp .5s ease;
}
@keyframes fadeUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:none}}
.login-card img{height:72px;margin-bottom:12px}
.login-card h1{font-size:20px;font-weight:700;color:#1e293b;margin:0 0 4px}
.login-card .sub{font-size:13px;color:#64748b;margin:0 0 28px}
.bidang{text-align:left;margin-bottom:18px}
.bidang label{display:block;font-size:13px;font-weight:600;color:#475569;margin-bottom:6px}
.bidang input{
    width:100%;padding:12px 14px;border:1.5px solid #e2e8f0;border-radius:10px;
    font-size:14px;font-family:inherit;background:#f8fafc;transition:.2s;
    outline:none;
}
.bidang input:focus{border-color:<?= $warna; ?>;background:#fff;box-shadow:0 0 0 3px <?= $warna; ?>22}
.btn-login{
    width:100%;padding:13px;border:none;border-radius:10px;cursor:pointer;
    font-size:15px;font-weight:600;font-family:inherit;color:#fff;
    background:linear-gradient(135deg,<?= $warna; ?>,#f39c12);
    transition:.2s;margin-top:4px;
}
.btn-login:hover{transform:translateY(-1px);box-shadow:0 8px 20px <?= $warna; ?>44}
.btn-login:active{transform:translateY(0)}
.pesan{padding:12px 14px;border-radius:8px;font-size:13px;margin-bottom:18px;text-align:left}
.pesan--galat{background:#fef2f2;color:#b91c1c;border:1px solid #fecaca}
.pesan--sukses{background:#f0fdf4;color:#166534;border:1px solid #bbf7d0}
.kembali{display:inline-block;margin-top:20px;font-size:13px;color:#64748b;text-decoration:none}
.kembali:hover{color:<?= $warna; ?>}
</style>
</head>
<body>

<div class="login-card">
    <img src="<?= $logo; ?>" alt="Logo CBIM">
    <h1><?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8'); ?></h1>
    <p class="sub">Panel Administrasi</p>

    <?php
    $err = $this->session->flashdata('error');
    $ok  = $this->session->flashdata('success');
    if ($err): ?>
        <div class="pesan pesan--galat"><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if ($ok): ?>
        <div class="pesan pesan--sukses"><?= htmlspecialchars($ok, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form method="post" action="<?= $url; ?>">
        <div class="bidang">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autocomplete="username" placeholder="Masukkan username">
        </div>
        <div class="bidang">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="Masukkan password">
        </div>
        <button type="submit" class="btn-login">Masuk</button>
    </form>

    <a class="kembali" href="<?= base_url(); ?>">&larr; Kembali ke situs utama</a>
</div>

</body>
</html>
