<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* ============================================================================
   VIDEO KEGIATAN
   Data: $data_video (id_video, judul_video, deskripsi, link).
   Aksi: admin/add_video, admin/update_video, admin/delete_video.
   Versi lama hanya menampilkan tautannya sebagai teks. Sekarang setiap video
   tampil dengan gambar sampulnya dari YouTube, dan formulirnya menampilkan
   pratinjau begitu tautan ditempel.
   ========================================================================== */

$rekam = [];
foreach ($data_video as $v) {
    $rekam['v' . $v['id_video']] = [
        'id_video'    => $v['id_video'],
        'judul_video' => html_entity_decode($v['judul_video'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        'deskripsi'   => $v['deskripsi'],
        'link'        => $v['link'],
    ];
}
?>

<div class="kepala">
    <div class="kepala__teks">
        <p>Video yang tampil di halaman Kegiatan situs yayasan. Cukup tempel tautan YouTube — bentuk <code>youtu.be/…</code>, <code>watch?v=…</code>, atau <code>shorts/…</code> semuanya diterima.</p>
    </div>
    <div class="kepala__aksi">
        <button type="button" class="tbl tbl--utama" data-buka="dialogTambah" data-tambah><?= adm_ikon('tambah', 16); ?> Tambah video</button>
    </div>
</div>

<section class="kartu" data-tabel data-per-halaman="12">
    <?php if (empty($data_video)): ?>
        <div class="kosong">
            <?= adm_ikon('video', 44); ?>
            <h3>Belum ada video</h3>
            <p>Tambahkan video kegiatan dari YouTube. Video langsung tampil di halaman Kegiatan.</p>
            <button type="button" class="tbl tbl--utama" data-buka="dialogTambah"><?= adm_ikon('tambah', 16); ?> Tambah video</button>
        </div>
    <?php else: ?>
        <div class="alat">
            <label class="cari">
                <span class="sr">Cari video</span>
                <?= adm_ikon('cari', 16); ?>
                <input type="search" data-cari placeholder="Cari judul atau deskripsi…">
            </label>
            <span class="alat__kanan" data-info></span>
        </div>
        <div class="grid-video">
            <?php foreach (array_reverse($data_video) as $v): $yt = adm_yt($v['link']); ?>
                <article class="foto" data-baris data-cari="<?= adm_e($v['judul_video'] . ' ' . adm_ringkas($v['deskripsi'], 200)); ?>">
                    <a class="video__gambar" href="<?= adm_e($v['link']); ?>" target="_blank" rel="noopener" title="Buka video">
                        <?php if ($yt): ?>
                            <img src="https://i.ytimg.com/vi/<?= $yt; ?>/hqdefault.jpg" alt="" loading="lazy">
                            <span class="video__main"><span><?= adm_ikon('main', 18); ?></span></span>
                        <?php else: ?>
                            <span class="video__tanpa">
                                <span><?= adm_ikon('tautan', 26); ?><br>Bukan tautan YouTube<br><small style="opacity:.7">Tidak bisa diputar di situs</small></span>
                            </span>
                        <?php endif; ?>
                        <span class="video__sumber"><span class="cap cap--polos <?= $yt ? 'cap--navy' : 'cap--kuning'; ?>"><?= $yt ? 'YouTube' : 'Tautan lain'; ?></span></span>
                    </a>
                    <div class="foto__isi">
                        <div class="foto__judul">
                            <?= adm_e($v['judul_video']); ?>
                            <small class="potong-2"><?= adm_e(adm_ringkas($v['deskripsi'], 110)); ?></small>
                        </div>
                        <div class="foto__aksi">
                            <button type="button" class="ikon-tbl" data-buka="dialogUbah" data-rekam="v<?= (int) $v['id_video']; ?>" title="Ubah" aria-label="Ubah video"><?= adm_ikon('ubah', 15); ?></button>
                            <button type="button" class="ikon-tbl ikon-tbl--hapus" title="Hapus" aria-label="Hapus video"
                                    data-hapus="<?= base_url('admin/delete_video/'); ?>"
                                    data-kirim='<?= adm_e(adm_json(['id_video' => $v['id_video']])); ?>'
                                    data-judul="Hapus video ini?"
                                    data-ket="Videonya tetap ada di YouTube; yang dihapus hanya dari situs."
                                    data-nama="<?= adm_e($v['judul_video']); ?>"><?= adm_ikon('hapus', 15); ?></button>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="kosong-cari" data-kosong-cari hidden>Tidak ada video yang cocok dengan pencarian.</div>
        <div class="halaman" data-halaman hidden><span class="halaman__teks"></span><div class="halaman__nav"></div></div>
    <?php endif; ?>
</section>

<script type="application/json" id="data-halaman"><?= adm_json($rekam); ?></script>

<?php foreach (['Tambah' => ['admin/add_video/', 'Tambah video', 'Tambah video'], 'Ubah' => ['admin/update_video/', 'Ubah video', 'Simpan perubahan']] as $jenis => $d): ?>
<dialog class="dialog dialog--lebar" id="dialog<?= $jenis; ?>" aria-labelledby="judul<?= $jenis; ?>">
    <form action="<?= base_url($d[0]); ?>" method="post"<?= $jenis === 'Tambah' ? ' data-kosongkan data-sukses="Video baru sudah ditambahkan."' : ' data-sukses="Perubahan video sudah disimpan."'; ?>>
        <?= adm_kepala_dialog($d[1], '', 'judul' . $jenis); ?>
        <div class="dialog__isi">
            <?php if ($jenis === 'Ubah'): ?><input type="hidden" name="id_video"><?php endif; ?>
            <div class="baris-bidang" style="align-items:start">
                <div>
                    <div class="bidang">
                        <label for="<?= $jenis; ?>_link">Tautan video <span class="wajib">*</span></label>
                        <input type="text" inputmode="url" id="<?= $jenis; ?>_link" name="link" required placeholder="https://youtu.be/…" data-yt-pratinjau="#pratinjau<?= $jenis; ?>">
                    </div>
                    <div class="bidang">
                        <label for="<?= $jenis; ?>_judul">Judul <span class="wajib">*</span></label>
                        <input type="text" id="<?= $jenis; ?>_judul" name="judul_video" required maxlength="250" placeholder="Contoh: Wisuda Universitas Citra Bangsa 2026">
                    </div>
                </div>
                <div class="bidang">
                    <span class="label">Pratinjau</span>
                    <div id="pratinjau<?= $jenis; ?>" class="video__gambar" style="border-radius:var(--r-kecil)">
                        <img alt="" hidden>
                        <span class="video__tanpa" data-yt-teks>Tempel tautan untuk melihat pratinjau.</span>
                    </div>
                </div>
            </div>
            <div class="bidang">
                <label for="editorVideo<?= $jenis; ?>">Deskripsi <span class="wajib">*</span></label>
                <textarea id="editorVideo<?= $jenis; ?>" name="deskripsi" data-editor data-wajib="Deskripsi video" rows="6"></textarea>
            </div>
        </div>
        <div class="dialog__kaki">
            <button type="button" class="tbl tbl--garis" data-tutup>Batal</button>
            <button type="submit" class="tbl tbl--utama"><?= $d[2]; ?></button>
        </div>
    </form>
</dialog>
<?php endforeach; ?>
