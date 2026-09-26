<?php
session_start();

if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}


require_once __DIR__ . "/conn.php";

$tipe = '';
$pesan = '';

if (isset($_POST['kirim'])) {
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama = strtolower(str_replace(' ', '', $_POST['username']));
        $email = trim($_POST['email']);
        $sandi = trim($_POST['password']);

        if(!empty($nama) && !empty($email) && !empty($sandi)) {
            $hash = password_hash($sandi, PASSWORD_DEFAULT);
            
            $stmt = mysqli_prepare($conn,"INSERT INTO user (nama_user, email, sandi) VALUES (?, ?, ?)");
            
            mysqli_stmt_bind_param($stmt, "sss", $nama, $email, $hash);

            $cek = mysqli_prepare($conn, "SELECT nama_user, email FROM user WHERE nama_user = ?");
            mysqli_stmt_bind_param($cek, 's', $nama);
            mysqli_stmt_execute($cek);

            $cek_data = mysqli_stmt_get_result($cek);

            if(mysqli_num_rows($cek_data) === 0) {
                mysqli_stmt_execute($stmt);

                mysqli_stmt_close($stmt);

                $_SESSION['login'] = true;
                header("Location: index.php");
                exit;

            } else {
                $tipe = 'danger';
                $pesan = 'Nama & email sudah terdaftar, gunakan yang lain';

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
<body class="bg-secondary m-4">

    <div class="container">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6 bg-light rounded-2 shadow border-dark p-2">
                <?php if($pesan != '' && $tipe != '') : ?>
                     <div class="alert alert-<?= $tipe; ?> alert-dismissible fade show" role="alert">
                        <?= $pesan; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                <?php endif; ?>

                <form action="" method="post">
                    <div class="">
                    <h2 class="text-primary fw-bold text-center">Register</h2>
                    <hr>
                    <div class="m-3">
                         <label class="form-label fw-semi">Masukkan username</label>
                         <input name="username" type="text" class="form-control border-secondary" required>
                    </div>

                    <div class="m-3">
                         <label class="form-label">Masukkan alamat email anda</label>
                         <input name="email" type="email" class="form-control border-secondary" required>
                    </div>

                    <div class="m-3">
                         <label class="form-label">Masukkan Password</label>
                         <input name="password" type="password" class="form-control border-secondary" required>
                    </div>

                    <div class="m-3 d-grid gap-1">
                        <button name="kirim" type="submit" class="btn btn-primary w-100 border-dark">Masuk</button>
                        <button type="reset" class="btn btn-secondary border-dark">Ulang</button>
                    </div>

                    <div class="m-3">
                        <p class="form label">Sudah punya akun? <a href="login.php">Login di sini</a></p>
                    </div>
                </div>
                </form>
                
            </div>
            <div class="col-md-3"></div>
        </div>
    </div>





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>