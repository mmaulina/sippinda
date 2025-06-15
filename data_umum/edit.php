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

if (!isset($_GET['id'])) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='?page=profil_admin';</script>";
    exit();
}

$id = $_GET['id'];
$id_user = $_SESSION['id_user'];
$role = $_SESSION['role'] ?? 'umum'; // default fallback ke 'umum' jika tidak tersedia

$database = new Database();
$pdo = $database->getConnection();

// Ambil data bidang berdasarkan ID
$sql = "SELECT * FROM data_umum WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$data_umum = $stmt->fetch(PDO::FETCH_ASSOC);

if (!isset($_GET['id'])) {
    echo "<script>alert('Data tidak ditemukan!');";

    if ($_SESSION['role'] == 'superadmin') {
        echo "window.location.href='?page=data_umum_tampil';";
    } elseif ($_SESSION['role'] == 'umum') {
        echo "window.location.href='?page=profil_perusahaan';";
    } else {
        // Role lain, misalnya kominfo atau instansi, bisa diarahkan ke halaman umum
        echo "window.location.href='?page=beranda';";
    }

    echo "</script>";
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    function sanitize_input($data)
    {
        return trim(strip_tags($data));
    }

    $nama_perusahaan = sanitize_input($_POST['nama_perusahaan']);
    $periode_laporan = sanitize_input($_POST['periode_laporan']);
    $nilai_investasi_mesin = sanitize_input($_POST['nilai_investasi_mesin']);
    $nilai_investasi_lainnya = sanitize_input($_POST['nilai_investasi_lainnya']);
    $modal_kerja = sanitize_input($_POST['modal_kerja']);
    $periode_laporan = sanitize_input($_POST['periode_laporan']);

    if (!empty($nama_perusahaan)) {
        $sql = "UPDATE bidang_perusahaan SET nama_perusahaan = ?, bidang = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$nama_perusahaan, $periode_laporan, $id]);

        if ($success) {
            if ($role === 'superadmin') {
                echo "<script>alert('Bidang berhasil diperbarui!'); window.location.href='?page=profil_admin';</script>";
            } else {
                echo "<script>alert('Bidang berhasil diperbarui!'); window.location.href='?page=profil_perusahaan';</script>";
            }
        } else {
            echo "<script>alert('Gagal memperbarui bidang. Silakan coba lagi.');</script>";
        }
    } else {
        echo "<script>alert('Nama perusahaan dan bidang tidak boleh kosong!');</script>";
    }
}
?>