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


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $hasil["nama_pasien"] ?> - Detail Hasil Diagnosa</title>

    <link rel="stylesheet" href="../assets/extensions/choices.js/public/assets/styles/choices.css">
    <link rel="shortcut icon" href="../assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="shortcut icon" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACEAAAAiCAYAAADRcLDBAAAEs2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS41LjAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIgogICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgZXhpZjpQaXhlbFhEaW1lbnNpb249IjMzIgogICBleGlmOlBpeGVsWURpbWVuc2lvbj0iMzQiCiAgIGV4aWY6Q29sb3JTcGFjZT0iMSIKICAgdGlmZjpJbWFnZVdpZHRoPSIzMyIKICAgdGlmZjpJbWFnZUxlbmd0aD0iMzQiCiAgIHRpZmY6UmVzb2x1dGlvblVuaXQ9IjIiCiAgIHRpZmY6WFJlc29sdXRpb249Ijk2LjAiCiAgIHRpZmY6WVJlc29sdXRpb249Ijk2LjAiCiAgIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiCiAgIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIKICAgeG1wOk1vZGlmeURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiCiAgIHhtcDpNZXRhZGF0YURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiPgogICA8eG1wTU06SGlzdG9yeT4KICAgIDxyZGY6U2VxPgogICAgIDxyZGY6bGkKICAgICAgc3RFdnQ6YWN0aW9uPSJwcm9kdWNlZCIKICAgICAgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWZmaW5pdHkgRGVzaWduZXIgMS4xMC4xIgogICAgICBzdEV2dDp3aGVuPSIyMDIyLTAzLTMxVDEwOjUwOjIzKzAyOjAwIi8+CiAgICA8L3JkZjpTZXE+CiAgIDwveG1wTU06SGlzdG9yeT4KICA8L3JkZjpEZXNjcmlwdGlvbj4KIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+Cjw/eHBhY2tldCBlbmQ9InIiPz5V57uAAAABgmlDQ1BzUkdCIElFQzYxOTY2LTIuMQAAKJF1kc8rRFEUxz9maORHo1hYKC9hISNGTWwsRn4VFmOUX5uZZ36oeTOv954kW2WrKLHxa8FfwFZZK0WkZClrYoOe87ypmWTO7dzzud97z+nec8ETzaiaWd4NWtYyIiNhZWZ2TvE946WZSjqoj6mmPjE1HKWkfdxR5sSbgFOr9Ll/rXoxYapQVik8oOqGJTwqPL5i6Q5vCzeo6dii8KlwpyEXFL519LjLLw6nXP5y2IhGBsFTJ6ykijhexGra0ITl5bRqmWU1fx/nJTWJ7PSUxBbxJkwijBBGYYwhBgnRQ7/MIQIE6ZIVJfK7f/MnyUmuKrPOKgZLpEhj0SnqslRPSEyKnpCRYdXp/9++msneoFu9JgwVT7b91ga+LfjetO3PQ9v+PgLvI1xkC/m5A+h7F32zoLXug38dzi4LWnwHzjeg8UGPGbFfySvuSSbh9QRqZ6H+Gqrm3Z7l9zm+h+iafNUV7O5Bu5z3L/wAdthn7QIme0YAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAJTSURBVFiF7Zi9axRBGIefEw2IdxFBRQsLWUTBaywSK4ubdSGVIY1Y6HZql8ZKCGIqwX/AYLmCgVQKfiDn7jZeEQMWfsSAHAiKqPiB5mIgELWYOW5vzc3O7niHhT/YZvY37/swM/vOzJbIqVq9uQ04CYwCI8AhYAlYAB4Dc7HnrOSJWcoJcBS4ARzQ2F4BZ2LPmTeNuykHwEWgkQGAet9QfiMZjUSt3hwD7psGTWgs9pwH1hC1enMYeA7sKwDxBqjGnvNdZzKZjqmCAKh+U1kmEwi3IEBbIsugnY5avTkEtIAtFhBrQCX2nLVehqyRqFoCAAwBh3WGLAhbgCRIYYinwLolwLqKUwwi9pxV4KUlxKKKUwxC6ZElRCPLYAJxGfhSEOCz6m8HEXvOB2CyIMSk6m8HoXQTmMkJcA2YNTHm3congOvATo3tE3A29pxbpnFzQSiQPcB55IFmFNgFfEQeahaAGZMpsIJIAZWAHcDX2HN+2cT6r39GxmvC9aPNwH5gO1BOPFuBVWAZue0vA9+A12EgjPadnhCuH1WAE8ivYAQ4ohKaagV4gvxi5oG7YSA2vApsCOH60WngKrA3R9IsvQUuhIGY00K4flQG7gHH/mLytB4C42EgfrQb0mV7us8AAMeBS8mGNMR4nwHamtBB7B4QRNdaS0M8GxDEog7iyoAguvJ0QYSBuAOcAt71Kfl7wA8DcTvZ2KtOlJEr+ByyQtqqhTyHTIeB+ONeqi3brh+VgIN0fohUgWGggizZFTplu12yW8iy/YLOGWMpDMTPXnl+Az9vj2HERYqPAAAAAElFTkSuQmCC" type="image/png">
    <link rel="stylesheet" href="../assets/extensions/simple-datatables/style.css">
    <link rel="stylesheet" href="../assets/compiled/css/table-datatable.css">
    <link rel="stylesheet" href="../assets/compiled/css/app.css">
    <link rel="stylesheet" href="../assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="../assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="../assets/extensions/@fortawesome/fontawesome-free/css/all.min.css">
</head>

<body>
    <script src="../assets/static/js/initTheme.js"></script>
    <div id="app">
        <!-- Sidebar -->
        <?php require_once '../partials/sidebar.php'; ?>
        <!-- End Sidebar -->
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
            <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last mb-2">
                            <h3>Detail Hasil Diagnosa</h3>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="../dashboard">Home</a></li>
                                    <li class="breadcrumb-item">Laporan</li>
                                    <li class="breadcrumb-item" aria-current="page">Detail Hasil Diagnosa</li>
                                    <li class="breadcrumb-item active" aria-current="page"><a href=""><?= $hasil["nama_pasien"] ?></a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <section class="section mt-4">
                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Data Pasien</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 col-12">
                                            <div class="form-group">
                                                <label for="nama_pasien" class="form-label">Nama Pasien</label>
                                                <input
                                                    type="text"
                                                    id="nama_pasien"
                                                    class="form-control"
                                                    name="nama_pasien"
                                                    value="<?= $hasil["nama_pasien"] ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-12">
                                            <div class="form-group">
                                                <label for="nik" class="form-label">Nomor Induk Kependudukan</label>
                                                <input
                                                    type="text"
                                                    id="nik"
                                                    class="form-control"
                                                    name="nik"
                                                    value="<?= $hasil["nik"] ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-12">
                                            <div class="form-group">
                                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                                <input
                                                    type="text"
                                                    id="jenis_kelamin"
                                                    class="form-control"
                                                    name="jenis_kelamin"
                                                    value="<?= $hasil["jenis_kelamin"] ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-12">
                                            <div class="form-group">
                                                <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                                <input
                                                    type="text"
                                                    id="tanggal_lahir"
                                                    class="form-control"
                                                    name="tanggal_lahir"
                                                    value="<?= $hasil["tanggal_lahir"] ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 col-12">
                                            <div class="form-group">
                                                <label for="usia" class="form-label">Usia</label>
                                                <input
                                                    type="text"
                                                    id="usia"
                                                    class="form-control"
                                                    name="usia"
                                                    value="<?= $hasil["usia"] ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-12">
                                            <div class="form-group">
                                                <label for="alamat" class="form-label">Alamat</label>
                                                <input
                                                    type="text"
                                                    id="alamat"
                                                    class="form-control"
                                                    name="alamat"
                                                    value="<?= $hasil["alamat"] ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-12">
                                            <div class="form-group">
                                                <label for="no_hp" class="form-label">Phone</label>
                                                <input
                                                    type="text"
                                                    id="no_hp"
                                                    class="form-control"
                                                    name="no_hp"
                                                    value="<?= $hasil["no_hp"] ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Riwayat Gejala Pasien</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped" id="">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Gejala</th>
                                        <th>Nilai Bobot</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;

                                    // Hilangkan koma dan spasi di awal string
                                    $cleanString = ltrim($hasil["daftar_gejala"], ", ");

                                    preg_match_all('/(.*?)\s*\(([\d.]+)\)/', $cleanString, $matches, PREG_SET_ORDER);

                                    foreach ($matches as $match):
                                        // Hapus koma dan spasi di depan nama gejala
                                        $namaGejala = ltrim($match[1], " ,");
                                        $nilaiBobot = trim($match[2]);
                                    ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($namaGejala); ?></td>
                                            <td><?= htmlspecialchars($nilaiBobot); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Hasil Diagnosa</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped" id="">
                                <thead>
                                    <tr>
                                        <th>Tanggal Diagnosa</th>
                                        <th>Diagnosa Penyakit</th>
                                        <th>Persentase Diagnosa</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?= $hasil["tanggal_diagnosa"] ?></td>
                                        <td><?= $hasil["diagnosa"] ?></td>
                                        <td><?= round($hasil["nilai_cf"] * 100, 2) ?>%</td>
                                        <td><?= $hasil["keterangan"] ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
            <!-- Footer -->
            <?php require_once '../partials/footer.php'; ?>
            <!-- End Footer -->
        </div>
    </div>
    <script src="../assets/extensions/jquery/jquery.min.js"></script>
    <script src="../assets/static/js/components/dark.js"></script>
    <script src="../assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="../assets/compiled/js/app.js"></script>
    <script src="../assets/extensions/simple-datatables/umd/simple-datatables.js"></script>
    <script src="../assets/static/js/pages/simple-datatables.js"></script>
    <script src="../assets/extensions/parsleyjs/parsley.min.js"></script>
    <script src="../assets/static/js/pages/parsley.js"></script>
    <script src="../assets/extensions/choices.js/public/assets/scripts/choices.js"></script>
    <script src="../assets/static/js/pages/form-element-select.js"></script>
    <script src="../assets/extensions/sweetalert2/sweetalert2.min.js"></script>
    <script src="../assets/static/js/logoutsweetalert.js"></script>

    <script>
        $(document).ready(function() {
            $(document).on('click', '.tombol-hapus', function(e) {
                e.preventDefault();
                const href = $(this).attr('href');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This Data Will Be Deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: href,
                            type: 'GET',
                            success: function(response) {
                                let res = JSON.parse(response);
                                if (res.status === 'success') {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: 'Data Successfully Deleted',
                                        icon: 'success',
                                        showConfirmButton: true,
                                    }).then(() => {
                                        location.reload(); // atau reload DataTable ajax jika pakai ajax
                                    });
                                } else {
                                    Swal.fire('Error', 'Gagal menghapus data', 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>

</body>

</html>