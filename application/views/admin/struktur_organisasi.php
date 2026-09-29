<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* ============================================================================
   STRUKTUR ORGANISASI
   Data: $data_struktur (id_struktur, nama, jabatan, foto).
   Aksi: admin/add_struktur (nama_pejabat, jabatan, foto),
         admin/update_struktur (+ id_struktur, foto_lama),
         admin/delete_struktur (id_struktur, foto).
   Catatan: di formulir, nama disimpan lewat field "nama_pejabat" — begitu
   controller membacanya, jadi namanya tidak diubah.
   ========================================================================== */

$folder = 'uploads/avatars/';
$rekam  = [];
foreach ($data_struktur as $s) {
    $rekam['s' . $s['id_struktur']] = [
        'id_struktur'  => $s['id_struktur'],
        'nama_pejabat' => html_entity_decode($s['nama'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        'jabatan'      => html_entity_decode($s['jabatan'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        'foto_lama'    => $s['foto'],
        '_pratinjau'   => $s['foto'] ? base_url($folder . rawurlencode($s['foto'])) : '',
    ];
}
?>

<div class="kepala">
    <div class="kepala__teks">
        <p>Pengurus yang tampil di halaman Struktur Organisasi situs yayasan. Pakai foto potret dengan wajah di tengah supaya tidak terpotong.</p>
    </div>
    <div class="kepala__aksi">
        <button type="button" class="tbl tbl--utama" data-buka="dialogTambah" data-tambah><?= adm_ikon('tambah', 16); ?> Tambah pengurus</button>
    </div>
</div>

<section class="kartu" data-tabel data-per-halaman="0">
    <?php if (empty($data_struktur)): ?>
        <div class="kosong">
            <?= adm_ikon('struktur', 44); ?>
            <h3>Belum ada pengurus</h3>
            <p>Tambahkan pengurus yayasan beserta jabatan dan fotonya.</p>
            <button type="button" class="tbl tbl--utama" data-buka="dialogTambah"><?= adm_ikon('tambah', 16); ?> Tambah pengurus</button>
        </div>
    <?php else: ?>
        <div class="alat">
            <label class="cari">
                <span class="sr">Cari pengurus</span>
                <?= adm_ikon('cari', 16); ?>
                <input type="search" data-cari placeholder="Cari nama atau jabatan…">
            </label>
            <span class="alat__kanan" data-info></span>
        </div>
        <div class="grid-orang">
            <?php foreach ($data_struktur as $s): ?>
                <article class="orang" data-baris data-cari="<?= adm_e($s['nama'] . ' ' . $s['jabatan']); ?>">
                    <img src="<?= base_url($folder . rawurlencode($s['foto'])); ?>" alt="" loading="lazy">
                    <h4><?= adm_e($s['nama']); ?></h4>
                    <p><?= adm_e($s['jabatan']); ?></p>
                    <div class="aksi">
                        <button type="button" class="tbl tbl--garis tbl--kecil" data-buka="dialogUbah" data-rekam="s<?= (int) $s['id_struktur']; ?>"><?= adm_ikon('ubah', 14); ?> Ubah</button>
                        <button type="button" class="ikon-tbl ikon-tbl--hapus" title="Hapus" aria-label="Hapus pengurus"
                                data-hapus="<?= base_url('admin/delete_struktur/'); ?>"
                                data-kirim='<?= adm_e(adm_json(['id_struktur' => $s['id_struktur'], 'foto' => $s['foto']])); ?>'
                                data-judul="Hapus pengurus ini?"
                                data-ket="Foto pengurus ikut terhapus dari server."
                                data-nama="<?= adm_e($s['nama'] . ' — ' . $s['jabatan']); ?>"><?= adm_ikon('hapus', 15); ?></button>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="kosong-cari" data-kosong-cari hidden>Tidak ada pengurus yang cocok dengan pencarian.</div>
    <?php endif; ?>
</section>

<script type="application/json" id="data-halaman"><?= adm_json($rekam); ?></script>

<dialog class="dialog" id="dialogTambah" aria-labelledby="judulTambah">
    <form action="<?= base_url('admin/add_struktur/'); ?>" method="post" enctype="multipart/form-data" data-kosongkan data-sukses="Pengurus baru sudah ditambahkan.">
        <?= adm_kepala_dialog('Tambah pengurus', '', 'judulTambah'); ?>
        <div class="dialog__isi">
            <div class="bidang">
                <label for="t_nama">Nama lengkap <span class="wajib">*</span></label>
                <input type="text" id="t_nama" name="nama_pejabat" required maxlength="250" placeholder="Beserta gelar, misalnya Dr. …, M.Pd.">
            </div>
            <div class="bidang">
                <label for="t_jabatan">Jabatan <span class="wajib">*</span></label>
                <input type="text" id="t_jabatan" name="jabatan" required maxlength="250" placeholder="Contoh: Ketua Pengurus">
            </div>
            <div class="bidang">
                <span class="label">Foto <span class="wajib">*</span></span>
                <?= adm_unggah('foto', TRUE, 'Foto potret, wajah di tengah. JPG atau PNG, maksimal 5 MB.', TRUE); ?>
            </div>
        </div>
        <div class="dialog__kaki">
            <button type="button" class="tbl tbl--garis" data-tutup>Batal</button>
            <button type="submit" class="tbl tbl--utama">Tambah pengurus</button>
        </div>
    </form>
</dialog>

<dialog class="dialog" id="dialogUbah" aria-labelledby="judulUbah">
    <form action="<?= base_url('admin/update_struktur/'); ?>" method="post" enctype="multipart/form-data" data-sukses="Data pengurus sudah diperbarui.">
        <?= adm_kepala_dialog('Ubah data pengurus', '', 'judulUbah'); ?>
        <div class="dialog__isi">
            <input type="hidden" name="id_struktur">
            <input type="hidden" name="foto_lama">
            <div class="bidang">
                <label for="u_nama">Nama lengkap <span class="wajib">*</span></label>
                <input type="text" id="u_nama" name="nama_pejabat" required maxlength="250">
            </div>
            <div class="bidang">
                <label for="u_jabatan">Jabatan <span class="wajib">*</span></label>
                <input type="text" id="u_jabatan" name="jabatan" required maxlength="250">
            </div>
            <div class="bidang">
                <span class="label">Foto</span>
                <?= adm_unggah('foto', FALSE, 'JPG atau PNG, maksimal 5 MB.', TRUE); ?>
            </div>
        </div>
        <div class="dialog__kaki">
            <button type="button" class="tbl tbl--garis" data-tutup>Batal</button>
            <button type="submit" class="tbl tbl--utama">Simpan perubahan</button>
        </div>
    </form>
</dialog>
