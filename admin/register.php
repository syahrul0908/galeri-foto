<?php
session_start();
include "../database.php";

$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $cek = $conn->query("SELECT * FROM admin WHERE username='$username'");
    if ($cek->num_rows > 0) {
        $msg = "❌ Username sudah dipakai!";
    } else {
        $conn->query("INSERT INTO admin (username, password) VALUES ('$username', '$password')");
        $msg = "✅ Registrasi berhasil! Silakan login.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Register Admin</title>
  <!-- Font Awesome buat ikon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      background: linear-gradient(-45deg, #89f7fe, #66a6ff, #0052d4, #4364f7);
      background-size: 400% 400%;
      animation: gradientBG 10s ease infinite;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    @keyframes gradientBG {
      0% {background-position: 0% 50%;}
      50% {background-position: 100% 50%;}
      100% {background-position: 0% 50%;}
    }
    .register-box {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.2);
      text-align: center;
      width: 360px;
      animation: fadeIn 1s ease;
    }
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(-20px);}
      to {opacity: 1; transform: translateY(0);}
    }
    .register-box h1 {
      margin-bottom: 25px;
      font-size: 24px;
      color: #333;
    }
    .register-box form {
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .input-group {
      position: relative;
      width: 85%;
      margin: 10px 0;
    }
    .input-group input {
      width: 100%;
      padding: 12px 40px 12px 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
      transition: all 0.3s;
      box-sizing: border-box;
    }
    .input-group input:focus {
      border-color: #2575fc;
      outline: none;
      box-shadow: 0 0 8px rgba(37,117,252,0.3);
    }
    .toggle-password {
      position: absolute;
      top: 50%;
      right: 12px;
      transform: translateY(-50%);
      cursor: pointer;
      font-size: 16px;
      color: #555;
    }
    .register-box button {
      width: 90%;
      padding: 12px;
      margin-top: 15px;
      background: linear-gradient(135deg, #2575fc, #6a11cb);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .register-box button:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.25);
    }
    .message {
      margin-top: 15px;
      font-size: 14px;
    }
    .message.success { color: green; font-weight: 600; }
    .message.error { color: red; font-weight: 600; }
    .register-box p {
      margin-top: 15px;
      font-size: 14px;
    }
    .register-box a {
      color: #2575fc;
      text-decoration: none;
      font-weight: 600;
    }
    .register-box a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="register-box">
    <h1>📝 Register Admin</h1>
    <form method="POST">
      <div class="input-group">
        <input type="text" name="username" placeholder="Username" required>
      </div>
      <div class="input-group">
        <input type="password" name="password" id="password" placeholder="Password" required>
        <i class="fa-solid fa-eye toggle-password" onclick="togglePassword()" id="eyeIcon"></i>
      </div>
      <button type="submit">Register</button>
    </form>
    <?php if ($msg): ?>
      <p class="message <?= strpos($msg, '✅') !== false ? 'success' : 'error' ?>"><?= $msg ?></p>
    <?php endif; ?>
    <p><a href="login.php">⬅ Kembali ke Login</a></p>
  </div>

  <script>
    function togglePassword() {
      const pass = document.getElementById("password");
      const eyeIcon = document.getElementById("eyeIcon");
      if (pass.type === "password") {
        pass.type = "text";
        eyeIcon.classList.remove("fa-eye");
        eyeIcon.classList.add("fa-eye-slash");
      } else {
        pass.type = "password";
        eyeIcon.classList.remove("fa-eye-slash");
        eyeIcon.classList.add("fa-eye");
      }
    }
  </script>
</body>
</html>
