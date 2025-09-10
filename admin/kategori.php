<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin/login.php");
    exit;
}
include "database.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$kat_id = intval($_GET['id']);
$kategori = $conn->query("SELECT * FROM kategori WHERE id=$kat_id")->fetch_assoc();
if (!$kategori) {
    echo "Kategori tidak ditemukan!";
    exit;
}

$result = $conn->query("SELECT * FROM galeri WHERE kategori_id = $kat_id ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Galeri <?= $kategori['nama'] ?></title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .container { display: flex; flex-wrap: wrap; gap: 15px; }
    .card { border: 1px solid #ccc; padding: 10px; width: 200px; text-align: center; }
    img { width: 100%; height: 150px; object-fit: cover; }
  </style>
</head>
<body>
  <h1>Galeri: <?= $kategori['nama'] ?></h1>
  <a href="index.php">⬅ Kembali ke semua galeri</a>
  <hr>

  <div class="container">
    <?php while($row = $result->fetch_assoc()): ?>
      <div class="card">
        <a href="detail.php?id=<?= $row['id'] ?>">
          <img src="uploads/<?= $row['file'] ?>" alt="<?= $row['judul'] ?>">
        </a>
        <p><b><?= $row['judul'] ?></b></p>
      </div>
    <?php endwhile; ?>
  </div>
</body>
</html>
