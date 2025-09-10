<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
include "../database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $kategori = $_POST['kategori'];
    $file = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];
    $target = "../uploads/" . basename($file);

    if (move_uploaded_file($tmp, $target)) {
        $conn->query("INSERT INTO galeri (judul, deskripsi, kategori_id, file, tanggal_upload) 
                      VALUES ('$judul', '$deskripsi', '$kategori', '$file', NOW())");
        header("Location: foto.php");
        exit;
    } else {
        $error = "Upload gagal!";
    }
}

// Ambil kategori
$kategoriRes = $conn->query("SELECT * FROM kategori ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Upload Foto Baru</title>
</head>
<body>
<h1>Upload Foto Baru</h1>
<a href="foto.php">⬅ Kembali ke Manajemen Foto</a>
<form method="POST" enctype="multipart/form-data">
  <label>Judul:</label><br>
  <input type="text" name="judul" required><br><br>

  <label>Deskripsi:</label><br>
  <textarea name="deskripsi" rows="4" required></textarea><br><br>

  <label>Kategori:</label><br>
  <select name="kategori" required>
    <?php while($kat = $kategoriRes->fetch_assoc()): ?>
      <option value="<?= $kat['id'] ?>"><?= htmlspecialchars($kat['nama']) ?></option>
    <?php endwhile; ?>
  </select><br><br>

  <label>Foto:</label><br>
  <input type="file" name="foto" accept="image/*" required><br><br>

  <button type="submit">Upload</button>
</form>
</body>
</html>
