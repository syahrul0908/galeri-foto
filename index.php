<?php
include "includes/header.php";
include "database.php";

// Ambil kategori
$kategoriRes = $conn->query("SELECT * FROM kategori ORDER BY nama ASC");

// Cek filter kategori
$filter = isset($_GET['kategori']) ? intval($_GET['kategori']) : 0;

$sql = "SELECT galeri.*, kategori.nama AS kategori 
        FROM galeri 
        LEFT JOIN kategori ON galeri.kategori_id = kategori.id";
if ($filter > 0) {
    $sql .= " WHERE galeri.kategori_id = $filter";
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
  background: linear-gradient(-45deg, #6a11cb, #b91d73, #2575fc, #00c6ff);
  background-size: 600% 600%;
  animation: waveBackground 8s ease infinite;
  padding: 40px 20px;
  text-align: center;
  border-radius: 12px;
  color: white;
  margin-bottom: 30px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.35);
}
@keyframes waveBackground {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
h1 { font-size:2.5rem; font-weight:bold; margin:0; text-shadow:0 2px 6px rgba(0,0,0,0.3); }
.typing { border-right: 2px solid #fff; display:inline-block; white-space:nowrap; overflow:hidden; animation: caret 0.8s infinite; }
@keyframes caret { 50% { border-color: transparent; } }

/* Tombol kategori */
.kategori-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  justify-content: center;
  margin-bottom: 30px;
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
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff;
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(99,102,241,0.4);
}
.kategori-buttons a.active {
  background: linear-gradient(135deg, #4f46e5, #9333ea);
  color: #fff;
  border: 2px solid transparent;
  box-shadow: 0 6px 12px rgba(79,70,229,0.5);
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
  <h1><span id="typing" class="typing"></span></h1>
</div>

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
  <p style="text-align:center; color:#333;">Belum ada foto di kategori ini.</p>
<?php endif; ?>

<script>
  // Typing efek judul
  const text = "📸 Galeri Foto";
  let i=0;
  function typeEffect(){
    if(i<text.length){
      document.getElementById("typing").innerHTML+=text.charAt(i);
      i++;
      setTimeout(typeEffect,100);
    }
  }
  window.onload = typeEffect;

  // Fade-in animasi kartu
  const cards = document.querySelectorAll('.card');
  window.addEventListener('load', ()=>{
    cards.forEach((card,index)=>{
      setTimeout(()=>{ card.classList.add('show'); }, index*100);
    });
  });
</script>

<?php include "includes/footer.php"; ?>
