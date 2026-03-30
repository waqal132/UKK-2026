<?php
session_start();
include '../db.php';

// Cek login admin
if ($_SESSION['login'] != true || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// Ambil ID aspirasi dari URL
$id = (int)$_GET['id'];

// Proses simpan perubahan status & feedback
if (isset($_POST['submit'])) {
    $status   = mysqli_real_escape_string($conn, $_POST['status']);
    $feedback = mysqli_real_escape_string($conn, $_POST['feedback']);

    if ($status == 'Selesai') {
        // Kalau selesai, catat juga tanggal selesainya
        mysqli_query($conn, "UPDATE aspirasi SET status='$status', feedback='$feedback', tgl_selesai=NOW() WHERE id_aspirasi=$id");
    } else {
        mysqli_query($conn, "UPDATE aspirasi SET status='$status', feedback='$feedback' WHERE id_aspirasi=$id");
    }

    echo '<script>alert("Berhasil diperbarui!")</script>';
}

// Ambil data aspirasi berdasarkan ID
$q = mysqli_query($conn, "
    SELECT a.*, i.nis, i.lokasi, i.ket, k.ket_kategori, s.kelas
    FROM aspirasi a
    JOIN input_aspirasi i ON a.id_pelaporan = i.id_pelaporan
    JOIN kategori k ON i.id_kategori = k.id_kategori
    JOIN siswa s ON i.nis = s.nis
    WHERE a.id_aspirasi = $id
");

// Kalau data tidak ditemukan, kembali ke daftar
if (mysqli_num_rows($q) == 0) {
    echo '<script>alert("Data tidak ditemukan!"); window.location="data-aspirasi.php"</script>';
    exit;
}

$row = mysqli_fetch_array($q);

// Tentukan badge warna status
$badge = 'badge-menunggu';
if ($row['status'] == 'Proses')  $badge = 'badge-proses';
if ($row['status'] == 'Selesai') $badge = 'badge-selesai';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Aspirasi | Admin</title>
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
        <h3>Detail Aspirasi</h3>
        <p><a href="data-aspirasi.php">&larr; Kembali</a></p>

        <!-- Informasi aspirasi siswa -->
        <div class="box">
            <table class="table">
                <tr><td width="200"><strong>ID Aspirasi</strong></td>   <td>#<?php echo $row['id_aspirasi']; ?></td></tr>
                <tr><td><strong>Tanggal Diajukan</strong></td>          <td><?php echo date('d-m-Y H:i', strtotime($row['tgl_diajukan'])); ?></td></tr>
                <tr><td><strong>NIS Siswa</strong></td>                  <td><?php echo $row['nis']; ?></td></tr>
                <tr><td><strong>Kelas</strong></td>                     <td><?php echo $row['kelas']; ?></td></tr>
                <tr><td><strong>Kategori</strong></td>                  <td><?php echo $row['ket_kategori']; ?></td></tr>
                <tr><td><strong>Lokasi</strong></td>                    <td><?php echo $row['lokasi']; ?></td></tr>
                <tr><td><strong>Keterangan</strong></td>                <td><?php echo $row['ket']; ?></td></tr>
                <tr><td><strong>Status</strong></td>                    <td><span class="badge <?php echo $badge; ?>"><?php echo $row['status']; ?></span></td></tr>
                <tr><td><strong>Tanggal Selesai</strong></td>           <td><?php echo $row['tgl_selesai'] ? date('d-m-Y H:i', strtotime($row['tgl_selesai'])) : '-'; ?></td></tr>
            </table>
        </div>

        <!-- Form update status dan feedback -->
        <h3>Update Status & Umpan Balik</h3>
        <div class="box">
            <form method="POST">
                <div class="form-group">
                    <label>Status Penyelesaian</label>
                    <select name="status">
                        <option value="Menunggu" <?php if($row['status']=='Menunggu') echo 'selected'; ?>>Menunggu</option>
                        <option value="Proses"   <?php if($row['status']=='Proses')   echo 'selected'; ?>>Proses</option>
                        <option value="Selesai"  <?php if($row['status']=='Selesai')  echo 'selected'; ?>>Selesai</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Umpan Balik / Feedback</label>
                    <textarea name="feedback" placeholder="Tulis balasan untuk siswa..."><?php echo $row['feedback']; ?></textarea>
                </div>
                <input type="submit" name="submit" class="btn" value="Simpan Perubahan">
            </form>
        </div>

    </div>
</div>

<footer><div class="container"><small>Copyright &copy; 2026 - Aspirasi Sekolah.</small></div></footer>

</body>
</html>
