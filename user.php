<?php
require_once __DIR__ . "/conn.php";

$query = "SELECT id, nama_user, email from user";

$sql = mysqli_query($conn, $query); 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <title>Document</title>
</head>
<body class="mt-4">

<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <table class="table table-bordered border-dark">
                <thead class="table-dark">
                    <tr>
                        <th colspan="2">Table user</th>
                        <th colspan="1"><a href="index.php"><button class="btn btn-primary w-100 btn-sm">Tabel Produk</button></a></th>
                        
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <th>ID</th>
                        <th>Nama user</th>
                        <th>Email user</th>
                    </tr>

                    <?php 
                    $id = 1;
                    while($row = mysqli_fetch_assoc($sql)) : ?>
                        <tr>
                            <td><?= $id++ ;?></td>
                            <td><?= $row['nama_user']; ?></td>
                            <td><?= $row['email']; ?></td>
                            
                        </tr>

                    <?php endwhile; ?>

                    
                </tbody>

            </table>
        </div>
        <div class="col-md-3"></div>
    </div>
</div>
    
</body>
</html>