<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* ============================================================================
   HALAMAN MASUK PANEL ADMIN
   Dipanggil Auth::index(). Formulir dikirim ke auth/login dengan field
   username dan password, sama seperti versi lama.

   Pesan "Login gagal!" dari controller tampil sebagai notifikasi melayang,
   kotak masuk bergoyang sebentar, dan kursor langsung ditaruh di kolom
   password — yang paling sering salah ketik.
   ========================================================================== */

$pesan_sukses = (string) $this->session->flashdata('success');
$pesan_galat  = (string) $this->session->flashdata('error');
$this->session->unset_userdata(['success', 'error']);
$bersih = function ($s) { return trim(html_entity_decode(strip_tags($s), ENT_QUOTES, 'UTF-8')); };

$logo  = base_url('assets/templates/media/logos/logo-cbim.png');
$v_css = @filemtime(FCPATH . 'assets/admin/admin.css') ?: '1';
$v_js  = @filemtime(FCPATH . 'assets/admin/admin.js') ?: '1';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Masuk — Panel Admin Yayasan CBIM</title>
<link rel="icon" href="<?= $logo; ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets/admin/admin.css?v=' . $v_css); ?>">
</head>
<body>

<div class="masuk">
    <aside class="masuk__sampul">
        <a class="masuk__merek" href="<?= base_url(); ?>">
            <img src="<?= $logo; ?>" alt="">
            <span>
                <strong>Yayasan Citra Bina<br>Insan Mandiri</strong>
                <small>Kupang &middot; Sejak 2007</small>
            </span>
        </a>
        <div class="masuk__kutip">
            <span></span>
            <h1>Panel pengelola situs yayasan</h1>
            <p>Berita, galeri, video kegiatan, struktur organisasi, pendaftaran, dan pesan masuk — semuanya dikelola dari sini.</p>
        </div>
        <div class="masuk__kaki">&copy; <?= date('Y'); ?> Yayasan Citra Bina Insan Mandiri</div>
    </aside>

    <main class="masuk__form">
        <div class="masuk__kotak" id="kotakMasuk">
            <h2>Masuk</h2>
            <p>Gunakan akun admin yang diberikan pengurus yayasan.</p>

            <form action="<?= base_url('auth/login'); ?>" method="post" id="formMasuk" data-tanpa-kunci>
                <div class="bidang">
                    <label for="username">Nama pengguna</label>
                    <input type="text" id="username" name="username" required autocomplete="username"
                           autocapitalize="none" spellcheck="false" autofocus>
                </div>
                <div class="bidang">
                    <label for="password">Kata sandi</label>
                    <div class="sandi">
                        <input type="password" id="password" name="password" required autocomplete="current-password">
                        <button type="button" id="lihatSandi" aria-label="Tampilkan kata sandi" aria-pressed="false">
                            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
                <button type="submit" class="tbl tbl--utama tbl--blok" id="tombolMasuk" style="padding:13px 16px;font-size:.95rem;margin-top:6px">Masuk</button>
            </form>

            <a class="masuk__kembali" href="<?= base_url(); ?>">&larr; Kembali ke situs yayasan</a>
        </div>
    </main>
</div>

<script>
window.ADMIN_PESAN = <?= json_encode(['sukses' => $bersih($pesan_sukses), 'galat' => $bersih($pesan_galat)], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>
<script src="<?= base_url('assets/admin/admin.js?v=' . $v_js); ?>"></script>
<script>
(function () {
    var galat = (window.ADMIN_PESAN || {}).galat;
    var kotak = document.getElementById('kotakMasuk');
    var sandi = document.getElementById('password');

    if (galat) {
        if (kotak && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            kotak.classList.add('goyang');
            setTimeout(function () { kotak.classList.remove('goyang'); }, 450);
        }
        if (sandi) { sandi.focus(); }
    }

    var lihat = document.getElementById('lihatSandi');
    if (lihat && sandi) {
        lihat.addEventListener('click', function () {
            var buka = sandi.type === 'password';
            sandi.type = buka ? 'text' : 'password';
            lihat.setAttribute('aria-pressed', buka ? 'true' : 'false');
            lihat.setAttribute('aria-label', buka ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            sandi.focus();
        });
    }

    var form = document.getElementById('formMasuk');
    if (form) {
        form.addEventListener('submit', function () {
            var b = document.getElementById('tombolMasuk');
            if (b) { b.disabled = true; b.textContent = 'Memeriksa…'; }
        });
    }
})();
</script>
</body>
</html>
