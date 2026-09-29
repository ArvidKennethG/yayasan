<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* ============================================================================
   PESAN MASUK
   Data: $data_pesan (id_pesan, nama, email, subjek, pesan, ip_address,
   status, created_at). Aksi: admin/update_status_pesan (id_pesan, status),
   admin/delete_pesan (id_pesan).
   Disusun seperti kotak masuk surel: pesan belum dibaca ditebalkan, klik untuk
   membaca isi lengkap dan membalas.
   ========================================================================== */

$status_semua = ['Belum Dibaca' => 'merah', 'Sudah Dibaca' => 'kuning', 'Dibalas' => 'hijau'];
$warna_css    = ['merah' => 'var(--merah)', 'kuning' => 'var(--kuning)', 'hijau' => 'var(--hijau)'];
$jumlah = array_fill_keys(array_keys($status_semua), 0);
$rekam  = [];
foreach ($data_pesan as $p) {
    if (isset($jumlah[$p['status']])) { $jumlah[$p['status']]++; }
    $rekam['m' . $p['id_pesan']] = adm_dekode([
        'id_pesan'   => $p['id_pesan'],
        'status'     => $p['status'],
        'nama'       => $p['nama'],
        'email'      => $p['email'],
        'subjek'     => $p['subjek'],
        'pesan'      => $p['pesan'],
        'ip_address' => $p['ip_address'],
        'waktu'      => adm_tgl($p['created_at']),
        '_balas'     => $p['email'] . '?subject=' . rawurlencode('Balasan: ' . html_entity_decode($p['subjek'], ENT_QUOTES, 'UTF-8')),
    ]);
}
?>

<div class="kepala">
    <div class="kepala__teks">
        <p>Pertanyaan dan pesan dari formulir Kontak situs yayasan. Tandai <strong>Dibalas</strong> setelah menjawab supaya pengurus lain tahu pesan itu sudah ditangani.</p>
    </div>
</div>

<section class="kartu" data-tabel data-per-halaman="20">
    <?php if (empty($data_pesan)): ?>
        <div class="kosong">
            <?= adm_ikon('pesan', 44); ?>
            <h3>Kotak masuk kosong</h3>
            <p>Pesan yang dikirim pengunjung lewat halaman Kontak akan muncul di sini.</p>
        </div>
    <?php else: ?>
        <div class="alat">
            <label class="cari">
                <span class="sr">Cari pesan</span>
                <?= adm_ikon('cari', 16); ?>
                <input type="search" data-cari placeholder="Cari pengirim, subjek, atau isi pesan…">
            </label>
            <div class="saring" data-saring-kunci="status" role="group" aria-label="Saring menurut status">
                <button type="button" data-saring-nilai="" aria-pressed="true">Semua <b><?= count($data_pesan); ?></b></button>
                <?php foreach ($status_semua as $st => $w): if (!$jumlah[$st]) { continue; } ?>
                    <button type="button" data-saring-nilai="<?= $st; ?>" aria-pressed="false"><?= $st; ?> <b><?= $jumlah[$st]; ?></b></button>
                <?php endforeach; ?>
            </div>
            <span class="alat__kanan" data-info></span>
        </div>
        <div>
            <?php foreach ($data_pesan as $p): $baru = $p['status'] === 'Belum Dibaca'; ?>
                <div class="pesan-baris<?= $baru ? ' pesan-baris--baru' : ''; ?>" data-baris data-status="<?= adm_e($p['status']); ?>"
                     data-buka="dialogBaca" data-rekam="m<?= (int) $p['id_pesan']; ?>" tabindex="0" role="button"
                     aria-label="Baca pesan dari <?= adm_e($p['nama']); ?>: <?= adm_e($p['subjek']); ?>">
                    <span class="pesan__bulat" aria-hidden="true"><?= adm_e(mb_strtoupper(mb_substr(html_entity_decode($p['nama'], ENT_QUOTES, 'UTF-8'), 0, 1, 'UTF-8'), 'UTF-8')); ?></span>
                    <div class="pesan__isi">
                        <div class="pesan__atas">
                            <span class="pesan__nama"><?= adm_e($p['nama']); ?></span>
                            <span class="cap cap--<?= adm_warna_status($p['status']); ?>"><?= adm_e($p['status']); ?></span>
                            <span class="pesan__waktu"><?= adm_tgl($p['created_at']); ?></span>
                        </div>
                        <span class="pesan__subjek"><?= adm_e($p['subjek']); ?></span>
                        <span class="pesan__potong"><?= adm_e(adm_ringkas($p['pesan'], 140)); ?></span>
                    </div>
                    <button type="button" class="ikon-tbl ikon-tbl--hapus" title="Hapus" aria-label="Hapus pesan"
                            data-hapus="<?= base_url('admin/delete_pesan'); ?>"
                            data-kirim='<?= adm_e(adm_json(['id_pesan' => $p['id_pesan']])); ?>'
                            data-judul="Hapus pesan ini?"
                            data-nama="<?= adm_e($p['nama'] . ' — ' . $p['subjek']); ?>"><?= adm_ikon('hapus', 15); ?></button>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="kosong-cari" data-kosong-cari hidden>Tidak ada pesan yang cocok.</div>
        <div class="halaman" data-halaman hidden><span class="halaman__teks"></span><div class="halaman__nav"></div></div>
    <?php endif; ?>
</section>

<script type="application/json" id="data-halaman"><?= adm_json($rekam); ?></script>

<dialog class="dialog dialog--lebar" id="dialogBaca" aria-labelledby="judulBaca">
    <form action="<?= base_url('admin/update_status_pesan'); ?>" method="post">
        <div class="dialog__kepala">
            <div>
                <h2 id="judulBaca" data-tampil="subjek">—</h2>
                <p><strong data-tampil="nama"></strong> &lt;<span data-tampil="email"></span>&gt; &middot; <span data-tampil="waktu"></span></p>
            </div>
            <button type="button" class="dialog__tutup" data-tutup aria-label="Tutup"><?= adm_ikon('tutup', 18); ?></button>
        </div>
        <div class="dialog__isi">
            <input type="hidden" name="id_pesan">
            <div class="pesan__teks" data-tampil="pesan"></div>
            <p class="petunjuk">Dikirim dari alamat IP <span data-tampil="ip_address"></span></p>

            <div class="bidang" style="margin-top:18px">
                <span class="label">Status pesan</span>
                <div class="pilih-status">
                    <?php foreach ($status_semua as $st => $w): ?>
                        <label><input type="radio" name="status" value="<?= $st; ?>"><span style="--w:<?= $warna_css[$w]; ?>"><?= $st; ?></span></label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="dialog__kaki">
            <a class="tbl tbl--emas kiri" href="#" data-href="_balas" data-awalan="mailto:"><?= adm_ikon('balas', 16); ?> Balas lewat surel</a>
            <button type="button" class="tbl tbl--garis" data-tutup>Tutup</button>
            <button type="submit" class="tbl tbl--utama">Simpan status</button>
        </div>
    </form>
</dialog>
