<?php
    session_start();
    include '../db.php';
    if ($_SESSION['login'] != true || $_SESSION['role'] != 'admin') {
        echo '<script>window.location="../login.php"</script>';
        exit;
    }

    // Tambah siswa
    if (isset($_POST['tambah'])) {
        $nis   = (int)$_POST['nis'];
        $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
        $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);

        $cek = mysqli_query($conn, "SELECT nis FROM siswa WHERE nis = $nis");
        if (mysqli_num_rows($cek) > 0) {
            echo '<script>alert("NIS sudah terdaftar!")</script>';
        } else {
            $insert = mysqli_query($conn, "INSERT INTO siswa (nis, nama, kelas) VALUES ($nis, '$nama', '$kelas')");
            if ($insert) {
                echo '<script>alert("Siswa berhasil ditambahkan!")</script>';
            } else {
                echo '<script>alert("Gagal: ' . mysqli_error($conn) . '")</script>';
            }
        }
    }

    // Hapus siswa
    if (isset($_GET['hapus'])) {
        $nis = (int)$_GET['hapus'];
        mysqli_query($conn, "DELETE FROM siswa WHERE nis = $nis");
        echo '<script>window.location="data-siswa.php"</script>';
    }

    // Cari siswa
    $cari = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : '';
    $where = '';
    if ($cari != '') {
        $where = "WHERE nis LIKE '%$cari%' OR nama LIKE '%$cari%' OR kelas LIKE '%$cari%'";
    }

    $daftar = mysqli_query($conn, "SELECT * FROM siswa $where ORDER BY kelas, nama ASC");
    $total  = mysqli_num_rows($daftar);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa | Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="dashboard.php">Aspirasi Sekolah</a></h1>
            <span id="waktu-realtime"></span>
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
            <h3>Data Siswa</h3>

            <!-- Form tambah siswa -->
            <div class="box">
                <h4 style="margin-bottom:12px;">Tambah Siswa Baru</h4>
                <form action="" method="POST">
                    <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end;">
                        <div>
                            <label style="display:block; font-size:13px; margin-bottom:4px;">NIS</label>
                            <input type="number" name="nis" placeholder="Contoh: 10006" class="input-control" style="width:150px; margin-bottom:0;" required>
                        </div>
                        <div>
                            <label style="display:block; font-size:13px; margin-bottom:4px;">Nama Lengkap</label>
                            <input type="text" name="nama" placeholder="Nama siswa" class="input-control" style="width:220px; margin-bottom:0;" required>
                        </div>
                        <div>
                            <label style="display:block; font-size:13px; margin-bottom:4px;">Kelas</label>
                            <select name="kelas" class="input-control" style="width:160px; margin-bottom:0;" required>
                                <option value="">-- Pilih Kelas --</option>
                                <optgroup label="Kelas X">
                                    <option value="X RPL 1">X RPL 1</option>
                                    <option value="X RPL 2">X RPL 2</option>
                                    <option value="X RPL 3">X RPL 3</option>

                                    <option value="X TKJ 1">X TKJ 1</option>
                                    <option value="X TKJ 2">X TKJ 2</option>
                                    <option value="X TKJ 3">X TKJ 3</option>

                                    <option value="X MM 1">X MM 1</option>
                                    <option value="X MM 2">X MM 2</option>
                                    <option value="X MM 3">X MM 3</option>
                                </optgroup>
                                <optgroup label="Kelas XI">
                                    <option value="XI RPL 1">XI RPL 1</option>
                                    <option value="XI RPL 2">XI RPL 2</option>
                                    <option value="XI RPL 3">XI RPL 3</option>

                                    <option value="XI TKJ 1">XI TKJ 1</option>
                                    <option value="XI TKJ 2">XI TKJ 2</option>
                                    <option value="XI TKJ 3">XI TKJ 3</option>

                                    <option value="XI MM 1">XI MM 1</option>
                                    <option value="XI MM 2">XI MM 2</option>
                                    <option value="XI MM 3">XI MM 3</option>
                                </optgroup>
                                <optgroup label="Kelas XII">
                                    <option value="XII RPL 1">XII RPL 1</option>
                                    <option value="XII RPL 2">XII RPL 2</option>
                                    <option value="XII RPL 3">XII RPL 3</option>

                                    <option value="XII TKJ 1">XII TKJ 1</option>
                                    <option value="XII TKJ 2">XII TKJ 2</option>
                                    <option value="XII TKJ 3">XII TKJ 3</option>

                                    <option value="XII MM 1">XII MM 1</option>
                                    <option value="XII MM 2">XII MM 2</option>
                                    <option value="XII MM 3">XII MM 3</option>
                                </optgroup>
                            </select>
                        </div>
                        <div>
                            <input type="submit" name="tambah" value="Tambah Siswa" class="btn">
                        </div>
                    </div>
                </form>
            </div>

            <!-- Pencarian + info total -->
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                <p style="color:#666;">Total siswa terdaftar: <strong><?php echo $total ?></strong></p>
                <form action="" method="GET" style="display:flex; gap:5px;">
                    <input type="text" name="cari" placeholder="Cari NIS / Nama / Kelas..." value="<?php echo $cari ?>"
                        style="padding:7px 10px; border:1px solid #ccc; border-radius:3px; width:250px;">
                    <input type="submit" value="Cari" class="btn">
                    <?php if ($cari != '') { ?>
                    <a href="data-siswa.php" class="btn" style="background:#666;">Reset</a>
                    <?php } ?>
                </form>
            </div>

            <!-- Tabel siswa -->
            <div class="box">
                <table border="1" cellspacing="0" class="table">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th width="100">NIS</th>
                            <th>Nama</th>
                            <th width="130">Kelas</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if (mysqli_num_rows($daftar) > 0) {
                            // Reset pointer karena sudah dihitung sebelumnya
                            mysqli_data_seek($daftar, 0);
                            while ($row = mysqli_fetch_array($daftar)) {
                        ?>
                        <tr>
                            <td><?php echo $no++ ?></td>
                            <td><?php echo $row['nis'] ?></td>
                            <td><?php echo $row['nama'] ?></td>
                            <td><?php echo $row['kelas'] ?></td>
                            <td>
                                <a href="data-siswa.php?hapus=<?php echo $row['nis'] ?>"
                                   class="btn-sm"
                                   style="background:#c0392b;"
                                   onclick="return confirm('Hapus siswa <?php echo $row['nama'] ?> (NIS: <?php echo $row['nis'] ?>)?')">Hapus</a>
                            </td>
                        </tr>
                        <?php } } else { ?>
                        <tr>
                            <td colspan="5" style="text-align:center; color:#999;">
                                <?php echo $cari != '' ? 'Siswa tidak ditemukan.' : 'Belum ada data siswa.' ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <footer>
        <div class="container">
            <small>Copyright &copy; 2026 - Aspirasi Sekolah.</small>
        </div>
    </footer>
</body>
</html>