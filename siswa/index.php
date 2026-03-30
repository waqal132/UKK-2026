<?php
session_start();
include '../db.php';

// Cek login siswa
if ($_SESSION['login'] != true || $_SESSION['role'] != 'siswa') {
    header('Location: ../login.php');
    exit;
}

$nis = $_SESSION['nis']; // Ambil NIS dari sesi

// Hitung jumlah aspirasi milik siswa ini
$semua   = $conn->query("SELECT COUNT(*) FROM aspirasi")->fetch_row()[0];
$tunggu  = $conn->query("SELECT COUNT(*) FROM aspirasi WHERE status='Menunggu'")->fetch_row()[0];
$proses  = $conn->query("SELECT COUNT(*) FROM aspirasi WHERE status='Proses'")->fetch_row()[0];
$selesai = $conn->query("SELECT COUNT(*) FROM aspirasi WHERE status='Selesai'")->fetch_row()[0];

// Ambil 5 aspirasi terbaru milik siswa ini
$data = mysqli_query($conn, "
    SELECT a.id_aspirasi, a.status, a.tgl_diajukan, a.feedback,
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
    <title>Dashboard Siswa | Aspirasi Sekolah</title>
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
        <h3>Halo, Siswa  <?php echo $nis; ?>!</h3>
        <div class="box">
            <p>Kelas: <strong><?php echo isset($_SESSION['kelas']) ? $_SESSION['kelas'] : 'Belum diisi'; ?></strong></p>
            <p>Nama: <strong><?php echo isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Belum diisi'; ?></strong></p>
            <p style="margin-top:8px;">Gunakan menu <strong>Buat Pengaduan</strong> untuk menyampaikan aspirasi kamu.</p>
        </div>

        <!-- Statistik aspirasi siswa -->
        <div style="margin-top:10px;">
            <div class="col-stat"><p>Total</p><h2><?php echo $semua; ?></h2></div>
            <div class="col-stat"><p>Menunggu</p><h2><?php echo $tunggu; ?></h2></div>
            <div class="col-stat"><p>Proses</p><h2><?php echo $proses; ?></h2></div>
            <div class="col-stat"><p>Selesai</p><h2><?php echo $selesai; ?></h2></div>
        </div>

        <!-- Aspirasi terbaru -->
        <h3 style="margin-top:20px;">Aspirasi Terbaru Saya</h3>
        <div class="box">
            <?php
            if (mysqli_num_rows($data) > 0) {
                while ($row = mysqli_fetch_array($data)) {
                    $badge = 'badge-menunggu';
                    if ($row['status'] == 'Proses')  $badge = 'badge-proses';
                    if ($row['status'] == 'Selesai') $badge = 'badge-selesai';
            ?>
            <div class="card-aspirasi">
                <h4>
                    <?php echo $row['ket_kategori']; ?> - <?php echo $row['lokasi']; ?>
                    <span class="badge <?php echo $badge; ?>" style="margin-left:8px;"><?php echo $row['status']; ?></span>
                </h4>
                <p><?php echo $row['ket']; ?></p>
                <p style="color:#999; font-size:12px;">Diajukan: <?php echo date('d-m-Y H:i', strtotime($row['tgl_diajukan'])); ?></p>
                <?php if ($row['feedback']) { ?>
                <div class="feedback"><strong>Balasan Admin:</strong> <?php echo $row['feedback']; ?></div>
                <?php } ?>
            </div>
            <?php } } else { ?>
            <p>Belum ada aspirasi. <a href="form-aspirasi.php">Buat sekarang</a></p>
            <?php } ?>
            <p style="margin-top:10px;"><a href="histori.php">Lihat semua &rarr;</a></p>
        </div>
    </div>
</div>

<footer><div class="container"><small>Copyright &copy; 2026 - Aspirasi Sekolah.</small></div></footer>

</body>
</html>
