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

// Cek apakah ID tersedia di URL
if (!isset($_GET['id'])) {
    $role = $_SESSION['role'] ?? 'umum';
    $redirectPage = ($role === 'superadmin') ? 'profil_admin' : 'profil_perusahaan';
    echo "<script>alert('ID tidak ditemukan!'); window.location.href='?page=$redirectPage';</script>";
    exit();
}

$id = $_GET['id'];
$role = $_SESSION['role'] ?? 'umum'; // fallback jika role tidak tersedia

$database = new Database();
$pdo = $database->getConnection();

// Pastikan data bidang benar-benar ada sebelum menghapus
$sql = "SELECT * FROM data_umum WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='?page=data_umum_tampil';</script>";
    exit();
}

// Eksekusi penghapusan
$sqlDelete = "DELETE FROM data_umum WHERE id = ?";
$stmtDelete = $pdo->prepare($sqlDelete);
$success = $stmtDelete->execute([$id]);

if ($success) {
    echo "<script>alert('Bidang berhasil dihapus!'); window.location.href='?page=" . ($role === 'superadmin' ? 'data_umum_tampil' : 'profil_perusahaan') . "';</script>";
} else {
    echo "<script>alert('Gagal menghapus bidang. Silakan coba lagi.'); window.history.back();</script>";
}
?>
