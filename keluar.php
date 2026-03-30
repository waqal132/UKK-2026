<?php
// File ini untuk logout
session_start();     // Mulai sesi
session_destroy();   // Hapus semua data sesi (logout)
echo '<script>window.location="login.php"</script>';
?>
