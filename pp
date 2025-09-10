<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Galeri Foto</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    nav { margin-bottom: 20px; }
    nav a {
        margin-right: 15px;
        text-decoration: none;
        color: #007BFF;
    }
    nav a:hover {
        text-decoration: underline;
    }
  </style>
</head>
<body>
  <nav>
    <a href="index.php">🏠 Galeri</a>
    <?php if (isset($_SESSION['admin_id'])): ?>
        <a href="admin/upload.php">➕ Upload</a>
        <a href="admin/foto.php">⚙️ Manajemen Foto</a>
        <a href="admin/logout.php">🚪 Logout</a>
    <?php else: ?>
        <a href="admin/login.php">🔑 Login</a>
        <a href="admin/register.php">📝 Register</a>
    <?php endif; ?>
  </nav>
  <hr>
