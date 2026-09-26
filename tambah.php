<?php
session_start();
require_once __DIR__ . "/conn.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = $_POST['nama'];
    $harga = (int) $_POST['harga'];
    $stok  = (int) $_POST['stok'];
    
    $nama_file = 'default.jpg';

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $filename  = $_FILES['foto']['name'];
        $tmp_name  = $_FILES['foto']['tmp_name'];
        $ekstensi  = pathinfo($filename, PATHINFO_EXTENSION);
        $ext_valid = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array(strtolower($ekstensi), $ext_valid)) {
            $nama_file = time() . '_' . uniqid() . '.' . $ekstensi;
            move_uploaded_file($tmp_name, 'uploads/' . $nama_file);
        }
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO produk (nama, harga, stok, gambar) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "siis", $nama, $harga, $stok, $nama_file);
    mysqli_stmt_execute($stmt);

    $_SESSION['tipe'] = 'success';
    $_SESSION['pesan'] = 'Tambah produk berhasil';

    header("Location: index.php");
    exit;
}
?>