<?php
session_start();
include_once("../auth_check.php");
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: ../login");
    exit;
}

if ($_SESSION['role'] !== 'Admin') {
    header("HTTP/1.1 404 Not Found");
    include("../error/error-403.html");
    exit;
}


if (isset($_GET["id_rule"]) && is_numeric($_GET["id_rule"])) {
    $id_rule = $_GET["id_rule"];
} else {
    header("HTTP/1.1 404 Not Found");
    include("../error/error-404.html");
    exit;
}

$rule = query("SELECT * FROM rule WHERE id_rule = $id_rule");

if (empty($rule)) {
    header("HTTP/1.1 404 Not Found");
    include("../error/error-404.html");
    exit;
}
$rule = $rule[0];

$penyakit = query("SELECT * FROM penyakit");
$gejala = query("SELECT * FROM gejala");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $result = editRule($_POST);
    if ($result > 0) {
        echo json_encode(["status" => "success", "message" => "Data Successfully Updated"]);
    } elseif ($result == -1) {
        echo json_encode(["status" => "error", "message" => "Kode Rule Already Existed"]);
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
    <title>Edit - <?= $rule["kode_rule"]; ?></title>


    <link rel="stylesheet" href="../assets/extensions/choices.js/public/assets/styles/choices.css">
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
                <div class="page-title mb-3">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>Edit Rule</h3>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav
                                aria-label="breadcrumb"
                                class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="../dashboard/">Dashboard</a></li>
                                    <li class="breadcrumb-item" aria-current="page">
                                        Master Data
                                    </li>
                                    <li class="breadcrumb-item" aria-current="page">Rule</li>
                                    <li class="breadcrumb-item" aria-current="page">Edit</li>
                                    <li class="breadcrumb-item active" aria-current="page"><?= $rule["kode_rule"]; ?></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- // Basic multiple Column Form section start -->
                <section id="basic-horizontal-layouts">
                    <div class="row match-height">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">DATA RULE</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form method="POST" action="" enctype="multipart/form-data" class="form form-horizontal" data-parsley-validate id="myForm">
                                            <input type="hidden" name="id_rule" value="<?= $rule["id_rule"]; ?>">
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="kode_rule" class="form-label">Kode Rule <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-md-5 form-group mandatory">
                                                        <input type="text" id="kode_rule" class="form-control" name="kode_rule"
                                                            placeholder="Kode Rule" value="<?= $rule["kode_rule"]; ?>" data-parsley-required="true" />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="id_penyakit" class="form-label">Jenis Penyakit <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div class="form-group">
                                                            <select class="choices form-select" name="id_penyakit" id="id_penyakit" required>
                                                                <option value="" disabled selected>Choose One..</option>
                                                                <?php foreach ($penyakit as $p) : ?>
                                                                    <option value="<?= $p["id_penyakit"]; ?>" <?= $p["nama_penyakit"] ? "selected" : "" ?>>(<?= $p["kode_penyakit"]; ?>) <?= $p["nama_penyakit"]; ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="id_gejala" class="form-label">Jenis Gejala <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div class="form-group">
                                                            <select class="choices form-select" name="id_gejala" id="id_gejala" required>
                                                                <option value="" disabled selected>Choose One..</option>
                                                                <?php foreach ($gejala as $g) : ?>
                                                                    <option value="<?= $g["id_gejala"]; ?>" <?= $g["id_gejala"] == $rule["id_gejala"] ? "selected" : "" ?>>
                                                                        (<?= $g["kode_gejala"]; ?>) <?= $g["nama_gejala"]; ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="nilai_mb">Nilai MB (Measure of Belief) <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-md-5 form-group">
                                                        <input type="number" id="nilai_mb" class="form-control" name="nilai_mb"
                                                            placeholder="Nilai MB (Measure of Belief)" step="0.01" value="<?= $rule["nilai_mb"]; ?>" data-parsley-required="true" />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="nilai_md">Nilai MD (Measure of Disbelief) <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-md-5 form-group">
                                                        <input type="number" id="nilai_md" class="form-control" name="nilai_md"
                                                            placeholder="Nilai MD (Measure of Disbelief)" step="0.01" value="<?= $rule["nilai_md"]; ?>" data-parsley-required="true" />
                                                    </div>
                                                    <div class="col-sm-12 d-flex justify-content-end mt-3">
                                                        <button type="submit" class="btn btn-primary me-1 mb-1">Submit</button>
                                                        <button type="reset"
                                                            class="btn btn-light-secondary me-1 mb-1">Reset</button>
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
                <!-- // Basic multiple Column Form section end -->
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
    <script src="../assets/extensions/choices.js/public/assets/scripts/choices.js"></script>
    <script src="../assets/static/js/pages/form-element-select.js"></script>
    <script src="../assets/extensions/sweetalert2/sweetalert2.min.js"></script>
    <script src="../assets/static/js/pages/sweetalert2.js"></script>
    <script src="../assets/static/js/logoutsweetalert.js"></script>
    <script>
        $(document).ready(function() {
            $('#myForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: '',
                    type: 'POST',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        const res = JSON.parse(response);
                        if (res.status === 'success') {
                            Swal2.fire({
                                title: "Success",
                                text: res.message,
                                icon: "success"
                            }).then(() => {
                                window.location.href = '../rule';
                            });
                        } else {
                            Swal2.fire('Error', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal2.fire('Error', 'Terjadi kesalahan pada server', 'error');
                    }
                });
            });
        });
    </script>
</body>

</html>