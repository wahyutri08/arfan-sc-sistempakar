<?php
session_start();
include_once("../auth_check.php");

// Pastikan user sudah login
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: ../login");
    exit;
}

// Ambil dan validasi ID
$id_user = $_SESSION['id'];
$role = $_SESSION['role'];

// Ambil semua pasien berdasarkan role
if ($role === 'Admin') {
    $query = mysqli_query($db, "SELECT id_pasien FROM pasien");
} else {
    $query = mysqli_query($db, "SELECT id_pasien FROM pasien WHERE user_id = $id_user");
}

if (!$query || mysqli_num_rows($query) === 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Tidak ada pasien yang ditemukan untuk diproses.'
    ]);
    exit;
}

$jumlah_berhasil = 0;
$jumlah_gagal = 0;

while ($row = mysqli_fetch_assoc($query)) {
    $id_pasien = $row['id_pasien'];

    $hasil = hitungDiagnosaStroke($id_pasien, $id_user);

    if ($hasil > 0) {
        echo json_encode([
            "status" => "success",
            "message" => "Data berhasil diproses."
        ]);
    } elseif ($hasil === -2) {
        echo json_encode([
            "status" => "error",
            "message" => "Pasien sudah memiliki hasil diagnosa."
        ]);
    } elseif ($hasil === -3) {
        echo json_encode([
            "status" => "error",
            "message" => "Data gejala tidak ditemukan."
        ]);
    } elseif ($hasil === -4) {
        echo json_encode([
            "status" => "error",
            "message" => "Data pasien atau penyakit tidak ditemukan."
        ]);
    } elseif ($hasil === -5) {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal menyimpan hasil diagnosa ke database."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Terjadi kesalahan tak terduga."
        ]);
    }
    exit;
    // if ($hasil['status']) {
    //     $jumlah_berhasil++;
    // } else {
    //     $jumlah_gagal++;
    // }
}

// if ($hasil['status']) {
//     echo json_encode([
//         "status" => "success",
//         "message" => "Berhasil: $jumlah_berhasil pasien"
//     ]);
// } else {
//     echo json_encode([
//         "status" => "error",
//         "message" => "Gagal: $jumlah_gagal pasien"
//     ]);
// }
// exit;






// echo json_encode([
//     'status' => 'success',
//     'title' => 'Diagnosa Selesai!',
//     'message' => "Berhasil: $jumlah_berhasil pasien, Gagal: $jumlah_gagal pasien"
// ]);
// exit;
