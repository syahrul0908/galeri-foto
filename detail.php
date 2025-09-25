<?php
session_start();
include "database.php";

// validasi id
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);
$query = "SELECT galeri.*, kategori.nama AS kategori 
          FROM galeri 
          LEFT JOIN kategori ON galeri.kategori_id = kategori.id
          WHERE galeri.id = $id";
$result = $conn->query($query);

if ($result->num_rows == 0) {
    echo "Foto tidak ditemukan!";
    exit;
}

$foto = $result->fetch_assoc();

// Pastikan session array ada
if (!isset($_SESSION['voted'])) {
    $_SESSION['voted'] = [];
}

// Proses Like
if (isset($_POST['like'])) {
    if (!isset($_SESSION['voted'][$id])) {
        $conn->query("UPDATE galeri SET likes = likes + 1 WHERE id = $id");
        $_SESSION['voted'][$id] = 'like';
    } elseif ($_SESSION['voted'][$id] === 'like') {
        $conn->query("UPDATE galeri SET likes = likes - 1 WHERE id = $id");
        unset($_SESSION['voted'][$id]);
    } elseif ($_SESSION['voted'][$id] === 'unlike') {
        $conn->query("UPDATE galeri SET unlikes = unlikes - 1, likes = likes + 1 WHERE id = $id");
        $_SESSION['voted'][$id] = 'like';
    }
    $result = $conn->query($query);
    $foto = $result->fetch_assoc();
}

// Proses Unlike
if (isset($_POST['unlike'])) {
    if (!isset($_SESSION['voted'][$id])) {
        $conn->query("UPDATE galeri SET unlikes = unlikes + 1 WHERE id = $id");
        $_SESSION['voted'][$id] = 'unlike';
    } elseif ($_SESSION['voted'][$id] === 'unlike') {
        $conn->query("UPDATE galeri SET unlikes = unlikes - 1 WHERE id = $id");
        unset($_SESSION['voted'][$id]);
    } elseif ($_SESSION['voted'][$id] === 'like') {
        $conn->query("UPDATE galeri SET likes = likes - 1, unlikes = unlikes + 1 WHERE id = $id");
        $_SESSION['voted'][$id] = 'unlike';
    }
    $result = $conn->query($query);
    $foto = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($foto['judul']) ?> - Detail Foto</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 30px;
    }
    .container {
      max-width: 900px;
      margin: auto;
      display: flex;
      gap: 30px;
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
      flex-wrap: wrap;
      align-items: flex-start;
    }
    .img-wrapper {
      flex: 0 0 400px;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 400px;
      background: #f1f5f9;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.07);
      margin-bottom: 10px;
    }
    .img-wrapper img {
      max-width: 100%;
      max-height: 400px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.07);
      background: #fff;
      display: block;
      margin: auto;
    }
    .info {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      min-width: 250px;
    }
    h1 {
      margin-top: 0;
      color: #2c3e50;
    }
    p {
      font-size: 14px;
      line-height: 1.6;
      color: #555;
    }
    .meta {
      margin-top: 15px;
      font-size: 13px;
      color: #777;
    }
    .back, .download-btn, .like-btn, .unlike-btn {
      display: inline-block;
      margin-top: 10px;
      padding: 8px 14px;
      background: #3498db;
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
      font-size: 14px;
      margin-right: 10px;
      border: none;
      cursor: pointer;
    }
    .back:hover, .download-btn:hover {
      background: #2980b9;
    }
    .like-unlike {
      margin-top: 15px;
    }
    .like-btn { background: #10b981; }
    .like-btn:hover { background: #059669; }
    .unlike-btn { background: #ef4444; }
    .unlike-btn:hover { background: #dc2626; }
    .active {
      opacity: 0.8;
      border: 2px solid #000;
    }
    @media (max-width: 700px) {
      .container {
        flex-direction: column;
        gap: 0;
        padding: 10px;
      }
      .img-wrapper {
        width: 100%;
        min-height: 220px;
      }
      .img-wrapper img {
        max-height: 220px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="img-wrapper">
      <img src="uploads/<?= htmlspecialchars($foto['file']) ?>" alt="<?= htmlspecialchars($foto['judul']) ?>">
    </div>
    <div class="info">
      <div>
        <h1><?= htmlspecialchars($foto['judul']) ?></h1>
        <p><b>Kategori:</b> <?= $foto['kategori'] ?? 'Tanpa Kategori' ?></p>
        <p><b>Deskripsi:</b><br><?= !empty($foto['deskripsi']) ? nl2br(htmlspecialchars($foto['deskripsi'])) : '-' ?></p>
        <p class="meta"><b>Tanggal Upload:</b> <?= $foto['tanggal_upload'] ?></p>

        <!-- Tombol Download -->
        <a class="download-btn" href="uploads/<?= htmlspecialchars($foto['file']) ?>" download>⬇ Download Gambar</a>

        <!-- Tombol Like dan Unlike -->
        <div class="like-unlike">
          <form method="POST" style="display:inline;">
            <button type="submit" name="like" class="like-btn <?= (isset($_SESSION['voted'][$id]) && $_SESSION['voted'][$id] === 'like') ? 'active' : '' ?>">
              👍 Like (<?= $foto['likes'] ?? 0 ?>)
            </button>
          </form>
          <form method="POST" style="display:inline;">
            <button type="submit" name="unlike" class="unlike-btn <?= (isset($_SESSION['voted'][$id]) && $_SESSION['voted'][$id] === 'unlike') ? 'active' : '' ?>">
              👎 Unlike (<?= $foto['unlikes'] ?? 0 ?>)
            </button>
          </form>
        </div>
      </div>
      <div>
        <a class="back" href="index.php">⬅ Kembali ke Galeri</a>
      </div>
    </div>
  </div>
</body>
</html>
