<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* ============================================================================
   BERITA
   Data dari controller: $data_berita (id_berita, judul_berita, isi_berita,
   tanggal_post, tanggal_update, gambar).
   Formulir dikirim ke admin/add_berita, admin/update_berita, admin/delete_berita
   dengan nama field yang sama persis seperti versi lama.
   ========================================================================== */

$folder = 'uploads/berita/';
$rekam  = [];
foreach ($data_berita as $b) {
    $rekam['b' . $b['id_berita']] = [
        'id_berita'    => $b['id_berita'],
        'judul_berita' => html_entity_decode($b['judul_berita'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        'isi_berita'   => $b['isi_berita'],
        'tanggal_post' => $b['tanggal_post'],
        'gambar_lama'  => $b['gambar'],
        '_pratinjau'   => $b['gambar'] ? base_url($folder . rawurlencode($b['gambar'])) : '',
    ];
}
?>

<div class="kepala">
    <div class="kepala__teks">
        <p>Kabar dan pengumuman yang tampil di halaman Berita situs yayasan. Berita terbaru muncul paling atas di beranda.</p>
    </div>
    <div class="kepala__aksi">
        <button type="button" class="tbl tbl--utama" data-buka="dialogTambah" data-tambah><?= adm_ikon('tambah', 16); ?> Tulis berita</button>
    </div>
</div>

<section class="kartu" data-tabel data-per-halaman="10">
    <?php if (empty($data_berita)): ?>
        <div class="kosong">
            <?= adm_ikon('berita', 44); ?>
            <h3>Belum ada berita</h3>
            <p>Tulis berita pertama. Begitu disimpan, berita langsung tampil di situs.</p>
            <button type="button" class="tbl tbl--utama" data-buka="dialogTambah"><?= adm_ikon('tambah', 16); ?> Tulis berita</button>
        </div>
    <?php else: ?>
        <div class="alat">
            <label class="cari">
                <span class="sr">Cari berita</span>
                <?= adm_ikon('cari', 16); ?>
                <input type="search" data-cari placeholder="Cari judul atau isi berita…">
            </label>
            <span class="alat__kanan" data-info></span>
        </div>
        <div class="tabel-bungkus">
            <table>
                <thead>
                    <tr>
                        <th style="width:84px">Gambar</th>
                        <th data-urut>Judul</th>
                        <th data-urut style="width:160px">Diterbitkan</th>
                        <th data-urut style="width:160px">Diperbarui</th>
                        <th style="width:96px"><span class="sr">Aksi</span></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($data_berita as $b): $url = base_url($folder . rawurlencode($b['gambar'])); ?>
                    <tr data-baris data-cari="<?= adm_e($b['judul_berita'] . ' ' . adm_ringkas($b['isi_berita'], 300)); ?>">
                        <td>
                            <a href="<?= $url; ?>" target="_blank" rel="noopener" title="Buka gambar ukuran penuh">
                                <img class="gambar-mini" src="<?= $url; ?>" alt="" loading="lazy">
                            </a>
                        </td>
                        <td data-nilai="<?= adm_e(html_entity_decode($b['judul_berita'], ENT_QUOTES, 'UTF-8')); ?>">
                            <span class="utama-sel"><?= adm_e($b['judul_berita']); ?></span>
                            <span class="sub-sel potong-2"><?= adm_e(adm_ringkas($b['isi_berita'], 150)); ?></span>
                        </td>
                        <td data-nilai="<?= (int) strtotime($b['tanggal_post']); ?>" class="sub-sel" style="white-space:nowrap"><?= adm_tgl($b['tanggal_post']); ?></td>
                        <td data-nilai="<?= (int) strtotime($b['tanggal_update']); ?>" class="sub-sel" style="white-space:nowrap"><?= adm_tgl($b['tanggal_update']); ?></td>
                        <td>
                            <div class="aksi">
                                <button type="button" class="ikon-tbl" data-buka="dialogUbah" data-rekam="b<?= (int) $b['id_berita']; ?>" title="Ubah" aria-label="Ubah berita"><?= adm_ikon('ubah', 16); ?></button>
                                <button type="button" class="ikon-tbl ikon-tbl--hapus" title="Hapus" aria-label="Hapus berita"
                                        data-hapus="<?= base_url('admin/delete_berita/'); ?>"
                                        data-kirim='<?= adm_e(adm_json(['id_berita' => $b['id_berita']])); ?>'
                                        data-judul="Hapus berita ini?"
                                        data-nama="<?= adm_e($b['judul_berita']); ?>"><?= adm_ikon('hapus', 16); ?></button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="kosong-cari" data-kosong-cari hidden>Tidak ada berita yang cocok dengan pencarian.</div>
        </div>
        <div class="halaman" data-halaman hidden><span class="halaman__teks"></span><div class="halaman__nav"></div></div>
    <?php endif; ?>
</section>

<script type="application/json" id="data-halaman"><?= adm_json($rekam); ?></script>

<!-- ============================ TULIS BERITA ============================ -->
<dialog class="dialog dialog--lebar" id="dialogTambah" aria-labelledby="judulTambah">
    <form action="<?= base_url('admin/add_berita/'); ?>" method="post" enctype="multipart/form-data" data-kosongkan data-sukses="Berita baru sudah diterbitkan.">
        <?= adm_kepala_dialog('Tulis berita', 'Berita langsung tampil di situs begitu disimpan.', 'judulTambah'); ?>
        <div class="dialog__isi">
            <div class="bidang">
                <label for="t_judul">Judul <span class="wajib">*</span></label>
                <input type="text" id="t_judul" name="judul_berita" required maxlength="250" placeholder="Contoh: Rapat kerja tahunan pengurus yayasan">
            </div>
            <div class="bidang">
                <label for="editorBerita">Isi berita <span class="wajib">*</span></label>
                <textarea id="editorBerita" name="isi_berita" data-editor data-wajib="Isi berita" rows="10"></textarea>
            </div>
            <div class="bidang">
                <span class="label">Gambar utama <span class="wajib">*</span></span>
                <?= adm_unggah('gambar', TRUE, 'Tampil di kartu berita dan di atas artikel. JPG atau PNG, maksimal 5 MB.'); ?>
            </div>
        </div>
        <div class="dialog__kaki">
            <button type="button" class="tbl tbl--garis" data-tutup>Batal</button>
            <button type="submit" class="tbl tbl--utama">Terbitkan berita</button>
        </div>
    </form>
</dialog>

<!-- ============================ UBAH BERITA ============================ -->
<dialog class="dialog dialog--lebar" id="dialogUbah" aria-labelledby="judulUbah">
    <form action="<?= base_url('admin/update_berita/'); ?>" method="post" enctype="multipart/form-data" data-sukses="Perubahan berita sudah disimpan.">
        <?= adm_kepala_dialog('Ubah berita', 'Tanggal terbit tetap, tanggal diperbarui diisi otomatis.', 'judulUbah'); ?>
        <div class="dialog__isi">
            <input type="hidden" name="id_berita">
            <input type="hidden" name="tanggal_post">
            <input type="hidden" name="gambar_lama">
            <div class="bidang">
                <label for="u_judul">Judul <span class="wajib">*</span></label>
                <input type="text" id="u_judul" name="judul_berita" required maxlength="250">
            </div>
            <div class="bidang">
                <label for="editorBeritaUbah">Isi berita <span class="wajib">*</span></label>
                <textarea id="editorBeritaUbah" name="isi_berita" data-editor data-wajib="Isi berita" rows="10"></textarea>
            </div>
            <div class="bidang">
                <span class="label">Gambar utama</span>
                <?= adm_unggah('gambar', FALSE, 'JPG atau PNG, maksimal 5 MB.'); ?>
            </div>
        </div>
        <div class="dialog__kaki">
            <button type="button" class="tbl tbl--garis" data-tutup>Batal</button>
            <button type="submit" class="tbl tbl--utama">Simpan perubahan</button>
        </div>
    </form>
</dialog>
