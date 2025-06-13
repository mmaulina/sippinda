<?php
include 'koneksi.php'; // Pastikan koneksi sudah tersedia

// Pastikan ID ada di URL
if (!isset($_GET['id_profil'])) {
    echo "<script>alert('ID pengguna tidak ditemukan!'); window.location='?page=profil_admin';</script>";
    exit();
}

$id_profil = $_GET['id_profil'];

try {
    $db = new Database();
    $conn = $db->getConnection();
    // Periksa apakah pengguna ada dalam database
    $sql_check = "SELECT id_profil FROM profil WHERE id_profil = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([$id_profil]);

    if ($stmt_check->rowCount() == 0) {
        echo "<script>alert('Pengguna tidak ditemukan!'); window.location='?page=profil_admin';</script>";
        exit();
    }

    // Hapus pengguna berdasarkan ID
    $sql_delete = "DELETE FROM profil WHERE id_profil = ?";
    $stmt_delete = $conn->prepare($sql_delete);

    if ($stmt_delete->execute([$id_profil])) {
        echo "<script>alert('Pengguna berhasil dihapus!'); window.location='?page=profil_admin';</script>";
    } else {
        echo "<script>alert('Gagal menghapus pengguna!'); window.location='?page=profil_admin';</script>";
    }
} catch (PDOException $e) {
    echo "<script>alert('Terjadi kesalahan: " . $e->getMessage() . "'); window.location='?page=profil_admin';</script>";
}
?>
