<?php
session_start();
include '../db.php';

// Cek login siswa
if ($_SESSION['login'] != true || $_SESSION['role'] != 'siswa') {
    header('Location: ../login.php');
    exit;
}

$nis = $_SESSION['nis'];
$nama = $_SESSION['nama'];
$kelas = $_SESSION['kelas'];

// Proses kirim aspirasi
if (isset($_POST['submit'])) {
    $id_kategori = (int)$_POST['id_kategori'];
    $lokasi      = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $ket         = mysqli_real_escape_string($conn, $_POST['ket']);

    // Simpan ke tabel input_aspirasi dulu
    mysqli_query($conn, "INSERT INTO input_aspirasi (nis, id_kategori, lokasi, ket) VALUES ($nis, $id_kategori, '$lokasi', '$ket')");

    // Ambil ID yang baru disimpan
    $id_pelaporan = mysqli_insert_id($conn);

    // Simpan ke tabel aspirasi dengan status awal 'Menunggu'
    mysqli_query($conn, "INSERT INTO aspirasi (status, id_pelaporan, feedback, tgl_diajukan) VALUES ('Menunggu', $id_pelaporan, '', NOW())");

    echo '<script>alert("Aspirasi berhasil dikirim!"); window.location="histori.php"</script>';
}

// Ambil daftar kategori untuk ditampilkan di dropdown
$kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id_kategori");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Aspirasi | Siswa</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<header>
    <div class="container">
        <h1><a href="index.php">Aspirasi Sekolah</a></h1>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="form-aspirasi.php">Buat Pengaduan</a></li>
            <li><a href="histori.php">Histori</a></li>
            <li><a href="../keluar.php">Keluar</a></li>
        </ul>
    </div>
</header>

<div class="section">
    <div class="container">
        <h3>Form Pengaduan Sarana Sekolah</h3>
        <p><a href="index.php">Kembali</a></p>

        <div class="box">
            <p>NIS: <strong><?php echo $nis; ?></strong></p>
            <p>Nama: <strong><?php echo $nama; ?></strong></p>
            <p>Kelas: <strong><?php echo $kelas; ?></strong></p>
            <br>

            <form method="POST">
                <div class="form-group">
                    <label>Kategori Pengaduan</label>
                    <select name="id_kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php while ($k = mysqli_fetch_array($kategori)) { ?>
                        <option value="<?php echo $k['id_kategori']; ?>"><?php echo $k['ket_kategori']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Lokasi / Tempat</label>
                    <input type="text" name="lokasi" placeholder="Contoh: Ruang XII RPL 1, Kantin, Toilet lantai 2..." required>
                </div>
                <div class="form-group">
                    <label>Keterangan / Deskripsi</label>
                    <textarea name="ket" placeholder="Jelaskan masalah kamu secara singkat dan jelas..." required></textarea>
                </div>
                <input type="submit" name="submit" class="btn" value="Kirim Pengaduan">
            </form>
        </div>
    </div>
</div>

<footer><div class="container"><small>Copyright &copy; 2026 - Aspirasi Sekolah.</small></div></footer>

</body>
</html>
