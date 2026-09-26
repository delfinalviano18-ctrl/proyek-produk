<?php
session_start();

if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;


}

$tipe = '';
$pesan = '';

require_once __DIR__ . "/conn.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = strtolower(str_replace(' ', '', $_POST['username']));
    $sandi = $_POST['password'];
    $email = trim($_POST['email']);

    if(!empty($username)  && !empty($sandi) && !empty($email)) {
        $cek = mysqli_prepare($conn, "SELECT nama_user, email, sandi FROM user where nama_user = ?");
        mysqli_stmt_bind_param($cek, 's', $username);
        mysqli_stmt_execute($cek);

        $data = mysqli_stmt_get_result($cek);

        if($row = mysqli_fetch_assoc($data)) {
            $nama = $row['nama_user'];
            $gmail = $row['email'];
            $password = $row['sandi'];

            if($username == $nama && $email == $gmail && (password_verify($sandi, $password))) {
                mysqli_stmt_close($cek);
                
                $_SESSION['login'] = true;
                header("Location: index.php");
                exit;



            } else {
                $tipe = 'danger';
                $pesan = 'Email, username, atau sandi salah';

            }
        }

    }
}


?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <title>Document</title>
</head>
<body class="bg-secondary m-3">

   <div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6 bg-light p-2 rounded-2 shadow">
            <?php if($tipe != '' && $pesan != '') : ?>
                 <div class="alert alert-<?= $tipe; ?> alert-dismissible fade show" role="alert">
                    <?= $pesan; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>

            <?php endif; ?>
            <form action="" method="POST">
                <h2 class="fs-2 fw-bold text-center text-primary">Login</h2>
                <hr>
                <div class="m-3">
                    <label class="form-label">Masukkan username</label>
                    <input name="username" type="text" class="form-control border-secondary">
                </div>

                <div class="m-3">
                    <label class="form-label">Masukkan email</label>
                    <input name="email" type="email" class="form-control border-secondary">
                </div>

                <div class="m-3">
                    <label class="form-label">Masukkan password</label>
                    <input name="password" type="password" class="form-control border-secondary">
                </div>

                <div class="m-3">
                    <button name="klik" type="submit" class="btn btn-primary w-100 fw-semibold">Masuk Sekarang</button>
                </div>

                <div class="m-2 d-grid gap-2 p-1">
                    <button type="reset" class="btn btn-secondary w-100 ">Reset</button>
                    <p>Belum punya akun?<a href="register.php"  class="fw-semibold">Register di sini</a></p>
                </div>
            </form>

        </div>
        <div class="col-md-3"></div>
    </div>
   </div>



   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>