<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once "../database.php";

// hitung data statistik
$stats = [
    'photos'     => $conn->query("SELECT COUNT(*) FROM galeri")->fetch_row()[0],
    'categories' => $conn->query("SELECT COUNT(*) FROM kategori")->fetch_row()[0],
    'users'      => $conn->query("SELECT COUNT(*) FROM admin")->fetch_row()[0],
];

// ambil daftar foto terbaru
$sql = "SELECT galeri.*, kategori.nama AS kategori 
        FROM galeri 
        LEFT JOIN kategori ON galeri.kategori_id = kategori.id 
        ORDER BY galeri.tanggal_upload DESC 
        LIMIT 50";
$photos = $conn->query($sql);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Dashboard - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">Admin - Galeri</a>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="../index.php">Lihat Publik</a>
      <a class="btn btn-danger btn-sm" href="../logout.php">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div class="d-flex justify-content-between mb-3">
    <h4>Dashboard</h4>
    <div>
      <a class="btn btn-primary" href="upload.php">Upload Foto</a>
      <a class="btn btn-secondary" href="kategori.php">Kelola Kategori</a>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card p-3 text-center">
         <h6>Foto</h6>
        <strong><?= $stats['photos'] ?></strong>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3 text-center">
        <h6>Kategori</h6>
        <strong><?= $stats['categories'] ?></strong>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3 text-center">
        <h6>Admin</h6>
        <strong><?= $stats['users'] ?></strong>
      </div>
    </div>
  </div>

  <h5>Daftar Foto Terbaru</h5>
  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>#</th>
          <th>Preview</th>
          <th>Judul</th>
          <th>Kategori</th>
          <th>Tanggal Upload</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($photos->num_rows > 0): ?>
          <?php $i=1; while ($p = $photos->fetch_assoc()): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><img src="../uploads/<?= htmlspecialchars($p['file']) ?>" style="height:50px;object-fit:cover;border-radius:5px"></td>
            <td><?= htmlspecialchars($p['judul']) ?></td>
            <td><?= htmlspecialchars($p['kategori'] ?? '-') ?></td>
            <td><?= htmlspecialchars($p['tanggal_upload']) ?></td>
            <td>
              <a class="btn btn-sm btn-warning" href="edit_foto.php?id=<?= $p['id'] ?>">Edit</a>
              <a class="btn btn-sm btn-danger" href="hapus_foto.php?id=<?= $p['id'] ?>" onclick="return confirm('Hapus foto ini?')">Hapus</a>
            </td>
          </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6" class="text-center">Belum ada foto.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
