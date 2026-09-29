<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
</main>

<footer class="kaki">
    <div class="wadah">
        <div class="kaki__kisi">

            <div>
                <div class="kaki__merek">
                    <img src="<?= base_url('assets/img/' . $unit['logo']); ?>" alt="" width="50" height="50">
                    <strong>Yayasan Citra Bina Insan Mandiri</strong>
                </div>
                <p style="max-width:36ch">
                    Menyelenggarakan pendidikan Kristen dari jenjang anak usia dini sampai
                    perguruan tinggi, serta layanan kesehatan dan pelatihan bagi masyarakat
                    Kota Kupang.
                </p>

                <div class="kaki__sosial">
                    <?php if (!empty($pengaturan['facebook'])): ?>
                    <a href="<?= html_escape($pengaturan['facebook']); ?>" target="_blank" rel="noopener" aria-label="Facebook">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M14 9h3V6h-3c-2.2 0-4 1.8-4 4v2H8v3h2v7h3v-7h3l1-3h-4v-2c0-.6.4-1 1-1z"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($pengaturan['instagram'])): ?>
                    <a href="<?= html_escape($pengaturan['instagram']); ?>" target="_blank" rel="noopener" aria-label="Instagram">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($pengaturan['youtube'])): ?>
                    <a href="<?= html_escape($pengaturan['youtube']); ?>" target="_blank" rel="noopener" aria-label="YouTube">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 12s0-3.2-.4-4.7a2.5 2.5 0 0 0-1.8-1.8C18.3 5 12 5 12 5s-6.3 0-7.8.5A2.5 2.5 0 0 0 2.4 7.3C2 8.8 2 12 2 12s0 3.2.4 4.7a2.5 2.5 0 0 0 1.8 1.8C5.7 19 12 19 12 19s6.3 0 7.8-.5a2.5 2.5 0 0 0 1.8-1.8C22 15.2 22 12 22 12zM10 15V9l5 3z"/></svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <h4>Tentang</h4>
                <ul>
                    <li><a href="<?= base_url('profil'); ?>">Profil yayasan</a></li>
                    <li><a href="<?= base_url('struktur'); ?>">Struktur organisasi</a></li>
                    <li><a href="<?= base_url('jejaring'); ?>">Unit pendidikan</a></li>
                    <li><a href="<?= base_url('kontak'); ?>">Kontak</a></li>
                </ul>
            </div>

            <div>
                <h4>Informasi</h4>
                <ul>
                    <li><a href="<?= base_url('berita'); ?>">Berita</a></li>
                    <li><a href="<?= base_url('kegiatan'); ?>">Kegiatan</a></li>
                    <li><a href="<?= base_url('galeri'); ?>">Galeri</a></li>
                    <li><a href="<?= base_url('daftar'); ?>">Pendaftaran</a></li>
                </ul>
            </div>

            <div>
                <h4>Unit Pendidikan</h4>
                <ul>
                    <li><a href="<?= base_url('tk'); ?>">TK Kristen Citra Bangsa</a></li>
                    <li><a href="<?= base_url('sd'); ?>">SD Kristen Citra Bangsa Mandiri</a></li>
                    <li><a href="http://smpkcitrabangsa.com/" target="_blank" rel="noopener">SMP Kristen Citra Bangsa</a></li>
                    <li><a href="https://smakcitrabangsa.sch.id/" target="_blank" rel="noopener">SMA Kristen Citra Bangsa</a></li>
                    <li><a href="https://ucb.ac.id/" target="_blank" rel="noopener">Universitas Citra Bangsa</a></li>
                </ul>

                <h4 style="margin-top:28px; display: flex; align-items: center; gap: 6px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 22h16"/><path d="M4 22V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v18"/><path d="M10 22v-7a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v7"/><path d="M10 7h4"/><path d="M10 11h4"/><path d="M10 15h4"/></svg>
                    Kantor
                </h4>
                <p style="font-size:.9rem;margin:0"><?= html_escape($unit['alamat']); ?></p>
            </div>
        </div>

        <div class="kaki__bawah">
            <span>&copy; <?= date('Y'); ?> Yayasan Citra Bina Insan Mandiri</span>
            <span>
                <?= html_escape(isset($pengaturan['teks_footer']) ? $pengaturan['teks_footer'] : ''); ?>
                &nbsp;&middot;&nbsp;
                <a href="<?= base_url('auth'); ?>">Masuk Admin</a>
            </span>
        </div>
    </div>
</footer>


<!-- Tombol kembali ke atas. Halaman profil dan struktur cukup panjang, jadi
     tanpa ini pengunjung harus menggulir jauh hanya untuk membuka menu. -->
<button type="button" class="ke-atas" id="keAtas" aria-label="Kembali ke atas halaman" hidden>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="m6 15 6-6 6 6"/>
    </svg>
</button>

<script src="<?= aset('assets/js/yayasan.js'); ?>"></script>
</body>
</html>
