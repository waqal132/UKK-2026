<?php
session_start();
include '../db.php';

// Cek login siswa
if ($_SESSION['login'] != true || $_SESSION['role'] != 'siswa') {
    header('Location: ../login.php');
    exit;
}

$nis = $_SESSION['nis'];

// Ambil semua aspirasi milik siswa ini
$data = mysqli_query($conn, "
    SELECT a.id_aspirasi, a.status, a.tgl_diajukan, a.tgl_selesai, a.feedback,
           i.lokasi, i.ket,
           k.ket_kategori
    FROM aspirasi a
    JOIN input_aspirasi i ON a.id_pelaporan = i.id_pelaporan
    JOIN kategori k ON i.id_kategori = k.id_kategori
    WHERE i.nis = $nis
    ORDER BY a.id_aspirasi DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Histori Aspirasi | Siswa</title>
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
        <h3>Histori Aspirasi Saya</h3>
        <p>NIS: <strong><?php echo $nis; ?></strong></p>
        <br>

        <?php
        if (mysqli_num_rows($data) > 0) {
            while ($row = mysqli_fetch_array($data)) {
                $badge = 'badge-menunggu';
                if ($row['status'] == 'Proses')  $badge = 'badge-proses';
                if ($row['status'] == 'Selesai') $badge = 'badge-selesai';

                // Hitung persentase progress bar
                $persen = 20;
                $warna  = '#ffc107';
                if ($row['status'] == 'Proses')  { $persen = 60;  $warna = '#007bff'; }
                if ($row['status'] == 'Selesai') { $persen = 100; $warna = '#28a745'; }
        ?>
        <div class="card-aspirasi">
            <h4>
                #<?php echo $row['id_aspirasi']; ?> - <?php echo $row['ket_kategori']; ?>
                <span class="badge <?php echo $badge; ?>" style="margin-left:8px;"><?php echo $row['status']; ?></span>
            </h4>
            <p><strong>Lokasi:</strong> <?php echo $row['lokasi']; ?></p>
            <p><strong>Keterangan:</strong> <?php echo $row['ket']; ?></p>
            <p style="color:#999; font-size:12px;">
                Diajukan: <?php echo date('d-m-Y H:i', strtotime($row['tgl_diajukan'])); ?>
                <?php if ($row['tgl_selesai']) echo ' | Selesai: ' . date('d-m-Y H:i', strtotime($row['tgl_selesai'])); ?>
            </p>

            <!-- Progress bar sederhana -->
            <div style="margin-top:8px;">
                <small style="color:#666;">Progres:</small>
                <div style="background:#eee; border-radius:10px; height:8px; margin-top:4px;">
                    <div style="width:<?php echo $persen; ?>%; background:<?php echo $warna; ?>; height:8px; border-radius:10px;"></div>
                </div>
                <small style="color:#999;"><?php echo $persen; ?>% - <?php echo $row['status']; ?></small>
            </div>

            <!-- Tampilkan feedback admin kalau ada -->
            <?php if ($row['feedback']) { ?>
            <div class="feedback">
                <strong>Balasan Admin:</strong> <?php echo $row['feedback']; ?>
            </div>
            <?php } ?>
        </div>
        <?php } } else { ?>
        <div class="box">
            <p>Belum ada aspirasi. <a href="form-aspirasi.php">Buat sekarang</a></p>
        </div>
        <?php } ?>
    </div>
</div>

<footer><div class="container"><small>Copyright &copy; 2026 - Aspirasi Sekolah.</small></div></footer>

</body>
</html>
