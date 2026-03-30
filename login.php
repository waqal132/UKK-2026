<?php
session_start();
include 'db.php';

$pesan = '';

if (isset($_POST['submit'])) {
    $role = $_POST['role'];

    // ====== LOGIN ADMIN ======
    if ($role == 'admin') {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        $cek = $conn->query("SELECT * FROM admin WHERE username='$username' AND password='" . MD5($password) . "'");

        if ($cek->num_rows > 0) {
            $data = $cek->fetch_array();
            $_SESSION['login']    = true;
            $_SESSION['role']     = 'admin';
            $_SESSION['username'] = $data['username'];
            header('Location: admin/dashboard.php');
            exit;
        } else {
            $pesan = 'Username atau Password salah!';
        }

    // ====== LOGIN SISWA ======
    } else {
        $nis = (int)$_POST['nis'];

        if ($nis <= 0) {
            $pesan = 'NISN tidak boleh kosong!';
        } else {
            $cek = $conn->query("SELECT * FROM siswa WHERE nis=$nis")->fetch_array();

            if ($cek) {
                $_SESSION['login'] = true;
                $_SESSION['role']  = 'siswa';
                $_SESSION['nis']   = $cek['nis'];
                $_SESSION['kelas'] = $cek['kelas'];
                $_SESSION['nama'] = $cek['nama'];
                header('Location: siswa/index.php');
                exit;
            } else {
                $pesan = 'NIS tidak terdaftar! Hubungi guru.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | Aspirasi Sekolah</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body id="bg-login">

<div class="box-login">
    <h2>Aspirasi Sekolah</h2>
    <p>Pilih login sebagai:</p>

    <!-- Tampilkan pesan error kalau ada -->
    <?php if ($pesan != '') { ?>
    <div class="alert alert-danger"><?php echo $pesan; ?></div>
    <?php } ?>

    <!-- Tombol pilih role -->
    <div class="pilih-role">
        <button id="btn-admin" class="aktif" onclick="pilihRole('admin')">Admin</button>
        <button id="btn-siswa" onclick="pilihRole('siswa')">Siswa</button>
    </div>

    <!-- Form Login Admin -->
    <div id="form-admin">
        <form method="post">
            <input type="hidden" name="role" value="admin">
            <input type="text"     name="username" class="input-control" placeholder="Username" required>
            <input type="password" name="password" class="input-control" placeholder="Password" required>
            <input type="submit"   name="submit"   class="btn" value="Login Admin" style="width:100%">
        </form>
    </div>

    <!-- Form Login Siswa -->
    <div id="form-siswa" style="display:none">
        <form method="post">
            <input type="hidden" name="role" value="siswa">
            <input type="number" name="nis"   class="input-control" placeholder="Masukkan NIS" required>
            <input type="submit" name="submit" class="btn" value="Masuk" style="width:100%">
        </form>
    </div>
</div>

<script>
// Fungsi untuk menampilkan form admin atau siswa
function pilihRole(role) {
    if (role === 'admin') {
        document.getElementById('form-admin').style.display = 'block';
        document.getElementById('form-siswa').style.display = 'none';
        document.getElementById('btn-admin').classList.add('aktif');
        document.getElementById('btn-siswa').classList.remove('aktif');
    } else {
        document.getElementById('form-admin').style.display = 'none';
        document.getElementById('form-siswa').style.display = 'block';
        document.getElementById('btn-admin').classList.remove('aktif');
        document.getElementById('btn-siswa').classList.add('aktif');
    }
}

// Kalau error saat login siswa, langsung tampilkan form siswa
<?php if ($pesan != '' && isset($_POST['role']) && $_POST['role'] == 'siswa') { ?>
pilihRole('siswa');
<?php } ?>
</script>

</body>
</html>
