<?php
session_start();
include '../db.php';

// Cek login admin
if ($_SESSION['login'] != true || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// Ambil nilai filter dari URL (kalau ada)
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

// Buat kondisi WHERE sesuai filter
$where = "WHERE 1=1";
if ($filter_status != '') {
    $where .= " AND a.status = '$filter_status'";
}

// Ambil semua data aspirasi sesuai filter
$data = mysqli_query($conn, "
    SELECT a.id_aspirasi, a.status, a.tgl_diajukan, a.tgl_selesai,
           i.nis, i.lokasi, i.ket,
           k.ket_kategori
    FROM aspirasi a
    JOIN input_aspirasi i ON a.id_pelaporan = i.id_pelaporan
    JOIN kategori k ON i.id_kategori = k.id_kategori
    $where
    ORDER BY a.id_aspirasi DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Aspirasi | Admin</title>
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
        <h3>Data Aspirasi Siswa</h3>

        <!-- Filter berdasarkan status -->
        <div class="box">
            <form method="GET" class="filter-bar">
                <select name="status">
                    <option value="">-- Semua Status --</option>
                    <option value="Menunggu" <?php if($filter_status=='Menunggu') echo 'selected'; ?>>Menunggu</option>
                    <option value="Proses"   <?php if($filter_status=='Proses')   echo 'selected'; ?>>Proses</option>
                    <option value="Selesai"  <?php if($filter_status=='Selesai')  echo 'selected'; ?>>Selesai</option>
                </select>
                <input type="submit" value="Filter">
                <a href="data-aspirasi.php" style="margin-left:5px;">Reset</a>
            </form>

            <table class="table" border="1" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tgl Diajukan</th>
                        <th>NIS</th>
                        <th>Kategori</th>
                        <th>Lokasi</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th>Tgl Selesai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if (mysqli_num_rows($data) > 0) {
                        while ($row = mysqli_fetch_array($data)) {
                            $badge = 'badge-menunggu';
                            if ($row['status'] == 'Proses')  $badge = 'badge-proses';
                            if ($row['status'] == 'Selesai') $badge = 'badge-selesai';

                            // Tampilkan tanggal selesai kalau sudah ada
                            $tgl_selesai = $row['tgl_selesai'] ? date('d-m-Y H:i', strtotime($row['tgl_selesai'])) : '-';
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo date('d-m-Y H:i', strtotime($row['tgl_diajukan'])); ?></td>
                        <td><?php echo $row['nis']; ?></td>
                        <td><?php echo $row['ket_kategori']; ?></td>
                        <td><?php echo $row['lokasi']; ?></td>
                        <td><?php echo $row['ket']; ?></td>
                        <td><span class="badge <?php echo $badge; ?>"><?php echo $row['status']; ?></span></td>
                        <td><?php echo $tgl_selesai; ?></td>
                        <td><a href="detail-aspirasi.php?id=<?php echo $row['id_aspirasi']; ?>" class="btn-sm">Proses</a></td>
                    </tr>
                    <?php } } else { ?>
                    <tr><td colspan="9">Tidak ada data</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer><div class="container"><small>Copyright &copy; 2026 - Aspirasi Sekolah.</small></div></footer>

</body>
</html>
