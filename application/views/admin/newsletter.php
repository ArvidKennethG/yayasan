<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* ============================================================================
   NEWSLETTER
   Data: $subscribers (id_subscriber, email, preferensi, status, created_at).
   Aksi: admin/delete_subscriber (id_subscriber).
   Ditambah tombol "Salin email aktif" supaya daftar penerima bisa langsung
   ditempel ke kolom BCC surel — tanpa perlu mengekspor apa pun.
   ========================================================================== */

$aktif = array_values(array_filter($subscribers, function ($s) { return $s['status'] === 'Aktif'; }));
$prefs = [];
foreach ($subscribers as $s) { $k = trim((string) $s['preferensi']) ?: 'Umum'; $prefs[$k] = isset($prefs[$k]) ? $prefs[$k] + 1 : 1; }
arsort($prefs);
?>

<div class="kepala">
    <div class="kepala__teks">
        <p>Alamat surel pengunjung yang berlangganan kabar yayasan. Pelanggan yang berhenti berlangganan tetap tercatat dengan status <em>Unsubscribed</em> dan tidak boleh dikirimi lagi.</p>
    </div>
    <?php if ($aktif): ?>
    <div class="kepala__aksi">
        <button type="button" class="tbl tbl--utama" data-salin-dari="#daftarAktif" data-salin-pesan="<?= count($aktif); ?> alamat aktif tersalin. Tempel di kolom BCC supaya alamat penerima tidak saling terlihat."><?= adm_ikon('salin', 16); ?> Salin <?= count($aktif); ?> email aktif</button>
    </div>
    <?php endif; ?>
</div>
<span id="daftarAktif" hidden><?= adm_e(implode(', ', array_map(function ($s) { return html_entity_decode($s['email'], ENT_QUOTES, 'UTF-8'); }, $aktif))); ?></span>

<?php if ($subscribers): ?>
<div class="angka">
    <div class="angka__item angka__item--sorot">
        <span class="angka__label"><?= adm_ikon('surat', 15); ?> Pelanggan aktif</span>
        <span class="angka__nilai"><?= count($aktif); ?></span>
        <span class="angka__catatan">dari <?= count($subscribers); ?> yang pernah mendaftar</span>
    </div>
    <?php foreach (array_slice($prefs, 0, 3, TRUE) as $k => $n): ?>
        <div class="angka__item">
            <span class="angka__label">Minat: <?= adm_e($k); ?></span>
            <span class="angka__nilai"><?= $n; ?></span>
            <span class="angka__catatan">pelanggan</span>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<section class="kartu" data-tabel data-per-halaman="20">
    <?php if (empty($subscribers)): ?>
        <div class="kosong">
            <?= adm_ikon('surat', 44); ?>
            <h3>Belum ada pelanggan</h3>
            <p>Pengunjung yang mengisi kolom langganan di situs akan tercatat di sini.</p>
        </div>
    <?php else: ?>
        <div class="alat">
            <label class="cari">
                <span class="sr">Cari pelanggan</span>
                <?= adm_ikon('cari', 16); ?>
                <input type="search" data-cari placeholder="Cari alamat surel…">
            </label>
            <div class="saring" data-saring-kunci="status" role="group" aria-label="Saring menurut status">
                <button type="button" data-saring-nilai="" aria-pressed="true">Semua <b><?= count($subscribers); ?></b></button>
                <button type="button" data-saring-nilai="Aktif" aria-pressed="false">Aktif <b><?= count($aktif); ?></b></button>
                <?php if (count($subscribers) > count($aktif)): ?>
                    <button type="button" data-saring-nilai="Unsubscribed" aria-pressed="false">Berhenti <b><?= count($subscribers) - count($aktif); ?></b></button>
                <?php endif; ?>
            </div>
            <span class="alat__kanan" data-info></span>
        </div>
        <div class="tabel-bungkus">
            <table>
                <thead>
                    <tr>
                        <th data-urut>Alamat surel</th>
                        <th data-urut>Minat</th>
                        <th data-urut>Bergabung</th>
                        <th data-urut>Status</th>
                        <th style="width:60px"><span class="sr">Aksi</span></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($subscribers as $s): ?>
                    <tr data-baris data-status="<?= adm_e($s['status']); ?>">
                        <td><a class="utama-sel" style="text-decoration:none" href="mailto:<?= adm_e($s['email']); ?>"><?= adm_e($s['email']); ?></a></td>
                        <td><span class="cap cap--polos cap--emas"><?= adm_e($s['preferensi'] ?: 'Umum'); ?></span></td>
                        <td class="sub-sel" data-nilai="<?= (int) strtotime($s['created_at']); ?>" style="white-space:nowrap"><?= adm_tgl($s['created_at']); ?></td>
                        <td><span class="cap cap--<?= adm_warna_status($s['status']); ?>"><?= $s['status'] === 'Aktif' ? 'Aktif' : 'Berhenti'; ?></span></td>
                        <td>
                            <div class="aksi">
                                <button type="button" class="ikon-tbl ikon-tbl--hapus" title="Hapus" aria-label="Hapus pelanggan"
                                        data-hapus="<?= base_url('admin/delete_subscriber'); ?>"
                                        data-kirim='<?= adm_e(adm_json(['id_subscriber' => $s['id_subscriber']])); ?>'
                                        data-judul="Hapus pelanggan ini?"
                                        data-ket="Alamat ini tidak akan menerima kabar yayasan lagi."
                                        data-nama="<?= adm_e($s['email']); ?>"><?= adm_ikon('hapus', 15); ?></button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="kosong-cari" data-kosong-cari hidden>Tidak ada pelanggan yang cocok.</div>
        </div>
        <div class="halaman" data-halaman hidden><span class="halaman__teks"></span><div class="halaman__nav"></div></div>
    <?php endif; ?>
</section>
