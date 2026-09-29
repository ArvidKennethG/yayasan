<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* ============================================================================
   DASBOR
   ----------------------------------------------------------------------------
   Controller hanya mengirim tiga angka kunjungan ($daily_visits,
   $monthly_visits, $yearly_visits). Ringkasan lainnya — grafik 14 hari,
   pendaftar baru, pesan belum dibaca, berita terakhir — dihitung langsung di
   sini dengan kueri ringan, supaya Admin.php tidak perlu diubah.
   Setiap kueri memeriksa dulu apakah tabelnya ada.
   ========================================================================== */

$CI    =& get_instance();
$db    = $CI->db;
$peran = (string) $CI->session->userdata('role');
$super = in_array($peran, ['default', 'administrator'], TRUE);
$unit  = ['admin_tk' => 'TK', 'admin_sd' => 'SD', 'admin_smp' => 'SMP', 'admin_sma' => 'SMA', 'admin_ucb' => 'UCB'];
$jenjang_unit = isset($unit[$peran]) ? $unit[$peran] : NULL;

/* ---- Kunjungan 14 hari terakhir ---- */
$hari = [];
for ($i = 13; $i >= 0; $i--) { $hari[date('Y-m-d', strtotime("-$i day"))] = 0; }
if ($db->table_exists('visitor_logs')) {
    $rows = $db->select('visit_date, COUNT(*) AS n', FALSE)
               ->where('visit_date >=', array_keys($hari)[0])
               ->group_by('visit_date')->get('visitor_logs')->result_array();
    foreach ($rows as $r) { if (isset($hari[$r['visit_date']])) { $hari[$r['visit_date']] = (int) $r['n']; } }
}
$puncak = max(1, max($hari));
$nama_hari = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

/* ---- Ringkasan isi ---- */
$jml = [
    'berita'   => adm_hitung('berita'),
    'galeri'   => adm_hitung('galeri'),
    'video'    => adm_hitung('video_kegiatan'),
    'struktur' => adm_hitung('struktur_organisasi'),
];
// Hanya dari tabel `pendaftaran`. Tabel lama pendaftaran_sd dan pendaftaran_tk
// tidak lagi dihitung karena halamannya sudah tidak ada di panel.
$daftar_baru = (int) adm_hitung('pendaftaran', $jenjang_unit ? ['status' => 'Baru', 'jenjang' => $jenjang_unit] : ['status' => 'Baru']);
$pesan_baru = $super ? (int) adm_hitung('pesan_kontak', ['status' => 'Belum Dibaca']) : 0;
$pelanggan  = $super ? (int) adm_hitung('newsletter_subscribers', ['status' => 'Aktif']) : 0;

/* ---- Pendaftar terbaru ---- */
$pendaftar = [];
if ($db->table_exists('pendaftaran')) {
    if ($jenjang_unit) { $db->where('jenjang', $jenjang_unit); }
    $pendaftar = $db->order_by('id_pendaftaran', 'DESC')->limit(5)->get('pendaftaran')->result_array();
}

/* ---- Berita terbaru ---- */
$berita_akhir = [];
if ($super && $db->table_exists('berita')) {
    $berita_akhir = $db->order_by('tanggal_post', 'DESC')->limit(4)->get('berita')->result_array();
}

$jam = (int) date('G');
$salam = $jam < 11 ? 'Selamat pagi' : ($jam < 15 ? 'Selamat siang' : ($jam < 19 ? 'Selamat sore' : 'Selamat malam'));
$bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$hari_ini = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][(int) date('w')] . ', ' . date('j') . ' ' . $bulan[(int) date('n')] . ' ' . date('Y');
?>

<section class="sapa">
    <div>
        <div class="sapa__tanggal"><?= adm_e($hari_ini); ?></div>
        <h1><?= adm_e($salam); ?>, <?= adm_e($CI->session->userdata('username')); ?></h1>
        <p>
            <?php if ($daftar_baru || $pesan_baru): ?>
                Ada <?= $daftar_baru ? '<strong style="color:#fff">' . $daftar_baru . ' pendaftar baru</strong>' : ''; ?><?= $daftar_baru && $pesan_baru ? ' dan ' : ''; ?><?= $pesan_baru ? '<strong style="color:#fff">' . $pesan_baru . ' pesan belum dibaca</strong>' : ''; ?> yang menunggu.
            <?php else: ?>
                Tidak ada pendaftar atau pesan yang menunggu. Semuanya sudah ditangani.
            <?php endif; ?>
        </p>
    </div>
    <div class="kepala__aksi">
        <?php if ($super): ?>
            <a class="tbl tbl--emas" href="<?= base_url('admin/berita#tambah'); ?>"><?= adm_ikon('tambah', 16); ?> Tulis berita</a>
        <?php endif; ?>
        <a class="tbl tbl--garis" href="<?= base_url(); ?>" target="_blank" rel="noopener"><?= adm_ikon('situs', 16); ?> Lihat situs</a>
    </div>
</section>

<div class="angka">
    <div class="angka__item angka__item--sorot">
        <span class="angka__label"><?= adm_ikon('orang', 15); ?> Kunjungan hari ini</span>
        <span class="angka__nilai"><?= number_format((int) $daily_visits, 0, ',', '.'); ?></span>
        <span class="angka__catatan">pengunjung unik</span>
    </div>
    <div class="angka__item">
        <span class="angka__label"><?= adm_ikon('grafik', 15); ?> Bulan ini</span>
        <span class="angka__nilai"><?= number_format((int) $monthly_visits, 0, ',', '.'); ?></span>
        <span class="angka__catatan"><?= adm_e($bulan[(int) date('n')] . ' ' . date('Y')); ?></span>
    </div>
    <div class="angka__item">
        <span class="angka__label"><?= adm_ikon('grafik', 15); ?> Tahun ini</span>
        <span class="angka__nilai"><?= number_format((int) $yearly_visits, 0, ',', '.'); ?></span>
        <span class="angka__catatan">sejak 1 Januari <?= date('Y'); ?></span>
    </div>
    <?php if ($super || $jenjang_unit): ?>
        <a class="angka__item" href="<?= base_url('admin/pendaftaran_terpadu?status=Baru'); ?>">
            <span class="angka__label"><?= adm_ikon('daftar', 15); ?> Pendaftar baru</span>
            <span class="angka__nilai"><?= $daftar_baru; ?></span>
            <span class="angka__catatan">belum dihubungi</span>
        </a>
    <?php endif; ?>
    <?php if ($super): ?>
        <a class="angka__item" href="<?= base_url('admin/pesan_kontak'); ?>">
            <span class="angka__label"><?= adm_ikon('pesan', 15); ?> Pesan belum dibaca</span>
            <span class="angka__nilai"><?= $pesan_baru; ?></span>
            <span class="angka__catatan"><?= $pelanggan; ?> pelanggan newsletter aktif</span>
        </a>
    <?php endif; ?>
</div>

<div class="kisi kisi--utama">
    <div class="kisi">
        <section class="kartu">
            <div class="kartu__kepala">
                <h3>Kunjungan 14 hari terakhir</h3>
                <span class="cap cap--polos"><?= number_format(array_sum($hari), 0, ',', '.'); ?> kunjungan</span>
            </div>
            <div class="kartu__isi" style="padding-top:6px">
                <div class="grafik" role="img" aria-label="Grafik batang jumlah kunjungan per hari selama 14 hari terakhir">
                    <?php foreach ($hari as $tgl => $n):
                        $t = round($n / $puncak * 100); $w = strtotime($tgl); ?>
                        <div class="grafik__batang" style="--t:<?= $t; ?>%" title="<?= adm_e(adm_tgl($tgl, FALSE)); ?>: <?= $n; ?> kunjungan">
                            <span style="height:<?= $t; ?>%"></span>
                            <b><?= $n ?: ''; ?></b>
                            <i><?= $tgl === date('Y-m-d') ? 'Ini' : $nama_hari[(int) date('w', $w)] . ' ' . date('j', $w); ?></i>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <?php if ($super || $jenjang_unit): ?>
        <section class="kartu">
            <div class="kartu__kepala">
                <h3>Pendaftar terbaru<?= $jenjang_unit ? ' · ' . adm_e($jenjang_unit) : ''; ?></h3>
                <a href="<?= base_url('admin/pendaftaran_terpadu'); ?>">Lihat semua &rarr;</a>
            </div>
            <?php if (empty($pendaftar)): ?>
                <div class="kosong" style="padding:32px 20px">
                    <p style="margin:0">Belum ada pendaftar yang masuk lewat formulir terpadu.</p>
                </div>
            <?php else: ?>
                <div class="tabel-bungkus">
                    <table>
                        <thead><tr><th>Calon siswa</th><th>Jenjang</th><th>Tanggal</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach ($pendaftar as $p): ?>
                            <tr>
                                <td>
                                    <span class="utama-sel"><?= adm_e($p['nama_lengkap']); ?></span>
                                    <span class="sub-sel"><?= adm_e($p['no_registrasi']); ?></span>
                                </td>
                                <td><span class="cap cap--polos cap--navy"><?= adm_e($p['jenjang']); ?></span></td>
                                <td class="sub-sel" style="white-space:nowrap"><?= adm_tgl($p['tanggal_daftar']); ?></td>
                                <td><span class="cap cap--<?= adm_warna_status($p['status']); ?>"><?= adm_e($p['status']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
        <?php endif; ?>
    </div>

    <div class="kisi" style="align-content:start">
        <?php if ($super): ?>
        <section class="kartu">
            <div class="kartu__kepala"><h3>Pintasan</h3></div>
            <div class="pintasan">
                <a href="<?= base_url('admin/berita#tambah'); ?>"><?= adm_ikon('berita'); ?> Tulis berita</a>
                <a href="<?= base_url('admin/galeri#tambah'); ?>"><?= adm_ikon('galeri'); ?> Unggah foto</a>
                <a href="<?= base_url('admin/video_kegiatan#tambah'); ?>"><?= adm_ikon('video'); ?> Tambah video</a>
                <a href="<?= base_url('admin/struktur_organisasi'); ?>"><?= adm_ikon('struktur'); ?> Struktur</a>
                <a href="<?= base_url('admin/manajemen_konten'); ?>"><?= adm_ikon('konten'); ?> Profil yayasan</a>
                <a href="<?= base_url('backup/database'); ?>"><?= adm_ikon('unduh'); ?> Unduh cadangan</a>
            </div>
        </section>

        <section class="kartu">
            <div class="kartu__kepala"><h3>Isi situs</h3></div>
            <ul class="daftar-ringkas">
                <li><span class="isi"><strong>Berita</strong></span><span class="cap cap--polos"><?= (int) $jml['berita']; ?></span></li>
                <li><span class="isi"><strong>Foto galeri</strong></span><span class="cap cap--polos"><?= (int) $jml['galeri']; ?></span></li>
                <li><span class="isi"><strong>Video kegiatan</strong></span><span class="cap cap--polos"><?= (int) $jml['video']; ?></span></li>
                <li><span class="isi"><strong>Pengurus</strong></span><span class="cap cap--polos"><?= (int) $jml['struktur']; ?></span></li>
            </ul>
        </section>

        <section class="kartu">
            <div class="kartu__kepala">
                <h3>Berita terakhir</h3>
                <a href="<?= base_url('admin/berita'); ?>">Kelola &rarr;</a>
            </div>
            <?php if (empty($berita_akhir)): ?>
                <div class="kosong" style="padding:28px 20px"><p style="margin:0">Belum ada berita.</p></div>
            <?php else: ?>
                <ul class="daftar-ringkas">
                    <?php foreach ($berita_akhir as $b): ?>
                        <li>
                            <img class="gambar-mini" src="<?= base_url('uploads/berita/' . rawurlencode($b['gambar'])); ?>" alt="" loading="lazy">
                            <span class="isi">
                                <strong><?= adm_e($b['judul_berita']); ?></strong>
                                <span><?= adm_tgl($b['tanggal_post'], FALSE); ?></span>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
        <?php elseif (!$jenjang_unit): ?>
        <section class="kartu">
            <div class="kartu__kepala"><h3>Pintasan</h3></div>
            <div class="pintasan" style="grid-template-columns:1fr">
                <a href="<?= base_url('katalog'); ?>"><?= adm_ikon('katalog'); ?> Kelola katalog buku</a>
            </div>
        </section>
        <?php endif; ?>
    </div>
</div>
