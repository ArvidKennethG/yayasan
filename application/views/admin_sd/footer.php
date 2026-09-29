<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* FOOTER ADMIN SD */
$pesan_sukses = (string) $this->session->flashdata('success');
$pesan_galat  = (string) $this->session->flashdata('error');
$this->session->unset_userdata(['success', 'error']);

$bersih = function ($s) {
    return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($s), ENT_QUOTES, 'UTF-8')));
};

$menu = isset($menu) ? $menu : '';
$pakai_editor = in_array($menu, ['profil'], TRUE);
$v_js = @filemtime(FCPATH . 'assets/admin/admin.js') ?: '1';
?>
        </main>

        <footer class="kaki">
            &copy; <?= date('Y'); ?> SD K Citra Bangsa Mandiri &middot; Yayasan CBIM, Kupang
        </footer>
    </div>
</div>

<script>
window.ADMIN_PESAN = <?= json_encode(['sukses' => $bersih($pesan_sukses), 'galat' => $bersih($pesan_galat)], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>
<?php if ($pakai_editor): ?>
<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
<?php endif; ?>
<script src="<?= base_url('assets/admin/admin.js?v=' . $v_js); ?>"></script>
</body>
</html>
