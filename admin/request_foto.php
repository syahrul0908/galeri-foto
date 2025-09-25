<?php

session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
include "../database.php";

// Approve foto
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $req = $conn->query("SELECT * FROM request_foto WHERE id=$id AND status='pending'")->fetch_assoc();
    if ($req) {
        // Ganti 'tanggal' dengan nama kolom tanggal di tabel galeri, misal 'tanggal_upload'
        $stmt = $conn->prepare("INSERT INTO galeri (judul, deskripsi, kategori_id, file, tanggal_upload) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssis", $req['judul'], $req['deskripsi'], $req['kategori_id'], $req['file']);
        $stmt->execute();
        // Update status request
        $conn->query("UPDATE request_foto SET status='approved' WHERE id=$id");
        $msg = "Foto berhasil disetujui dan masuk galeri.";
    }
}

// Reject foto
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $conn->query("UPDATE request_foto SET status='rejected' WHERE id=$id");
    $msg = "Foto berhasil ditolak.";
}

// Ambil request foto pending
$result = $conn->query("SELECT request_foto.*, kategori.nama AS kategori FROM request_foto LEFT JOIN kategori ON request_foto.kategori_id = kategori.id WHERE status='pending' ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Foto Publik</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --blue-dark: #1e3a8a;
            --blue-medium: #3b82f6;
            --blue-light: #60a5fa;
            --blue-very-light: #dbeafe;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --light: #f8fafc;
            --dark: #1e293b;
            --gray: #64748b;
            --light-gray: #f1f5f9;
            --border-radius: 12px;
            --box-shadow: 0 4px 20px rgba(30, 58, 138, 0.15);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-title {
            background: linear-gradient(135deg, var(--blue-dark), var(--blue-medium));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2.2rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-title i {
            background: linear-gradient(135deg, var(--blue-dark), var(--blue-medium));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: linear-gradient(135deg, var(--blue-dark), var(--blue-medium));
            color: white;
            text-decoration: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
        }

        .back-btn:hover {
            background: linear-gradient(135deg, var(--blue-medium), var(--blue-light));
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(59, 130, 246, 0.4);
        }

        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid var(--blue-very-light);
        }

        .card-header {
            padding: 20px 25px;
            background: linear-gradient(135deg, var(--blue-dark), var(--blue-medium));
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pending-count {
            background: rgba(255, 255, 255, 0.3);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .card-body {
            padding: 25px;
        }

        .message {
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            background: #d1fae5;
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        .table-responsive {
            overflow-x: auto;
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: linear-gradient(135deg, var(--blue-very-light), #e0f2fe);
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            color: var(--blue-dark);
            border-bottom: 2px solid var(--blue-light);
        }

        td {
            padding: 15px 12px;
            border-bottom: 1px solid var(--light-gray);
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background: var(--blue-very-light);
        }

        .img-thumb {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid var(--blue-very-light);
            transition: var(--transition);
            box-shadow: 0 2px 8px rgba(30, 58, 138, 0.1);
        }

        .img-thumb:hover {
            transform: scale(1.05);
            border-color: var(--blue-medium);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .judul {
            font-weight: 600;
            color: var(--blue-dark);
        }

        .deskripsi {
            color: var(--gray);
            font-size: 0.9rem;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .kategori {
            background: var(--blue-very-light);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            color: var(--blue-dark);
            font-weight: 500;
            border: 1px solid var(--blue-light);
        }

        .tanggal {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: var(--transition);
            font-size: 0.9rem;
            text-decoration: none;
        }

        .btn-approve {
            background: linear-gradient(135deg, var(--success), #34d399);
            color: white;
        }

        .btn-approve:hover {
            background: linear-gradient(135deg, #059669, var(--success));
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(16, 185, 129, 0.4);
        }

        .btn-reject {
            background: linear-gradient(135deg, var(--danger), #f87171);
            color: white;
        }

        .btn-reject:hover {
            background: linear-gradient(135deg, #dc2626, var(--danger));
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(239, 68, 68, 0.4);
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray);
        }

        .no-data i {
            font-size: 4rem;
            margin-bottom: 15px;
            background: linear-gradient(135deg, var(--blue-medium), var(--blue-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .no-data h3 {
            margin-bottom: 10px;
            color: var(--blue-dark);
            font-size: 1.5rem;
        }

        .no-data p {
            color: var(--gray);
            font-size: 1rem;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            color: var(--gray);
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .card-header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
            
            th, td {
                padding: 12px 8px;
                font-size: 0.9rem;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .judul, .deskripsi {
                max-width: 150px;
            }
            
            .img-thumb {
                width: 60px;
                height: 45px;
            }
            
            .page-title {
                font-size: 1.8rem;
            }
        }

        /* Animasi untuk elemen yang muncul */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            animation: fadeIn 0.5s ease;
        }

        tr {
            animation: fadeIn 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="page-title"><i class="fas fa-camera-retro"></i> Request Foto Publik</h1>
            <a href="foto.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Kembali ke Manajemen Foto
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-clock"></i> Menunggu Persetujuan</h2>
                <span class="pending-count"><?= $result->num_rows ?> Permintaan</span>
            </div>
            
            <div class="card-body">
                <?php if (!empty($msg)): ?>
                    <div class="message">
                        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($msg) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Foto</th>
                                    <th>Judul</th>
                                    <th>Deskripsi</th>
                                    <th>Kategori</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <a href="../uploads/<?= htmlspecialchars($row['file']) ?>" target="_blank">
                                            <img src="../uploads/<?= htmlspecialchars($row['file']) ?>" class="img-thumb" alt="foto">
                                        </a>
                                    </td>
                                    <td>
                                        <div class="judul"><?= htmlspecialchars($row['judul']) ?></div>
                                    </td>
                                    <td>
                                        <div class="deskripsi"><?= htmlspecialchars($row['deskripsi']) ?></div>
                                    </td>
                                    <td>
                                        <span class="kategori"><?= htmlspecialchars($row['kategori'] ?? '-') ?></span>
                                    </td>
                                    <td>
                                        <div class="tanggal"><?= date('d-m-Y H:i', strtotime($row['tanggal'])) ?></div>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="?approve=<?= $row['id'] ?>" class="btn btn-approve" onclick="return confirm('Setujui foto ini dan tampilkan di galeri?')">
                                                <i class="fas fa-check"></i> Setujui
                                            </a>
                                            <a href="?reject=<?= $row['id'] ?>" class="btn btn-reject" onclick="return confirm('Tolak foto ini?')">
                                                <i class="fas fa-times"></i> Tolak
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-inbox"></i>
                        <h3>Tidak ada request foto</h3>
                        <p>Tidak ada request foto publik yang menunggu persetujuan.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; <?= date('Y') ?> Sistem Manajemen Galeri Foto</p>
        </div>
    </div>

    <script>
        // Tambahkan efek hover yang lebih halus
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
            
            // Auto-hide pesan sukses setelah 5 detik
            const message = document.querySelector('.message');
            if (message) {
                setTimeout(() => {
                    message.style.opacity = '0';
                    message.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => {
                        message.style.display = 'none';
                    }, 500);
                }, 5000);
            }
            
            // Animasi untuk baris tabel
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach((row, index) => {
                row.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
</body>
</html>