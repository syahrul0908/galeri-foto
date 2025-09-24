<?php
include "includes/header.php";
include "database.php";

// Ambil kategori
$kategoriRes = $conn->query("SELECT * FROM kategori ORDER BY nama ASC");

// Cek filter kategori
$filter = isset($_GET['kategori']) ? intval($_GET['kategori']) : 0;

// Cek pencarian
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

$sql = "SELECT galeri.*, kategori.nama AS kategori 
        FROM galeri 
        LEFT JOIN kategori ON galeri.kategori_id = kategori.id
        WHERE 1=1";

if ($filter > 0) {
    $sql .= " AND galeri.kategori_id = $filter";
}
if (!empty($search)) {
    $searchSafe = $conn->real_escape_string($search);
    $sql .= " AND (galeri.judul LIKE '%$searchSafe%' OR galeri.deskripsi LIKE '%$searchSafe%')";
}

$sql .= " ORDER BY galeri.id DESC";
$result = $conn->query($sql);
?>

<style>
body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 20px;
  background: #f4f6f9;
}

/* Judul */
.title-container {
  background: linear-gradient(-45deg, #0c1445, #1a237e, #283593, #303f9f);
  background-size: 600% 600%;
  animation: waveBackground 8s ease infinite;
  padding: 40px 20px;
  text-align: center;
  border-radius: 12px;
  color: white;
  margin-bottom: 30px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.35);
  position: relative;
  overflow: hidden;
}
@keyframes waveBackground {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

.title-container h1 {
  font-size: 3rem;
  font-weight: bold;
  margin: 0;
  color: #ffffff;
  text-shadow: 
    0 0 10px rgba(255, 255, 255, 0.8),
    0 0 20px rgba(255, 255, 255, 0.6),
    0 0 30px rgba(255, 255, 255, 0.4);
  letter-spacing: 2px;
  position: relative;
  z-index: 2;
}

/* Efek cahaya tambahan */
.title-container::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
  animation: rotate 10s linear infinite;
}

@keyframes rotate {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Container kategori + search */
.kategori-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 15px;
  margin-bottom: 30px;
}

/* Tombol kategori */
.kategori-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}
.kategori-buttons a {
  padding: 10px 20px;
  border-radius: 999px;
  background: #fff;
  color: #444;
  text-decoration: none;
  font-weight: 600;
  font-size: 14px;
  border: 2px solid transparent;
  transition: all 0.3s ease;
  box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}
.kategori-buttons a:hover {
  background: linear-gradient(135deg, #1a237e, #283593);
  color: #fff;
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(26,35,126,0.4);
}
.kategori-buttons a.active {
  background: linear-gradient(135deg, #0c1445, #1a237e);
  color: #fff;
  border: 2px solid transparent;
  box-shadow: 0 6px 12px rgba(12,20,69,0.5);
}

/* Search bar */
.search-bar {
  display: flex;
  gap: 8px;
}
.search-bar input[type="text"] {
  padding: 8px 14px;
  border-radius: 999px;
  border: 1px solid #ccc;
  outline: none;
}
.search-bar input[type="text"]:focus {
  border-color: #1a237e;
  box-shadow: 0 0 6px rgba(26,35,126,0.3);
}
.search-bar button {
  padding: 8px 16px;
  border: none;
  border-radius: 999px;
  background: linear-gradient(135deg, #1a237e, #283593);
  color: #fff;
  font-weight: bold;
  cursor: pointer;
}
.search-bar button:hover {
  background: linear-gradient(135deg, #0c1445, #1a237e);
}

/* Galeri pakai Grid */
.gallery-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}
.card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.12);
  opacity: 0;
  transform: scale(0.9);
  transition: all 0.4s ease;
  overflow: hidden;
}
.card.show { opacity: 1; transform: scale(1); }
.card:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: 0 12px 24px rgba(0,0,0,0.25);
}
.card img {
  width: 100%;
  height: 220px;
  object-fit: cover;
  display: block;
  border-radius: 12px 12px 0 0;
  transition: transform 0.4s ease;
}
.card:hover img { transform: scale(1.05); }

.card .info {
  padding: 12px 15px;
}
.card h3 { margin: 0; font-size:16px; font-weight:bold; color:#333; }
.card p { margin: 4px 0 0; font-size:13px; color:#666; }
</style>

<div class="title-container">
  <h1>📸 Galeri Foto</h1>
</div>

<!-- Container kategori + search -->
<div class="kategori-container">
  <!-- Tombol kategori -->
  <div class="kategori-buttons">
    <a href="index.php" class="<?= $filter === 0 ? 'active' : '' ?>">Semua</a>
    <?php while ($kat = $kategoriRes->fetch_assoc()): ?>
      <a href="index.php?kategori=<?= $kat['id'] ?>" 
         class="<?= $filter == $kat['id'] ? 'active' : '' ?>">
         <?= htmlspecialchars($kat['nama']) ?>
      </a>
    <?php endwhile; ?>
  </div>

  <!-- Search bar -->
  <form method="GET" class="search-bar">
    <?php if ($filter > 0): ?>
      <input type="hidden" name="kategori" value="<?= $filter ?>">
    <?php endif; ?>
    <input type="text" name="search" placeholder="🔍 Cari foto..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Cari</button>
  </form>
</div>

<?php if ($result->num_rows > 0): ?>
  <div class="gallery-container" id="gallery">
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="card">
        <a href="detail.php?id=<?= $row['id'] ?>">
          <img src="uploads/<?= htmlspecialchars($row['file']) ?>" alt="<?= htmlspecialchars($row['judul']) ?>">
        </a>
        <div class="info">
          <h3><?= htmlspecialchars($row['judul']) ?></h3>
          <p><?= $row['kategori'] ?? "Tanpa Kategori" ?></p>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
<?php else: ?>
  <p style="text-align:center; color:#333;">❌ Tidak ada foto ditemukan.</p>
<?php endif; ?>

<script>
  // Fade-in animasi kartu
  const cards = document.querySelectorAll('.card');
  window.addEventListener('load', ()=>{
    cards.forEach((card,index)=>{
      setTimeout(()=>{ card.classList.add('show'); }, index*100);
    });
  });
</script>

<?php include "includes/footer.php"; ?>