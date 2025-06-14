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

if (isset($_GET['id_user'])) {
    $id_user = $_SESSION['id_user']; // ID user dari session
    $id_hapus = $_GET['id_user']; // ID user yang ingin dihapus

    // Pastikan pengguna hanya bisa menghapus profilnya sendiri
    if ($id_user != $id_hapus) {
        echo "<script>alert('Anda tidak memiliki izin untuk menghapus profil ini!'); window.location.href='?page=profil_perusahaan';</script>";
        exit();
    }

    try {
        $db = new Database();
        $conn = $db->getConnection();
        // Periksa apakah profil ada
        $sql_check = "SELECT id_user FROM profil_perusahaan WHERE id_user = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([$id_hapus]);

        if ($stmt_check->rowCount() > 0) {
            // Hapus profil
            $sql_delete = "DELETE FROM profil_perusahaan WHERE id_user = ?";
            $stmt_delete = $conn->prepare($sql_delete);

            if ($stmt_delete->execute([$id_hapus])) {
                echo "<script>alert('Profil berhasil dihapus!'); window.location.href='?page=profil_perusahaan';</script>";
            } else {
                echo "<script>alert('Gagal menghapus profil. Silakan coba lagi.'); window.location.href='?page=profil_perusahaan';</script>";
            }
        } else {
            echo "<script>alert('Profil tidak ditemukan!'); window.location.href='?page=profil_perusahaan';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Terjadi kesalahan: " . $e->getMessage() . "'); window.location.href='?page=profil_perusahaan';</script>";
    }
} else {
    echo "<script>alert('ID Profil tidak valid!'); window.location.href='?page=profil_perusahaan';</script>";
}
?>
