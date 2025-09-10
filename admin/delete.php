<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
include "../database.php";

if (!isset($_GET['id'])) {
    header("Location: foto.php");
    exit;
}

$id = intval($_GET['id']);
$foto = $conn->query("SELECT * FROM galeri WHERE id=$id")->fetch_assoc();

if ($foto) {
    if (file_exists("../uploads/" . $foto['file'])) {
        unlink("../uploads/" . $foto['file']);
    }
    $conn->query("DELETE FROM galeri WHERE id=$id");
}

header("Location: foto.php");
exit;
