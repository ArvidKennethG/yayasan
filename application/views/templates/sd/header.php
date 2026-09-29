<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? html_escape($title) : 'SD K Citra Bangsa — Yayasan CBIM'; ?></title>
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/templates/media/logos/logo-cbim.png" />
    <meta name="description" content="<?= isset($subsite_meta['deskripsi']) ? html_escape($subsite_meta['deskripsi']) : 'SD K Citra Bangsa, Kupang. Di bawah naungan Yayasan Citra Bina Insan Mandiri (YCBIM).'; ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700;800&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
    /* ============================================================
       TOKEN DESAIN
       ============================================================ */
    :root {
        --kertas:      #FFF7EC;
        --kertas-tua:  #FBEBD6;
        --langit:      #BFE7FF;
        --langit-muda: #E6F5FF;
        --tinta:       #3B3355;
        --tinta-muda:  #6B6285;
        --kuning:      #FFC53D;
        --merah:       #EE5D4E;
        --biru:        #2E9BD6;
        --hijau:       #57B979;
        --ungu:        #9B7EDE;
        --malam:       #2A2450;

        --lebar: 1120px;
        --radius-l: 34px;
        --radius-m: 22px;

        --font-judul: "Baloo 2", "Trebuchet MS", sans-serif;
        --font-isi:   "Nunito", "Segoe UI", sans-serif;
    }

    /* ============================================================
       DASAR
       ============================================================ */
    *, *::before, *::after { box-sizing: border-box; }

    html { scroll-behavior: smooth; }

    body {
        margin: 0;
        background: var(--kertas);
        color: var(--tinta);
        font-family: var(--font-isi);
        font-size: 17px;
        line-height: 1.65;
        -webkit-font-smoothing: antialiased;
    }

    h1, h2, h3, h4 {
        font-family: var(--font-judul);
        font-weight: 700;
        line-height: 1.15;
        margin: 0 0 .5em;
        letter-spacing: -.01em;
    }
    h1 { font-size: clamp(2.1rem, 6.2vw, 3.9rem); font-weight: 800; }
    h2 { font-size: clamp(1.8rem, 4.4vw, 2.8rem); }
    h3 { font-size: clamp(1.3rem, 2.6vw, 1.7rem); }

    p { margin: 0 0 1rem; max-width: 66ch; }

    a { color: var(--biru); }

    img, svg { max-width: 100%; }

    :focus-visible {
        outline: 3px solid var(--ungu);
        outline-offset: 3px;
        border-radius: 6px;
    }

    .tk-wadah {
        width: 100%;
        max-width: var(--lebar);
        margin-inline: auto;
        padding-inline: 22px;
    }

    .tk-lewati {
        position: absolute; left: -9999px;
        background: var(--tinta); color: #fff;
        padding: 12px 20px; border-radius: 0 0 14px 0; z-index: 999;
    }
    .tk-lewati:focus { left: 0; top: 0; }

    /* ============================================================
       TOMBOL
       ============================================================ */
    .tk-tombol {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-family: var(--font-judul);
        font-weight: 700;
        font-size: 1.12rem;
        text-decoration: none;
        padding: 14px 30px 16px;
        border: none;
        border-radius: 999px;
        cursor: pointer;
        transition: transform .12s ease, box-shadow .12s ease;
    }
    .tk-tombol--utama {
        background: var(--merah);
        color: #fff;
        box-shadow: 0 6px 0 #C7402F;
    }
    .tk-tombol--utama:hover,
    .tk-tombol--utama:focus-visible {
        transform: translateY(2px);
        box-shadow: 0 4px 0 #C7402F;
    }
    .tk-tombol--kedua {
        background: #fff;
        color: var(--tinta);
        box-shadow: 0 6px 0 rgba(59,51,85,.18);
    }
    .tk-tombol--kedua:hover,
    .tk-tombol--kedua:focus-visible {
        transform: translateY(2px);
        box-shadow: 0 4px 0 rgba(59,51,85,.18);
    }
    .tk-tombol--kuning {
        background: var(--kuning);
        color: #4A3200;
        box-shadow: 0 6px 0 #D69F1E;
    }
    .tk-tombol--kuning:hover,
    .tk-tombol--kuning:focus-visible {
        transform: translateY(2px);
        box-shadow: 0 4px 0 #D69F1E;
    }

    /* ============================================================
       HEADER
       ============================================================ */
    .tk-header {
        position: sticky;
        top: 0;
        z-index: 60;
        background: rgba(255,247,236,.92);
        backdrop-filter: blur(8px);
        transition: box-shadow .2s ease;
    }
    .tk-header.melayang { box-shadow: 0 6px 24px rgba(59,51,85,.12); }

    .tk-header__isi {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 12px 0;
    }
    .tk-header__logo { display: flex; align-items: center; gap: 12px; text-decoration: none; color: var(--tinta); }
    .tk-header__logo img { height: 46px; width: auto; }
    .tk-header__nama { font-family: var(--font-judul); font-weight: 700; font-size: 1.05rem; line-height: 1.1; }
    .tk-header__nama small { display: block; font-family: var(--font-isi); font-weight: 600; font-size: .72rem; color: var(--tinta-muda); }

    .tk-nav { display: flex; align-items: center; gap: 4px; }
    .tk-nav a {
        font-family: var(--font-judul);
        font-weight: 600;
        font-size: 1.05rem;
        color: var(--tinta);
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 999px;
    }
    .tk-nav a:hover { background: var(--kuning); }
    .tk-nav a[aria-current="page"] { background: var(--kuning); }

    .tk-header__aksi { display: flex; align-items: center; gap: 10px; }
    .tk-header__aksi .tk-tombol { padding: 10px 22px 12px; font-size: 1rem; }

    .tk-burger {
        display: none;
        background: #fff;
        border: none;
        width: 46px; height: 46px;
        border-radius: 14px;
        box-shadow: 0 4px 0 rgba(59,51,85,.15);
        cursor: pointer;
        padding: 0;
        place-items: center;
    }
    .tk-burger span {
        display: block; width: 20px; height: 2.5px;
        background: var(--tinta); border-radius: 2px; margin: 3px auto;
        transition: transform .3s cubic-bezier(.4,0,.2,1), opacity .2s ease;
        transform-origin: center;
    }

    @media (max-width: 900px) {
        .tk-burger { display: grid; }
        .tk-burger[aria-expanded="true"] span:nth-child(1) { transform: translateY(5.5px) rotate(45deg); }
        .tk-burger[aria-expanded="true"] span:nth-child(2) { opacity: 0; transform: scaleX(0); }
        .tk-burger[aria-expanded="true"] span:nth-child(3) { transform: translateY(-5.5px) rotate(-45deg); }
        .tk-nav {
            position: absolute;
            top: 100%; left: 0; right: 0;
            flex-direction: column;
            align-items: stretch;
            background: var(--kertas);
            gap: 2px;
            box-shadow: 0 12px 24px rgba(59,51,85,.14);
            display: flex;
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            padding: 0 22px;
            transition: max-height .4s cubic-bezier(.4,0,.2,1),
                        opacity .3s ease,
                        padding .35s cubic-bezier(.4,0,.2,1);
        }
        .tk-nav.terbuka {
            max-height: 350px;
            opacity: 1;
            padding: 10px 22px 22px;
        }
        .tk-nav a {
            padding: 13px 16px;
            transform: translateY(-10px);
            opacity: 0;
            transition: transform .3s cubic-bezier(.4,0,.2,1),
                        opacity .3s ease,
                        background .12s ease;
        }
        .tk-nav.terbuka a {
            transform: translateY(0);
            opacity: 1;
        }
        .tk-nav.terbuka a:nth-child(1) { transition-delay: .06s; }
        .tk-nav.terbuka a:nth-child(2) { transition-delay: .12s; }
        .tk-nav.terbuka a:nth-child(3) { transition-delay: .18s; }
        .tk-nav.terbuka a:nth-child(4) { transition-delay: .24s; }
        .tk-header__nama small { display: none; }
        .tk-header__aksi .tk-tombol--kedua { display: none; }
    }

    /* ============================================================
       HERO HALAMAN
       ============================================================ */
    .tk-hero {
        position: relative;
        background: linear-gradient(180deg, var(--langit) 0%, var(--langit-muda) 62%, #F3FBFF 100%);
        overflow: hidden;
        padding: clamp(44px, 7vw, 80px) 0 0;
        text-align: center;
    }
    .tk-hero__teks { position: relative; z-index: 3; max-width: 760px; margin-inline: auto; }
    .tk-hero h1 { color: var(--tinta); text-wrap: balance; }
    .tk-hero__sub {
        font-size: clamp(1.05rem, 2.1vw, 1.28rem);
        color: #4C4467;
        margin-inline: auto;
        max-width: 54ch;
        margin-bottom: 30px;
    }
    .tk-hero__aksi { display: flex; flex-wrap: wrap; gap: 14px; justify-content: center; }

    .tk-matahari { position: absolute; top: 30px; right: 6%; width: 130px; height: 130px; z-index: 1; }
    .tk-matahari__sinar { transform-origin: 50% 50%; animation: putar 90s linear infinite; }
    @keyframes putar { to { transform: rotate(360deg); } }

    .tk-awan { position: absolute; z-index: 1; fill: #fff; opacity: .9; }
    .tk-awan--1 { top: 60px;  left: 4%;  width: 120px; }
    .tk-awan--2 { top: 150px; left: 26%; width: 78px; opacity: .7; }
    .tk-awan--3 { top: 96px;  right: 30%; width: 96px; opacity: .75; }

    .tk-bukit { display: block; width: 100%; margin-top: -20px; position: relative; z-index: 2; }

    @media (max-width: 700px) {
        .tk-matahari { width: 82px; height: 82px; top: 14px; right: 5%; }
        .tk-awan--2, .tk-awan--3 { display: none; }
        .tk-awan--1 { width: 74px; top: 130px; opacity: .65; }
    }

    /* ============================================================
       TEPI KERTAS BERGUNTING
       ============================================================ */
    .tk-gunting { display: block; width: 100%; height: 34px; }

    /* ============================================================
       BAGIAN
       ============================================================ */
    .tk-bagian { padding: clamp(56px, 8vw, 96px) 0; }
    .tk-bagian--kertas-tua { background: var(--kertas-tua); }
    .tk-bagian--putih { background: #fff; }

    .tk-judul-bagian { max-width: 40ch; margin-bottom: 44px; }
    .tk-judul-bagian p { color: var(--tinta-muda); font-size: 1.08rem; margin-bottom: 0; }
    .tk-judul-bagian--tengah { margin-inline: auto; text-align: center; }
    .tk-judul-bagian--tengah p { margin-inline: auto; }

    .tk-coret { display: block; width: 148px; height: 12px; margin: 6px 0 18px; }
    .tk-judul-bagian--tengah .tk-coret { margin-inline: auto; }

    /* ============================================================
       GRID & KARTU
       ============================================================ */
    .tk-grid-3 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 26px;
    }
    .tk-grid-2 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 26px;
    }
    .tk-grid-4 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 26px;
    }
    .tk-kartu {
        background: #fff;
        border-radius: var(--radius-l);
        padding: 34px 30px 30px;
        box-shadow: 0 10px 0 rgba(59,51,85,.07);
    }
    .tk-kartu h3 { margin-bottom: .35em; }
    .tk-kartu p { color: var(--tinta-muda); margin-bottom: 0; font-size: 1.02rem; }
    .tk-kartu__ikon {
        width: 74px; height: 74px;
        border-radius: 50%;
        display: grid; place-items: center;
        margin-bottom: 20px;
    }
    .tk-kartu__ikon--kuning { background: #FFF0C9; }
    .tk-kartu__ikon--biru   { background: #D6EDFA; }
    .tk-kartu__ikon--hijau  { background: #DBF0E2; }
    .tk-kartu__ikon--merah  { background: #FDDDD9; }
    .tk-kartu__ikon--ungu   { background: #EDE5F9; }
    .tk-kartu--tengah { text-align: center; }
    .tk-kartu--tengah .tk-kartu__ikon { margin-inline: auto; }

    /* ============================================================
       TENTANG
       ============================================================ */
    .tk-tentang {
        display: grid;
        grid-template-columns: 1.15fr .85fr;
        gap: 52px;
        align-items: center;
    }
    @media (max-width: 860px) { .tk-tentang { grid-template-columns: 1fr; gap: 40px; } }

    .tk-tempel {
        position: relative;
        background: #fff;
        border-radius: 8px;
        padding: 34px 30px 30px;
        transform: rotate(-1.2deg);
        box-shadow: 0 14px 30px rgba(59,51,85,.12);
    }
    .tk-tempel::before {
        content: "";
        position: absolute;
        top: -13px; left: 50%;
        width: 128px; height: 30px;
        transform: translateX(-50%) rotate(-2.5deg);
        background: rgba(255,197,61,.62);
        border-left: 1px dashed rgba(0,0,0,.10);
        border-right: 1px dashed rgba(0,0,0,.10);
    }
    .tk-tempel p:last-child { margin-bottom: 0; }
    .tk-tempel--lurus { transform: none; }

    .tk-angka {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
        margin-top: 30px;
    }
    .tk-angka div {
        background: var(--kertas);
        border-radius: var(--radius-m);
        padding: 20px 22px;
    }
    .tk-angka strong {
        display: block;
        font-family: var(--font-judul);
        font-size: 2rem;
        line-height: 1.1;
        color: var(--merah);
    }
    .tk-angka span { font-size: .95rem; color: var(--tinta-muda); }

    /* ============================================================
       JADWAL HARIAN
       ============================================================ */
    .tk-jam {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
        margin-bottom: 34px;
    }
    .tk-jam button {
        font-family: var(--font-judul);
        font-weight: 700;
        font-size: 1.02rem;
        color: var(--tinta);
        background: #fff;
        border: 2.5px solid rgba(59,51,85,.10);
        border-radius: 999px;
        padding: 9px 20px 11px;
        cursor: pointer;
        transition: transform .12s ease, background .12s ease, border-color .12s ease;
    }
    .tk-jam button:hover { transform: translateY(-2px); border-color: var(--biru); }
    .tk-jam button[aria-selected="true"] {
        background: var(--biru);
        border-color: var(--biru);
        color: #fff;
    }

    .tk-panel {
        display: grid;
        grid-template-columns: 130px 1fr;
        gap: 32px;
        align-items: center;
        background: #fff;
        border-radius: var(--radius-l);
        padding: 38px 40px;
        box-shadow: 0 10px 0 rgba(59,51,85,.07);
        max-width: 800px;
        margin-inline: auto;
    }
    .tk-panel[hidden] { display: none; }
    .tk-panel h3 { margin-bottom: .3em; }
    .tk-panel p { margin-bottom: 0; color: var(--tinta-muda); }
    .tk-panel__doodle {
        width: 130px; height: 130px;
        border-radius: 50%;
        display: grid; place-items: center;
        background: var(--kertas);
    }
    @media (max-width: 640px) {
        .tk-panel { grid-template-columns: 1fr; text-align: center; padding: 30px 24px; gap: 20px; }
        .tk-panel__doodle { margin-inline: auto; width: 104px; height: 104px; }
        .tk-panel p { max-width: none; }
    }

    /* ============================================================
       PROGRAM
       ============================================================ */
    .tk-program {
        background: #fff;
        border-radius: var(--radius-l);
        padding: 34px 32px 30px;
        border-top: 9px solid var(--kuning);
        box-shadow: 0 10px 0 rgba(59,51,85,.07);
    }
    .tk-program--biru  { border-top-color: var(--biru); }
    .tk-program--hijau { border-top-color: var(--hijau); }
    .tk-program--merah { border-top-color: var(--merah); }
    .tk-program--ungu  { border-top-color: var(--ungu); }
    .tk-program p { color: var(--tinta-muda); margin-bottom: 0; }
    .tk-program ul { margin: 0; padding-left: 20px; color: var(--tinta-muda); }
    .tk-program li { margin-bottom: 6px; }

    /* ============================================================
       PAPAN CORET-CORET
       ============================================================ */
    .tk-coretan { max-width: 720px; margin-inline: auto; text-align: center; }
    .tk-papan {
        background: #fff;
        border-radius: var(--radius-m);
        box-shadow: 0 10px 0 rgba(59,51,85,.07);
        padding: 14px;
        margin-top: 26px;
    }
    .tk-papan canvas {
        display: block;
        width: 100%;
        height: 300px;
        border-radius: 14px;
        background: var(--kertas);
        touch-action: none;
        cursor: crosshair;
    }
    .tk-krayon {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
        align-items: center;
        margin-top: 16px;
    }
    .tk-krayon__warna {
        width: 40px; height: 40px;
        border-radius: 50%;
        border: 3px solid transparent;
        cursor: pointer;
        padding: 0;
        transition: transform .12s ease;
    }
    .tk-krayon__warna:hover { transform: scale(1.12); }
    .tk-krayon__warna[aria-pressed="true"] {
        border-color: var(--tinta);
        transform: scale(1.12);
    }
    .tk-hapus {
        font-family: var(--font-judul);
        font-weight: 700;
        background: var(--kertas-tua);
        color: var(--tinta);
        border: none;
        border-radius: 999px;
        padding: 9px 20px 11px;
        cursor: pointer;
        margin-left: 6px;
    }
    .tk-hapus:hover { background: var(--kuning); }

    /* ============================================================
       TESTIMONI
       ============================================================ */
    .tk-testimoni {
        max-width: 640px;
        margin-inline: auto;
        text-align: center;
    }
    .tk-testimoni blockquote {
        margin: 0 0 18px;
        font-family: var(--font-judul);
        font-weight: 500;
        font-size: clamp(1.25rem, 2.6vw, 1.6rem);
        line-height: 1.45;
        color: var(--tinta);
    }
    .tk-testimoni cite { font-style: normal; font-weight: 700; color: var(--tinta-muda); }

    /* ============================================================
       AJAKAN
       ============================================================ */
    .tk-ajakan {
        background: var(--kuning);
        border-radius: var(--radius-l);
        padding: clamp(34px, 5vw, 56px);
        text-align: center;
        color: #4A3200;
        margin-bottom: -60px;
        position: relative;
        z-index: 3;
        box-shadow: 0 16px 34px rgba(59,51,85,.16);
    }
    .tk-ajakan h2 { color: #4A3200; }
    .tk-ajakan p { color: #6B4B08; margin-inline: auto; margin-bottom: 26px; }

    /* ============================================================
       FOOTER
       ============================================================ */
    .tk-footer {
        background: var(--malam);
        color: #fff;
        padding: 110px 0 0;
    }
    .tk-footer__grid {
        display: grid;
        grid-template-columns: 1.3fr 1fr 1fr;
        gap: 44px;
        padding-bottom: 46px;
    }
    @media (max-width: 820px) { .tk-footer__grid { grid-template-columns: 1fr; gap: 34px; } }

    .tk-footer h4 {
        font-size: 1.05rem;
        color: var(--kuning);
        margin-bottom: 16px;
    }
    .tk-footer p { color: rgba(255,255,255,.7); font-size: .98rem; }
    .tk-footer a {
        color: rgba(255,255,255,.78);
        text-decoration: none;
        font-size: .98rem;
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 11px;
    }
    .tk-footer a:hover { color: var(--kuning); }
    .tk-footer img.tk-ikon-sosial { height: 19px; width: auto; }

    .tk-footer__bawah {
        border-top: 1px solid rgba(255,255,255,.12);
        padding: 22px 0 26px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 14px;
        color: rgba(255,255,255,.55);
        font-size: .9rem;
    }
    .tk-footer__bawah img { height: 34px; width: auto; }

    /* ============================================================
       TOMBOL KE ATAS
       ============================================================ */
    .tk-keatas {
        position: fixed;
        right: 20px; bottom: 20px;
        width: 50px; height: 50px;
        border-radius: 50%;
        background: var(--merah);
        color: #fff;
        border: none;
        display: grid; place-items: center;
        cursor: pointer;
        box-shadow: 0 6px 0 #C7402F;
        opacity: 0;
        visibility: hidden;
        transition: opacity .2s ease, visibility .2s ease;
        z-index: 50;
    }
    .tk-keatas.tampil { opacity: 1; visibility: visible; }

    /* ============================================================
       ANIMASI SCROLL-TRIGGERED
       ============================================================ */
    .tk-naik {
        opacity: 0;
        transform: translateY(44px);
        transition: opacity 1.2s cubic-bezier(.22,1,.36,1),
                    transform 1.2s cubic-bezier(.22,1,.36,1);
    }
    .tk-naik.tk-tampil { opacity: 1; transform: translateY(0); }

    .tk-muncul {
        opacity: 0;
        transform: scale(.82);
        transition: opacity 1s cubic-bezier(.22,1,.36,1),
                    transform 1s cubic-bezier(.22,1,.36,1);
    }
    .tk-muncul.tk-tampil { opacity: 1; transform: scale(1); }

    .tk-kiri {
        opacity: 0;
        transform: translateX(-50px);
        transition: opacity 1.2s cubic-bezier(.22,1,.36,1),
                    transform 1.2s cubic-bezier(.22,1,.36,1);
    }
    .tk-kiri.tk-tampil { opacity: 1; transform: translateX(0); }

    .tk-kanan {
        opacity: 0;
        transform: translateX(50px);
        transition: opacity 1.2s cubic-bezier(.22,1,.36,1),
                    transform 1.2s cubic-bezier(.22,1,.36,1);
    }
    .tk-kanan.tk-tampil { opacity: 1; transform: translateX(0); }

    .tk-zoom {
        opacity: 0;
        transform: scale(.88);
        transition: opacity 1s ease, transform 1s cubic-bezier(.22,1,.36,1);
    }
    .tk-zoom.tk-tampil { opacity: 1; transform: scale(1); }

    /* Staggered delays */
    .tk-tunda-1 { transition-delay: .18s; }
    .tk-tunda-2 { transition-delay: .36s; }
    .tk-tunda-3 { transition-delay: .54s; }
    .tk-tunda-4 { transition-delay: .72s; }
    .tk-tunda-5 { transition-delay: .9s; }
    .tk-tunda-6 { transition-delay: 1.08s; }

    /* Hover effects */
    .tk-kartu {
        transition: transform .25s cubic-bezier(.22,1,.36,1),
                    box-shadow .25s ease;
    }
    .tk-kartu:hover {
        transform: translateY(-8px);
        box-shadow: 0 18px 36px rgba(59,51,85,.15);
    }
    .tk-kartu__ikon {
        transition: transform .3s cubic-bezier(.22,1,.36,1);
    }
    .tk-kartu:hover .tk-kartu__ikon {
        transform: scale(1.12) rotate(-6deg);
    }
    .tk-program {
        transition: transform .25s cubic-bezier(.22,1,.36,1),
                    box-shadow .25s ease;
    }
    .tk-program:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 32px rgba(59,51,85,.13);
    }
    .tk-tempel {
        transition: transform .3s cubic-bezier(.22,1,.36,1),
                    box-shadow .3s ease;
    }
    .tk-tempel:hover {
        transform: rotate(0deg) translateY(-4px);
        box-shadow: 0 20px 40px rgba(59,51,85,.16);
    }
    .tk-ajakan {
        transition: box-shadow .3s ease;
    }
    .tk-ajakan:hover {
        box-shadow: 0 20px 50px rgba(255,197,61,.35);
    }

    /* Floating decorations */
    @keyframes tk-melayang {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-14px); }
    }
    @keyframes tk-melayang-lambat {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-10px) rotate(8deg); }
    }
    @keyframes tk-kedip {
        0%, 100% { opacity: 1; }
        50% { opacity: .5; }
    }
    .tk-dekorasi {
        position: absolute;
        pointer-events: none;
        z-index: 1;
        font-size: 2rem;
        filter: drop-shadow(0 4px 8px rgba(0,0,0,.12));
    }
    .tk-dekorasi--1 { top: 30px; left: 8%; animation: tk-melayang 3.5s ease-in-out infinite; }
    .tk-dekorasi--2 { top: 80px; right: 10%; animation: tk-melayang-lambat 4s ease-in-out .5s infinite; font-size: 1.6rem; }
    .tk-dekorasi--3 { bottom: 160px; left: 15%; animation: tk-melayang 3s ease-in-out 1s infinite; font-size: 1.4rem; }
    .tk-dekorasi--4 { top: 40px; right: 25%; animation: tk-kedip 2.5s ease-in-out infinite; font-size: 1.2rem; }
    @media (max-width: 700px) {
        .tk-dekorasi--2, .tk-dekorasi--3, .tk-dekorasi--4 { display: none; }
        .tk-dekorasi--1 { font-size: 1.4rem; top: 120px; }
    }

    /* ============================================================
       HORMATI PREFERENSI GERAK
       ============================================================ */
    @media (prefers-reduced-motion: reduce) {
        html { scroll-behavior: auto; }
        *, *::before, *::after {
            animation-duration: .001ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: .001ms !important;
        }
        .tk-naik, .tk-muncul, .tk-kiri, .tk-kanan, .tk-zoom {
            opacity: 1 !important;
            transform: none !important;
        }
    }
    </style>
</head>

<body>
<a class="tk-lewati" href="#konten">Lewati ke konten utama</a>

<!-- ============================ HEADER ============================ -->
<header class="tk-header" id="header">
    <div class="tk-wadah tk-header__isi">
        <a class="tk-header__logo" href="<?= base_url('sd'); ?>">
            <img src="<?= base_url(); ?>assets/templates/media/logos/logo-cbim.png" alt="Logo SD K Citra Bangsa">
            <span class="tk-header__nama">
                SD K Citra Bangsa
                <small>Yayasan Citra Bina Insan Mandiri</small>
            </span>
        </a>

        <nav class="tk-nav" id="nav" aria-label="Menu utama">
            <a href="<?= base_url('sd'); ?>" <?= (isset($active_menu) && $active_menu == 'home') ? 'aria-current="page"' : ''; ?>>Beranda</a>
            <a href="<?= base_url('sd/profil'); ?>" <?= (isset($active_menu) && $active_menu == 'profil') ? 'aria-current="page"' : ''; ?>>Profil</a>
            <a href="<?= base_url('sd/fasilitas'); ?>" <?= (isset($active_menu) && $active_menu == 'fasilitas') ? 'aria-current="page"' : ''; ?>>Fasilitas</a>
            <a href="<?= base_url('sd/kegiatan'); ?>" <?= (isset($active_menu) && $active_menu == 'kegiatan') ? 'aria-current="page"' : ''; ?>>Kegiatan</a>
            <a href="<?= base_url('sd/ppdb'); ?>" <?= (isset($active_menu) && $active_menu == 'ppdb') ? 'aria-current="page"' : ''; ?>>Pendaftaran</a>
        </nav>

        <div class="tk-header__aksi">
            <a href="<?= base_url('admin-sd/login'); ?>" class="tk-tombol tk-tombol--utama">Login Admin</a>
            <a href="<?= base_url(); ?>" class="tk-tombol tk-tombol--kedua">Yayasan</a>
            <button class="tk-burger" id="burger" aria-label="Buka menu" aria-expanded="false" aria-controls="nav">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>

<main id="konten">