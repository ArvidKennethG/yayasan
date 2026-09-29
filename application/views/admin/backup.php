<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* ============================================================================
   CADANGAN DATA
   Data: $backup_files (name, size, time) dari folder uploads/backups/.
   Tombol unduh memanggil backup/database, sama seperti versi lama.
   ========================================================================== */

$files = $backup_files;
usort($files, function ($a, $b) { return strcmp($b['time'], $a['time']); });
$terakhir = $files ? $files[0] : NULL;
$umur_hari = $terakhir ? (int) floor((time() - strtotime($terakhir['time'])) / 86400) : NULL;
$perintah = 'php ' . rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'index.php backup run';
?>

<div class="kepala">
    <div class="kepala__teks">
        <p>Salinan seluruh isi database — berita, pendaftar, pengurus, pesan, dan lainnya. Simpan salinannya di tempat lain selain server, misalnya Google Drive, supaya data tetap aman kalau server bermasalah.</p>
    </div>
</div>

<div class="kisi kisi--utama">
    <div class="kisi" style="align-content:start">
        <section class="kartu">
            <div class="kartu__isi" style="display:flex;gap:18px;align-items:center;flex-wrap:wrap">
                <span class="konten__ikon" style="width:52px;height:52px"><?= adm_ikon('cadangan', 26); ?></span>
                <div style="flex:1;min-width:220px">
                    <h3 style="margin:0 0 3px;font-family:var(--serif);font-weight:600;font-size:1.15rem">Buat cadangan sekarang</h3>
                    <p style="margin:0;color:var(--redup);font-size:.9rem">
                        <?php if ($terakhir): ?>
                            Cadangan terakhir dibuat <strong><?= adm_tgl($terakhir['time']); ?></strong>
                            (<?= $umur_hari === 0 ? 'hari ini' : $umur_hari . ' hari lalu'; ?>).
                        <?php else: ?>
                            Belum pernah ada cadangan di server ini.
                        <?php endif; ?>
                    </p>
                </div>
                <a class="tbl tbl--utama" href="<?= base_url('backup/database'); ?>"><?= adm_ikon('unduh', 16); ?> Unduh cadangan (.sql.gz)</a>
            </div>
            <?php if ($umur_hari !== NULL && $umur_hari > 7): ?>
                <div style="margin:0 18px 18px;padding:10px 14px;border-radius:var(--r-kecil);background:var(--kuning-p);color:var(--kuning);font-size:.86rem;display:flex;gap:10px;align-items:center">
                    <?= adm_ikon('awas', 18); ?> Cadangan terakhir sudah lebih dari seminggu. Sebaiknya buat yang baru.
                </div>
            <?php endif; ?>
        </section>

        <section class="kartu" data-tabel data-per-halaman="10">
            <div class="kartu__kepala">
                <h3>Arsip cadangan di server</h3>
                <span class="cap cap--polos"><?= count($files); ?> berkas</span>
            </div>
            <?php if (empty($files)): ?>
                <div class="kosong" style="padding:36px 20px">
                    <p style="margin:0">Belum ada berkas cadangan. Klik <strong>Unduh cadangan</strong> di atas — salinannya juga otomatis disimpan di sini.</p>
                </div>
            <?php else: ?>
                <div class="tabel-bungkus">
                    <table>
                        <thead><tr><th data-urut>Nama berkas</th><th data-urut>Ukuran</th><th data-urut>Dibuat</th></tr></thead>
                        <tbody>
                        <?php foreach ($files as $f): ?>
                            <tr data-baris>
                                <td><span class="kode"><?= adm_e($f['name']); ?></span></td>
                                <td class="sub-sel" data-nilai="<?= (float) $f['size']; ?>"><?= adm_e($f['size']); ?></td>
                                <td class="sub-sel" data-nilai="<?= (int) strtotime($f['time']); ?>" style="white-space:nowrap"><?= adm_tgl($f['time']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="halaman" data-halaman hidden><span class="halaman__teks"></span><div class="halaman__nav"></div></div>
            <?php endif; ?>
        </section>
    </div>

    <section class="kartu" style="align-self:start">
        <div class="kartu__kepala"><h3>Cadangan otomatis setiap malam</h3></div>
        <div class="kartu__isi">
            <p style="font-size:.88rem;color:var(--redup)">Supaya tidak perlu mengingat, minta pengelola server menjadwalkan perintah ini di cPanel (<em>Cron Jobs</em>) atau Task Scheduler Windows:</p>
            <div class="perintah" style="margin-bottom:18px">
                <code id="perintahCron"><?= adm_e($perintah); ?></code>
                <button type="button" data-salin-dari="#perintahCron" data-salin-pesan="Perintah tersalin.">Salin</button>
            </div>
            <ol class="langkah">
                <li>Berkas cadangan disimpan terkompresi di folder <code>uploads/backups/</code>.</li>
                <li>Cadangan yang berumur lebih dari 30 hari dihapus otomatis supaya disk tidak penuh.</li>
                <li>Sesekali unduh satu cadangan dan simpan di luar server.</li>
            </ol>
        </div>
    </section>
</div>
