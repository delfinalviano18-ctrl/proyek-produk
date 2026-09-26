<?php
require_once __DIR__ . "/conn.php";

if($_SERVER['REQUEST_METHOD'] === "POST") {
    $id = (int) $_POST['id'];

    if(!empty($id)) {
        $hapus = mysqli_prepare($conn, "DELETE FROM produk WHERE id = ?");
        mysqli_stmt_bind_param($hapus, "i", $id);
        mysqli_stmt_execute($hapus);
        mysqli_stmt_close($hapus);

        header("Location: index.php");
        mysqli_close($conn);
        exit();
        

    } 
}


?>