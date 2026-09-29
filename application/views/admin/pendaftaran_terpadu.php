<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* ============================================================================
   PPDB TERPADU — semua jenjang dalam satu tabel `pendaftaran`
   Data: $data_pendaftaran (sudah disaring controller), $filter_jenjang,
         $filter_status.
   Penyaringan jenjang dan status tetap dikerjakan controller lewat ?jenjang=
   dan ?status=, persis seperti versi lama. Admin unit (admin_tk, admin_sd,
   dst.) otomatis hanya melihat jenjangnya sendiri — itu diatur controller.
   Aksi: admin/update_status_pendaftaran, admin/delete_pendaftaran (hanya
   administrator), admin/export_pendaftaran_csv.
   ========================================================================== */

$CI      =& get_instance();
$peran   = (string) $CI->session->userdata('role');
$super   = in_array($peran, ['default', 'administrator'], TRUE);
$f_jen   = isset($filter_jenjang) ? (string) $filter_jenjang : '';
$f_sta   = isset($filter_status) ? (string) $filter_status : '';
$terkunci = !$super && in_array($peran, ['admin_tk', 'admin_sd', 'admin_smp', 'admin_sma', 'admin_ucb'], TRUE);

$semua_jenjang = ['TK' => 'PAUD / TK', 'SD' => 'SD', 'SMP' => 'SMP', 'SMA' => 'SMA', 'UCB' => 'Universitas'];
$status_semua  = ['Baru' => 'biru', 'Dihubungi' => 'kuning', 'Diterima' => 'hijau', 'Ditolak' => 'merah'];
$warna_css     = ['biru' => 'var(--biru)', 'kuning' => 'var(--kuning)', 'hijau' => 'var(--hijau)', 'merah' => 'var(--merah)'];

/* Jumlah per jenjang dan per status untuk label penyaring */
$jml_jenjang = []; $jml_status = [];
if ($CI->db->table_exists('pendaftaran')) {
    foreach ($CI->db->select('jenjang, COUNT(*) n', FALSE)->group_by('jenjang')->get('pendaftaran')->result_array() as $r) { $jml_jenjang[$r['jenjang']] = (int) $r['n']; }
    if ($f_jen !== '') { $CI->db->where('jenjang', $f_jen); }
    foreach ($CI->db->select('status, COUNT(*) n', FALSE)->group_by('status')->get('pendaftaran')->result_array() as $r) { $jml_status[$r['status']] = (int) $r['n']; }
}

$tautan = function ($jen, $sta) {
    $q = array_filter(['jenjang' => $jen, 'status' => $sta], 'strlen');
    return base_url('admin/pendaftaran_terpadu') . ($q ? '?' . http_build_query($q) : '');
};

$rekam = [];
foreach ($data_pendaftaran as $r) {
    $usia = '';
    if (!empty($r['tgl_lahir']) && strpos($r['tgl_lahir'], '0000') !== 0) {
        $usia = ' (' . (new DateTime($r['tgl_lahir']))->diff(new DateTime())->y . ' tahun)';
    }
    $rekam['p' . $r['id_pendaftaran']] = adm_dekode([
        'id_pendaftaran' => $r['id_pendaftaran'],
        'status'         => $r['status'],
        'nama_lengkap'   => $r['nama_lengkap'],
        'no_registrasi'  => $r['no_registrasi'],
        'jenjang'        => isset($semua_jenjang[$r['jenjang']]) ? $semua_jenjang[$r['jenjang']] : $r['jenjang'],
        'nik_nisn'       => $r['nik_nisn'],
        'jenis_kelamin'  => $r['jenis_kelamin'],
        'ttl'            => trim($r['tempat_lahir'] . ', ' . adm_tgl($r['tgl_lahir'], FALSE), ', ') . $usia,
        'agama'          => $r['agama'],
        'nama_ortu'      => $r['nama_ortu'],
        'pekerjaan_ortu' => $r['pekerjaan_ortu'],
        'no_hp'          => $r['no_hp'],
        'email'          => $r['email'],
        'alamat'         => $r['alamat'],
        'asal_sekolah'   => $r['asal_sekolah'],
        'catatan'        => $r['catatan'],
        'tanggal_daftar' => adm_tgl($r['tanggal_daftar']),
        '_wa'            => adm_wa($r['no_hp']),
        '_email'         => $r['email'],
    ]);
}
?>

<div class="kepala">
    <div class="kepala__teks">
        <p>Seluruh pendaftar dari formulir terpadu<?= $terkunci ? ' untuk jenjang <strong>' . adm_e($f_jen) . '</strong>' : ' — TK, SD, SMP, SMA, dan Universitas'; ?>. Klik nama untuk melihat data lengkap.</p>
    </div>
    <div class="kepala__aksi">
        <a class="tbl tbl--garis" href="<?= base_url('admin/export_pendaftaran_csv'); ?>" title="Mengunduh seluruh data pendaftaran sebagai berkas CSV yang bisa dibuka di Excel"><?= adm_ikon('unduh', 16); ?> Ekspor CSV</a>
    </div>
</div>

<?php if (!$terkunci): ?>
<div class="saring" style="margin-bottom:12px" aria-label="Saring menurut jenjang">
    <a href="<?= $tautan('', $f_sta); ?>"<?= $f_jen === '' ? ' aria-current="true"' : ''; ?>>Semua jenjang <b><?= array_sum($jml_jenjang); ?></b></a>
    <?php foreach ($semua_jenjang as $k => $l): ?>
        <a href="<?= $tautan($k, $f_sta); ?>"<?= $f_jen === $k ? ' aria-current="true"' : ''; ?>><?= adm_e($l); ?> <b><?= isset($jml_jenjang[$k]) ? $jml_jenjang[$k] : 0; ?></b></a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<section class="kartu" data-tabel data-per-halaman="15">
    <div class="alat">
        <label class="cari">
            <span class="sr">Cari pendaftar</span>
            <?= adm_ikon('cari', 16); ?>
            <input type="search" data-cari placeholder="Cari nama, nomor registrasi, orang tua…">
        </label>
        <div class="saring" aria-label="Saring menurut status">
            <a href="<?= $tautan($f_jen, ''); ?>"<?= $f_sta === '' ? ' aria-current="true"' : ''; ?>>Semua status</a>
            <?php foreach ($status_semua as $st => $w): ?>
                <a href="<?= $tautan($f_jen, $st); ?>"<?= $f_sta === $st ? ' aria-current="true"' : ''; ?>><?= $st; ?> <b><?= isset($jml_status[$st]) ? $jml_status[$st] : 0; ?></b></a>
            <?php endforeach; ?>
        </div>
        <span class="alat__kanan" data-info></span>
    </div>

    <?php if (empty($data_pendaftaran)): ?>
        <div class="kosong">
            <?= adm_ikon('daftar', 44); ?>
            <h3>Tidak ada pendaftar</h3>
            <p><?= ($f_jen !== '' || $f_sta !== '') && !$terkunci ? 'Tidak ada yang cocok dengan penyaring ini.' : 'Pendaftar yang mengisi formulir di situs akan muncul di sini.'; ?></p>
            <?php if (($f_jen !== '' || $f_sta !== '') && !$terkunci): ?>
                <a class="tbl tbl--garis" href="<?= $tautan('', ''); ?>">Hapus penyaring</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="tabel-bungkus">
            <table>
                <thead>
                    <tr>
                        <th data-urut>No. registrasi</th>
                        <th data-urut>Calon siswa</th>
                        <th data-urut>Jenjang</th>
                        <th>Orang tua</th>
                        <th data-urut>Tanggal daftar</th>
                        <th data-urut>Status</th>
                        <th style="width:124px"><span class="sr">Aksi</span></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($data_pendaftaran as $r): ?>
                    <tr data-baris>
                        <td><span class="kode"><?= adm_e($r['no_registrasi']); ?></span></td>
                        <td data-nilai="<?= adm_e($r['nama_lengkap']); ?>">
                            <a href="#" class="utama-sel" style="text-decoration:none" data-buka="dialogRincian" data-rekam="p<?= (int) $r['id_pendaftaran']; ?>"><?= adm_e($r['nama_lengkap']); ?></a>
                            <span class="sub-sel"><?= adm_e($r['jenis_kelamin']); ?><?= !empty($r['asal_sekolah']) ? ' &middot; dari ' . adm_e($r['asal_sekolah']) : ''; ?></span>
                        </td>
                        <td><span class="cap cap--polos cap--navy"><?= adm_e($r['jenjang']); ?></span></td>
                        <td>
                            <span class="utama-sel" style="font-weight:500;color:var(--teks)"><?= adm_e($r['nama_ortu']); ?></span>
                            <span class="sub-sel"><?= adm_e($r['no_hp']); ?></span>
                        </td>
                        <td class="sub-sel" style="white-space:nowrap" data-nilai="<?= (int) strtotime($r['tanggal_daftar']); ?>"><?= adm_tgl($r['tanggal_daftar']); ?></td>
                        <td data-nilai="<?= (int) array_search($r['status'], array_keys($status_semua)); ?>"><span class="cap cap--<?= adm_warna_status($r['status']); ?>"><?= adm_e($r['status']); ?></span></td>
                        <td>
                            <div class="aksi">
                                <a class="ikon-tbl ikon-tbl--wa" href="https://wa.me/<?= adm_wa($r['no_hp']); ?>" target="_blank" rel="noopener" title="Hubungi lewat WhatsApp" aria-label="WhatsApp orang tua"><?= adm_ikon('wa', 16); ?></a>
                                <button type="button" class="ikon-tbl" data-buka="dialogRincian" data-rekam="p<?= (int) $r['id_pendaftaran']; ?>" title="Lihat dan ubah status" aria-label="Lihat data lengkap"><?= adm_ikon('lihat', 16); ?></button>
                                <?php if ($super): ?>
                                    <button type="button" class="ikon-tbl ikon-tbl--hapus" title="Hapus" aria-label="Hapus pendaftar"
                                            data-hapus="<?= base_url('admin/delete_pendaftaran'); ?>"
                                            data-kirim='<?= adm_e(adm_json(['id_pendaftaran' => $r['id_pendaftaran']])); ?>'
                                            data-judul="Hapus data pendaftar?"
                                            data-nama="<?= adm_e($r['nama_lengkap'] . ' (' . $r['no_registrasi'] . ')'); ?>"><?= adm_ikon('hapus', 16); ?></button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="kosong-cari" data-kosong-cari hidden>Tidak ada pendaftar yang cocok dengan pencarian.</div>
        </div>
        <div class="halaman" data-halaman hidden><span class="halaman__teks"></span><div class="halaman__nav"></div></div>
    <?php endif; ?>
</section>

<script type="application/json" id="data-halaman"><?= adm_json($rekam); ?></script>

<dialog class="dialog dialog--lebar" id="dialogRincian" aria-labelledby="judulRincian">
    <form action="<?= base_url('admin/update_status_pendaftaran'); ?>" method="post">
        <div class="dialog__kepala">
            <div>
                <p style="margin:0 0 2px"><span data-tampil="no_registrasi"></span> &middot; <span data-tampil="jenjang"></span></p>
                <h2 id="judulRincian" data-tampil="nama_lengkap">—</h2>
            </div>
            <button type="button" class="dialog__tutup" data-tutup aria-label="Tutup"><?= adm_ikon('tutup', 18); ?></button>
        </div>
        <div class="dialog__isi">
            <input type="hidden" name="id_pendaftaran">
            <div class="kisi kisi--2" style="gap:16px;margin-bottom:20px">
                <div>
                    <span class="label">Data calon siswa</span>
                    <dl class="rincian">
                        <dt>NIK / NISN</dt><dd data-tampil="nik_nisn"></dd>
                        <dt>Jenis kelamin</dt><dd data-tampil="jenis_kelamin"></dd>
                        <dt>Tempat, tgl lahir</dt><dd data-tampil="ttl"></dd>
                        <dt>Agama</dt><dd data-tampil="agama"></dd>
                        <dt>Asal sekolah</dt><dd data-tampil="asal_sekolah"></dd>
                    </dl>
                </div>
                <div>
                    <span class="label">Orang tua &amp; kontak</span>
                    <dl class="rincian">
                        <dt>Nama</dt><dd data-tampil="nama_ortu"></dd>
                        <dt>Pekerjaan</dt><dd data-tampil="pekerjaan_ortu"></dd>
                        <dt>No. HP</dt><dd data-tampil="no_hp"></dd>
                        <dt>Surel</dt><dd data-tampil="email"></dd>
                        <dt>Alamat</dt><dd data-tampil="alamat"></dd>
                    </dl>
                </div>
            </div>
            <div class="bidang">
                <span class="label">Catatan dari pendaftar</span>
                <div class="pesan__teks" style="max-height:140px" data-tampil="catatan"></div>
                <p class="petunjuk">Mendaftar <span data-tampil="tanggal_daftar"></span></p>
            </div>
            <span class="label">Status pendaftaran</span>
            <div class="pilih-status">
                <?php foreach ($status_semua as $st => $w): ?>
                    <label><input type="radio" name="status" value="<?= $st; ?>"><span style="--w:<?= $warna_css[$w]; ?>"><?= $st; ?></span></label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="dialog__kaki">
            <a class="tbl tbl--garis kiri" href="#" target="_blank" rel="noopener" data-href="_wa" data-awalan="https://wa.me/"><?= adm_ikon('wa', 16); ?> WhatsApp</a>
            <a class="tbl tbl--garis" href="#" data-href="_email" data-awalan="mailto:"><?= adm_ikon('pesan', 16); ?> Surel</a>
            <button type="submit" class="tbl tbl--utama">Simpan status</button>
        </div>
    </form>
</dialog>
