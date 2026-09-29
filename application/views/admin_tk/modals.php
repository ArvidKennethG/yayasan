<?php
defined('BASEPATH') or exit('No direct script access allowed');
/* Dialog hapus bersama — replika dari admin yayasan */
?>
<dialog class="dialog dialog--kecil dialog--bahaya" id="dialogHapus" aria-labelledby="dialogHapusJudul">
    <form method="post" action="">
        <div class="dialog__kepala">
            <span class="dialog__ikon-bahaya"><?= adm_ikon('hapus', 20); ?></span>
            <div>
                <h2 id="dialogHapusJudul" data-hapus-judul>Hapus data ini?</h2>
                <p data-hapus-ket>Tindakan ini tidak bisa dibatalkan.</p>
            </div>
            <button type="button" class="dialog__tutup" data-tutup aria-label="Tutup"><?= adm_ikon('tutup', 18); ?></button>
        </div>
        <div class="dialog__isi">
            <p style="margin:0">Yang akan dihapus: <strong data-hapus-nama></strong></p>
        </div>
        <div class="dialog__kaki">
            <button type="button" class="tbl tbl--garis" data-tutup>Batal</button>
            <button type="submit" class="tbl tbl--merah" data-memuat="Menghapus…"><?= adm_ikon('hapus', 16); ?> Ya, hapus</button>
        </div>
    </form>
</dialog>
