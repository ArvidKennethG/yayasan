<?php
defined('BASEPATH') or exit('No direct script access allowed');

$logo = base_url('assets/img/' . $unit['logo']);
$home = base_url();

/**
 * Menu situs yayasan.
 *
 * Hanya lima item induk. Halaman yang jarang dibuka disimpan di dalam laci
 * (dropdown), supaya baris menu tetap pendek dan tidak terlihat berantakan.
 *
 * Bentuknya: kunci => [label, tautan-atau-null, aktif-untuk-halaman-apa, isi-laci]
 */
$menu = [
    'beranda' => ['Beranda', $home, ['beranda'], []],

    'tentang' => ['Tentang', NULL, ['profil', 'struktur'], [
        ['Profil Yayasan',      'Legalitas, visi, misi, dan nilai',      base_url('profil')],
        ['Struktur Organisasi', 'Dewan pembina, pengurus, dan direksi',  base_url('struktur')],
    ]],

    'unit' => ['Unit', NULL, ['jejaring'], [
        ['Unit Pendidikan',        'TK, SD, SMP, SMA, dan universitas',   base_url('jejaring')],
        ['Unit Layanan Masyarakat','Klinik, pelatihan bahasa, dan guru',  base_url('jejaring') . '#layanan'],
    ]],

    'informasi' => ['Informasi', NULL, ['berita', 'kegiatan', 'galeri'], [
        ['Berita',   'Kabar terbaru dari yayasan',      base_url('berita')],
        ['Kegiatan', 'Dokumentasi video kegiatan',      base_url('kegiatan')],
        ['Galeri',   'Dokumentasi foto',                base_url('galeri')],
    ]],

    'kontak' => ['Kontak', base_url('kontak'), ['kontak'], []],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= html_escape($judul_hal); ?></title>

<meta name="description" content="<?= html_escape($deskripsi); ?>">
<meta name="robots" content="index, follow">
<link rel="canonical" href="<?= html_escape(current_url()); ?>">
<link rel="icon" href="<?= base_url('assets/img/favicon.ico'); ?>">
<link rel="apple-touch-icon" href="<?= $logo; ?>">

<meta property="og:type" content="website">
<meta property="og:locale" content="id_ID">
<meta property="og:site_name" content="Yayasan Citra Bina Insan Mandiri">
<meta property="og:title" content="<?= html_escape($judul_hal); ?>">
<meta property="og:description" content="<?= html_escape($deskripsi); ?>">
<meta property="og:url" content="<?= html_escape(current_url()); ?>">
<meta property="og:image" content="<?= $logo; ?>">
<meta name="twitter:card" content="summary_large_image">

<script type="application/ld+json">
<?= json_encode([
    '@context'    => 'https://schema.org',
    '@type'       => 'EducationalOrganization',
    'name'        => $unit['nama'],
    'alternateName' => 'Yayasan CBIM',
    'description' => $unit['deskripsi'],
    'url'         => rtrim($home, '/'),
    'logo'        => $logo,
    'telephone'   => $unit['telepon'],
    'email'       => $unit['email'],
    'foundingDate'=> '2007-07-31',
    'address'     => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => $unit['alamat'],
        'addressLocality' => 'Kota Kupang',
        'addressRegion'   => 'Nusa Tenggara Timur',
        'addressCountry'  => 'ID',
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:opsz,wght@8..60,400;8..60,600;8..60,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets/css/yayasan.css?v=' . time()); ?>">
</head>

<body>
<a class="lewati" href="#utama">Lompat ke isi halaman</a>

<!-- Baris atas: kontak singkat dan tautan ke situs sekolah -->
<div class="bar">
    <div class="wadah bar__isi">
        <div class="bar__kiri">
            <?php if (!empty($unit['telepon'])): ?>
                <a href="tel:<?= preg_replace('/[^0-9+]/', '', $unit['telepon']); ?>"><?= html_escape($unit['telepon']); ?></a>
            <?php endif; ?>
            <?php if (!empty($unit['email'])): ?>
                <a href="mailto:<?= html_escape($unit['email']); ?>"><?= html_escape($unit['email']); ?></a>
            <?php endif; ?>
        </div>
        <div class="bar__kiri">
            <span>Senin&ndash;Jumat, 07.30&ndash;15.30 WITA</span>
        </div>
    </div>
</div>

<header class="kepala" id="kepala">
    <div class="wadah kepala__isi">
        <a class="merek" href="<?= $home; ?>">
            <img src="<?= $logo; ?>" alt="" width="52" height="52">
            <span class="merek__teks">
                Yayasan Citra Bina Insan Mandiri
                <small>Kupang &middot; Nusa Tenggara Timur</small>
            </span>
        </a>

        <nav class="menu" id="menu" aria-label="Menu utama">
            <?php foreach ($menu as $k => $m):
                $aktif = in_array($halaman, $m[2], TRUE); ?>

                <?php if (empty($m[3])): ?>
                    <div class="menu__item">
                        <a class="menu__tautan" href="<?= $m[1]; ?>"<?= $aktif ? ' aria-current="page"' : ''; ?>><?= $m[0]; ?></a>
                    </div>
                <?php else: ?>
                    <div class="menu__item" data-laci>
                        <button type="button" class="menu__tautan menu__laci-toggle" aria-expanded="false"
                                aria-controls="laci-<?= $k; ?>"<?= $aktif ? ' aria-current="page"' : ''; ?>>
                            <?= $m[0]; ?>
                            <svg class="menu__panah" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m6 9 6 6 6-6"/>
                            </svg>
                        </button>
                        <div class="laci" id="laci-<?= $k; ?>">
                            <?php foreach ($m[3] as $sub): ?>
                                <a href="<?= $sub[2]; ?>">
                                    <strong><?= html_escape($sub[0]); ?></strong>
                                    <span><?= html_escape($sub[1]); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>

        <div class="kepala__aksi">
            <!-- Tautan admin sengaja bergaya tenang, bukan tombol mencolok.
                 Tempat paling menonjol di kepala halaman semestinya untuk
                 pengunjung, sedangkan yang membuka panel cuma beberapa orang
                 dan mereka sudah tahu letaknya. -->
            <a class="tbl tbl--garis tbl--kecil kepala__admin" href="<?= base_url('auth'); ?>">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="4" y="10.5" width="16" height="10" rx="2"/>
                    <path d="M8 10.5V7a4 4 0 0 1 8 0v3.5"/>
                </svg>
                Masuk Admin
            </a>
            <button class="tombol-menu" id="tombolMenu" aria-label="Buka menu" aria-expanded="false" aria-controls="menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>

<main id="utama">
