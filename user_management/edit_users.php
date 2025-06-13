<?php
session_start();
include_once("../auth_check.php");
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: ../login");
    exit;
}

if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $id = $_GET["id"];
} else {
    header("HTTP/1.1 404 Not Found");
    include("../error/error-404.html");
    exit;
}

$users = query("SELECT * FROM users WHERE id = $id");
if (empty($users)) {
    header("HTTP/1.1 404 Not Found");
    include("../error/error-404.html");
    exit;
}

$users = $users[0];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai yang dikirimkan untuk username baru
    $newUsername = $_POST["username"];

    // Lakukan pemeriksaan dengan database
    $query = "SELECT username FROM users WHERE username = '$newUsername'";
    $result = mysqli_query($db, $query);

    // Jika username yang dikirim sudah ada di database selain username saat ini, tampilkan pesan kesalahan
    if (mysqli_num_rows($result) > 0 && $newUsername !== $users["username"]) {
        echo json_encode(["status" => "error", "message" => "Username sudah ada. Silakan pilih username lain."]);
    } else {
        // Lanjutkan dengan pembaruan data jika tidak ada masalah
        $result = editUsers($_POST);
        if ($result > 0) {

            // Update session data dengan data baru
            $_SESSION['user_data']['username'] = $_POST['username'];
            $_SESSION['user_data']['nama'] = $_POST['nama'];
            $_SESSION['user_data']['email'] = $_POST['email'];
            $_SESSION['user_data']['role'] = $_POST['role'];
            // $_SESSION['user_data']['avatar'] = $_POST['avatar'];

            echo json_encode(["status" => "success", "message" => "Data Successfully Updated"]);
        } elseif ($result == -1) {
            echo json_encode(["status" => "error", "message" => "Non-Image File Format"]);
        } elseif ($result == -2) {
            echo json_encode(["status" => "error", "message" => "Image Size Too Large"]);
        } elseif ($result == -3) {
            echo json_encode(["status" => "error", "message" => "Confirm Password Incorrect"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Data Failed to Update"]);
        }
    }
    exit;
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit - <?= $users["nama"]; ?></title>



    <link rel="shortcut icon" href="../assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="shortcut icon" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACEAAAAiCAYAAADRcLDBAAAEs2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS41LjAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIgogICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgZXhpZjpQaXhlbFhEaW1lbnNpb249IjMzIgogICBleGlmOlBpeGVsWURpbWVuc2lvbj0iMzQiCiAgIGV4aWY6Q29sb3JTcGFjZT0iMSIKICAgdGlmZjpJbWFnZVdpZHRoPSIzMyIKICAgdGlmZjpJbWFnZUxlbmd0aD0iMzQiCiAgIHRpZmY6UmVzb2x1dGlvblVuaXQ9IjIiCiAgIHRpZmY6WFJlc29sdXRpb249Ijk2LjAiCiAgIHRpZmY6WVJlc29sdXRpb249Ijk2LjAiCiAgIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiCiAgIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIKICAgeG1wOk1vZGlmeURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiCiAgIHhtcDpNZXRhZGF0YURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiPgogICA8eG1wTU06SGlzdG9yeT4KICAgIDxyZGY6U2VxPgogICAgIDxyZGY6bGkKICAgICAgc3RFdnQ6YWN0aW9uPSJwcm9kdWNlZCIKICAgICAgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWZmaW5pdHkgRGVzaWduZXIgMS4xMC4xIgogICAgICBzdEV2dDp3aGVuPSIyMDIyLTAzLTMxVDEwOjUwOjIzKzAyOjAwIi8+CiAgICA8L3JkZjpTZXE+CiAgIDwveG1wTU06SGlzdG9yeT4KICA8L3JkZjpEZXNjcmlwdGlvbj4KIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+Cjw/eHBhY2tldCBlbmQ9InIiPz5V57uAAAABgmlDQ1BzUkdCIElFQzYxOTY2LTIuMQAAKJF1kc8rRFEUxz9maORHo1hYKC9hISNGTWwsRn4VFmOUX5uZZ36oeTOv954kW2WrKLHxa8FfwFZZK0WkZClrYoOe87ypmWTO7dzzud97z+nec8ETzaiaWd4NWtYyIiNhZWZ2TvE946WZSjqoj6mmPjE1HKWkfdxR5sSbgFOr9Ll/rXoxYapQVik8oOqGJTwqPL5i6Q5vCzeo6dii8KlwpyEXFL519LjLLw6nXP5y2IhGBsFTJ6ykijhexGra0ITl5bRqmWU1fx/nJTWJ7PSUxBbxJkwijBBGYYwhBgnRQ7/MIQIE6ZIVJfK7f/MnyUmuKrPOKgZLpEhj0SnqslRPSEyKnpCRYdXp/9++msneoFu9JgwVT7b91ga+LfjetO3PQ9v+PgLvI1xkC/m5A+h7F32zoLXug38dzi4LWnwHzjeg8UGPGbFfySvuSSbh9QRqZ6H+Gqrm3Z7l9zm+h+iafNUV7O5Bu5z3L/wAdthn7QIme0YAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAJTSURBVFiF7Zi9axRBGIefEw2IdxFBRQsLWUTBaywSK4ubdSGVIY1Y6HZql8ZKCGIqwX/AYLmCgVQKfiDn7jZeEQMWfsSAHAiKqPiB5mIgELWYOW5vzc3O7niHhT/YZvY37/swM/vOzJbIqVq9uQ04CYwCI8AhYAlYAB4Dc7HnrOSJWcoJcBS4ARzQ2F4BZ2LPmTeNuykHwEWgkQGAet9QfiMZjUSt3hwD7psGTWgs9pwH1hC1enMYeA7sKwDxBqjGnvNdZzKZjqmCAKh+U1kmEwi3IEBbIsugnY5avTkEtIAtFhBrQCX2nLVehqyRqFoCAAwBh3WGLAhbgCRIYYinwLolwLqKUwwi9pxV4KUlxKKKUwxC6ZElRCPLYAJxGfhSEOCz6m8HEXvOB2CyIMSk6m8HoXQTmMkJcA2YNTHm3congOvATo3tE3A29pxbpnFzQSiQPcB55IFmFNgFfEQeahaAGZMpsIJIAZWAHcDX2HN+2cT6r39GxmvC9aPNwH5gO1BOPFuBVWAZue0vA9+A12EgjPadnhCuH1WAE8ivYAQ4ohKaagV4gvxi5oG7YSA2vApsCOH60WngKrA3R9IsvQUuhIGY00K4flQG7gHH/mLytB4C42EgfrQb0mV7us8AAMeBS8mGNMR4nwHamtBB7B4QRNdaS0M8GxDEog7iyoAguvJ0QYSBuAOcAt71Kfl7wA8DcTvZ2KtOlJEr+ByyQtqqhTyHTIeB+ONeqi3brh+VgIN0fohUgWGggizZFTplu12yW8iy/YLOGWMpDMTPXnl+Az9vj2HERYqPAAAAAElFTkSuQmCC" type="image/png">
    <link rel="stylesheet" href="../assets/extensions/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="../assets/compiled/css/app.css">
    <link rel="stylesheet" href="../assets/compiled/css/app-dark.css">
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
                            <h3>Edit User</h3>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav
                                aria-label="breadcrumb"
                                class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="../home">Home</a></li>
                                    <li class="breadcrumb-item" aria-current="page">
                                        Setting
                                    </li>
                                    <li class="breadcrumb-item" aria-current="page">User Management</li>
                                    <li class="breadcrumb-item" aria-current="page">Edit</li>
                                    <li class="breadcrumb-item active" aria-current="page"><?= $users["nama"]; ?></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- // Basic multiple Column Form section start -->
                <section id="multiple-column-form">
                    <div class="row match-height">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">DATA USER</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form method="POST" action="" enctype="multipart/form-data" class="form" data-parsley-validate id="myForm">
                                            <input type="hidden" name="id" value="<?= $users["id"]; ?>">
                                            <input type="hidden" name="avatarLama" value="<?= $users["avatar"]; ?>">
                                            <div class="row">
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group mandatory">
                                                        <label for="username" class="form-label">Username</label>
                                                        <input
                                                            type="text"
                                                            id="username"
                                                            class="form-control"
                                                            placeholder="Username"
                                                            name="username"
                                                            value="<?= $users["username"]; ?>"
                                                            data-parsley-required="true" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group mandatory">
                                                        <label for="nama" class="form-label">Nama</label>
                                                        <input
                                                            type="text"
                                                            id="nama"
                                                            class="form-control"
                                                            placeholder="Nama"
                                                            name="nama"
                                                            value="<?= $users["nama"]; ?>"
                                                            data-parsley-required="true" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group mandatory">
                                                        <label for="email" class="form-label">Email</label>
                                                        <input
                                                            type="email"
                                                            id="email"
                                                            class="form-control"
                                                            placeholder="EMail"
                                                            name="email"
                                                            value="<?= $users["email"]; ?>"
                                                            data-parsley-required="true" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group mandatory">
                                                        <label for="password" class="form-label">Password</label>
                                                        <input
                                                            type="password"
                                                            id="password"
                                                            class="form-control"
                                                            name="password"
                                                            placeholder="Password"
                                                            data-parsley-required="true" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group mandatory">
                                                        <label for="password2" class="form-label">Konfirmasi Password</label>
                                                        <input
                                                            type="password"
                                                            id="password2"
                                                            class="form-control"
                                                            name="password2"
                                                            placeholder="Konfirmasi Password"
                                                            data-parsley-required="true" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <fieldset class="form-group mandatory">
                                                        <label for="role" class="form-label">Role</label>
                                                        <select class="form-select" id="role" name="role" required>
                                                            <option value="" disabled selected>Choose..</option>
                                                            <option value="Admin" <?= ($users["role"] == "Admin") ? "selected" : "" ?>>Admin</option>
                                                            <option value="Perawat" <?= ($users["role"] == "Perawat") ? "selected" : "" ?>>Perawat</option>
                                                        </select>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <fieldset class="form-group mandatory">
                                                        <label for="status" class="form-label">Status</label>
                                                        <select class="form-select" id="status" name="status" required>
                                                            <option value="" disabled selected>Choose..</option>
                                                            <option value="Aktif" <?= ($users["status"] == "Aktif") ? "selected" : "" ?>>Aktif</option>
                                                            <option value="Tidak Aktif" <?= ($users["status"] == "Tidak Aktif") ? "selected" : "" ?>>Tidak Aktif</option>
                                                        </select>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="mb-3">
                                                        <label for="avatar" class="form-label">Photo Profile</label>
                                                        <input class="form-control" type="file" id="avatar" name="avatar">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 d-flex justify-content-end">
                                                    <button type="submit" class="btn btn-primary me-1 mb-1">
                                                        Submit
                                                    </button>
                                                    <button
                                                        type="reset"
                                                        class="btn btn-light-secondary me-1 mb-1">
                                                        Reset
                                                    </button>
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
    <script src="../assets/extensions/sweetalert2/sweetalert2.min.js"></script>
    <script src="../assets/static/js/pages/sweetalert2.js"></script>>
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
                                window.location.href = '../user_management';
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