<?php
// Set default Content-Type
header('Content-Type: text/html; charset=utf-8');

// Display errors for debugging
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

// Tangkap fatal error sebelum Lambda crash
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        if (!headers_sent()) {
            http_response_code(200);
            header('Content-Type: text/html; charset=utf-8');
        }
        echo '<div style="font-family:sans-serif;max-width:800px;margin:40px auto;padding:24px;border:1px solid #f87171;border-radius:12px;background:#fef2f2;color:#991b1b;">';
        echo '<h2 style="margin-top:0">Terjadi Kendala PHP di Vercel:</h2>';
        echo '<p><strong>Pesan:</strong> ' . htmlspecialchars($error['message']) . '</p>';
        echo '<p><strong>File:</strong> ' . htmlspecialchars($error['file']) . ' (Baris ' . $error['line'] . ')</p>';
        echo '</div>';
    }
});

// Cek apakah database environment variables sudah disetel jika berjalan di Vercel
$db_host = getenv('DB_HOST');
if (getenv('VERCEL') && (empty($db_host) || $db_host === 'localhost')) {
    http_response_code(200);
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Setup Database Cloud - Yayasan CBIM</title>
        <style>
            * { box-sizing: border-box; }
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #f8fafc; margin: 0; padding: 40px 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
            .card { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 36px; max-width: 680px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
            .badge { display: inline-block; background: #10b981; color: #fff; font-size: 13px; font-weight: 700; padding: 6px 14px; border-radius: 999px; margin-bottom: 16px; letter-spacing: 0.5px; }
            h1 { font-size: 24px; margin: 0 0 12px; color: #fff; }
            p { color: #94a3b8; line-height: 1.6; margin: 0 0 20px; font-size: 15px; }
            .step-box { background: #0f172a; border: 1px solid #334155; border-left: 4px solid #38bdf8; border-radius: 8px; padding: 18px; margin-bottom: 24px; }
            .step-title { font-weight: 600; color: #38bdf8; margin-bottom: 8px; font-size: 15px; }
            .env-table { width: 100%; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 13px; color: #e2e8f0; border-collapse: collapse; margin-top: 10px; }
            .env-table td { padding: 6px 0; border-bottom: 1px solid #1e293b; }
            .env-key { color: #f472b6; font-weight: bold; width: 120px; }
            .env-val { color: #93c5fd; }
            .hint { font-size: 13px; color: #64748b; line-height: 1.5; margin-top: 16px; }
        </style>
    </head>
    <body>
        <div class="card">
            <span class="badge">&#10003; VERCEL SERVERLESS BERHASIL TERHUBUNG</span>
            <h1>Aplikasi Berhasil Berjalan di Vercel!</h1>
            <p>Kode PHP CodeIgniter telah berhasil diproses oleh Vercel. Agar halaman web dapat menampilkan data yayasan, hubungkan database MySQL online Anda.</p>
            
            <div class="step-box">
                <div class="step-title">Pengaturan Environment Variables di Vercel:</div>
                <div style="color: #94a3b8; font-size: 13px; margin-bottom: 10px;">
                    Buka Dashboard Vercel &rarr; Proyek Anda &rarr; <strong>Settings</strong> &rarr; <strong>Environment Variables</strong>, lalu masukkan 6 variabel berikut:
                </div>
                <table class="env-table">
                    <tr><td class="env-key">DB_HOST</td><td class="env-val">host database cloud Anda (misal: gateway01.ap-southeast-1.prod.aws.tidbcloud.com)</td></tr>
                    <tr><td class="env-key">DB_USER</td><td class="env-val">username database</td></tr>
                    <tr><td class="env-key">DB_PASS</td><td class="env-val">password database</td></tr>
                    <tr><td class="env-key">DB_NAME</td><td class="env-val">u1711594_yayasan_v2</td></tr>
                    <tr><td class="env-key">DB_PORT</td><td class="env-val">3306</td></tr>
                    <tr><td class="env-key">CI_ENV</td><td class="env-val">production</td></tr>
                </table>
            </div>

            <div class="hint">
                &#128204; <em>Catatan: Karena Vercel berjalan di cloud serverless, MySQL <code>localhost</code> (XAMPP di laptop Anda) tidak dapat diakses dari internet. Anda bisa menggunakan database cloud gratis dari <strong>Aiven MySQL</strong>, <strong>TiDB Cloud</strong>, atau hosting cPanel Anda yang sudah mengaktifkan Remote MySQL.</em>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Set working directory ke root project
chdir(dirname(__DIR__));

// Normalisasi SCRIPT_NAME dan SCRIPT_FILENAME untuk routing CodeIgniter
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'index.php';

// Deteksi HTTPS dari edge proxy Vercel
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

// Eksekusi CodeIgniter front controller
require __DIR__ . '/../index.php';
