/* ============================================================================
   PANEL ADMIN — YAYASAN CBIM
   ----------------------------------------------------------------------------
   Pengganti jQuery, Bootstrap JS, DataTables, dan SweetAlert yang dulu dimuat
   dari Metronic. Semua perilaku panel ada di sini, ditulis tanpa pustaka.

   Satu-satunya pustaka luar yang masih dipakai adalah CKEditor 5 untuk kolom
   isi berita, isi konten, dan deskripsi video — sama seperti versi lama.
   Kalau CKEditor gagal dimuat (misalnya tanpa internet), kolomnya tetap bisa
   diisi sebagai teks biasa.
   ========================================================================== */
(function () {
    'use strict';

    var $  = function (s, akar) { return (akar || document).querySelector(s); };
    var $$ = function (s, akar) { return Array.prototype.slice.call((akar || document).querySelectorAll(s)); };

    /* ------------------------------------------------------------ IKON */
    var IKON = {
        sukses: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8.5 12.5 2.5 2.5 4.5-5"/></svg>',
        galat:  '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7.5v5.5"/><path d="M12 16.5h.01"/></svg>'
    };

    /* ============================================================ TOAST */
    var wadahToast = null;
    function toast(jenis, judul, teks) {
        if (!wadahToast) {
            wadahToast = document.createElement('div');
            wadahToast.className = 'toast-wadah';
            wadahToast.setAttribute('aria-live', 'polite');
            document.body.appendChild(wadahToast);
        }
        var t = document.createElement('div');
        t.className = 'toast toast--' + jenis;
        t.setAttribute('role', jenis === 'galat' ? 'alert' : 'status');
        t.innerHTML = '<span class="toast__ikon">' + IKON[jenis] + '</span>' +
            '<div class="toast__isi"><div class="toast__judul"></div><div class="toast__teks"></div></div>' +
            '<button type="button" class="toast__tutup" aria-label="Tutup">&times;</button>';
        // textContent, bukan innerHTML: pesan dari server tidak boleh bisa
        // menyuntikkan HTML ke halaman.
        t.querySelector('.toast__judul').textContent = judul;
        t.querySelector('.toast__teks').textContent = teks;
        var tutup = function () { t.classList.add('pergi'); setTimeout(function () { t.remove(); }, 230); };
        t.querySelector('.toast__tutup').addEventListener('click', tutup);
        wadahToast.appendChild(t);
        setTimeout(tutup, jenis === 'galat' ? 7000 : 4200);
    }
    window.adminToast = toast;

    /* Sebagian aksi di controller (tambah berita, ubah foto, dan lainnya)
       tidak mengirim pesan sukses — halaman hanya dimuat ulang. Supaya admin
       tetap tahu simpanannya berhasil, formulir menitipkan pesan di
       sessionStorage sebelum dikirim. Kalau controller mengirim pesan galat,
       pesan galat itu yang tampil dan titipan dibuang. */
    var titip = '';
    try { titip = sessionStorage.getItem('admin_sukses') || ''; sessionStorage.removeItem('admin_sukses'); } catch (x) {}

    var pesan = window.ADMIN_PESAN || {};
    if (pesan.galat)       { toast('galat', 'Belum berhasil', pesan.galat); }
    else if (pesan.sukses) { toast('sukses', 'Berhasil', pesan.sukses); }
    else if (titip)        { toast('sukses', 'Berhasil', titip); }

    /* ====================================================== MENU SAMPING */
    var sisi = $('#sisi'), tirai = $('#tirai'), tombolSisi = $('#tombolSisi');
    function aturSisi(buka) {
        if (!sisi) { return; }
        sisi.classList.toggle('buka', buka);
        if (tirai) { tirai.classList.toggle('tampak', buka); }
        if (tombolSisi) { tombolSisi.setAttribute('aria-expanded', buka ? 'true' : 'false'); }
        document.body.style.overflow = buka ? 'hidden' : '';
    }
    if (tombolSisi) { tombolSisi.addEventListener('click', function () { aturSisi(!sisi.classList.contains('buka')); }); }
    if (tirai) { tirai.addEventListener('click', function () { aturSisi(false); }); }

    /* ======================================================= MENU AKUN */
    var akunTombol = $('#akunTombol'), akunMenu = $('#akunMenu');
    if (akunTombol && akunMenu) {
        akunTombol.addEventListener('click', function (e) {
            e.stopPropagation();
            var buka = akunMenu.hidden;
            akunMenu.hidden = !buka;
            akunTombol.setAttribute('aria-expanded', buka ? 'true' : 'false');
        });
        document.addEventListener('click', function (e) {
            if (!akunMenu.hidden && !akunMenu.contains(e.target)) {
                akunMenu.hidden = true;
                akunTombol.setAttribute('aria-expanded', 'false');
            }
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') { return; }
        aturSisi(false);
        if (akunMenu && !akunMenu.hidden) { akunMenu.hidden = true; }
    });

    /* ========================================================= CKEDITOR */
    /* Satu editor per <textarea data-editor>. Disimpan per id supaya dialog
       ubah bisa mengisinya dengan setData(). */
    var editor = {};
    if (window.ClassicEditor) {
        $$('textarea[data-editor]').forEach(function (ta) {
            window.ClassicEditor.create(ta, {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', '|', 'bulletedList', 'numberedList', 'blockQuote', '|', 'insertTable', '|', 'undo', 'redo']
            }).then(function (ed) {
                editor[ta.id] = ed;
                if (ta.dataset.nilaiTunda !== undefined) {
                    ed.setData(ta.dataset.nilaiTunda);
                    delete ta.dataset.nilaiTunda;
                }
            }).catch(function () { /* tetap pakai textarea biasa */ });
        });
    }
    function isiTeks(ta, nilai) {
        ta.value = nilai;
        if (editor[ta.id]) { editor[ta.id].setData(nilai || ''); }
        else if (ta.hasAttribute('data-editor')) { ta.dataset.nilaiTunda = nilai || ''; }
    }
    function nilaiTeks(ta) {
        return editor[ta.id] ? editor[ta.id].getData() : ta.value;
    }

    /* ==================================================== UNGGAH BERKAS */
    function aturUnggah(kotak) {
        var input = $('input[type=file]', kotak);
        var pratinjau = $('.unggah__pratinjau', kotak);
        var nama = $('.unggah__nama', kotak);
        if (!input) { return; }
        var asliPratinjau = pratinjau ? pratinjau.innerHTML : '';
        var asliNama = nama ? nama.textContent : '';
        var maks = parseFloat(kotak.dataset.maks || '5') * 1024 * 1024;
        var tipe = (input.getAttribute('accept') || '.jpg,.jpeg,.png').toLowerCase().split(',').map(function (s) { return s.trim(); });

        kotak._reset = function () {
            input.value = '';
            input.setCustomValidity('');
            kotak.classList.remove('unggah--galat');
            if (pratinjau) { pratinjau.innerHTML = asliPratinjau; }
            if (nama) { nama.textContent = asliNama; }
        };
        kotak._tampilkan = function (url) {
            if (!pratinjau) { return; }
            pratinjau.innerHTML = url ? '<img alt="" src="' + url.replace(/"/g, '&quot;') + '">' : asliPratinjau;
            asliPratinjau = pratinjau.innerHTML;
        };

        input.addEventListener('change', function () {
            var f = input.files && input.files[0];
            kotak.classList.remove('unggah--galat');
            input.setCustomValidity('');
            if (!f) { kotak._reset(); return; }

            // Diperiksa di sini, sebelum dikirim. Controller lama tidak
            // mengalihkan halaman kalau unggahan ditolak, jadi tanpa
            // pemeriksaan ini admin berakhir di halaman kosong.
            var ext = '.' + f.name.split('.').pop().toLowerCase();
            var galat = '';
            if (tipe.indexOf(ext) === -1) { galat = 'Format ' + ext + ' tidak diterima. Pakai JPG atau PNG.'; }
            else if (f.size > maks) { galat = 'Ukurannya ' + (f.size / 1048576).toFixed(1) + ' MB. Maksimal ' + (maks / 1048576) + ' MB.'; }

            if (galat) {
                input.setCustomValidity(galat);
                kotak.classList.add('unggah--galat');
                if (nama) { nama.textContent = galat; }
                return;
            }
            if (nama) { nama.textContent = f.name + ' · ' + Math.max(1, Math.round(f.size / 1024)) + ' KB'; }
            if (pratinjau && /^image\//.test(f.type)) {
                var r = new FileReader();
                r.onload = function () { pratinjau.innerHTML = '<img alt="" src="' + r.result + '">'; };
                r.readAsDataURL(f);
            }
        });
        ['dragenter', 'dragover'].forEach(function (ev) { kotak.addEventListener(ev, function () { kotak.classList.add('seret'); }); });
        ['dragleave', 'drop'].forEach(function (ev) { kotak.addEventListener(ev, function () { kotak.classList.remove('seret'); }); });
    }
    $$('.unggah').forEach(aturUnggah);

    /* ============================================== PRATINJAU YOUTUBE */
    function idYoutube(url) {
        var m = String(url || '').match(/(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/(?:watch\?(?:.*&)?v=|embed\/|shorts\/|live\/|v\/))([A-Za-z0-9_-]{11})/);
        return m ? m[1] : '';
    }
    window.adminIdYoutube = idYoutube;
    $$('input[data-yt-pratinjau]').forEach(function (inp) {
        var tujuan = $(inp.dataset.ytPratinjau);
        var perbarui = function () {
            if (!tujuan) { return; }
            var id = idYoutube(inp.value);
            var img = $('img', tujuan), teks = $('[data-yt-teks]', tujuan);
            if (id) {
                img.classList.remove('img-rusak');
                img.src = 'https://i.ytimg.com/vi/' + id + '/hqdefault.jpg';
                img.hidden = false;
                if (teks) { teks.hidden = true; }
            } else {
                img.hidden = true;
                img.removeAttribute('src');
                if (teks) {
                    teks.hidden = false;
                    teks.textContent = inp.value ? 'Bukan tautan YouTube. Tautannya tetap disimpan, tapi tidak bisa diputar di situs.' : 'Tempel tautan untuk melihat pratinjau.';
                }
            }
        };
        inp.addEventListener('input', perbarui);
        inp._perbarui = perbarui;
    });

    /* =========================================================== DIALOG */
    var dataHalaman = {};
    var blob = $('#data-halaman');
    if (blob) { try { dataHalaman = JSON.parse(blob.textContent); } catch (e) { dataHalaman = {}; } }

    function bukaDialog(d) {
        if (!d) { return; }
        if (typeof d.showModal === 'function') { d.showModal(); }
        else { d.setAttribute('open', ''); }
    }
    function tutupDialog(d) {
        if (!d) { return; }
        if (typeof d.close === 'function') { d.close(); } else { d.removeAttribute('open'); }
    }

    /* Mengisi dialog dari satu rekaman data halaman. Kunci rekaman sama
       dengan atribut name= di formulir, jadi tidak perlu peta tambahan. */
    function isiDialog(d, rek) {
        Object.keys(rek).forEach(function (k) {
            var v = rek[k] === null || rek[k] === undefined ? '' : String(rek[k]);

            $$('[name="' + k + '"]', d).forEach(function (el) {
                if (el.type === 'file') { return; }
                if (el.type === 'radio' || el.type === 'checkbox') { el.checked = (el.value === v); }
                else if (el.tagName === 'TEXTAREA') { isiTeks(el, v); }
                else { el.value = v; }
            });
            $$('[data-tampil="' + k + '"]', d).forEach(function (el) { el.textContent = v || '—'; });
            $$('[data-href="' + k + '"]', d).forEach(function (el) {
                if (v) { el.href = el.dataset.awalan ? el.dataset.awalan + v : v; el.hidden = false; }
                else { el.hidden = true; }
            });
            $$('[data-src="' + k + '"]', d).forEach(function (el) {
                if (v) { el.src = v; el.hidden = false; } else { el.hidden = true; el.removeAttribute('src'); }
            });
            $$('[data-kelas="' + k + '"]', d).forEach(function (el) {
                el.className = el.dataset.kelasDasar + ' ' + v;
            });
        });
        if (rek._pratinjau !== undefined) {
            $$('.unggah', d).forEach(function (u) { if (u._reset) { u._reset(); u._tampilkan(rek._pratinjau); } });
        }
        $$('input[data-yt-pratinjau]', d).forEach(function (inp) { if (inp._perbarui) { inp._perbarui(); } });
    }

    function kosongkanDialog(d) {
        var f = $('form', d);
        if (f && f.dataset.kosongkan !== undefined) {
            f.reset();
            $$('textarea', f).forEach(function (ta) { isiTeks(ta, ''); });
            $$('.unggah', f).forEach(function (u) { if (u._reset) { u._tampilkan(''); u._reset(); } });
            $$('input[data-yt-pratinjau]', f).forEach(function (inp) { if (inp._perbarui) { inp._perbarui(); } });
        }
    }

    document.addEventListener('click', function (e) {
        /* ---- Tombol hapus: satu dialog konfirmasi untuk seluruh halaman */
        var hapus = e.target.closest('[data-hapus]');
        if (hapus) {
            e.preventDefault();
            var dh = document.getElementById('dialogHapus');
            if (!dh) { return; }
            var form = $('form', dh);
            form.action = hapus.dataset.hapus;
            $$('input[data-sementara]', form).forEach(function (i) { i.remove(); });
            var kirim = {};
            try { kirim = JSON.parse(hapus.dataset.kirim || '{}'); } catch (x) {}
            Object.keys(kirim).forEach(function (k) {
                var i = document.createElement('input');
                i.type = 'hidden'; i.name = k; i.value = kirim[k]; i.setAttribute('data-sementara', '');
                form.appendChild(i);
            });
            $('[data-hapus-judul]', dh).textContent = hapus.dataset.judul || 'Hapus data ini?';
            $('[data-hapus-nama]', dh).textContent = hapus.dataset.nama || '';
            var ket = $('[data-hapus-ket]', dh);
            if (ket) { ket.textContent = hapus.dataset.ket || 'Tindakan ini tidak bisa dibatalkan.'; }
            form.dataset.sukses = (hapus.dataset.nama ? '“' + hapus.dataset.nama + '” ' : 'Data ') + 'sudah dihapus.';
            bukaDialog(dh);
            return;
        }

        var pemicu = e.target.closest('[data-buka]');
        if (pemicu) {
            e.preventDefault();
            var d = document.getElementById(pemicu.dataset.buka);
            if (!d) { return; }
            kosongkanDialog(d);
            if (pemicu.dataset.rekam && dataHalaman[pemicu.dataset.rekam]) { isiDialog(d, dataHalaman[pemicu.dataset.rekam]); }
            if (pemicu.dataset.isi) { try { isiDialog(d, JSON.parse(pemicu.dataset.isi)); } catch (x) {} }
            bukaDialog(d);
            var fokus = $('[autofocus], input[type=text]:not([readonly]), select, textarea', d);
            if (fokus && !fokus.hasAttribute('data-editor')) { setTimeout(function () { fokus.focus(); }, 30); }
            return;
        }
        var tutup = e.target.closest('[data-tutup]');
        if (tutup) { tutupDialog(tutup.closest('dialog')); return; }

        // Klik di luar kotak dialog (pada latar gelapnya) menutup dialog
        if (e.target.tagName === 'DIALOG' && e.target.classList.contains('dialog')) {
            var r = e.target.getBoundingClientRect();
            var diLuar = e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom;
            if (diLuar) { tutupDialog(e.target); }
        }

    });

    // Baris yang bisa diklik (misalnya daftar pesan) juga bisa dibuka dengan keyboard
    document.addEventListener('keydown', function (e) {
        if ((e.key === 'Enter' || e.key === ' ') && e.target.matches('[data-buka][tabindex]')) {
            e.preventDefault();
            e.target.click();
        }
    });

    /* ==================================================== KIRIM FORMULIR */
    document.addEventListener('submit', function (e) {
        var f = e.target;
        if (!(f instanceof HTMLFormElement)) { return; }

        // Kolom editor yang wajib diisi. `required` biasa tidak bisa dipakai
        // karena CKEditor menyembunyikan textarea aslinya.
        var kurang = $$('textarea[data-wajib]', f).filter(function (ta) {
            return nilaiTeks(ta).replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim() === '';
        });
        if (kurang.length) {
            e.preventDefault();
            toast('galat', 'Belum lengkap', (kurang[0].dataset.wajib || 'Isi') + ' masih kosong.');
            return;
        }
        $$('textarea', f).forEach(function (ta) { if (editor[ta.id]) { ta.value = editor[ta.id].getData(); } });

        if (f.dataset.sukses) {
            try { sessionStorage.setItem('admin_sukses', f.dataset.sukses); } catch (x) {}
        }

        // Mencegah terkirim dua kali saat jaringan lambat
        var b = f.querySelector('button[type=submit]');
        if (b && !f.hasAttribute('data-tanpa-kunci')) {
            setTimeout(function () {
                b.disabled = true;
                b.dataset.asli = b.innerHTML;
                b.textContent = b.dataset.memuat || 'Menyimpan…';
            }, 0);
        }
    });

    /* ======================================================= TABEL PINTAR */
    /* Pengganti DataTables: pencarian, penyaring status, urut kolom, dan
       pembagian halaman. Bekerja untuk tabel maupun kisi kartu — cukup beri
       elemen pembungkus [data-tabel] dan setiap baris [data-baris]. */
    function tabelPintar(akar) {
        var baris   = $$('[data-baris]', akar);
        var cari    = $('[data-cari]', akar);
        var info    = $('[data-info]', akar);
        var nav     = $('[data-halaman]', akar);
        var kosong  = $('[data-kosong-cari]', akar);
        var perHal  = parseInt(akar.dataset.perHalaman || '15', 10);
        var hal     = 1;
        var kata    = '';
        var saring  = {};
        var tbody   = baris.length ? baris[0].parentNode : null;

        baris.forEach(function (b) { b._teks = (b.dataset.cari || b.textContent).toLowerCase().replace(/\s+/g, ' '); });

        function cocok(b) {
            if (kata && b._teks.indexOf(kata) === -1) { return false; }
            for (var k in saring) {
                if (saring[k] && b.getAttribute('data-' + k) !== saring[k]) { return false; }
            }
            return true;
        }

        function gambar() {
            var lolos = baris.filter(cocok);
            var total = lolos.length;
            var jmlHal = perHal > 0 ? Math.max(1, Math.ceil(total / perHal)) : 1;
            if (hal > jmlHal) { hal = jmlHal; }
            var awal = perHal > 0 ? (hal - 1) * perHal : 0;
            var akhir = perHal > 0 ? awal + perHal : total;

            baris.forEach(function (b) { b.hidden = true; });
            lolos.slice(awal, akhir).forEach(function (b) { b.hidden = false; });

            if (kosong) { kosong.hidden = total !== 0 || baris.length === 0; }
            if (info) {
                info.textContent = total === baris.length
                    ? baris.length + ' data'
                    : total + ' dari ' + baris.length + ' data';
            }
            if (nav) {
                nav.hidden = jmlHal <= 1;
                var tombol = $('.halaman__nav', nav);
                var teks = $('.halaman__teks', nav);
                if (teks) { teks.textContent = total ? 'Menampilkan ' + (awal + 1) + '–' + Math.min(akhir, total) + ' dari ' + total : ''; }
                if (tombol) {
                    tombol.innerHTML = '';
                    var buat = function (label, ke, aktif, mati) {
                        var x = document.createElement('button');
                        x.type = 'button'; x.innerHTML = label;
                        if (aktif) { x.setAttribute('aria-current', 'page'); }
                        if (mati) { x.disabled = true; }
                        x.addEventListener('click', function () { hal = ke; gambar(); akar.scrollIntoView({ block: 'nearest' }); });
                        tombol.appendChild(x);
                    };
                    buat('&lsaquo;', hal - 1, false, hal === 1);
                    for (var i = 1; i <= jmlHal; i++) {
                        if (jmlHal > 7 && i !== 1 && i !== jmlHal && Math.abs(i - hal) > 1) {
                            if (i === 2 || i === jmlHal - 1) {
                                var sela = document.createElement('button');
                                sela.type = 'button'; sela.textContent = '…'; sela.disabled = true;
                                tombol.appendChild(sela);
                            }
                            continue;
                        }
                        buat(String(i), i, i === hal, false);
                    }
                    buat('&rsaquo;', hal + 1, false, hal === jmlHal);
                }
            }
        }

        if (cari) {
            var jeda;
            cari.addEventListener('input', function () {
                clearTimeout(jeda);
                jeda = setTimeout(function () { kata = cari.value.toLowerCase().trim(); hal = 1; gambar(); }, 120);
            });
        }

        $$('[data-saring-kunci]', akar).forEach(function (grup) {
            var kunci = grup.dataset.saringKunci;
            $$('button[data-saring-nilai]', grup).forEach(function (btn) {
                btn.addEventListener('click', function () {
                    $$('button', grup).forEach(function (x) { x.setAttribute('aria-pressed', x === btn ? 'true' : 'false'); });
                    saring[kunci] = btn.dataset.saringNilai;
                    hal = 1; gambar();
                });
            });
        });

        $$('th[data-urut]', akar).forEach(function (th) {
            th.tabIndex = 0;
            var urut = function () {
                var kol = Array.prototype.indexOf.call(th.parentNode.children, th);
                var naik = th.getAttribute('aria-sort') !== 'ascending';
                $$('th[data-urut]', akar).forEach(function (x) { x.removeAttribute('aria-sort'); });
                th.setAttribute('aria-sort', naik ? 'ascending' : 'descending');
                var ambil = function (b) {
                    var sel = b.children[kol];
                    if (!sel) { return ''; }
                    var v = sel.dataset.nilai !== undefined ? sel.dataset.nilai : sel.textContent.trim();
                    return v;
                };
                baris.sort(function (a, b) {
                    var x = ambil(a), y = ambil(b);
                    var nx = parseFloat(x), ny = parseFloat(y);
                    var hasil = (!isNaN(nx) && !isNaN(ny) && /^-?[\d.]+$/.test(x) && /^-?[\d.]+$/.test(y))
                        ? nx - ny
                        : x.localeCompare(y, 'id', { sensitivity: 'base', numeric: true });
                    return naik ? hasil : -hasil;
                });
                if (tbody) { baris.forEach(function (b) { tbody.appendChild(b); }); }
                hal = 1; gambar();
            };
            th.addEventListener('click', urut);
            th.addEventListener('keydown', function (e) { if (e.key === 'Enter') { urut(); } });
        });

        gambar();
    }
    $$('[data-tabel]').forEach(tabelPintar);

    /* ============================================================ SALIN */
    document.addEventListener('click', function (e) {
        var b = e.target.closest('[data-salin], [data-salin-dari]');
        if (!b) { return; }
        var teks = b.dataset.salin;
        if (b.dataset.salinDari) { var el = $(b.dataset.salinDari); teks = el ? el.textContent.trim() : ''; }
        if (!teks) { toast('galat', 'Tidak ada yang disalin', 'Daftarnya masih kosong.'); return; }
        var selesai = function () { toast('sukses', 'Tersalin', b.dataset.salinPesan || 'Teks sudah ada di papan klip.'); };
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(teks).then(selesai, function () { cadangan(teks); selesai(); });
        } else { cadangan(teks); selesai(); }
    });
    function cadangan(teks) {
        var ta = document.createElement('textarea');
        ta.value = teks; ta.style.position = 'fixed'; ta.style.opacity = '0';
        document.body.appendChild(ta); ta.select();
        try { document.execCommand('copy'); } catch (x) {}
        ta.remove();
    }
})();

/* Tautan dengan akhiran #tambah (misalnya dari pintasan dasbor) langsung
   membuka formulir tambah di halaman tujuan. */
(function () {
    if (location.hash !== '#tambah') { return; }
    var t = document.querySelector('[data-tambah]');
    if (t) { setTimeout(function () { t.click(); }, 60); }
    if (history.replaceState) { history.replaceState(null, '', location.pathname + location.search); }
})();

/* Kalau admin menekan tombol Kembali di browser, halaman bisa dipulihkan dari
   cache lengkap dengan tombol simpan yang masih terkunci. Dibuka lagi di sini. */
window.addEventListener('pageshow', function (e) {
    if (!e.persisted) { return; }
    Array.prototype.forEach.call(document.querySelectorAll('button[type=submit][disabled]'), function (b) {
        b.disabled = false;
        if (b.dataset.asli) { b.innerHTML = b.dataset.asli; }
    });
});

/* Gambar yang tidak ditemukan (misalnya berkasnya terhapus dari server tapi
   datanya masih ada) diganti ikon polos supaya tata letak kartu tidak rusak. */
document.addEventListener('error', function (e) {
    var img = e.target;
    if (!img || img.tagName !== 'IMG' || img.classList.contains('img-rusak')) { return; }
    img.classList.add('img-rusak');
    img.title = 'Berkas gambar tidak ditemukan di server';
    img.src = 'data:image/svg+xml,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#B5AD9C" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4.5" width="18" height="15" rx="1.5"/><circle cx="8.5" cy="9.5" r="1.6"/><path d="m4 17.5 4.5-4.5 3.5 3.5 3-3 5 5"/><path d="M3 3l18 18"/></svg>');
}, true);
