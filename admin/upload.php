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

$kategoriRes = $conn->query("SELECT * FROM kategori ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Upload Foto Baru</title>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background: linear-gradient(135deg, #0c1445, #1a237e);
        color: #334155;
        line-height: 1.6;
        padding: 20px;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .container {
        max-width: 500px;
        width: 100%;
    }
    
    .card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        padding: 35px;
        margin-top: 0;
    }
    
    .header {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .header h1 {
        color: #1e293b;
        font-size: 1.6rem;
        font-weight: 600;
        margin-bottom: 8px;
    }
    
    .header p {
        color: #64748b;
        font-size: 0.95rem;
    }
    
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #3b82f6;
        text-decoration: none;
        font-size: 0.9rem;
        margin-bottom: 15px;
        font-weight: 500;
    }
    
    .back-link:hover {
        color: #2563eb;
    }
    
    .form-group {
        margin-bottom: 22px;
    }
    
    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #374151;
        font-size: 0.92rem;
    }
    
    input[type="text"],
    textarea,
    select,
    input[type="file"] {
        width: 100%;
        padding: 11px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        background: white;
    }
    
    input[type="text"]:focus,
    textarea:focus,
    select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    textarea {
        resize: vertical;
        min-height: 85px;
    }
    
    input[type="file"] {
        padding: 9px;
        background: #f8fafc;
        border: 1px dashed #d1d5db;
    }
    
    input[type="file"]:hover {
        border-color: #3b82f6;
        background: #f0f7ff;
    }
    
    .btn {
        width: 100%;
        padding: 13px;
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 0.96rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 10px;
    }
    
    .btn:hover {
        background: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }
    
    .btn:active {
        transform: translateY(0);
    }
    
    .btn:disabled {
        background: #9ca3af;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    
    .preview {
        margin-top: 15px;
        text-align: center;
    }
    
    .preview img {
        max-width: 100%;
        max-height: 220px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border: 1px solid #e5e7eb;
    }
    
    .spinner {
        width: 16px;
        height: 16px;
        border: 2px solid transparent;
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .error {
        background: #fef2f2;
        color: #dc2626;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 0.9rem;
        border-left: 4px solid #dc2626;
    }

    /* Animasi halus untuk card */
    .card {
        animation: slideUp 0.5s ease-out;
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
</head>
<body>
    <div class="container">
        <a href="foto.php" class="back-link">
            ← Kembali ke Manajemen Foto
        </a>
        
        <div class="card">
            <div class="header">
                <h1>Upload Foto Baru</h1>
                <p>Tambahkan foto baru ke galeri</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" onsubmit="showLoading()">
                <div class="form-group">
                    <label for="judul">Judul Foto</label>
                    <input type="text" id="judul" name="judul" required 
                           placeholder="Masukkan judul foto">
                </div>
                
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" required 
                              placeholder="Deskripsi singkat tentang foto"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="kategori">Kategori</label>
                    <select id="kategori" name="kategori" required>
                        <option value="">Pilih Kategori</option>
                        <?php while($kat = $kategoriRes->fetch_assoc()): ?>
                            <option value="<?= $kat['id'] ?>">
                                <?= htmlspecialchars($kat['nama']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="foto">Pilih Foto</label>
                    <input type="file" id="foto" name="foto" accept="image/*" required 
                           onchange="previewImage(event)">
                    <div class="preview" id="preview"></div>
                </div>
                
                <button type="submit" class="btn" id="submitBtn">
                    📤 Upload Foto
                </button>
            </form>
        </div>
    </div>

<script>
function previewImage(event) {
    const preview = document.getElementById('preview');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        }
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '';
    }
}

function showLoading() {
    const btn = document.getElementById("submitBtn");
    btn.innerHTML = '<div class="spinner"></div> Mengupload...';
    btn.disabled = true;
}
</script>
</body>
</html>