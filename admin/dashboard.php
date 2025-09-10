<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin</title>
</head>
<body>
  <h1>Selamat datang, Admin!</h1>
  <a href="foto.php">📸 Kelola Foto</a>
  <a href="upload.php">➕ Upload Foto Baru</a>
  <p><a href="logout.php">🚪 Logout</a></p>
</body>
</html>
