<?php
session_start();
include_once("../auth_check.php");

if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: ../login");
    exit;
}

$id_user = $_SESSION['id'];
$role = $_SESSION['role'];

// Ambil semua pasien berdasarkan role
if ($role === 'Admin') {
    $query = mysqli_query($db, "SELECT id_pasien FROM pasien");
} else {
    $query = mysqli_query($db, "SELECT id_pasien FROM pasien WHERE user_id = $id_user");
}

$jumlah_berhasil = 0;
$jumlah_gagal = 0;

while ($row = mysqli_fetch_assoc($query)) {
    $id_pasien = $row['id_pasien'];
    $hasil = hitungDiagnosaStroke($id_pasien, $id_user);

    if ($hasil['status']) {
        $jumlah_berhasil++;
    } else {
        $jumlah_gagal++;
    }
}

echo "<script>
    alert('Diagnosa selesai. Berhasil: $jumlah_berhasil pasien, Gagal: $jumlah_gagal pasien');
    window.location='hasil_diagnosa.php';
</script>";
