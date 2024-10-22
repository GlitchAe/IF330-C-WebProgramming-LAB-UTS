<?php
session_start();
session_unset(); // Menghapus semua variabel sesi
session_destroy(); // Menghancurkan sesi

// Arahkan pengguna kembali ke login.php setelah logout
header("Location: login.php");
exit;
?>
