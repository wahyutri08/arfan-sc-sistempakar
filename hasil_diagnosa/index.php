<?php
session_start();
include_once("../auth_check.php");
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: ../login");
    exit;
}
date_default_timezone_set('Asia/Jakarta');



$user_id = $_SESSION['id'];
$role = $_SESSION['role'];
$date_report = $_GET['tanggal_diagnosa'] ?? '';
$filter_user_id = $_GET['user_id'] ?? '';
$nama_pasien = $_GET['nama_pasien'] ?? '';
$nik = $_GET['nik'] ?? '';
// $keterangan = $_GET['keterangan'] ?? '';

// Query dasar menghitung jumlah data
$queryCount = "SELECT COUNT(*) AS total 
               FROM hasil_diagnosa hf
               JOIN users u ON hf.user_id = u.id
               WHERE 1=1";

// Filter berdasarkan tanggal
if (!empty($date_report)) {
    $queryCount .= " AND DATE(hf.tanggal_diagnosa) = '$date_report'";
}

// Filter berdasarkan user_id untuk Staff
if ($role == 'Perawat') {
    $queryCount .= " AND hf.user_id = '$user_id'";
} elseif (!empty($filter_user_id) && $filter_user_id !== 'all') {
    $queryCount .= " AND hf.user_id = '$filter_user_id'";
}

if (!empty($nama_pasien) && $nama_pasien !== 'all') {
    $queryCount .= " AND hf.nama_pasien = '$nama_pasien'";
}

// Query untuk mengambil data dengan filter dan paginasi
$queryData = "SELECT hf.*, u.nama AS nama_user 
              FROM hasil_diagnosa hf
              JOIN users u ON hf.user_id = u.id
              WHERE 1=1";

// Filter berdasarkan tanggal
if (!empty($date_report)) {
    $queryData .= " AND DATE(hf.tanggal_diagnosa) = '$date_report'";
}

// Filter berdasarkan user_id untuk Staff
if ($role == 'Perawat') {
    $queryData .= " AND hf.user_id = '$user_id'";
} elseif (!empty($filter_user_id) && $filter_user_id !== 'all') {
    $queryData .= " AND hf.user_id = '$filter_user_id'";
}

if (!empty($nama_pasien) && $nama_pasien !== 'all') {
    $queryData .= " AND hf.nama_pasien = '$nama_pasien'";
}

$result = query($queryData);
$users = query("SELECT * FROM users");
if ($role == 'Admin') {
    $pasien = query("SELECT * FROM pasien");
} else {
    $pasien = query("SELECT * FROM pasien WHERE user_id = $user_id");
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Diagnosa</title>

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
                            <h3>Laporan Hasil Diagnosa</h3>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="../home">Home</a></li>
                                    <li class="breadcrumb-item">Laporan</li>
                                    <li class="breadcrumb-item active" aria-current="page"><a href="">Laporan Hasil Diagnosa</a></li>
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
                                    <h3 class="card-title">Filter Search</h3>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form method="GET" action="" class="form form-horizontal" id="myForm">
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-md-3 col-12">
                                                        <div class="form-group">
                                                            <label for="tanggal_diagnosa" class="form-label">Tanggal Diagnosa: </label>
                                                            <input
                                                                type="date"
                                                                id="tanggal_diagnosa"
                                                                class="form-control"
                                                                name="tanggal_diagnosa"
                                                                placeholder="Tanggal Lahir"
                                                                value="<?= htmlspecialchars($date_report); ?>" />
                                                        </div>
                                                    </div>
                                                    <?php if ($role == 'Admin'): ?>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label for="user_id" class="form-label">Staff Admin / Perawat: </label>
                                                                <select class="choices form-select" name="user_id" id="user_id">
                                                                    <option value="all">-All-</option>
                                                                    <?php foreach ($users as $user) : ?>
                                                                        <option value="<?= $user['id']; ?>" <?= $filter_user_id == $user['id'] ? 'selected' : ''; ?>><?= $user['nama']; ?> (<?= $user['username']; ?>) | (<?= $user['role']; ?>)</option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="col-md-3 col-12">
                                                        <div class="form-group">
                                                            <label for="nama_pasien" class="form-label">NIK Dan Nama Pasien: </label>
                                                            <select class="choices form-select" name="nama_pasien" id="nama_pasien">
                                                                <option value="all">-All Pasien-</option>
                                                                <?php foreach ($pasien as $p) : ?>
                                                                    <option value="<?= $p['nama_pasien']; ?>" <?= $nama_pasien == $p['nama_pasien'] ? 'selected' : ''; ?>>(<?= $p['nik']; ?>) <?= $p['nama_pasien']; ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-12 d-flex justify-content-start">
                                                        <button type="submit" class="btn btn-sm btn-warning me-3 mb-1">
                                                            Search
                                                        </button>
                                                        <button type="submit" class="btn btn-sm btn-danger me-1 mb-1">
                                                            Cetak
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="section">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-striped" id="table1">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <?php if ($role == 'Admin') : ?>
                                            <th>Nama Staff Perawat</th>
                                        <?php endif; ?>
                                        <th>Nama Pasien</th>
                                        <th>Tanggal Diagnosa</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result): ?>
                                        <?php foreach ($result as $index => $row): ?>
                                            <tr>
                                                <td><?= $index + 1; ?></td>
                                                <?php if ($role == 'Admin') : ?>
                                                    <td><?= htmlspecialchars($row["nama_user"]); ?></td>
                                                <?php endif; ?>
                                                <td><?= htmlspecialchars($row['nama_pasien']); ?></td>
                                                <td><?= htmlspecialchars($row['tanggal_diagnosa']); ?></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-info dropdown-toggle me-1" type="button"
                                                            id="dropdownMenuButton3" data-bs-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            Action
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton3">
                                                            <a class="dropdown-item" href="cetak_hasil.php?id_hasil=<?= $row["id_hasil"]; ?>">Cetak Hasil</a>
                                                            <a class="dropdown-item" href="view.php?id_hasil=<?= $row["id_hasil"]; ?>">View</a>
                                                            <?php if ($role == 'Admin') : ?>
                                                                <a class="dropdown-item tombol-hapus" href="delete_hasil.php?id_hasil=<?= $row["id_hasil"]; ?>">Delete</a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>

                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
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
    <script src="../assets/extensions/sweetalert2/sweetalert2.all.min.js"></script>
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