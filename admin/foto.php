<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
include "../database.php";

// Ambil semua kategori
$kategoriRes = $conn->query("SELECT * FROM kategori ORDER BY nama ASC");

// Ambil semua foto
$result = $conn->query("SELECT galeri.*, kategori.nama as kategori 
                        FROM galeri 
                        LEFT JOIN kategori ON galeri.kategori_id = kategori.id
                        ORDER BY id DESC");

// Cek apakah ada foto yang ingin diedit
$editFoto = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editFoto = $conn->query("SELECT * FROM galeri WHERE id=$editId")->fetch_assoc();
}

// Proses update
if ($editFoto && $_SERVER["REQUEST_METHOD"] == "POST") {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $kat = $_POST['kategori'];

    if (!empty($_FILES['foto']['name'])) {
        $file = $_FILES['foto']['name'];
        $tmp = $_FILES['foto']['tmp_name'];
        $target = "../uploads/" . basename($file);

        if (move_uploaded_file($tmp, $target)) {
            if (file_exists("../uploads/" . $editFoto['file'])) {
                unlink("../uploads/" . $editFoto['file']);
            }
            $conn->query("UPDATE galeri SET judul='$judul', deskripsi='$deskripsi', kategori_id='$kat', file='$file' WHERE id={$editFoto['id']}");
            $editFoto['file'] = $file; // Update preview
        }
    } else {
        $conn->query("UPDATE galeri SET judul='$judul', deskripsi='$deskripsi', kategori_id='$kat' WHERE id={$editFoto['id']}");
    }

    header("Location: foto.php?edit={$editFoto['id']}");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Manajemen Foto</title>
<style>
body { font-family: Arial, sans-serif; margin: 20px; background:#f4f6f9; }
.container { display: flex; gap: 20px; }
.edit-container { flex: 1; padding: 15px; border: 1px solid #ccc; border-radius: 8px; background: #fff; max-width:400px; }
.edit-container h2 { margin-top:0; }
.edit-container img { width:100%; max-height:300px; object-fit:cover; border-radius:8px; margin-bottom:10px; }
.table-container { flex: 2; }
table { border-collapse: collapse; width: 100%; background:#fff; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
img.table-img { width: 100px; height: 80px; object-fit: cover; }
input, select, textarea, button { width: 100%; padding: 6px; margin-bottom: 10px; }
button { background: #4f46e5; color: #fff; border: none; cursor: pointer; }
button:hover { background: #4338ca; }
a.button-link { display:inline-block; margin:10px 0; padding:6px 12px; background:#10b981; color:#fff; border-radius:6px; text-decoration:none; }
a.button-link:hover { background:#059669; }
</style>
</head>
<body>

<h1>Manajemen Foto</h1>
<div class="container">

  <!-- Form Edit -->
  <div class="edit-container">
    <?php if ($editFoto): ?>
      <h2>Edit Foto ID <?= $editFoto['id'] ?></h2>
      <img id="previewFoto" src="../uploads/<?= htmlspecialchars($editFoto['file']) ?>" alt="Preview Foto">
      <form method="POST" enctype="multipart/form-data">
        <label>Judul:</label>
        <input type="text" name="judul" value="<?= htmlspecialchars($editFoto['judul']) ?>" required>

        <label>Deskripsi:</label>
        <textarea name="deskripsi" rows="4" required><?= htmlspecialchars($editFoto['deskripsi']) ?></textarea>

        <label>Kategori:</label>
        <select name="kategori" required>
          <?php 
          $kategoriRes->data_seek(0); // Reset pointer
          while($kat = $kategoriRes->fetch_assoc()): ?>
            <option value="<?= $kat['id'] ?>" <?= ($kat['id'] == $editFoto['kategori_id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($kat['nama']) ?>
            </option>
          <?php endwhile; ?>
        </select>

        <label>Ganti Foto (opsional):</label>
        <input type="file" name="foto" accept="image/*" onchange="previewImage(event)">

        <button type="submit">Simpan Perubahan</button>
      </form>
      <script>
      function previewImage(event) {
          const reader = new FileReader();
          reader.onload = function(){
              document.getElementById('previewFoto').src = reader.result;
          };
          reader.readAsDataURL(event.target.files[0]);
      }
      </script>
    <?php else: ?>
      <p>Pilih foto dari daftar di sebelah kanan untuk diedit.</p>
    <?php endif; ?>
  </div>

  <!-- Table Foto -->
  <div class="table-container">
    <a href="../index.php" class="button-link">⬅ Kembali ke Dashboard</a>
    <a href="upload.php" class="button-link">➕ Upload Foto Baru</a>
    <hr>
    <table>
      <tr>
        <th>ID</th>
        <th>Preview</th>
        <th>Judul</th>
        <th>Kategori</th>
        <th>Tanggal Upload</th>
        <th>Aksi</th>
      </tr>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><img class="table-img" src="../uploads/<?= htmlspecialchars($row['file']) ?>" alt="<?= htmlspecialchars($row['judul']) ?>"></td>
        <td><?= htmlspecialchars($row['judul']) ?></td>
        <td><?= htmlspecialchars($row['kategori'] ?? 'Tanpa Kategori') ?></td>
        <td><?= htmlspecialchars($row['tanggal_upload']) ?></td>
        <td>
          <a href="?edit=<?= $row['id'] ?>">✏ Edit</a> | 
          <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus foto ini?')">❌ Hapus</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>

</div>

</body>
</html>
