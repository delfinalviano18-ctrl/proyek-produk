<?php
session_start();

if (!isset($_SESSION['login'])) {
    header('Location: register.php');
    exit;
}

require_once __DIR__ . "/conn.php";

// Proses Edit Produk
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $nama      = $_POST['Nama'] ?? '';
    $harga     = (int) ($_POST['harga'] ?? 0);
    $stok      = (int) ($_POST['stok'] ?? 0);
    $id        = (int) ($_POST['id'] ?? 0);
    $foto_lama = $_POST['foto_lama'] ?? 'default.jpg';

    $nama_file = $foto_lama;

    // Cek apakah ada file foto yang diunggah
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $filename  = $_FILES['foto']['name'];
        $tmp_name  = $_FILES['foto']['tmp_name'];
        $ekstensi  = pathinfo($filename, PATHINFO_EXTENSION);
        $ext_valid = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array(strtolower($ekstensi), $ext_valid)) {
            $nama_file = time() . '_' . uniqid() . '.' . strtolower($ekstensi);
            $tujuan    = 'uploads/' . $nama_file;

            if (move_uploaded_file($tmp_name, $tujuan)) {
                // Hapus foto lama jika bukan foto default
                if (!empty($foto_lama) && $foto_lama !== 'default.jpg' && file_exists('uploads/' . $foto_lama)) {
                    unlink('uploads/' . $foto_lama);
                }
            } else {
                $_SESSION['tipe']  = 'danger';
                $_SESSION['pesan'] = 'Gagal memindahkan file ke folder uploads!';
                header("Location: index.php");
                exit;
            }
        } else {
            $_SESSION['tipe']  = 'danger';
            $_SESSION['pesan'] = 'Format file harus JPG, JPEG, PNG, atau WEBP!';
            header("Location: index.php");
            exit;
        }
    } elseif (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Jika file diunggah tapi ukurannya terlalu besar
        $_SESSION['tipe']  = 'danger';
        $_SESSION['pesan'] = 'Ukuran file terlalu besar! Maksimal 2MB.';
        header("Location: index.php");
        exit;
    }

    if (!empty($nama) && $harga >= 0 && $stok >= 0 && $id > 0) {
        $update = mysqli_prepare($conn, "UPDATE produk SET nama = ?, harga = ?, stok = ?, gambar = ? WHERE id = ?");
        mysqli_stmt_bind_param($update, "siisi", $nama, $harga, $stok, $nama_file, $id);

        if (mysqli_stmt_execute($update)) {
            $_SESSION['tipe']  = 'success';
            $_SESSION['pesan'] = 'Edit produk berhasil';

        } else {
            $_SESSION['tipe']  = 'danger';
            $_SESSION['pesan'] = 'Gagal memperbarui database!';
        }

        header("Location: index.php");
        exit;
    }
}

$tipe  = $_SESSION['tipe'] ?? '';
$pesan = $_SESSION['pesan'] ?? '';

unset($_SESSION['tipe']);
unset($_SESSION['pesan']);

$sql = "SELECT id, nama, stok, harga, gambar FROM produk";
$wee = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <title>Kelola Produk</title>
</head>

<body class="m-3 bg-secondary">

    <div class="container">
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-10 bg-light rounded-2 p-3">

                <?php if ($pesan != '' && $tipe != '') : ?>
                    <div class="alert alert-<?= $tipe; ?> alert-dismissible fade show" role="alert">
                        <?= $pesan; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <a href="user.php">
                    <button class="btn btn-primary mb-3">Table dashboard user</button>
                </a>

                <table class="table table-striped-columns align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th colspan="5" class="fs-4 text-start">Tabel Produk</th>
                            <th>
                                <button class="btn btn-primary btn-sm w-100" type="button" data-bs-toggle="modal" data-bs-target="#modalTambah">+Tambah</button>
                            </th>
                            <th>
                                <a href="logout.php" class="btn btn-danger btn-sm w-100">Logout!</a>
                            </th>
                        </tr>
                        <tr class="fs-6">
                            <th>No</th>
                            <th>Foto</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Edit</th>
                            <th>Hapus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($wee)) :
                            $gambar = !empty($row['gambar']) && file_exists('uploads/' . $row['gambar']) ? $row['gambar'] : 'default.jpg';
                        ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <img src="uploads/<?= $gambar; ?>" alt="Foto Produk" width="100" height="100" class="img-thumbnail object-fit-cover">
                                </td>
                                <td class="text-start"><?= htmlspecialchars($row['nama']); ?></td>
                                <td>Rp<?= number_format($row['harga'], 0, ',', '.'); ?></td>
                                <td><?= $row['stok']; ?></td>
                                <td>
                                    <button class="btn btn-success w-100 btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEdit"
                                        type="button"
                                        data-id="<?= $row['id']; ?>"
                                        data-nama="<?= htmlspecialchars($row['nama']); ?>"
                                        data-harga="<?= $row['harga']; ?>"
                                        data-stok="<?= $row['stok']; ?>"
                                        data-gambar="<?= $gambar; ?>">Edit</button>
                                </td>
                                <td>
                                    <button class="btn btn-warning w-100 btn-sm"
                                        type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalHapus"
                                        data-id="<?= $row['id']; ?>"
                                        data-produk="<?= htmlspecialchars($row['nama']); ?>">Hapus</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

            </div>
            <div class="col-md-1"></div>
        </div>
    </div>

    <!-- MODAL HAPUS -->
    <div class="modal fade" id="modalHapus" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-light">Hapus produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah anda yakin ingin menghapus <strong id="Produk"></strong> dari tabel?
                </div>
                <div class="modal-footer">
                    <form action="hapus.php" method="post">
                        <input type="hidden" name="id" id="id">
                        <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Batal</button>
                        <button type="submit" class="btn btn-warning">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH PRODUK -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-light">Tambah Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="tambah.php" method="POST" enctype="multipart/form-data">
                        <div class="d-grid gap-2">
                            <label class="form-label mb-0">Nama Produk</label>
                            <input type="text" class="form-control" name="nama" required>

                            <label class="form-label mb-0">Harga Produk</label>
                            <input type="number" class="form-control" name="harga" required>

                            <label class="form-label mb-0">Stok Barang</label>
                            <input type="number" class="form-control" name="stok" required>

                            <label class="form-label mb-0">Foto Produk</label>
                            <input type="file" class="form-control" name="foto" accept="image/*">

                            <div class="modal-footer mt-2">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Tambah</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT PRODUK -->
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-light">Edit Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="d-grid gap-2">
                            <input type="hidden" name="id" id="editId">
                            <input type="hidden" name="foto_lama" id="editFotoLama">

                            <label class="form-label mb-0">Nama Produk</label>
                            <input class="form-control" type="text" name="Nama" id="editProduk" required>

                            <label class="form-label mb-0">Harga Produk</label>
                            <input class="form-control" type="number" name="harga" id="editHarga" required>

                            <label class="form-label mb-0">Stok Produk</label>
                            <input class="form-control" type="number" name="stok" id="editStok" required>

                            <label class="form-label mb-0">Ganti Foto (Opsional)</label>
                            <input class="form-control" id="fotoKitaBlur" type="file" name="foto" accept="image/*">
                            <small class="text-muted">*Biarkan kosong jika tidak ingin mengganti foto</small>

                            <div class="modal-footer mt-2">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const modalEdit = document.getElementById('modalEdit');
        modalEdit.addEventListener('show.bs.modal', function(event) {
            const tombol = event.relatedTarget;

            document.getElementById('editId').value = tombol.getAttribute('data-id');
            document.getElementById('editProduk').value = tombol.getAttribute('data-nama');
            document.getElementById('editHarga').value = tombol.getAttribute('data-harga');
            document.getElementById('editStok').value = tombol.getAttribute('data-stok');

            document.getElementById('fotoKitaBlur').value = tombol.getAttribute('data-gambar')
        });

        const modalHapus = document.getElementById('modalHapus');
        modalHapus.addEventListener('show.bs.modal', function(event) {
            const tombol = event.relatedTarget;

            document.getElementById('Produk').textContent = tombol.getAttribute('data-produk');
            document.getElementById('id').value = tombol.getAttribute('data-id');
        });
    </script>
</body>

</html>