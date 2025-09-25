<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
include "../database.php";

// Tambah kategori baru
if (isset($_POST['kategori_baru'])) {
    $namaKat = trim($_POST['kategori_baru']);
    if ($namaKat !== "") {
        $stmt = $conn->prepare("INSERT INTO kategori (nama) VALUES (?)");
        $stmt->bind_param("s", $namaKat);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: foto.php");
    exit;
}

// Hapus kategori
if (isset($_GET['delete_kategori'])) {
    $id = intval($_GET['delete_kategori']);
    $conn->query("DELETE FROM kategori WHERE id=$id");
    header("Location: foto.php");
    exit;
}

// Ambil kategori untuk dropdown
$kategoriRes = $conn->query("SELECT * FROM kategori ORDER BY nama ASC");

// Ambil semua foto
$result = $conn->query("SELECT galeri.*, kategori.nama as kategori 
                        FROM galeri 
                        LEFT JOIN kategori ON galeri.kategori_id = kategori.id
                        ORDER BY id DESC");

// Ambil data foto yang mau diedit
$editFoto = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editFoto = $conn->query("SELECT * FROM galeri WHERE id=$editId")->fetch_assoc();
}

// Update foto
if ($editFoto && $_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['kategori_baru']) && !isset($_POST['kategori_edit'])) {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $kat = intval($_POST['kategori']);

    if (!empty($_FILES['foto']['name'])) {
        $file = $_FILES['foto']['name'];
        $tmp = $_FILES['foto']['tmp_name'];
        $target = "../uploads/" . basename($file);

        if (move_uploaded_file($tmp, $target)) {
            if (file_exists("../uploads/" . $editFoto['file'])) {
                unlink("../uploads/" . $editFoto['file']);
            }
            $stmt = $conn->prepare("UPDATE galeri SET judul=?, deskripsi=?, kategori_id=?, file=? WHERE id=?");
            $stmt->bind_param("ssisi", $judul, $deskripsi, $kat, $file, $editFoto['id']);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        $stmt = $conn->prepare("UPDATE galeri SET judul=?, deskripsi=?, kategori_id=? WHERE id=?");
        $stmt->bind_param("ssii", $judul, $deskripsi, $kat, $editFoto['id']);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: foto.php");
    exit;
}

// Ambil kategori yang mau diedit
$editKat = null;
if (isset($_GET['edit_kategori'])) {
    $editId = intval($_GET['edit_kategori']);
    $editKat = $conn->query("SELECT * FROM kategori WHERE id=$editId")->fetch_assoc();
}

// Update kategori
if ($editKat && isset($_POST['kategori_edit'])) {
    $namaKat = trim($_POST['kategori_edit']);
    if ($namaKat !== "") {
        $stmt = $conn->prepare("UPDATE kategori SET nama=? WHERE id=?");
        $stmt->bind_param("si", $namaKat, $editKat['id']);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: foto.php");
    exit;
}

// Statistik
$totalFoto = $conn->query("SELECT COUNT(*) as jml FROM galeri")->fetch_assoc()['jml'];
$totalKategori = $conn->query("SELECT COUNT(*) as jml FROM kategori")->fetch_assoc()['jml'];
$totalAdmin = $conn->query("SELECT COUNT(*) as jml FROM admin")->fetch_assoc()['jml'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin</title>
<style>
body { 
    font-family: Arial, sans-serif; 
    margin: 0; 
    background: #f4f6f9; 
}
h1 { 
    padding: 20px; 
    margin: 0; 
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); 
    color: #fff; 
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
.table-container { 
    padding: 20px; 
}

.stats { 
    display: flex; 
    gap: 20px; 
    margin: 20px 0; 
}
.stats .card {
    flex: 1; 
    background: #fff; 
    padding: 20px;
    border-radius: 10px; 
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    border: 2px solid #1e3a8a; /* Bingkai biru tua */
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}
.stats .card .angka { 
    font-size: 2rem; 
    font-weight: bold; 
    color: #1e3a8a; 
}
.stats .card p { 
    margin: 5px 0 0; 
    color: #1e293b; 
    font-weight: bold; 
}

/* Tabel */
table { 
    border-collapse: collapse; 
    width: 100%; 
    background: #fff; 
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); 
    border-radius: 8px; 
    overflow: hidden; 
}
th, td { 
    border-bottom: 1px solid #e2e8f0; 
    padding: 10px; 
    text-align: center; 
}
th { 
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); 
    color: #fff; 
}
img.table-img { 
    width: 100px; 
    height: 80px; 
    object-fit: cover; 
    border-radius: 6px; 
}
a.action-link { 
    color: #1e40af; 
    text-decoration: none; 
    font-weight: bold; 
}
a.action-link:hover { 
    text-decoration: underline; 
    color: #1e3a8a;
}
a.button-link { 
    display: inline-block; 
    margin: 10px 10px 20px 0; 
    padding: 8px 14px; 
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); 
    color: #fff; 
    border-radius: 6px; 
    text-decoration: none; 
    font-weight: bold; 
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}
a.button-link:hover { 
    background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%); 
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* Panel */
.edit-panel, .kategori-panel {
    position: fixed;
    top: 0;
    height: 100%;
    background: #fff;
    box-shadow: 0 0 12px rgba(0, 0, 0, 0.2);
    padding: 20px;
    transition: 0.4s;
    overflow-y: auto;
    z-index: 1000;
}
.edit-panel { 
    left: -420px;
    width: 400px;
}
.edit-panel.active { 
    left: 0; 
}
.kategori-panel {
    right: -420px;
    width: 400px;
}
.kategori-panel.active { 
    right: 0; 
}
label { 
    font-weight: bold; 
    display: block; 
    margin-top: 12px; 
    color: #1e3a8a;
}
input, textarea, select, button {
    width: 100%;
    padding: 8px;
    margin-top: 6px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 0.95rem;
}
button {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
    color: #fff;
    border: none;
    margin-top: 15px;
    cursor: pointer;
    font-weight: bold;
    padding: 10px;
    transition: all 0.3s ease;
}
button:hover { 
    background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* Close Button */
.close-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #dc2626;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
}
.close-btn:hover { 
    background: #b91c1c; 
}

/* Overlay */
.overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.4);
    display: none;
    z-index: 900;
}
.overlay.active { 
    display: block; 
}

/* Daftar kategori */
.kategori-list { 
    margin-top: 20px; 
}
.kategori-list table { 
    width: 100%; 
    border-collapse: collapse; 
    font-size: 14px; 
}
.kategori-list th, .kategori-list td { 
    padding: 8px; 
    border-bottom: 1px solid #e2e8f0; 
    text-align: left; 
}
.kategori-list th { 
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); 
    color: #1e3a8a;
}
.kategori-list a { 
    color: #1e40af; 
    font-weight: bold; 
    text-decoration: none; 
}
.kategori-list a:hover { 
    text-decoration: underline; 
    color: #1e3a8a;
}
.kategori-list a.delete { 
    color: #dc2626; 
}
.kategori-list a.delete:hover { 
    color: #b91c1c;
}

/* Judul daftar foto */
h2 {
    margin: 20px 0; 
    color: #1e3a8a;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    padding: 10px 15px;
    border-radius: 6px;
    border-left: 4px solid #1e3a8a;
}
</style>
</head>
<body>

<h1>📊 Dashboard Admin</h1>

<div class="table-container">

  <!-- Statistik -->
  <div class="stats">
    <div class="card">
      <div class="angka"><?= $totalFoto ?></div>
      <p>Foto</p>
    </div>
    <div class="card">
      <div class="angka"><?= $totalKategori ?></div>
      <p>Kategori</p>
    </div>
    <div class="card">
      <div class="angka"><?= $totalAdmin ?></div>
      <p>Admin</p>
    </div>
  </div>

  <!-- Tombol aksi -->
  <div style="margin:20px 0;">
    <a href="../index.php" class="button-link">⬅ Dashboard</a>
    <a href="upload.php" class="button-link">➕ Upload Foto</a>
    <a href="#" class="button-link" onclick="openKategoriPanel()">➕ Tambah Kategori</a>
    <a href="request_foto.php" class="button-link" style="background:linear-gradient(135deg,#388e3c,#43a047);border:2px solid #388e3c;">📝 Request Foto</a>
  </div>

  <!-- Judul daftar foto -->
  <h2>📷 Daftar Foto</h2>

  <table>
    <tr>
      <th>No</th>
      <th>Preview</th>
      <th>Judul</th>
      <th>Kategori</th>
      <th>Tanggal Upload</th>
      <th>Aksi</th>
    </tr>
    <?php $no=1; while($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><img class="table-img" src="../uploads/<?= htmlspecialchars($row['file']) ?>" alt="<?= htmlspecialchars($row['judul']) ?>"></td>
      <td><?= htmlspecialchars($row['judul']) ?></td>
      <td><?= htmlspecialchars($row['kategori'] ?? 'Tanpa Kategori') ?></td>
      <td><?= htmlspecialchars($row['tanggal_upload']) ?></td>
      <td>
        <a href="?edit=<?= $row['id'] ?>" class="action-link">✏ Edit</a> | 
        <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus foto ini?')" class="action-link delete">❌ Hapus</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

<!-- Overlay -->
<div class="overlay <?= ($editFoto || $editKat) ? 'active' : '' ?>" id="overlay"></div>

<!-- Slide Edit Foto -->
<div class="edit-panel <?= $editFoto ? 'active' : '' ?>" id="editPanel">
  <?php if ($editFoto): ?>
  <button class="close-btn" id="closeBtn">×</button>
  <h2>Edit Foto ID <?= $editFoto['id'] ?></h2>
  <img id="previewFoto" src="../uploads/<?= htmlspecialchars($editFoto['file']) ?>" alt="Preview Foto" style="width:100%;border-radius:6px;margin-bottom:10px;">
  <form method="POST" enctype="multipart/form-data">
    <label>Judul:</label>
    <input type="text" name="judul" value="<?= htmlspecialchars($editFoto['judul']) ?>" required>

    <label>Deskripsi:</label>
    <textarea name="deskripsi" rows="4" required><?= htmlspecialchars($editFoto['deskripsi']) ?></textarea>

    <label>Kategori:</label>
    <select name="kategori" required>
      <?php 
      $kategoriRes->data_seek(0);
      while($kat = $kategoriRes->fetch_assoc()): ?>
        <option value="<?= $kat['id'] ?>" <?= ($kat['id'] == $editFoto['kategori_id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($kat['nama']) ?>
        </option>
      <?php endwhile; ?>
    </select>

    <label>Ganti Foto (opsional):</label>
    <input type="file" name="foto" accept="image/*" onchange="previewImage(event)">

    <button type="submit">💾 Simpan Perubahan</button>
  </form>
  <?php endif; ?>
</div>

<!-- Slide Tambah/Edit Kategori -->
<div class="kategori-panel <?= $editKat ? 'active' : '' ?>" id="kategoriPanel">
  <button class="close-btn" onclick="closeKategoriPanel()">×</button>
  <?php if ($editKat): ?>
    <h2>✏ Edit Kategori</h2>
    <form method="POST">
      <label>Nama Kategori:</label>
      <input type="text" name="kategori_edit" value="<?= htmlspecialchars($editKat['nama']) ?>" required>
      <button type="submit">💾 Update Kategori</button>
    </form>
  <?php else: ?>
    <h2>➕ Tambah Kategori Baru</h2>
    <form method="POST">
      <label>Nama Kategori:</label>
      <input type="text" name="kategori_baru" required placeholder="Misalnya: Landscape, Hewan, dll">
      <button type="submit">💾 Simpan Kategori</button>
    </form>
  <?php endif; ?>

  <div class="kategori-list">
    <h3>📂 Daftar Kategori</h3>
    <table>
      <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Aksi</th>
      </tr>
      <?php
      $allKat = $conn->query("SELECT * FROM kategori ORDER BY id ASC");
      $no = 1;
      while ($kat = $allKat->fetch_assoc()): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($kat['nama']) ?></td>
          <td>
            <a href="?delete_kategori=<?= $kat['id'] ?>" onclick="return confirm('Yakin hapus kategori ini?')" class="delete">❌ Hapus</a> | 
            <a href="?edit_kategori=<?= $kat['id'] ?>" class="action-link">✏ Edit</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById('previewFoto').src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

document.getElementById('overlay').addEventListener('click', closePanel);
const closeBtn = document.getElementById('closeBtn');
if (closeBtn) closeBtn.addEventListener('click', closePanel);

function closePanel() {
    document.getElementById('overlay').classList.remove('active');
    document.getElementById('editPanel').classList.remove('active');
    window.history.pushState({}, document.title, "foto.php");
}

function openKategoriPanel() {
    document.getElementById('overlay').classList.add('active');
    document.getElementById('kategoriPanel').classList.add('active');
}
function closeKategoriPanel() {
    document.getElementById('overlay').classList.remove('active');
    document.getElementById('kategoriPanel').classList.remove('active');
}
</script>

</body>
</html>