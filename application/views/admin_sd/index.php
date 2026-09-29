<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* ============================================================================
   DASBOR ADMIN SD
   ========================================================================== */

$CI    =& get_instance();
$db    = $CI->db;

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

/* ---- Pendaftar baru SD ---- */
$daftar_baru = (int) adm_hitung('pendaftaran', ['status' => 'Baru', 'jenjang' => 'SD']);
$total_sd    = (int) adm_hitung('pendaftaran', ['jenjang' => 'SD']);

/* ---- Pendaftar terbaru SD ---- */
$pendaftar = [];
if ($db->table_exists('pendaftaran')) {
    $pendaftar = $db->where('jenjang', 'SD')
                    ->order_by('id_pendaftaran', 'DESC')
                    ->limit(5)->get('pendaftaran')->result_array();
}

/* ---- Profil SD terisi ---- */
$profil_terisi = (int) adm_hitung('konten_sd');

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
            <?php if ($daftar_baru): ?>
                Ada <strong style="color:#fff"><?= $daftar_baru; ?> pendaftar SD baru</strong> yang menunggu ditindaklanjuti.
            <?php else: ?>
                Tidak ada pendaftar SD baru yang menunggu. Semua sudah ditangani! 📚
            <?php endif; ?>
        </p>
    </div>
    <div class="kepala__aksi">
        <a class="tbl tbl--garis" href="<?= base_url('sd'); ?>" target="_blank" rel="noopener" style="color:#fff;border-color:rgba(255,255,255,.5)"><?= adm_ikon('situs', 16); ?> Lihat situs SD</a>
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
    <div class="angka__item">
        <span class="angka__label"><?= adm_ikon('daftar', 15); ?> Pendaftar SD baru</span>
        <span class="angka__nilai"><?= $daftar_baru; ?></span>
        <span class="angka__catatan">dari total <?= $total_sd; ?> pendaftar SD</span>
    </div>
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

        <section class="kartu">
            <div class="kartu__kepala">
                <h3>Pendaftar SD terbaru</h3>
            </div>
            <?php if (empty($pendaftar)): ?>
                <div class="kosong" style="padding:32px 20px">
                    <p style="margin:0">Belum ada pendaftar SD yang masuk.</p>
                </div>
            <?php else: ?>
                <div class="tabel-bungkus">
                    <table>
                        <thead><tr><th>Calon siswa</th><th>Tanggal</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach ($pendaftar as $p): ?>
                            <tr>
                                <td>
                                    <span class="utama-sel"><?= adm_e($p['nama_lengkap']); ?></span>
                                    <span class="sub-sel"><?= adm_e($p['no_registrasi']); ?></span>
                                </td>
                                <td class="sub-sel" style="white-space:nowrap"><?= adm_tgl($p['tanggal_daftar']); ?></td>
                                <td><span class="cap cap--<?= adm_warna_status($p['status']); ?>"><?= adm_e($p['status']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <div class="kisi" style="align-content:start">
        <section class="kartu">
            <div class="kartu__kepala"><h3>Pintasan</h3></div>
            <div class="pintasan">
                <a href="<?= base_url('admin-sd/profil'); ?>"><?= adm_ikon('konten'); ?> Kelola profil SD</a>
                <a href="<?= base_url('sd'); ?>" target="_blank"><?= adm_ikon('situs'); ?> Lihat situs SD</a>
            </div>
        </section>

        <section class="kartu">
            <div class="kartu__kepala"><h3>Ringkasan</h3></div>
            <ul class="daftar-ringkas">
                <li><span class="isi"><strong>Total pendaftar SD</strong></span><span class="cap cap--polos"><?= $total_sd; ?></span></li>
                <li><span class="isi"><strong>Pendaftar baru</strong></span><span class="cap cap--biru"><?= $daftar_baru; ?></span></li>
                <li><span class="isi"><strong>Profil SD terisi</strong></span><span class="cap cap--polos"><?= $profil_terisi !== NULL ? $profil_terisi : 0; ?> bagian</span></li>
            </ul>
        </section>
    </div>
</div>
