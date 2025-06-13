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

    // Ambil user_id asli dari data pasien
    $get_user = mysqli_fetch_assoc(mysqli_query($db, "SELECT user_id FROM pasien WHERE id_pasien = $id_pasien"));
    $id_user_asli = $get_user ? (int)$get_user['user_id'] : null;

    if (perluDiagnosaUlang($id_pasien, $db)) {
        // Hapus diagnosa lama jika ada
        mysqli_query($db, "DELETE FROM hasil_diagnosa WHERE id_pasien = $id_pasien");

        // Hitung diagnosa menggunakan user_id asli pasien
        $hasil = hitungDiagnosaStroke($id_pasien, $id_user_asli);

        if ($hasil['status']) {
            $jumlah_berhasil++;
        } else {
            $jumlah_gagal++;
        }
    }
}

// Pakai ini klo ada error
// while ($row = mysqli_fetch_assoc($query)) {
//     $id_pasien = $row['id_pasien'];

//     if (perluDiagnosaUlang($id_pasien, $db)) {
//         // Hapus diagnosa lama jika ada
//         mysqli_query($db, "DELETE FROM hasil_diagnosa WHERE id_pasien = $id_pasien");

//         // Hitung diagnosa
//         $hasil = hitungDiagnosaStroke($id_pasien, $id_user);

//         if ($hasil['status']) {
//             $jumlah_berhasil++;
//         } else {
//             $jumlah_gagal++;
//         }
//     }
// }

// Kirim respons JSON untuk SweetAlert
echo json_encode([
    'status' => 'success',
    'message' => "Diagnosis Complete.\nSucceed: $jumlah_berhasil pasien\nFailed: $jumlah_gagal pasien"
]);
exit;
