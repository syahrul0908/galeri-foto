<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Galeri Foto</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
    }
    header {
      background: linear-gradient(135deg, #0c1445, #1a237e, #283593);
      color: white;
      padding: 15px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    .logo {
      font-size: 22px;
      font-weight: bold;
      letter-spacing: 1px;
    }
    nav {
      display: flex;
      gap: 20px;
    }
    nav a {
      text-decoration: none;
      color: white;
      font-weight: 500;
      transition: color 0.3s ease;
    }
    nav a:hover {
      color: #ffeb3b;
    }

    /* Tombol hamburger (hanya muncul di hp) */
    .hamburger {
      display: none;
      flex-direction: column;
      cursor: pointer;
      gap: 5px;
    }
    .hamburger span {
      width: 25px;
      height: 3px;
      background: white;
      border-radius: 3px;
    }

    /* Responsif */
    @media (max-width: 768px) {
      nav {
        position: absolute;
        top: 60px;
        right: 0;
        background: rgba(12, 20, 69, 0.95);
        flex-direction: column;
        gap: 15px;
        padding: 20px;
        display: none;
      }
      nav.show {
        display: flex;
      }
      .hamburger {
        display: flex;
      }
    }

    hr {
      margin: 0;
      border: none;
      border-top: 1px solid #ddd;
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">GALERI</div>
    <div class="hamburger" onclick="toggleMenu()">
      <span></span>
      <span></span>
      <span></span>
    </div>
    <nav id="menu">
      <a href="index.php">🏠 Galeri</a>
      <?php if (isset($_SESSION['admin_id'])): ?>
          <a href="admin/foto.php">⚙️ Dashboard Admin</a>
          <a href="admin/logout.php">🚪 Logout</a>
      <?php else: ?>
          <a href="admin/login.php">🔑 Login</a>
          <a href="admin/register.php">📝 Register</a>
      <?php endif; ?>
    </nav>
  </header>
  <hr>

  <script>
    function toggleMenu() {
      document.getElementById("menu").classList.toggle("show");
    }
  </script>