<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "koneksi.php";

// Pastikan pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!'); window.location.href='login/login.php';</script>";
    exit();
}


$id = $_GET['id_user'];

$database = new Database();
$pdo = $database->getConnection();

// Pastikan data bidang benar-benar ada sebelum menghapus
$sql = "SELECT * FROM users WHERE id_user = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='?page=pengguna_tampil';</script>";
    exit();
}

// Eksekusi penghapusan
$sqlDelete = "DELETE FROM users WHERE id_user = ?";
$stmtDelete = $pdo->prepare($sqlDelete);
$success = $stmtDelete->execute([$id]);

if ($success) {
    echo "<script>alert('Data berhasil dihapus!'); window.location.href='?page=pengguna_tampil';</script>";
} else {
    echo "<script>alert('Gagal menghapus bidang. Silakan coba lagi.'); window.history.back();</script>";
}
?>
