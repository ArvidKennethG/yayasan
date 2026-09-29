<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* ============================================================================
   PPDB TK — HALAMAN LAMA, SUDAH TIDAK DIPAKAI
   ----------------------------------------------------------------------------
   Menunya sudah dihapus dari panel atas permintaan. Berkas ini sengaja tidak
   ikut dihapus: controller Admin.php masih punya method pendaftaran_tk(), dan
   kalau viewnya hilang, siapa pun yang membuka alamat lamanya (misalnya dari
   penanda buku lama) akan mendapat pesan galat CodeIgniter, bukan halaman.

   Datanya sendiri TIDAK dihapus — tabel pendaftaran_tk masih utuh di database.
   ========================================================================== */
?>

<section class="kartu">
    <div class="kosong">
        <?= adm_ikon('tk', 44); ?>
        <h3>Halaman ini sudah tidak dipakai</h3>
        <p>
            Pendaftaran TK sekarang ikut di halaman <strong>Pendaftaran</strong> bersama jenjang lain.
            <?php if (!empty($data_pendaftaran)): ?>
                Data lama di halaman ini (<?= count($data_pendaftaran); ?> pendaftar) masih tersimpan di database dan tidak dihapus.
            <?php endif; ?>
        </p>
        <a class="tbl tbl--utama" href="<?= base_url('admin/pendaftaran_terpadu'); ?>"><?= adm_ikon('daftar', 16); ?> Buka halaman Pendaftaran</a>
    </div>
</section>
