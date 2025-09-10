<?php
session_start();
include "../database.php";

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM admin WHERE username='$username' LIMIT 1");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_user'] = $row['username'];
            header("Location: ../index.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login Admin</title>
  <style>
    body {
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      background: linear-gradient(-45deg, #6a11cb, #b91d73, #2575fc, #00c6ff);
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

    .login-box {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.2);
      text-align: center;
      width: 350px;
      animation: fadeIn 1s ease;
    }

    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(-20px);}
      to {opacity: 1; transform: translateY(0);}
    }

    .login-box h1 {
      margin-bottom: 25px;
      font-size: 24px;
      color: #333;
    }

    .login-box input {
      width: 100%;
      padding: 12px 15px;
      margin: 10px 0;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
      transition: all 0.3s;
    }

    .login-box input:focus {
      border-color: #6a11cb;
      outline: none;
      box-shadow: 0 0 8px rgba(106,17,203,0.3);
    }

    .login-box button {
      width: 100%;
      padding: 12px;
      margin-top: 15px;
      background: linear-gradient(135deg, #6a11cb, #b91d73, #2575fc);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .login-box button:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.25);
    }

    .error {
      color: red;
      margin-top: 10px;
      font-size: 14px;
    }

    .login-box p {
      margin-top: 15px;
      font-size: 14px;
    }

    .login-box a {
      color: #6a11cb;
      text-decoration: none;
      font-weight: 600;
    }
    .login-box a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h1>🔐 Login Admin</h1>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <p class="error"><?= $error ?></p>
    <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
  </div>
</body>
</html>
