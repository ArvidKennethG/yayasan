<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* ============================================================================
   KERANGKA PANEL ADMIN — BAGIAN ATAS
   ----------------------------------------------------------------------------
   Dipanggil controller Admin.php apa adanya: load->view('./templates/admin/
   header', $data). Tidak ada controller yang perlu diubah.

   Berkas ini juga memuat fungsi bantu kecil yang dipakai semua halaman admin.
   Ditaruh di sini karena header selalu dimuat paling awal.
   ========================================================================== */

if (!function_exists('adm_e')) {
    /**
     * Mencetak teks dengan aman.
     *
     * Controller lama menyimpan judul lewat htmlspecialchars() sebelum masuk
     * database, jadi sebagian data sudah berbentuk "&amp;". Kalau langsung
     * di-escape lagi, layar akan menampilkan "&amp;amp;". Maka didekode dulu,
     * baru di-escape sekali.
     */
    function adm_e($s)
    {
        return htmlspecialchars(html_entity_decode((string) $s, ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES, 'UTF-8');
    }

    /** Teks polos dari isi berformat HTML, dipotong di batas kata. */
    function adm_ringkas($html, $n = 120)
    {
        $t = preg_replace('~<(script|style)\b[^>]*>.*?</\1>~is', ' ', (string) $html);
        // Akhir paragraf, butir daftar, dan baris baru diberi spasi dulu, supaya
        // kata di dua paragraf berbeda tidak menempel setelah tag dibuang.
        $t = preg_replace('~<(br|/p|/li|/h[1-6]|/div|/tr|/td|/blockquote)\b[^>]*>~i', ' ', $t);
        $t = html_entity_decode(strip_tags($t), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $t = trim(preg_replace('/\s+/u', ' ', $t));
        if (mb_strlen($t, 'UTF-8') <= $n) { return $t; }
        $t = mb_substr($t, 0, $n, 'UTF-8');
        $sp = mb_strrpos($t, ' ', 0, 'UTF-8');
        return rtrim($sp > $n * 0.6 ? mb_substr($t, 0, $sp, 'UTF-8') : $t, " ,.;:") . '…';
    }

    /** Tanggal gaya Indonesia: 3 Sep 2026, 10.44 */
    function adm_tgl($dt, $jam = TRUE)
    {
        if (empty($dt) || strpos((string) $dt, '0000') === 0) { return '—'; }
        $w = strtotime($dt);
        if (!$w) { return '—'; }
        $b = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $s = date('j', $w) . ' ' . $b[(int) date('n', $w)] . ' ' . date('Y', $w);
        return $jam ? $s . ', ' . date('H.i', $w) : $s;
    }

    /** Jumlah baris, aman walau tabelnya belum ada di database. */
    function adm_hitung($tabel, $where = NULL)
    {
        $CI =& get_instance();
        if (!$CI->db->table_exists($tabel)) { return NULL; }
        if ($where) { $CI->db->where($where); }
        return (int) $CI->db->count_all_results($tabel);
    }

    /** ID video YouTube dari tautan bentuk apa pun, atau string kosong. */
    function adm_yt($url)
    {
        return preg_match('~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/|v/))([A-Za-z0-9_-]{11})~', (string) $url, $m) ? $m[1] : '';
    }

    /** Kelas warna untuk label status. */
    function adm_warna_status($status)
    {
        $peta = [
            'Baru' => 'biru', 'Dihubungi' => 'kuning', 'Diterima' => 'hijau', 'Ditolak' => 'merah',
            'Belum Dibaca' => 'merah', 'Sudah Dibaca' => 'kuning', 'Dibalas' => 'hijau',
            'Aktif' => 'hijau', 'Unsubscribed' => '',
        ];
        return isset($peta[$status]) ? $peta[$status] : '';
    }

    /** JSON yang aman ditaruh di dalam <script type="application/json">. */
    function adm_json($data)
    {
        return json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /** Mendekode entitas HTML di setiap nilai teks rekaman untuk dialog. */
    function adm_dekode(array $r)
    {
        foreach ($r as $k => $v) {
            if (is_string($v) && $k !== 'isi_berita' && $k !== 'isi_konten' && $k !== 'deskripsi') {
                $r[$k] = html_entity_decode($v, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }
        return $r;
    }

    /** Nomor WhatsApp: 0812... menjadi 62812... */
    function adm_wa($no)
    {
        return preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', (string) $no));
    }

    /**
     * Kotak unggah gambar dengan pratinjau.
     * Nama field ($name) harus sama persis dengan yang dibaca controller.
     */
    function adm_unggah($name, $wajib, $petunjuk = 'JPG atau PNG, maksimal 5 MB.', $bulat = FALSE)
    {
        $id = 'u_' . $name . '_' . substr(md5(uniqid('', TRUE)), 0, 6);
        return '<div class="unggah" data-maks="5">'
            . '<span class="unggah__pratinjau"' . ($bulat ? ' style="width:64px;border-radius:50%"' : '') . '>' . adm_ikon('foto', 22) . '</span>'
            . '<span class="unggah__teks"><strong>' . ($wajib ? 'Pilih gambar' : 'Ganti gambar (opsional)') . '</strong>'
            . 'Klik atau seret berkas ke sini. ' . htmlspecialchars($petunjuk, ENT_QUOTES, 'UTF-8')
            . '<span class="unggah__nama">' . ($wajib ? 'Belum ada berkas dipilih' : 'Kosongkan kalau gambarnya tidak diganti') . '</span></span>'
            . '<input type="file" id="' . $id . '" name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '" accept=".jpg,.jpeg,.png"' . ($wajib ? ' required' : '') . ' aria-label="Berkas gambar">'
            . '</div>';
    }

    /** Kepala dialog: judul, keterangan, dan tombol tutup. */
    function adm_kepala_dialog($judul, $ket = '', $id_judul = '')
    {
        return '<div class="dialog__kepala"><div>'
            . '<h2' . ($id_judul ? ' id="' . $id_judul . '"' : '') . ' data-tampil="_judul">' . htmlspecialchars($judul, ENT_QUOTES, 'UTF-8') . '</h2>'
            . ($ket ? '<p>' . htmlspecialchars($ket, ENT_QUOTES, 'UTF-8') . '</p>' : '')
            . '</div><button type="button" class="dialog__tutup" data-tutup aria-label="Tutup">' . adm_ikon('tutup', 18) . '</button></div>';
    }

    /** Ikon garis sederhana, sewarna teks di sekitarnya. */
    function adm_ikon($nama, $u = 18)
    {
        $p = [
            'dasbor'   => '<rect x="3" y="3" width="7.5" height="9" rx="1.2"/><rect x="13.5" y="3" width="7.5" height="5.5" rx="1.2"/><rect x="13.5" y="11.5" width="7.5" height="9.5" rx="1.2"/><rect x="3" y="15" width="7.5" height="6" rx="1.2"/>',
            'berita'   => '<path d="M4.5 4h11A1.5 1.5 0 0 1 17 5.5V19a1.5 1.5 0 0 0 1.5 1.5H5.5A1.5 1.5 0 0 1 4 19V4.5"/><path d="M17 8h2.5v11a1.5 1.5 0 0 1-3 0"/><path d="M7.5 8h6M7.5 11.5h6M7.5 15h3.5"/>',
            'galeri'   => '<rect x="3" y="4.5" width="18" height="15" rx="1.5"/><circle cx="8.5" cy="9.5" r="1.6"/><path d="m4 17.5 4.5-4.5 3.5 3.5 3-3 5 5"/>',
            'video'    => '<rect x="2.5" y="5" width="19" height="14" rx="1.5"/><path d="m10 9.2 5 2.8-5 2.8z"/>',
            'struktur' => '<rect x="9" y="3" width="6" height="5" rx="1"/><rect x="2.5" y="16" width="6" height="5" rx="1"/><rect x="15.5" y="16" width="6" height="5" rx="1"/><path d="M12 8v4M5.5 16v-2.5h13V16"/>',
            'konten'   => '<path d="M14 3H7a1.5 1.5 0 0 0-1.5 1.5v15A1.5 1.5 0 0 0 7 21h10a1.5 1.5 0 0 0 1.5-1.5V7.5z"/><path d="M14 3v4.5h4.5M9 12.5h6M9 16h4"/>',
            'daftar'   => '<rect x="4.5" y="3.5" width="15" height="17" rx="1.5"/><path d="M9 3.5V5h6V3.5M8.5 10h7M8.5 13.5h7M8.5 17h4"/>',
            'sd'       => '<path d="M4 19V6.5A1.5 1.5 0 0 1 5.5 5H11v14H5.5A1.5 1.5 0 0 0 4 20.5"/><path d="M20 19V6.5A1.5 1.5 0 0 0 18.5 5H13v14h5.5a1.5 1.5 0 0 1 1.5 1.5"/>',
            'tk'       => '<path d="M5 20v-8.5L12 5l7 6.5V20"/><path d="M9.5 20v-5h5v5"/><circle cx="12" cy="10.5" r="1.3"/>',
            'pesan'    => '<rect x="3" y="5" width="18" height="14" rx="1.5"/><path d="m3.5 6 8.5 7 8.5-7"/>',
            'surat'    => '<path d="M4 8.5 12 4l8 4.5v10A1.5 1.5 0 0 1 18.5 20h-13A1.5 1.5 0 0 1 4 18.5z"/><path d="m4 8.5 8 5 8-5"/>',
            'cadangan' => '<ellipse cx="12" cy="5.5" rx="7.5" ry="2.5"/><path d="M4.5 5.5v6c0 1.4 3.4 2.5 7.5 2.5s7.5-1.1 7.5-2.5v-6"/><path d="M4.5 11.5v6c0 1.4 3.4 2.5 7.5 2.5s7.5-1.1 7.5-2.5v-6"/>',
            'katalog'  => '<path d="M5 4.5A1.5 1.5 0 0 1 6.5 3H19v15H6.5A1.5 1.5 0 0 0 5 19.5z"/><path d="M5 19.5A1.5 1.5 0 0 0 6.5 21H19"/>',
            'situs'    => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.7 5.6 3.7 9s-1.2 6.4-3.7 9c-2.5-2.6-3.7-5.6-3.7-9S9.5 5.6 12 3z"/>',
            'keluar'   => '<path d="M9.5 20.5h-4A1.5 1.5 0 0 1 4 19V5a1.5 1.5 0 0 1 1.5-1.5h4"/><path d="m15.5 16.5 4.5-4.5-4.5-4.5M20 12H9.5"/>',
            'tambah'   => '<path d="M12 5v14M5 12h14"/>',
            'ubah'     => '<path d="M15.5 4.5 19.5 8.5 8.5 19.5H4.5v-4z"/><path d="m13.5 6.5 4 4"/>',
            'hapus'    => '<path d="M4.5 7h15M9.5 7V4.5h5V7M6.5 7l1 12.5A1.5 1.5 0 0 0 9 21h6a1.5 1.5 0 0 0 1.5-1.5L17.5 7"/><path d="M10 11v6M14 11v6"/>',
            'lihat'    => '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
            'cari'     => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
            'tutup'    => '<path d="M6 6l12 12M18 6 6 18"/>',
            'unduh'    => '<path d="M12 4v11M7.5 10.5 12 15l4.5-4.5M5 20h14"/>',
            'wa'       => '<path d="M4 20l1.2-4.1A8 8 0 1 1 8.3 19z"/><path d="M9 9.2c0 3 2.8 5.8 5.8 5.8l1.2-1.4-2-1-1 .8c-1-.4-2-1.4-2.4-2.4l.8-1-1-2z"/>',
            'balas'    => '<path d="M10 8 5 12.5 10 17"/><path d="M5 12.5h9a5 5 0 0 1 5 5V19"/>',
            'foto'     => '<rect x="3" y="4.5" width="18" height="15" rx="1.5"/><circle cx="12" cy="12" r="3.5"/><path d="M8 4.5 9.5 2.5h5L16 4.5"/>',
            'awas'     => '<path d="M12 3.5 2.5 20h19z"/><path d="M12 10v4.5M12 17.5h.01"/>',
            'salin'    => '<rect x="8.5" y="8.5" width="12" height="12" rx="1.5"/><path d="M15.5 8.5V5A1.5 1.5 0 0 0 14 3.5H5A1.5 1.5 0 0 0 3.5 5v9A1.5 1.5 0 0 0 5 15.5h3.5"/>',
            'jam'      => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
            'orang'    => '<circle cx="12" cy="8" r="4"/><path d="M4 20.5c0-4 3.6-6.5 8-6.5s8 2.5 8 6.5"/>',
            'grafik'   => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
            'tautan'   => '<path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7L11.5 6.8"/><path d="M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1.5-1.5"/>',
            'main'     => '<path d="M8 5.5v13l10.5-6.5z" fill="currentColor"/>',
            'menu'     => '<path d="M4 7h16M4 12h16M4 17h16"/>',
            'bawah'    => '<path d="m6 9 6 6 6-6"/>',
            'saring'   => '<path d="M3.5 5h17l-6.5 8v6l-4-2v-4z"/>',
        ];
        $d = isset($p[$nama]) ? $p[$nama] : $p['konten'];
        return '<svg width="' . (int) $u . '" height="' . (int) $u . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';
    }
}

/* ------------------------------------------------------------------ PERAN */
$peran    = (string) $this->session->userdata('role');
$pengguna = (string) $this->session->userdata('username');
$menu     = isset($menu) ? $menu : '';

$peran_super = in_array($peran, ['default', 'administrator'], TRUE);
$peran_unit  = in_array($peran, ['admin_tk', 'admin_sd', 'admin_smp', 'admin_sma', 'admin_ucb'], TRUE);

$label_peran = [
    'default' => 'Administrator Utama', 'administrator' => 'Administrator', 'katalog' => 'Pengelola Katalog',
    'admin_tk' => 'Admin TK', 'admin_sd' => 'Admin SD', 'admin_smp' => 'Admin SMP', 'admin_sma' => 'Admin SMA', 'admin_ucb' => 'Admin UCB',
];

/* ------------------------------------------------------------ LENCANA MENU */
/* Angka kecil di samping menu: berapa pendaftar yang belum diproses dan pesan
   yang belum dibaca. Supaya admin langsung tahu ada pekerjaan tanpa membuka
   halamannya satu per satu. */
$lencana = [];
if ($peran_super || $peran_unit) {
    $lencana['pendaftaran_terpadu'] = adm_hitung('pendaftaran', ['status' => 'Baru']);
}
if ($peran_super) {
    $lencana['pesan_kontak'] = adm_hitung('pesan_kontak', ['status' => 'Belum Dibaca']);
}

/* ------------------------------------------------------------------- MENU */
/* Susunan ini mengikuti hak akses yang sudah diperiksa controller:
   - default & administrator : semua menu
   - admin_tk, admin_sd, dst : dasbor dan pendaftaran
   - katalog & peran lain    : kelola katalog saja (seperti versi lama)

   Menu PPDB SD dan PPDB TK sudah dihapus atas permintaan. Keduanya membaca
   tabel lama `pendaftaran_sd` dan `pendaftaran_tk` peninggalan subsitus TK/SD,
   sedangkan pendaftaran yang berjalan sekarang semuanya lewat tabel
   `pendaftaran` di halaman Pendaftaran. */
$grup = [];
if ($peran_super) {
    $grup['Utama'] = [
        'dashboard'           => ['Dasbor', 'admin', 'dasbor'],
        'pendaftaran_terpadu' => ['Pendaftaran', 'admin/pendaftaran_terpadu', 'daftar'],
    ];
    $grup['Konten Situs'] = [
        'berita'              => ['Berita', 'admin/berita', 'berita'],
        'galeri'              => ['Galeri Foto', 'admin/galeri', 'galeri'],
        'video_kegiatan'      => ['Video Kegiatan', 'admin/video_kegiatan', 'video'],
        'struktur_organisasi' => ['Struktur Organisasi', 'admin/struktur_organisasi', 'struktur'],
        'manajemen_konten'    => ['Profil Yayasan', 'admin/manajemen_konten', 'konten'],
    ];
    $grup['Komunikasi'] = [
        'pesan_kontak' => ['Pesan Masuk', 'admin/pesan_kontak', 'pesan'],
        'newsletter'   => ['Newsletter', 'admin/newsletter', 'surat'],
    ];
    $grup['Sistem'] = [
        'backup' => ['Cadangan Data', 'admin/backup', 'cadangan'],
    ];
} elseif ($peran_unit) {
    $grup['Utama'] = [
        'dashboard'           => ['Dasbor', 'admin', 'dasbor'],
        'pendaftaran_terpadu' => ['Pendaftaran', 'admin/pendaftaran_terpadu', 'daftar'],
    ];
} else {
    $grup['Katalog'] = ['katalog' => ['Kelola Katalog', 'katalog', 'katalog']];
}

$judul_halaman = [
    'dashboard' => 'Dasbor', 'berita' => 'Berita', 'galeri' => 'Galeri Foto', 'video_kegiatan' => 'Video Kegiatan',
    'struktur_organisasi' => 'Struktur Organisasi', 'manajemen_konten' => 'Profil Yayasan',
    'pendaftaran_terpadu' => 'Pendaftaran', 'pendaftaran_sd' => 'PPDB SD (lama)', 'pendaftaran_tk' => 'PPDB TK (lama)',
    'pesan_kontak' => 'Pesan Masuk', 'newsletter' => 'Newsletter', 'backup' => 'Cadangan Data',
];
$judul = isset($judul_halaman[$menu]) ? $judul_halaman[$menu] : 'Panel Admin';
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
<title><?= adm_e($judul); ?> — Panel Admin Yayasan CBIM</title>
<link rel="icon" href="<?= $logo; ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets/admin/admin.css?v=' . $v_css); ?>">
</head>
<body>

<div class="tirai" id="tirai"></div>

<div class="tata">
    <aside class="sisi" id="sisi" aria-label="Menu panel admin">
        <a class="sisi__merek" href="<?= base_url('admin'); ?>">
            <img src="<?= $logo; ?>" alt="">
            <span>
                <strong>Yayasan CBIM</strong>
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
                        <?php if (!empty($lencana[$kunci])): ?>
                            <span class="sisi__lencana" title="<?= (int) $lencana[$kunci]; ?> belum diproses"><?= (int) $lencana[$kunci]; ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        <?php endforeach; ?>

        <div class="sisi__kaki">
            <a class="sisi__tautan" href="<?= base_url(); ?>" target="_blank" rel="noopener"><?= adm_ikon('situs'); ?> <span>Lihat situs</span></a>
            <a class="sisi__tautan" href="<?= base_url('logout'); ?>"><?= adm_ikon('keluar'); ?> <span>Keluar</span></a>
        </div>
    </aside>

    <div class="utama">
        <header class="atas">
            <button type="button" class="tombol-sisi" id="tombolSisi" aria-label="Buka menu" aria-expanded="false" aria-controls="sisi">
                <?= adm_ikon('menu', 20); ?>
            </button>
            <div>
                <p class="atas__jejak">
                    <a href="<?= base_url('admin'); ?>">Panel Admin</a><?php if ($kelompok && $kelompok !== 'Utama'): ?> &rsaquo; <?= adm_e($kelompok); ?><?php endif; ?>
                </p>
                <h1 class="atas__judul"><?= adm_e($judul); ?></h1>
            </div>
            <div class="atas__kanan">
                <div class="akun">
                    <button type="button" class="akun__tombol" id="akunTombol" aria-haspopup="true" aria-expanded="false" aria-controls="akunMenu">
                        <span class="akun__bulat" aria-hidden="true"><?= adm_e($inisial); ?></span>
                        <span class="akun__nama">
                            <?= adm_e($pengguna); ?>
                            <small><?= adm_e(isset($label_peran[$peran]) ? $label_peran[$peran] : $peran); ?></small>
                        </span>
                        <?= adm_ikon('bawah', 14); ?>
                    </button>
                    <div class="akun__menu" id="akunMenu" hidden>
                        <a href="<?= base_url(); ?>" target="_blank" rel="noopener"><?= adm_ikon('situs', 16); ?> Lihat situs yayasan</a>
                        <hr>
                        <a href="<?= base_url('logout'); ?>"><?= adm_ikon('keluar', 16); ?> Keluar</a>
                    </div>
                </div>
            </div>
        </header>

        <main class="ruang" id="isi">
