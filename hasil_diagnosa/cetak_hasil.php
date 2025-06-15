<?php
session_start();
include_once("../auth_check.php");

if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: ../login");
    exit;
}

$user_id = $_SESSION['id'];
$role = $_SESSION['role'];

if (isset($_GET["id_hasil"]) && is_numeric($_GET["id_hasil"])) {
    $id_hasil = $_GET["id_hasil"];
} else {
    header("HTTP/1.1 404 Not Found");
    include("../error/error-404.html");
    exit;
}

$id_hasil = (int)$id_hasil;
$user_id = (int)$user_id;


if ($role == 'Admin') {
    $hasil = query("
        SELECT 
            h.*, 
            ps.nik, ps.jenis_kelamin, ps.tanggal_lahir, ps.usia, ps.alamat, ps.no_hp,
            GROUP_CONCAT(CONCAT(g.nama_gejala, ' (', gp.nilai_bobot, ')') SEPARATOR ', ') AS daftar_gejala
        FROM hasil_diagnosa h
        JOIN pasien ps ON h.id_pasien = ps.id_pasien
        LEFT JOIN gejala_pasien gp ON gp.id_pasien = ps.id_pasien
        LEFT JOIN gejala g ON gp.id_gejala = g.id_gejala
        WHERE h.id_hasil = $id_hasil
        GROUP BY h.id_hasil
    ");
} else {
    $hasil = query("
        SELECT 
            h.*, 
            ps.nik, ps.jenis_kelamin, ps.tanggal_lahir, ps.usia, ps.alamat, ps.no_hp,
            GROUP_CONCAT(CONCAT(g.nama_gejala, ' (', gp.nilai_bobot, ')') SEPARATOR ', ') AS daftar_gejala
        FROM hasil_diagnosa h
        JOIN pasien ps ON h.id_pasien = ps.id_pasien
        LEFT JOIN gejala_pasien gp ON gp.id_pasien = ps.id_pasien
        LEFT JOIN gejala g ON gp.id_gejala = g.id_gejala
        WHERE h.id_hasil = $id_hasil AND h.user_id = $user_id
        GROUP BY h.id_hasil
    ");
}



if (empty($hasil)) {
    header("HTTP/1.1 404 Not Found");
    include("../error/error-404.html");
    exit;
}
$hasil = $hasil[0];

require_once __DIR__ . '/../vendor/autoload.php';

use Mpdf\Mpdf;

// Inisialisasi mPDF
$mpdf = new \Mpdf\Mpdf([
    'format' => 'A4',
    'orientation' => 'P'
]);
ob_start();
?>

<html>
<title>Hasil Diagnosa - <?= $hasil["nama_pasien"]; ?></title>

<head>
    <link rel="shortcut icon" href="../assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="shortcut icon" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACEAAAAiCAYAAADRcLDBAAAEs2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS41LjAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIgogICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgZXhpZjpQaXhlbFhEaW1lbnNpb249IjMzIgogICBleGlmOlBpeGVsWURpbWVuc2lvbj0iMzQiCiAgIGV4aWY6Q29sb3JTcGFjZT0iMSIKICAgdGlmZjpJbWFnZVdpZHRoPSIzMyIKICAgdGlmZjpJbWFnZUxlbmd0aD0iMzQiCiAgIHRpZmY6UmVzb2x1dGlvblVuaXQ9IjIiCiAgIHRpZmY6WFJlc29sdXRpb249Ijk2LjAiCiAgIHRpZmY6WVJlc29sdXRpb249Ijk2LjAiCiAgIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiCiAgIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIKICAgeG1wOk1vZGlmeURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiCiAgIHhtcDpNZXRhZGF0YURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiPgogICA8eG1wTU06SGlzdG9yeT4KICAgIDxyZGY6U2VxPgogICAgIDxyZGY6bGkKICAgICAgc3RFdnQ6YWN0aW9uPSJwcm9kdWNlZCIKICAgICAgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWZmaW5pdHkgRGVzaWduZXIgMS4xMC4xIgogICAgICBzdEV2dDp3aGVuPSIyMDIyLTAzLTMxVDEwOjUwOjIzKzAyOjAwIi8+CiAgICA8L3JkZjpTZXE+CiAgIDwveG1wTU06SGlzdG9yeT4KICA8L3JkZjpEZXNjcmlwdGlvbj4KIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+Cjw/eHBhY2tldCBlbmQ9InIiPz5V57uAAAABgmlDQ1BzUkdCIElFQzYxOTY2LTIuMQAAKJF1kc8rRFEUxz9maORHo1hYKC9hISNGTWwsRn4VFmOUX5uZZ36oeTOv954kW2WrKLHxa8FfwFZZK0WkZClrYoOe87ypmWTO7dzzud97z+nec8ETzaiaWd4NWtYyIiNhZWZ2TvE946WZSjqoj6mmPjE1HKWkfdxR5sSbgFOr9Ll/rXoxYapQVik8oOqGJTwqPL5i6Q5vCzeo6dii8KlwpyEXFL519LjLLw6nXP5y2IhGBsFTJ6ykijhexGra0ITl5bRqmWU1fx/nJTWJ7PSUxBbxJkwijBBGYYwhBgnRQ7/MIQIE6ZIVJfK7f/MnyUmuKrPOKgZLpEhj0SnqslRPSEyKnpCRYdXp/9++msneoFu9JgwVT7b91ga+LfjetO3PQ9v+PgLvI1xkC/m5A+h7F32zoLXug38dzi4LWnwHzjeg8UGPGbFfySvuSSbh9QRqZ6H+Gqrm3Z7l9zm+h+iafNUV7O5Bu5z3L/wAdthn7QIme0YAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAJTSURBVFiF7Zi9axRBGIefEw2IdxFBRQsLWUTBaywSK4ubdSGVIY1Y6HZql8ZKCGIqwX/AYLmCgVQKfiDn7jZeEQMWfsSAHAiKqPiB5mIgELWYOW5vzc3O7niHhT/YZvY37/swM/vOzJbIqVq9uQ04CYwCI8AhYAlYAB4Dc7HnrOSJWcoJcBS4ARzQ2F4BZ2LPmTeNuykHwEWgkQGAet9QfiMZjUSt3hwD7psGTWgs9pwH1hC1enMYeA7sKwDxBqjGnvNdZzKZjqmCAKh+U1kmEwi3IEBbIsugnY5avTkEtIAtFhBrQCX2nLVehqyRqFoCAAwBh3WGLAhbgCRIYYinwLolwLqKUwwi9pxV4KUlxKKKUwxC6ZElRCPLYAJxGfhSEOCz6m8HEXvOB2CyIMSk6m8HoXQTmMkJcA2YNTHm3congOvATo3tE3A29pxbpnFzQSiQPcB55IFmFNgFfEQeahaAGZMpsIJIAZWAHcDX2HN+2cT6r39GxmvC9aPNwH5gO1BOPFuBVWAZue0vA9+A12EgjPadnhCuH1WAE8ivYAQ4ohKaagV4gvxi5oG7YSA2vApsCOH60WngKrA3R9IsvQUuhIGY00K4flQG7gHH/mLytB4C42EgfrQb0mV7us8AAMeBS8mGNMR4nwHamtBB7B4QRNdaS0M8GxDEog7iyoAguvJ0QYSBuAOcAt71Kfl7wA8DcTvZ2KtOlJEr+ByyQtqqhTyHTIeB+ONeqi3brh+VgIN0fohUgWGggizZFTplu12yW8iy/YLOGWMpDMTPXnl+Az9vj2HERYqPAAAAAElFTkSuQmCC" type="image/png">
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .title,
        h1 {
            text-align: center;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
        }

        .no-border td {
            border: none;
        }

        table.no-border,
        table.no-border td,
        table.no-border tr {
            border: none !important;
        }
    </style>
</head>

<body>

    <div class="header">
        <table class="no-border" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="15%">
                    <img src="../assets/static/images/logo/logo2.png" alt="Logo Yayasan" width="150" height="90">
                </td>
                <td style="text-align: center;">
                    <div class="title">
                        <h1 style="color:#2b73cc"><strong>RUMAH SAKIT UMUM DAERAH</strong></h1>
                        <h1 style="color:#2bcc5e"><strong>PONDOK AREN</strong></h1>
                    </div>
                </td>
            </tr>
        </table>
        <hr>
        <div>HASIL DIAGNOSA PENYAKIT STROKE</div>
    </div>

    <table class="no-border">
        <tr>
            <td>Nama Pasien</td>
            <td>: <?= $hasil["nama_pasien"]; ?></td>
            <td>Nomor Induk Kependudukan</td>
            <td>: <?= $hasil["nik"]; ?></td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>: <?= $hasil["jenis_kelamin"]; ?></td>
            <td>Tanggal Lahir</td>
            <td>: <?= $hasil["tanggal_lahir"]; ?></td>
        </tr>
        <tr>
            <td>Umur</td>
            <td>: <?= $hasil["usia"]; ?></td>
            <td>Tanggal Diagnosa</td>
            <td>: <?= $hasil["tanggal_diagnosa"]; ?></td>
        </tr>
    </table>

    <h3 style="text-align: center;"><strong>Riwayat Gejala Pasien</strong></h3>
    <table>
        <tr>
            <th>Nama Gejala</th>
            <th>Nilai Bobot</th>
        </tr>
        <?php
        $gejalaList = explode(', ', $hasil["daftar_gejala"]);
        foreach ($gejalaList as $item):
            if (preg_match('/^(.*)\s\(([\d.]+)\)$/', $item, $matches)):
                $namaGejala = trim($matches[1]);
                $nilaiBobot = trim($matches[2]);
        ?>
                <tr>
                    <td><?= htmlspecialchars($namaGejala); ?></td>
                    <td style="text-align: center;"><?= htmlspecialchars($nilaiBobot); ?></td>
                </tr>
        <?php
            endif;
        endforeach;
        ?>
    </table>
    <h3 style="text-align: center;"><strong>Hasil Diagnosa</strong></h3>
    <table style="text-align: center;">
        <tr>
            <th>Tanggal Diagnosa</th>
            <th>Diagnosa Penyakit</th>
            <th>Persentase Diagnosa</th>
            <th>Keterangan</th>
        </tr>
        <tr>
            <td><?= $hasil["tanggal_diagnosa"] ?></td>
            <td><?= $hasil["diagnosa"] ?></td>
            <td><?= round($hasil["nilai_cf"] * 100, 2) ?>%</td>
            <td><?= $hasil["keterangan"] ?></td>
        </tr>
    </table>

    <p style="font-size: 14px; line-height: 1.6;">
        Berdasarkan Hasil Deteksi, Pasien <strong><?= $hasil["nama_pasien"]; ?></strong> Terindikasi Memiliki Penyakit <strong>Stroke</strong> Dengan Tingkat Kemungkinan Sebesar <strong><?= round($hasil["nilai_cf"] * 100, 2) ?>%</strong>.
    </p>

</body>

</html>

<?php
// Ambil isi buffer
$html = ob_get_clean();

// Masukkan HTML ke mPDF
$mpdf->WriteHTML($html);

// Output PDF ke browser
$mpdf->Output("Hasil Diagnosa - " . $hasil['nama_pasien'] . ".pdf", \Mpdf\Output\Destination::INLINE);
