<?php
session_start();
include '../db.php';

// Cek apakah yang login adalah admin, kalau bukan, kembali ke login
if ($_SESSION['login'] != true || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// Hitung jumlah aspirasi berdasarkan status
$semua   = $conn->query("SELECT COUNT(*) FROM aspirasi")->fetch_row()[0];
$tunggu  = $conn->query("SELECT COUNT(*) FROM aspirasi WHERE status='Menunggu'")->fetch_row()[0];
$proses  = $conn->query("SELECT COUNT(*) FROM aspirasi WHERE status='Proses'")->fetch_row()[0];
$selesai = $conn->query("SELECT COUNT(*) FROM aspirasi WHERE status='Selesai'")->fetch_row()[0];

// Ambil 10 aspirasi terbaru (JOIN beberapa tabel)
$data = mysqli_query($conn, "
    SELECT a.id_aspirasi, a.status, a.tgl_diajukan,
           i.nis, i.lokasi,
           k.ket_kategori
    FROM aspirasi a
    JOIN input_aspirasi i ON a.id_pelaporan = i.id_pelaporan
    JOIN kategori k ON i.id_kategori = k.id_kategori
    ORDER BY a.id_aspirasi DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin | Aspirasi Sekolah</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<!-- Navigasi atas -->
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
        <h3>Dashboard Admin</h3>

        <div class="box">
            <h4>Selamat datang, <?php echo $_SESSION['username']; ?>!</h4>
        </div>

        <!-- Statistik jumlah aspirasi -->
        <div style="margin-top:10px;">
            <div class="col-stat"><p>Total</p><h2><?php echo $semua; ?></h2></div>
            <div class="col-stat"><p>Menunggu</p><h2><?php echo $tunggu; ?></h2></div>
            <div class="col-stat"><p>Proses</p><h2><?php echo $proses; ?></h2></div>
            <div class="col-stat"><p>Selesai</p><h2><?php echo $selesai; ?></h2></div>
        </div>

        <!-- Tabel aspirasi terbaru -->
        <h3 style="margin-top:20px;">Aspirasi Terbaru</h3>
        <div class="box">
            <table class="table" border="1" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>NIS</th>
                        <th>Kategori</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if (mysqli_num_rows($data) > 0) {
                        while ($row = mysqli_fetch_array($data)) {
                            // Tentukan warna badge status
                            $badge = 'badge-menunggu';
                            if ($row['status'] == 'Proses')  $badge = 'badge-proses';
                            if ($row['status'] == 'Selesai') $badge = 'badge-selesai';
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo date('d-m-Y H:i', strtotime($row['tgl_diajukan'])); ?></td>
                        <td><?php echo $row['nis']; ?></td>
                        <td><?php echo $row['ket_kategori']; ?></td>
                        <td><?php echo $row['lokasi']; ?></td>
                        <td><span class="badge <?php echo $badge; ?>"><?php echo $row['status']; ?></span></td>
                        <td><a href="detail-aspirasi.php?id=<?php echo $row['id_aspirasi']; ?>" class="btn-sm">Detail</a></td>
                    </tr>
                    <?php } } else { ?>
                    <tr><td colspan="7">Belum ada aspirasi</td></tr>
                    <?php } ?>
                </tbody>
            </table>
            <p style="margin-top:10px;"><a href="data-aspirasi.php">Lihat semua &rarr;</a></p>
        </div>

    </div>
</div>

<footer><div class="container"><small>Copyright &copy; 2026 - Aspirasi Sekolah.</small></div></footer>

</body>
</html>
