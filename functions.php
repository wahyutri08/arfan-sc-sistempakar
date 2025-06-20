<?php
$db = mysqli_connect("localhost", "root", "", "dev-arfan");
date_default_timezone_set('Asia/Jakarta');

function query($query)
{
    global $db;
    $result = mysqli_query($db, $query);
    $rows = [];
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
        }
    } else {
        echo "Error: " . mysqli_error($db);
    }
    return $rows;
}

function register($data)
{
    global $db;

    $username = strtolower(stripcslashes($data["username"]));
    $nama = ucfirst(stripslashes($data["nama"]));
    $email = strtolower(stripslashes($data["email"]));
    $password = mysqli_real_escape_string($db, $data["password"]);
    $password2 = mysqli_real_escape_string($db, $data["password2"]);
    $role = htmlspecialchars($data["role"]);
    $status = htmlspecialchars($data["status"]);

    // //  Upload Gambar
    // $avatar = upload();
    // if (!$avatar) {
    //     return -3;
    // } elseif ($avatar === -1) {
    //     // Kesalahan Ukuran Terlalu Besar
    //     return -4;
    // }

    //  Upload Gambar
    $avatar = upload();
    if ($avatar === -1) {
        return -3;
    }

    $result = mysqli_query($db, "SELECT * FROM users WHERE username = '$username'");

    if (mysqli_fetch_assoc($result)) {
        // Jika Nama Username Sudah Ada
        return -1;
    }

    if ($password !== $password2) {
        // Password 1 tidak sesuai dengan password 2
        return -2;
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    mysqli_query($db, "INSERT INTO users 
    (username, nama, email, password, role, status, avatar) 
    VALUES 
    ('$username','$nama', '$email', '$password', '$role', '$status', '$avatar')");
    return mysqli_affected_rows($db);
}

function editUsers($data)
{
    global $db;
    $id = ($data["id"]);
    $username = strtolower(stripslashes($data["username"]));
    $nama = ucfirst(stripcslashes($data["nama"]));
    $email = strtolower(stripslashes($data["email"]));
    $password = mysqli_real_escape_string($db, $data["password"]);
    $password2 = mysqli_real_escape_string($db, $data["password2"]);
    $avatarLama = htmlspecialchars($data["avatarLama"]);
    $role = htmlspecialchars($data["role"]);
    $status = htmlspecialchars($data["status"]);
    // $usernameLama = htmlspecialchars($data["username"]);

    // Cek apakah user pilih avatar baru atau tidak
    if ($_FILES['avatar']['error'] === 4) {
        $avatar = $avatarLama;
    } else {
        $avatar = upload();
        if ($avatar === -1) {
            // Kesalahan Jika Bukan Gambar
            return -1;
        } elseif ($avatar === -2) {
            // Kesalahan Ukuran Terlalu Besar
            return -2;
        }
    }

    if ($password !== $password2) {
        // Password 1 tidak sesuai dengan password 2
        return -3;
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    $updatedAt = date('Y-m-d H:i:s');
    $query = "UPDATE users SET 
        username = '$username', 
        nama = '$nama', 
        email = '$email',
        password = '$password',
        role = '$role',
        status = '$status',
        avatar = '$avatar',
        updated_at = '$updatedAt' WHERE id = $id";
    mysqli_query($db, $query);

    return mysqli_affected_rows($db);
}

function deleteUsers($id)
{
    global $db;
    mysqli_query($db, "DELETE FROM users WHERE id = $id");
    return mysqli_affected_rows($db);
}

function editProfile($data)
{
    global $db;
    $id = ($data["id"]);
    $nama = ucfirst(stripcslashes($data["nama"]));
    $email = strtolower(stripslashes($data["email"]));
    $avatarLama = htmlspecialchars($data["avatarLama"]);

    // Cek apakah user pilih avatar baru atau tidak
    if ($_FILES['avatar']['error'] === 4) {
        $avatar = $avatarLama;
    } else {
        $avatar = upload();
        if ($avatar === -1) {
            // Kesalahan Jika Bukan Gambar
            return -1;
        } elseif ($avatar === -2) {
            // Kesalahan Jika Ukuran Terlalu Besar
            return -2;
        }
    }

    $query = "UPDATE users SET 
        nama = '$nama',  
        email = '$email',
        avatar = '$avatar' WHERE id = $id";
    mysqli_query($db, $query);

    return mysqli_affected_rows($db);
}

function changePassword($data)
{
    global $db;
    $id = ($data["id"]);
    $password = mysqli_real_escape_string($db, $data["password"]);
    $password2 = mysqli_real_escape_string($db, $data["password2"]);

    if ($password !== $password2) {
        return -1;
    }

    $password = password_hash($password, PASSWORD_DEFAULT);
    $query = "UPDATE users SET 
    password = '$password' WHERE id = $id";
    mysqli_query($db, $query);

    return mysqli_affected_rows($db);
}

function addPasien($data)
{
    global $db;

    $role = $_SESSION['role'];
    $user_id = $_SESSION['id'];

    $nama_pasien = ucfirst(stripcslashes($data["nama_pasien"]));
    $nik = mysqli_real_escape_string($db, $data["nik"]);
    $jenis_kelamin = htmlspecialchars($data["jenis_kelamin"]);
    $tanggal_lahir = htmlspecialchars($data["tanggal_lahir"]);
    $usia = htmlspecialchars($data["usia"]);
    $alamat = htmlspecialchars($data["alamat"]);
    $no_hp = htmlspecialchars($data["no_hp"]);

    // Periksa apakah NIK sudah ada di database
    $query = "SELECT * FROM pasien WHERE nik = '$nik'";
    $result = mysqli_query($db, $query);
    if (mysqli_fetch_assoc($result)) {
        // Jika NIK sudah ada, return -1
        return -1;
    }

    // Cek role pengguna
    if ($role == 'Perawat' || $role == 'Admin') {
        $query = "INSERT INTO pasien 
        (user_id, nama_pasien, nik, jenis_kelamin, tanggal_lahir, usia, alamat, no_hp)
        VALUES 
        ('$user_id', '$nama_pasien', '$nik', '$jenis_kelamin', '$tanggal_lahir', '$usia', '$alamat', '$no_hp')";

        mysqli_query($db, $query);
        return mysqli_affected_rows($db);
    }
}

function editPasien($data)
{
    global $db;
    $id_pasien = $data["id_pasien"];
    $nama_pasien = ucfirst(stripcslashes($data["nama_pasien"]));
    $nik = mysqli_real_escape_string($db, $data["nik"]);
    $jenis_kelamin = htmlspecialchars($data["jenis_kelamin"]);
    $tanggal_lahir = htmlspecialchars($data["tanggal_lahir"]);
    $usia = htmlspecialchars($data["usia"]);
    $alamat = htmlspecialchars($data["alamat"]);
    $no_hp = htmlspecialchars($data["no_hp"]);

    // Periksa apakah nik pasien sudah ada, tetapi abaikan baris yang sedang diedit
    $query = "SELECT * FROM pasien WHERE nik = '$nik' AND id_pasien != $id_pasien";
    $result = mysqli_query($db, $query);

    if (mysqli_fetch_assoc($result)) {
        return -1;
    }

    $updatedAt = date('Y-m-d H:i:s');
    // Jika nis siswa tidak ada yang duplikat, lakukan update
    $query = "UPDATE pasien SET nama_pasien = '$nama_pasien', 
                     nik = '$nik',
                     jenis_kelamin = '$jenis_kelamin',
                     tanggal_lahir = '$tanggal_lahir',
                     tanggal_lahir = '$tanggal_lahir',
                     usia = '$usia',
                     alamat = '$alamat',
                     no_hp = '$no_hp',
                     updated_at = '$updatedAt'
                      WHERE id_pasien = $id_pasien";
    mysqli_query($db, $query);

    return mysqli_affected_rows($db);
}

function deletePasien($id_pasien)
{
    global $db;
    mysqli_query($db, "DELETE FROM pasien WHERE id_pasien = $id_pasien");
    return mysqli_affected_rows($db);
}

function addGejala($data)
{
    global $db;

    $kode_gejala = mysqli_real_escape_string($db, $data["kode_gejala"]);
    $nama_gejala = htmlspecialchars($data["nama_gejala"]);

    // Periksa apakah Kode Gejala sudah ada di database
    $query = "SELECT * FROM gejala WHERE kode_gejala = '$kode_gejala'";
    $result = mysqli_query($db, $query);
    if (mysqli_fetch_assoc($result)) {
        // Jika Kode Gejala sudah ada, return -1
        return -1;
    }

    $query = "INSERT INTO gejala 
        (kode_gejala, nama_gejala)
        VALUES 
        ('$kode_gejala', '$nama_gejala')";

    mysqli_query($db, $query);
    return mysqli_affected_rows($db);
}

function editGejala($data)
{
    global $db;
    $id_gejala = $data["id_gejala"];
    $kode_gejala = mysqli_real_escape_string($db, $data["kode_gejala"]);
    $nama_gejala = htmlspecialchars($data["nama_gejala"]);

    // Periksa apakah nik pasien sudah ada, tetapi abaikan baris yang sedang diedit
    $query = "SELECT * FROM gejala WHERE kode_gejala = '$kode_gejala' AND id_gejala != $id_gejala";
    $result = mysqli_query($db, $query);

    if (mysqli_fetch_assoc($result)) {
        return -1;
    }

    $updatedAt = date('Y-m-d H:i:s');
    // Jika nis siswa tidak ada yang duplikat, lakukan update
    $query = "UPDATE gejala SET kode_gejala = '$kode_gejala', 
                     nama_gejala = '$nama_gejala',
                     updated_at = '$updatedAt'
                      WHERE id_gejala = $id_gejala";
    mysqli_query($db, $query);

    return mysqli_affected_rows($db);
}

function deleteGejala($id_gejala)
{
    global $db;
    mysqli_query($db, "DELETE FROM gejala WHERE id_gejala = $id_gejala");
    return mysqli_affected_rows($db);
}

function gejalaPasien($data)
{
    global $db;

    $id_pasien = htmlspecialchars($data["id_pasien"]);
    $id_gejala = $data["id_gejala"];
    $nilai_bobot = $data["nilai_bobot"];

    // Validasi jumlah input
    if (count($id_gejala) !== count($nilai_bobot) || empty($id_gejala)) {
        return 0; // Gagal input
    }

    // Hapus data lama (jika edit)
    mysqli_query($db, "DELETE FROM gejala_pasien WHERE id_pasien = '$id_pasien'");

    // Simpan ulang semua input
    $success = 0;
    for ($i = 0; $i < count($id_gejala); $i++) {
        $idG = htmlspecialchars($id_gejala[$i]);
        $nilai = htmlspecialchars($nilai_bobot[$i]);

        $query = "INSERT INTO gejala_pasien (id_pasien, id_gejala, nilai_bobot, created_at, updated_at) 
                  VALUES ('$id_pasien', '$idG', '$nilai', NOW(), NOW())";

        $insert = mysqli_query($db, $query);
        if ($insert) {
            $success++;
        }
    }

    return $success > 0 ? $success : 0;
}


function addPenyakit($data)
{
    global $db;

    $kode_penyakit = mysqli_real_escape_string($db, $data["kode_penyakit"]);
    $nama_penyakit = htmlspecialchars($data["nama_penyakit"]);
    $deskripsi = htmlspecialchars($data["deskripsi"]);
    $solusi = htmlspecialchars($data["solusi"]);

    // Periksa apakah Kode Gejala sudah ada di database
    $query = "SELECT * FROM penyakit WHERE kode_penyakit = '$kode_penyakit'";
    $result = mysqli_query($db, $query);
    if (mysqli_fetch_assoc($result)) {
        // Jika Kode Gejala sudah ada, return -1
        return -1;
    }

    $query = "INSERT INTO penyakit 
        (kode_penyakit, nama_penyakit, deskripsi, solusi)
        VALUES 
        ('$kode_penyakit', '$nama_penyakit', '$deskripsi', '$solusi')";

    mysqli_query($db, $query);
    return mysqli_affected_rows($db);
}

function editPenyakit($data)
{
    global $db;
    $id_penyakit = $data["id_penyakit"];
    $kode_penyakit = mysqli_real_escape_string($db, $data["kode_penyakit"]);
    $nama_penyakit = htmlspecialchars($data["nama_penyakit"]);
    $deskripsi = htmlspecialchars($data["deskripsi"]);
    $solusi = htmlspecialchars($data["solusi"]);

    // Periksa apakah kode sudah ada, tetapi abaikan baris yang sedang diedit
    $query = "SELECT * FROM penyakit WHERE kode_penyakit = '$kode_penyakit' AND id_penyakit != $id_penyakit";
    $result = mysqli_query($db, $query);

    if (mysqli_fetch_assoc($result)) {
        return -1;
    }

    $updatedAt = date('Y-m-d H:i:s');
    // Jika kode tidak ada yang duplikat, lakukan update
    $query = "UPDATE penyakit SET kode_penyakit = '$kode_penyakit', 
                     nama_penyakit = '$nama_penyakit',
                     deskripsi = '$deskripsi',
                     solusi = '$solusi',
                     updated_at = '$updatedAt'
                      WHERE id_penyakit = $id_penyakit";
    mysqli_query($db, $query);

    return mysqli_affected_rows($db);
}

function deletePenyakit($id_penyakit)
{
    global $db;
    mysqli_query($db, "DELETE FROM penyakit WHERE id_penyakit = $id_penyakit");
    return mysqli_affected_rows($db);
}

function addRule($data)
{
    global $db;

    $kode_rule = mysqli_real_escape_string($db, $data["kode_rule"]);
    $id_penyakit = mysqli_real_escape_string($db, $data["id_penyakit"]);
    $id_gejala = mysqli_real_escape_string($db, $data["id_gejala"]);
    $nilai_mb = mysqli_real_escape_string($db, $data["nilai_mb"]);
    $nilai_md = mysqli_real_escape_string($db, $data["nilai_md"]);


    // Periksa apakah Kode Rule sudah ada di database
    $query = "SELECT * FROM rule WHERE kode_rule = '$kode_rule'";
    $result = mysqli_query($db, $query);
    if (mysqli_fetch_assoc($result)) {
        // Jika Kode Rule sudah ada, return -1
        return -1;
    }

    $query = "INSERT INTO rule 
        (kode_rule, id_penyakit, id_gejala, nilai_mb, nilai_md)
        VALUES 
        ('$kode_rule', '$id_penyakit', '$id_gejala', '$nilai_mb', '$nilai_md')";

    mysqli_query($db, $query);
    return mysqli_affected_rows($db);
}

function editRule($data)
{
    global $db;
    $id_rule = $data["id_rule"];
    $kode_rule = mysqli_real_escape_string($db, $data["kode_rule"]);
    $id_penyakit = mysqli_real_escape_string($db, $data["id_penyakit"]);
    $id_gejala = mysqli_real_escape_string($db, $data["id_gejala"]);
    $nilai_mb = mysqli_real_escape_string($db, $data["nilai_mb"]);
    $nilai_md = mysqli_real_escape_string($db, $data["nilai_md"]);

    // Periksa apakah kode sudah ada, tetapi abaikan baris yang sedang diedit
    $query = "SELECT * FROM rule WHERE kode_rule = '$kode_rule' AND id_rule != $id_rule";
    $result = mysqli_query($db, $query);

    if (mysqli_fetch_assoc($result)) {
        return -1;
    }

    $updatedAt = date('Y-m-d H:i:s');
    // Jika kode tidak ada yang duplikat, lakukan update
    $query = "UPDATE rule SET kode_rule = '$kode_rule', 
                     id_penyakit = '$id_penyakit',
                     id_gejala = '$id_gejala',
                     nilai_mb = '$nilai_mb',
                     nilai_md = '$nilai_md',
                     updated_at = '$updatedAt'
                      WHERE id_rule = $id_rule";
    mysqli_query($db, $query);

    return mysqli_affected_rows($db);
}

function deleteRule($id_rule)
{
    global $db;
    mysqli_query($db, "DELETE FROM rule WHERE id_rule = $id_rule");
    return mysqli_affected_rows($db);
}

// pakai ini klo error
// function hitungDiagnosaStroke($id_pasien, $id_user)
// {
//     global $db;

//     $id_pasien = (int) $id_pasien;
//     $id_user = (int) $id_user;

//     $query_gejala = mysqli_query($db, "
//         SELECT gp.id_gejala, gp.nilai_bobot, r.nilai_mb, r.nilai_md, r.id_penyakit
//         FROM gejala_pasien gp
//         JOIN rule r ON gp.id_gejala = r.id_gejala
//         WHERE gp.id_pasien = $id_pasien
//     ");

//     $cf_values = [];
//     $id_penyakit = '';

//     while ($row = mysqli_fetch_assoc($query_gejala)) {
//         $mb = $row['nilai_mb'];
//         $md = $row['nilai_md'];
//         $cf_user = $row['nilai_bobot'];
//         $cf = ($mb - $md) * $cf_user;
//         $cf_values[] = $cf;
//         $id_penyakit = $row['id_penyakit'];
//     }

//     if (count($cf_values) === 0) {
//         return ['status' => false, 'message' => 'Data gejala tidak ditemukan'];
//     }

//     $cf_combine = $cf_values[0];
//     for ($i = 1; $i < count($cf_values); $i++) {
//         $cf_combine = $cf_combine + $cf_values[$i] * (1 - $cf_combine);
//     }

//     $pasien = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM pasien WHERE id_pasien = $id_pasien"));
//     $penyakit = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM penyakit WHERE id_penyakit = $id_penyakit"));

//     if (!$pasien || !$penyakit) {
//         return ['status' => false, 'message' => 'Data pasien atau penyakit tidak ditemukan'];
//     }

//     $nama_pasien = $pasien['nama_pasien'];
//     $kode_penyakit = $penyakit['kode_penyakit'];
//     $nama_penyakit = $penyakit['nama_penyakit'];
//     $keterangan = $penyakit['deskripsi'];

//     mysqli_query($db, "
//         INSERT INTO hasil_diagnosa (user_id, id_pasien, nama_pasien, kode_penyakit, nilai_cf, diagnosa, keterangan)
//         VALUES ($id_user, $id_pasien, '$nama_pasien', '$kode_penyakit', $cf_combine, '$nama_penyakit', '$keterangan')
//     ");

//     return [
//         'status' => true,
//         'cf' => $cf_combine,
//         'diagnosa' => $nama_penyakit,
//         'persentase' => round($cf_combine * 100, 2)
//     ];
// }

function hitungDiagnosaStroke($id_pasien)
{
    global $db;

    $id_pasien = (int) $id_pasien;

    // Ambil data pasien beserta user_id pemiliknya
    $pasien = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM pasien WHERE id_pasien = $id_pasien"));
    if (!$pasien) {
        return ['status' => false, 'message' => 'Data pasien tidak ditemukan'];
    }

    $id_user = $pasien['user_id']; // ambil user_id dari pemilik data pasien
    $nama_pasien = $pasien['nama_pasien'];

    // Ambil data gejala dan rule
    $query_gejala = mysqli_query($db, "
        SELECT gp.id_gejala, gp.nilai_bobot, r.nilai_mb, r.nilai_md, r.id_penyakit
        FROM gejala_pasien gp
        JOIN rule r ON gp.id_gejala = r.id_gejala
        WHERE gp.id_pasien = $id_pasien
    ");

    $cf_values = [];
    $id_penyakit = '';

    while ($row = mysqli_fetch_assoc($query_gejala)) {
        $mb = $row['nilai_mb'];
        $md = $row['nilai_md'];
        $cf_user = $row['nilai_bobot'];
        $cf = ($mb - $md) * $cf_user;
        $cf_values[] = $cf;
        $id_penyakit = $row['id_penyakit'];
    }

    if (count($cf_values) === 0) {
        return ['status' => false, 'message' => 'Data gejala tidak ditemukan'];
    }

    $cf_combine = $cf_values[0];
    for ($i = 1; $i < count($cf_values); $i++) {
        $cf_combine = $cf_combine + $cf_values[$i] * (1 - $cf_combine);
    }

    $penyakit = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM penyakit WHERE id_penyakit = $id_penyakit"));

    if (!$penyakit) {
        return ['status' => false, 'message' => 'Data penyakit tidak ditemukan'];
    }

    $kode_penyakit = $penyakit['kode_penyakit'];
    $nama_penyakit = $penyakit['nama_penyakit'];
    $keterangan = $penyakit['deskripsi'];

    mysqli_query($db, "
        INSERT INTO hasil_diagnosa (user_id, id_pasien, nama_pasien, kode_penyakit, nilai_cf, diagnosa, keterangan)
        VALUES ($id_user, $id_pasien, '$nama_pasien', '$kode_penyakit', $cf_combine, '$nama_penyakit', '$keterangan')
    ");

    return [
        'status' => true,
        'cf' => $cf_combine,
        'diagnosa' => $nama_penyakit,
        'persentase' => round($cf_combine * 100, 2)
    ];
}


function perluDiagnosaUlang($id_pasien, $db)
{
    $cek_diagnosa = mysqli_fetch_assoc(mysqli_query($db, "
        SELECT created_at FROM hasil_diagnosa
        WHERE id_pasien = $id_pasien
        ORDER BY created_at DESC LIMIT 1
    "));

    $cek_update_gejala = mysqli_fetch_assoc(mysqli_query($db, "
        SELECT MAX(updated_at) AS last_update FROM gejala_pasien
        WHERE id_pasien = $id_pasien
    "));

    if (!$cek_diagnosa) return true;

    return $cek_update_gejala && $cek_diagnosa['created_at'] < $cek_update_gejala['last_update'];
}

function deleteHasil($id_hasil)
{
    global $db;
    mysqli_query($db, "DELETE FROM hasil_diagnosa WHERE id_hasil = $id_hasil");
    return mysqli_affected_rows($db);
}

function upload()
{

    $namaFile = $_FILES['avatar']['name'];
    $ukuranFiles = $_FILES['avatar']['size'];
    $error = $_FILES['avatar']['error'];
    $tmpName = $_FILES['avatar']['tmp_name'];

    // Cek apakah yang diupload adalah gambar
    $ekstensiAvatarValid = ['', 'jpg', 'jpeg', 'png'];
    $ekstensiAvatar = explode('.', $namaFile);
    $ekstensiAvatar = strtolower(end($ekstensiAvatar));
    if (!in_array($ekstensiAvatar, $ekstensiAvatarValid)) {
        // Jika Avatar Bukan Gambar
        return -1;
    }

    if ($ukuranFiles > 10000000) {
        // Cek jika ukuran terlalu besar
        return -2;
    }

    // Gambar Siap Upload
    // generate nama gambar baru

    $namaFileBaru = uniqid();
    $namaFileBaru .= '.';
    $namaFileBaru .= $ekstensiAvatar;

    move_uploaded_file($tmpName, '../assets/static/images/' . $namaFileBaru);

    return $namaFileBaru;
}

function is_user_active($id)
{
    global $db;

    // Cek status pengguna berdasarkan ID
    $result = mysqli_query($db, "SELECT status FROM users WHERE id = '$id'");
    $row = mysqli_fetch_assoc($result);

    // Jika data ditemukan
    if ($row) {
        // Cek apakah statusnya 'Aktif'
        if ($row['status'] === 'Aktif') {
            return true;
        }
    }

    // Jika tidak aktif atau tidak ditemukan
    return false;
}

function logout()
{
    // Hapus semua data sesi
    $_SESSION = array();

    // Hapus cookie sesi jika ada
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Hancurkan sesi
    session_destroy();

    // Alihkan ke halaman login
    header("Location: ../login"); // Sesuaikan dengan halaman login Anda
    exit;
}
