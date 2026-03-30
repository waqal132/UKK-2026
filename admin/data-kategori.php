<?php
session_start();
include '../db.php';

// Cek login admin
if ($_SESSION['login'] != true || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// Tambah kategori baru
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    mysqli_query($conn, "INSERT INTO kategori (ket_kategori) VALUES ('$nama')");
    echo '<script>alert("Kategori berhasil ditambahkan!")</script>';
}

// Edit kategori
if (isset($_POST['edit'])) {
    $id   = (int)$_POST['id_kategori'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    mysqli_query($conn, "UPDATE kategori SET ket_kategori='$nama' WHERE id_kategori=$id");
    echo '<script>alert("Kategori berhasil diubah!")</script>';
}

// Hapus kategori
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM kategori WHERE id_kategori=$id");
    header('Location: data-kategori.php');
    exit;
}

// Ambil semua kategori
$daftar = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id_kategori ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kategori | Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<header>
    <div class="container">
        <h1><a href="dashboard.php">Aspirasi Sekolah</a></h1>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="data-aspirasi.php">Data Aspirasi</a></li>
            <li><a href="data-kategori.php">Kategori</a></li>
            <li><a href="data-siswa.php">Data Siswa</a></li>
            <li><a href="../keluar.php">Keluar</a></li>
        </ul>
    </div>
</header>

<div class="section">
    <div class="container">
        <h3>Data Kategori</h3>

        <!-- Form tambah kategori -->
        <div class="box">
            <h4>Tambah Kategori Baru</h4>
            <form method="POST" style="display:flex; gap:10px; align-items:center;">
                <input type="text" name="nama_kategori" class="input-control" placeholder="Nama Kategori" style="width:250px; margin:0;" required>
                <input type="submit" name="tambah" class="btn" value="Tambah">
            </form>
        </div>

        <!-- Tabel daftar kategori -->
        <div class="box">
            <table class="table" border="1" cellspacing="0">
                <thead>
                    <tr>
                        <th width="40px">No</th>
                        <th>Nama Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if (mysqli_num_rows($daftar) > 0) {
                        while ($row = mysqli_fetch_array($daftar)) {
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td>
                            <!-- Form edit langsung di baris tabel -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id_kategori" value="<?php echo $row['id_kategori']; ?>">
                                <input type="text" name="nama_kategori" value="<?php echo $row['ket_kategori']; ?>" style="padding:4px 8px; border:1px solid #ccc; border-radius:3px;">
                                <input type="submit" name="edit" class="btn-sm" value="Simpan">
                            </form>
                        </td>
                        <td>
                            <a href="data-kategori.php?hapus=<?php echo $row['id_kategori']; ?>"
                               class="btn-sm" style="background:#c0392b;"
                               onclick="return confirm('Hapus kategori ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php } } else { ?>
                    <tr><td colspan="3">Belum ada kategori</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer><div class="container"><small>Copyright &copy; 2026 - Aspirasi Sekolah.</small></div></footer>

</body>
</html>
