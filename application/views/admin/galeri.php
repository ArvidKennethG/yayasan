<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* ============================================================================
   GALERI FOTO
   Data: $data_galeri (id_foto, judul_foto, foto).
   Aksi: admin/add_foto, admin/update_foto, admin/delete_foto.
   Ditampilkan sebagai kisi foto, bukan tabel — admin mengenali foto dari
   gambarnya, bukan dari nama berkasnya.
   ========================================================================== */

$folder = 'uploads/galeri/';
$rekam  = [];
foreach ($data_galeri as $g) {
    $rekam['g' . $g['id_foto']] = [
        'id_foto'    => $g['id_foto'],
        'judul_foto' => html_entity_decode($g['judul_foto'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        'foto_lama'  => $g['foto'],
        '_pratinjau' => base_url($folder . rawurlencode($g['foto'])),
    ];
}
?>

<div class="kepala">
    <div class="kepala__teks">
        <p>Dokumentasi yang tampil di halaman Galeri situs yayasan. Klik foto untuk melihat ukuran penuh.</p>
    </div>
    <div class="kepala__aksi">
        <button type="button" class="tbl tbl--utama" data-buka="dialogTambah" data-tambah><?= adm_ikon('tambah', 16); ?> Unggah foto</button>
    </div>
</div>

<section class="kartu" data-tabel data-per-halaman="24">
    <?php if (empty($data_galeri)): ?>
        <div class="kosong">
            <?= adm_ikon('galeri', 44); ?>
            <h3>Galeri masih kosong</h3>
            <p>Unggah foto kegiatan pertama. Foto langsung tampil di halaman Galeri situs.</p>
            <button type="button" class="tbl tbl--utama" data-buka="dialogTambah"><?= adm_ikon('tambah', 16); ?> Unggah foto</button>
        </div>
    <?php else: ?>
        <div class="alat">
            <label class="cari">
                <span class="sr">Cari foto</span>
                <?= adm_ikon('cari', 16); ?>
                <input type="search" data-cari placeholder="Cari judul foto…">
            </label>
            <span class="alat__kanan" data-info></span>
        </div>
        <div class="grid-foto">
            <?php foreach (array_reverse($data_galeri) as $g): $url = base_url($folder . rawurlencode($g['foto'])); ?>
                <article class="foto" data-baris data-cari="<?= adm_e($g['judul_foto']); ?>">
                    <a class="foto__gambar" href="<?= $url; ?>" target="_blank" rel="noopener" title="Buka ukuran penuh">
                        <img src="<?= $url; ?>" alt="<?= adm_e($g['judul_foto']); ?>" loading="lazy">
                    </a>
                    <div class="foto__isi">
                        <div class="foto__judul"><?= adm_e($g['judul_foto']); ?></div>
                        <div class="foto__aksi">
                            <button type="button" class="ikon-tbl" data-buka="dialogUbah" data-rekam="g<?= (int) $g['id_foto']; ?>" title="Ubah" aria-label="Ubah foto"><?= adm_ikon('ubah', 15); ?></button>
                            <button type="button" class="ikon-tbl ikon-tbl--hapus" title="Hapus" aria-label="Hapus foto"
                                    data-hapus="<?= base_url('admin/delete_foto/'); ?>"
                                    data-kirim='<?= adm_e(adm_json(['id_foto' => $g['id_foto'], 'foto' => $g['foto']])); ?>'
                                    data-judul="Hapus foto ini?"
                                    data-nama="<?= adm_e($g['judul_foto']); ?>"><?= adm_ikon('hapus', 15); ?></button>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="kosong-cari" data-kosong-cari hidden>Tidak ada foto yang cocok dengan pencarian.</div>
        <div class="halaman" data-halaman hidden><span class="halaman__teks"></span><div class="halaman__nav"></div></div>
    <?php endif; ?>
</section>

<script type="application/json" id="data-halaman"><?= adm_json($rekam); ?></script>

<dialog class="dialog" id="dialogTambah" aria-labelledby="judulTambah">
    <form action="<?= base_url('admin/add_foto/'); ?>" method="post" enctype="multipart/form-data" data-kosongkan data-sukses="Foto sudah diunggah ke galeri.">
        <?= adm_kepala_dialog('Unggah foto', 'Foto langsung tampil di halaman Galeri.', 'judulTambah'); ?>
        <div class="dialog__isi">
            <div class="bidang">
                <span class="label">Foto <span class="wajib">*</span></span>
                <?= adm_unggah('foto', TRUE); ?>
            </div>
            <div class="bidang">
                <label for="t_judul">Judul foto <span class="wajib">*</span></label>
                <input type="text" id="t_judul" name="judul_foto" required maxlength="250" placeholder="Contoh: Ibadah awal tahun ajaran">
                <p class="petunjuk">Tampil sebagai keterangan saat foto diperbesar di situs.</p>
            </div>
        </div>
        <div class="dialog__kaki">
            <button type="button" class="tbl tbl--garis" data-tutup>Batal</button>
            <button type="submit" class="tbl tbl--utama" data-memuat="Mengunggah…">Unggah</button>
        </div>
    </form>
</dialog>

<dialog class="dialog" id="dialogUbah" aria-labelledby="judulUbah">
    <form action="<?= base_url('admin/update_foto/'); ?>" method="post" enctype="multipart/form-data" data-sukses="Perubahan foto sudah disimpan.">
        <?= adm_kepala_dialog('Ubah foto', '', 'judulUbah'); ?>
        <div class="dialog__isi">
            <input type="hidden" name="id_foto">
            <input type="hidden" name="foto_lama">
            <div class="bidang">
                <span class="label">Foto</span>
                <?= adm_unggah('foto', FALSE); ?>
            </div>
            <div class="bidang">
                <label for="u_judul">Judul foto <span class="wajib">*</span></label>
                <input type="text" id="u_judul" name="judul_foto" required maxlength="250">
            </div>
        </div>
        <div class="dialog__kaki">
            <button type="button" class="tbl tbl--garis" data-tutup>Batal</button>
            <button type="submit" class="tbl tbl--utama">Simpan perubahan</button>
        </div>
    </form>
</dialog>
