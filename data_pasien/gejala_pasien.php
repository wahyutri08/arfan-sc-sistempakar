<?php
session_start();
include_once("../auth_check.php");
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: ../login");
    exit;
}

$user_id = $_SESSION['id'];
$role = $_SESSION['role'];


if (isset($_GET["id_pasien"]) && is_numeric($_GET["id_pasien"])) {
    $id_pasien = $_GET["id_pasien"];
} else {
    header("HTTP/1.1 404 Not Found");
    include("../error/error-404.html");
    exit;
}


if ($role == 'Admin') {
    $pasien = query("SELECT * FROM pasien WHERE id_pasien = $id_pasien");
} else {
    $pasien = query("SELECT * FROM pasien WHERE id_pasien = $id_pasien AND user_id = $user_id");
}


if (empty($pasien)) {
    header("HTTP/1.1 404 Not Found");
    include("../error/error-404.html");
    exit;
}
$pasien = $pasien[0];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $aksi = $_POST['aksi'] ?? '';
    $id_pasien = $_POST['id_pasien'] ?? '';

    // Hapus semua gejala
    if ($aksi === 'hapus') {
        $hapus = mysqli_query($db, "DELETE FROM gejala_pasien WHERE id_pasien = '$id_pasien'");

        if ($hapus) {
            echo json_encode(["status" => "success", "message" => "All Data Was Successfully Deleted"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed To Delete Data"]);
        }
        exit;
    }

    // Simpan gejala
    $result = gejalaPasien($_POST);
    if ($result > 0) {
        echo json_encode(["status" => "success", "message" => "Data Successfully Updated"]);
    } elseif ($result == -1) {
        echo json_encode(["status" => "error", "message" => "NIK Already Existed"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Data Failed to Update"]);
    }
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Gejala - <?= $pasien["nama_pasien"]; ?></title>



    <link rel="shortcut icon" href="../assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="shortcut icon" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACEAAAAiCAYAAADRcLDBAAAEs2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS41LjAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIgogICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgZXhpZjpQaXhlbFhEaW1lbnNpb249IjMzIgogICBleGlmOlBpeGVsWURpbWVuc2lvbj0iMzQiCiAgIGV4aWY6Q29sb3JTcGFjZT0iMSIKICAgdGlmZjpJbWFnZVdpZHRoPSIzMyIKICAgdGlmZjpJbWFnZUxlbmd0aD0iMzQiCiAgIHRpZmY6UmVzb2x1dGlvblVuaXQ9IjIiCiAgIHRpZmY6WFJlc29sdXRpb249Ijk2LjAiCiAgIHRpZmY6WVJlc29sdXRpb249Ijk2LjAiCiAgIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiCiAgIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIKICAgeG1wOk1vZGlmeURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiCiAgIHhtcDpNZXRhZGF0YURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiPgogICA8eG1wTU06SGlzdG9yeT4KICAgIDxyZGY6U2VxPgogICAgIDxyZGY6bGkKICAgICAgc3RFdnQ6YWN0aW9uPSJwcm9kdWNlZCIKICAgICAgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWZmaW5pdHkgRGVzaWduZXIgMS4xMC4xIgogICAgICBzdEV2dDp3aGVuPSIyMDIyLTAzLTMxVDEwOjUwOjIzKzAyOjAwIi8+CiAgICA8L3JkZjpTZXE+CiAgIDwveG1wTU06SGlzdG9yeT4KICA8L3JkZjpEZXNjcmlwdGlvbj4KIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+Cjw/eHBhY2tldCBlbmQ9InIiPz5V57uAAAABgmlDQ1BzUkdCIElFQzYxOTY2LTIuMQAAKJF1kc8rRFEUxz9maORHo1hYKC9hISNGTWwsRn4VFmOUX5uZZ36oeTOv954kW2WrKLHxa8FfwFZZK0WkZClrYoOe87ypmWTO7dzzud97z+nec8ETzaiaWd4NWtYyIiNhZWZ2TvE946WZSjqoj6mmPjE1HKWkfdxR5sSbgFOr9Ll/rXoxYapQVik8oOqGJTwqPL5i6Q5vCzeo6dii8KlwpyEXFL519LjLLw6nXP5y2IhGBsFTJ6ykijhexGra0ITl5bRqmWU1fx/nJTWJ7PSUxBbxJkwijBBGYYwhBgnRQ7/MIQIE6ZIVJfK7f/MnyUmuKrPOKgZLpEhj0SnqslRPSEyKnpCRYdXp/9++msneoFu9JgwVT7b91ga+LfjetO3PQ9v+PgLvI1xkC/m5A+h7F32zoLXug38dzi4LWnwHzjeg8UGPGbFfySvuSSbh9QRqZ6H+Gqrm3Z7l9zm+h+iafNUV7O5Bu5z3L/wAdthn7QIme0YAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAJTSURBVFiF7Zi9axRBGIefEw2IdxFBRQsLWUTBaywSK4ubdSGVIY1Y6HZql8ZKCGIqwX/AYLmCgVQKfiDn7jZeEQMWfsSAHAiKqPiB5mIgELWYOW5vzc3O7niHhT/YZvY37/swM/vOzJbIqVq9uQ04CYwCI8AhYAlYAB4Dc7HnrOSJWcoJcBS4ARzQ2F4BZ2LPmTeNuykHwEWgkQGAet9QfiMZjUSt3hwD7psGTWgs9pwH1hC1enMYeA7sKwDxBqjGnvNdZzKZjqmCAKh+U1kmEwi3IEBbIsugnY5avTkEtIAtFhBrQCX2nLVehqyRqFoCAAwBh3WGLAhbgCRIYYinwLolwLqKUwwi9pxV4KUlxKKKUwxC6ZElRCPLYAJxGfhSEOCz6m8HEXvOB2CyIMSk6m8HoXQTmMkJcA2YNTHm3congOvATo3tE3A29pxbpnFzQSiQPcB55IFmFNgFfEQeahaAGZMpsIJIAZWAHcDX2HN+2cT6r39GxmvC9aPNwH5gO1BOPFuBVWAZue0vA9+A12EgjPadnhCuH1WAE8ivYAQ4ohKaagV4gvxi5oG7YSA2vApsCOH60WngKrA3R9IsvQUuhIGY00K4flQG7gHH/mLytB4C42EgfrQb0mV7us8AAMeBS8mGNMR4nwHamtBB7B4QRNdaS0M8GxDEog7iyoAguvJ0QYSBuAOcAt71Kfl7wA8DcTvZ2KtOlJEr+ByyQtqqhTyHTIeB+ONeqi3brh+VgIN0fohUgWGggizZFTplu12yW8iy/YLOGWMpDMTPXnl+Az9vj2HERYqPAAAAAElFTkSuQmCC" type="image/png">
    <link rel="stylesheet" href="../assets/extensions/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="../assets/compiled/css/app.css">
    <link rel="stylesheet" href="../assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="../assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="../assets/extensions/@fortawesome/fontawesome-free/css/all.min.css">
</head>

<body>
    <script src="../assets/static/js/initTheme.js"></script>
    <div id="pageLoader" style="position:fixed;left:0;top:0;width:100vw;height:100vh;z-index:2000;background:rgba(30,32,38,0.80);display:flex;align-items:center;justify-content:center;">
        <!-- <img src="../assets/compiled/svg/audio.svg" class="me-4" style="width: 3rem" alt="audio"> -->
        <button class="btn btn-secondary" type="button" disabled>
            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
            Processing...
        </button>
    </div>
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
                <div class="page-title mb-4">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>Tambah Gejala Pasien</h3>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav
                                aria-label="breadcrumb"
                                class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="../home/">Dashboard</a></li>
                                    <li class="breadcrumb-item" aria-current="page">
                                        Master Data
                                    </li>
                                    <li class="breadcrumb-item" aria-current="page">Data Pasien</li>
                                    <li class="breadcrumb-item" aria-current="page">Tambah Gejala</li>
                                    <li class="breadcrumb-item active" aria-current="page"><?= $pasien["nama_pasien"]; ?></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="row" id="table-borderless">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Gejala Pasien</h4>
                                    <h6 class="text-subtitle text-muted"><?= $pasien["nama_pasien"]; ?></h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="" enctype="multipart/form-data" class="form" id="myForm">
                                        <input type="hidden" name="aksi" id="aksi" value="">
                                        <input type="hidden" name="id_pasien" value="<?= $pasien['id_pasien']; ?>">

                                        <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Jenis Gejala</th>
                                                        <th class="text-center">Sangat Yakin</th>
                                                        <th class="text-center">Yakin</th>
                                                        <th class="text-center">Cukup Yakin</th>
                                                        <th class="text-center">Kurang Yakin</th>
                                                        <th class="text-center">Tidak Tahu</th>
                                                        <th class="text-center">Tidak</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $gejala = query("SELECT * FROM gejala");

                                                    // Ambil data nilai_bobot sebelumnya jika ada
                                                    $data_bobot = [];
                                                    $check = query("SELECT * FROM gejala_pasien WHERE id_pasien = {$pasien['id_pasien']}");
                                                    foreach ($check as $item) {
                                                        $data_bobot[$item['id_gejala']] = $item['nilai_bobot'];
                                                    }

                                                    foreach ($gejala as $g) :
                                                        $idG = $g['id_gejala'];
                                                        $selected_value = $data_bobot[$idG] ?? null;

                                                        $keyakinan = [
                                                            '1' => 'Sangat Yakin',
                                                            '0.8' => 'Yakin',
                                                            '0.6' => 'Cukup Yakin',
                                                            '0.4' => 'Kurang Yakin',
                                                            '0.2' => 'Tidak Tahu',
                                                            '0' => 'Tidak'
                                                        ];
                                                    ?>
                                                        <tr>
                                                            <td class="text-bold-500">(<?= $g["kode_gejala"]; ?>) <?= $g["nama_gejala"]; ?></td>
                                                            <?php foreach ($keyakinan as $ky => $label) : ?>
                                                                <td class="text-center">
                                                                    <li class="d-inline-block me-2 mb-1">
                                                                        <div class="form-check">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="radio" class="form-check-input form-check-success"
                                                                                    name="cf[<?= $idG ?>]"
                                                                                    id="<?= $ky ?>_<?= $idG ?>"
                                                                                    value="<?= $ky ?>"
                                                                                    <?= ($selected_value !== null && $selected_value == $ky) ? 'checked' : '' ?>>
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                </td>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-12 d-flex justify-content-end mt-2">
                                                <button type="submit" class="btn btn-primary me-2 mb-1">Save Change</button>
                                                <button type="reset" class="btn btn-light-secondary me-2 mb-1">Reset</button>
                                                <button type="button" class="btn btn-danger me-2 mb-1 btnDelete">Delete</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Footer -->
            <?php require_once '../partials/footer.php'; ?>
            <!-- End Footer -->
        </div>
    </div>
    <script src="../assets/static/js/components/dark.js"></script>
    <script src="../assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="../assets/compiled/js/app.js"></script>
    <script src="../assets/extensions/jquery/jquery.min.js"></script>
    <script src="../assets/extensions/parsleyjs/parsley.min.js"></script>
    <script src="../assets/static/js/pages/parsley.js"></script>
    <script src="../assets/extensions/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="../assets/static/js/pages/sweetalert2.js"></script>
    <script src="../assets/static/js/logoutsweetalert.js"></script>

    <script>
        $(document).ready(function() {
            // Tombol hapus ditekan
            $('.btnDelete').click(function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Are You Sure?',
                    text: "All This Data Will Be Deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Deleted!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#aksi').val('hapus'); // tandai aksi sebagai hapus
                        $('#myForm').submit(); // submit form
                    }
                });
            });

            // Handle submit form (baik simpan atau hapus)
            $('#myForm').on('submit', function(e) {
                e.preventDefault();

                const aksi = $('#aksi').val();

                // ✅ Validasi minimal 1 radio dipilih (hanya saat bukan hapus)
                if (aksi !== 'hapus') {
                    const minimalSatuDipilih = $('input[type="radio"][name^="cf["]:checked').length > 0;
                    if (!minimalSatuDipilih) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Oops!',
                            text: 'At Least One Must Be Selected!'
                        });
                        return; // stop submit
                    }
                }

                const formData = new FormData(this);

                $.ajax({
                    url: '',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        const res = JSON.parse(response);

                        Swal.fire({
                            icon: res.status,
                            title: res.status === 'success' ? 'Berhasil' : 'Gagal',
                            text: res.message
                        }).then(() => {
                            if (res.status === 'success') {
                                if (aksi === 'hapus') {
                                    location.reload(); // Tetap di halaman form
                                } else {
                                    window.location.href = '../data_pasien'; // Redirect ke halaman data
                                }
                            }
                        });
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                    }
                });
            });
        });
    </script>
    <script>
        $(window).on('load', function() {
            $('#pageLoader').fadeOut(250);
        });
    </script>
</body>

</html>