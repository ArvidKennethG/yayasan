<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* ============================================================================
   KERANGKA PANEL ADMIN SD — BAGIAN ATAS (HEADER + SIDEBAR)
   Replika dari templates/admin/header.php dengan branding SD (biru).
   ========================================================================== */

if (!function_exists('adm_e')) {
    function adm_e($s) {
        return htmlspecialchars(html_entity_decode((string) $s, ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES, 'UTF-8');
    }
    function adm_ringkas($html, $n = 120) {
        $t = preg_replace('~<(script|style)\b[^>]*>.*?</\1>~is', ' ', (string) $html);
        $t = preg_replace('~<(br|/p|/li|/h[1-6]|/div|/tr|/td|/blockquote)\b[^>]*>~i', ' ', $t);
        $t = html_entity_decode(strip_tags($t), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $t = trim(preg_replace('/\s+/u', ' ', $t));
        if (mb_strlen($t, 'UTF-8') <= $n) return $t;
        $t = mb_substr($t, 0, $n, 'UTF-8');
        $sp = mb_strrpos($t, ' ', 0, 'UTF-8');
        return rtrim($sp > $n * 0.6 ? mb_substr($t, 0, $sp, 'UTF-8') : $t, " ,.;:") . '…';
    }
    function adm_tgl($dt, $jam = TRUE) {
        if (empty($dt) || strpos((string) $dt, '0000') === 0) return '—';
        $w = strtotime($dt);
        if (!$w) return '—';
        $b = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $s = date('j', $w) . ' ' . $b[(int) date('n', $w)] . ' ' . date('Y', $w);
        return $jam ? $s . ', ' . date('H.i', $w) : $s;
    }
    function adm_hitung($tabel, $where = NULL) {
        $CI =& get_instance();
        if (!$CI->db->table_exists($tabel)) return NULL;
        if ($where) $CI->db->where($where);
        return (int) $CI->db->count_all_results($tabel);
    }
    function adm_json($data) {
        return json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    function adm_dekode(array $r) {
        foreach ($r as $k => $v) {
            if (is_string($v) && $k !== 'isi_berita' && $k !== 'isi_konten' && $k !== 'deskripsi') {
                $r[$k] = html_entity_decode($v, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }
        return $r;
    }
    function adm_wa($no) {
        return preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', (string) $no));
    }
    function adm_warna_status($status) {
        $peta = [
            'Baru' => 'biru', 'Dihubungi' => 'kuning', 'Diterima' => 'hijau', 'Ditolak' => 'merah',
        ];
        return isset($peta[$status]) ? $peta[$status] : '';
    }
    function adm_kepala_dialog($judul, $ket = '', $id_judul = '') {
        return '<div class="dialog__kepala"><div>'
            . '<h2' . ($id_judul ? ' id="' . $id_judul . '"' : '') . ' data-tampil="_judul">' . htmlspecialchars($judul, ENT_QUOTES, 'UTF-8') . '</h2>'
            . ($ket ? '<p>' . htmlspecialchars($ket, ENT_QUOTES, 'UTF-8') . '</p>' : '')
            . '</div><button type="button" class="dialog__tutup" data-tutup aria-label="Tutup">' . adm_ikon('tutup', 18) . '</button></div>';
    }
    function adm_ikon($nama, $u = 18) {
        $p = [
            'dasbor'   => '<rect x="3" y="3" width="7.5" height="9" rx="1.2"/><rect x="13.5" y="3" width="7.5" height="5.5" rx="1.2"/><rect x="13.5" y="11.5" width="7.5" height="9.5" rx="1.2"/><rect x="3" y="15" width="7.5" height="6" rx="1.2"/>',
            'berita'   => '<path d="M4.5 4h11A1.5 1.5 0 0 1 17 5.5V19a1.5 1.5 0 0 0 1.5 1.5H5.5A1.5 1.5 0 0 1 4 19V4.5"/><path d="M17 8h2.5v11a1.5 1.5 0 0 1-3 0"/><path d="M7.5 8h6M7.5 11.5h6M7.5 15h3.5"/>',
            'konten'   => '<path d="M14 3H7a1.5 1.5 0 0 0-1.5 1.5v15A1.5 1.5 0 0 0 7 21h10a1.5 1.5 0 0 0 1.5-1.5V7.5z"/><path d="M14 3v4.5h4.5M9 12.5h6M9 16h4"/>',
            'daftar'   => '<rect x="4.5" y="3.5" width="15" height="17" rx="1.5"/><path d="M9 3.5V5h6V3.5M8.5 10h7M8.5 13.5h7M8.5 17h4"/>',
            'sd'       => '<path d="M4 19V6.5A1.5 1.5 0 0 1 5.5 5H11v14H5.5A1.5 1.5 0 0 0 4 20.5"/><path d="M20 19V6.5A1.5 1.5 0 0 0 18.5 5H13v14h5.5a1.5 1.5 0 0 1 1.5 1.5"/>',
            'situs'    => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.7 5.6 3.7 9s-1.2 6.4-3.7 9c-2.5-2.6-3.7-5.6-3.7-9S9.5 5.6 12 3z"/>',
            'keluar'   => '<path d="M9.5 20.5h-4A1.5 1.5 0 0 1 4 19V5a1.5 1.5 0 0 1 1.5-1.5h4"/><path d="m15.5 16.5 4.5-4.5-4.5-4.5M20 12H9.5"/>',
            'tambah'   => '<path d="M12 5v14M5 12h14"/>',
            'ubah'     => '<path d="M15.5 4.5 19.5 8.5 8.5 19.5H4.5v-4z"/><path d="m13.5 6.5 4 4"/>',
            'hapus'    => '<path d="M4.5 7h15M9.5 7V4.5h5V7M6.5 7l1 12.5A1.5 1.5 0 0 0 9 21h6a1.5 1.5 0 0 0 1.5-1.5L17.5 7"/><path d="M10 11v6M14 11v6"/>',
            'cari'     => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
            'tutup'    => '<path d="M6 6l12 12M18 6 6 18"/>',
            'orang'    => '<circle cx="12" cy="8" r="4"/><path d="M4 20.5c0-4 3.6-6.5 8-6.5s8 2.5 8 6.5"/>',
            'grafik'   => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
            'menu'     => '<path d="M4 7h16M4 12h16M4 17h16"/>',
            'bawah'    => '<path d="m6 9 6 6 6-6"/>',
            'foto'     => '<rect x="3" y="4.5" width="18" height="15" rx="1.5"/><circle cx="12" cy="12" r="3.5"/><path d="M8 4.5 9.5 2.5h5L16 4.5"/>',
        ];
        $d = isset($p[$nama]) ? $p[$nama] : $p['konten'];
        return '<svg width="' . (int) $u . '" height="' . (int) $u . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';
    }
}

/* ------------------------------------------------------------------ PERAN */
$pengguna = (string) $this->session->userdata('username');
$peran    = (string) $this->session->userdata('role');
$menu     = isset($menu) ? $menu : '';

$label_peran = [
    'default' => 'Administrator Utama', 'administrator' => 'Administrator',
    'admin_sd' => 'Admin SD',
];

/* ------------------------------------------------------------------- MENU */
$grup = [];
$grup['Utama'] = [
    'dashboard' => ['Dasbor', 'admin-sd', 'dasbor'],
    'profil'    => ['Kelola Profil', 'admin-sd/profil', 'konten'],
];

$judul_halaman = [
    'dashboard' => 'Dasbor',
    'profil'    => 'Kelola Profil SD',
];
$judul = isset($judul_halaman[$menu]) ? $judul_halaman[$menu] : 'Panel Admin SD';
$kelompok = '';
foreach ($grup as $nama_grup => $isi) { if (isset($isi[$menu])) { $kelompok = $nama_grup; } }

$inisial = strtoupper(mb_substr($pengguna !== '' ? $pengguna : 'A', 0, 1, 'UTF-8'));
$logo    = base_url('assets/templates/media/logos/logo-cbim.png');
$v_css   = @filemtime(FCPATH . 'assets/admin/admin.css') ?: '1';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= adm_e($judul); ?> — Panel Admin SD CBIM</title>
<link rel="icon" href="<?= $logo; ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets/admin/admin.css?v=' . $v_css); ?>">
<style>
    /* Override warna aksen untuk branding SD */
    :root {
        --aksen: #2980b9;
        --aksen-gelap: #1a5276;
        --aksen-terang: #ebf5fb;
    }
    .sisi__merek strong { color: #2980b9 !important; }
    .sapa { background: linear-gradient(135deg, #2980b9 0%, #3498db 100%) !important; }
    .tbl--utama { background: #2980b9 !important; border-color: #2980b9 !important; }
    .tbl--utama:hover { background: #1a5276 !important; border-color: #1a5276 !important; }
    .sisi__tautan[aria-current="page"] { background: rgba(41, 128, 185, 0.1) !important; color: #2980b9 !important; }
    .sisi__tautan[aria-current="page"]::before { background: #2980b9 !important; }
    .angka__item--sorot { background: linear-gradient(135deg, #2980b9, #3498db) !important; }
</style>
</head>
<body>

<div class="tirai" id="tirai"></div>

<div class="tata">
    <aside class="sisi" id="sisi" aria-label="Menu panel admin SD">
        <a class="sisi__merek" href="<?= base_url('admin-sd'); ?>">
            <img src="<?= $logo; ?>" alt="">
            <span>
                <strong>SD K Citra Bangsa</strong>
                <small>Panel Admin</small>
            </span>
        </a>

        <?php foreach ($grup as $nama_grup => $isi): ?>
            <nav class="sisi__grup" aria-label="<?= adm_e($nama_grup); ?>">
                <div class="sisi__judul"><?= adm_e($nama_grup); ?></div>
                <?php foreach ($isi as $kunci => $m): ?>
                    <a class="sisi__tautan" href="<?= base_url($m[1]); ?>"<?= $menu === $kunci ? ' aria-current="page"' : ''; ?>>
                        <?= adm_ikon($m[2]); ?>
                        <span><?= adm_e($m[0]); ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>
        <?php endforeach; ?>

        <div class="sisi__kaki">
            <a class="sisi__tautan" href="<?= base_url('sd'); ?>" target="_blank" rel="noopener"><?= adm_ikon('situs'); ?> <span>Lihat situs SD</span></a>
            <a class="sisi__tautan" href="<?= base_url('admin-sd/logout'); ?>"><?= adm_ikon('keluar'); ?> <span>Keluar</span></a>
        </div>
    </aside>

    <div class="utama">
        <header class="atas">
            <button type="button" class="tombol-sisi" id="tombolSisi" aria-label="Buka menu" aria-expanded="false" aria-controls="sisi">
                <?= adm_ikon('menu', 20); ?>
            </button>
            <div>
                <p class="atas__jejak">
                    <a href="<?= base_url('admin-sd'); ?>">Panel Admin SD</a><?php if ($kelompok && $kelompok !== 'Utama'): ?> &rsaquo; <?= adm_e($kelompok); ?><?php endif; ?>
                </p>
                <h1 class="atas__judul"><?= adm_e($judul); ?></h1>
            </div>
            <div class="atas__kanan">
                <div class="akun">
                    <button type="button" class="akun__tombol" id="akunTombol" aria-haspopup="true" aria-expanded="false" aria-controls="akunMenu">
                        <span class="akun__bulat" aria-hidden="true" style="background:#2980b9"><?= adm_e($inisial); ?></span>
                        <span class="akun__nama">
                            <?= adm_e($pengguna); ?>
                            <small><?= adm_e(isset($label_peran[$peran]) ? $label_peran[$peran] : $peran); ?></small>
                        </span>
                        <?= adm_ikon('bawah', 14); ?>
                    </button>
                    <div class="akun__menu" id="akunMenu" hidden>
                        <a href="<?= base_url('sd'); ?>" target="_blank" rel="noopener"><?= adm_ikon('situs', 16); ?> Lihat situs SD</a>
                        <hr>
                        <a href="<?= base_url('admin-sd/logout'); ?>"><?= adm_ikon('keluar', 16); ?> Keluar</a>
                    </div>
                </div>
            </div>
        </header>

        <main class="ruang" id="isi">
