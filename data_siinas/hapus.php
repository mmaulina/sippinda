<?php
include 'koneksi.php'; // Pastikan koneksi sudah tersedia

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('ID tidak valid!'); window.location='?page=perizinan_tampil';</script>";
    exit();
}

$id = intval($_GET['id']);

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Ambil data file terlebih dahulu
    $sql = "SELECT upload_berkas FROM perizinan WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        echo "<script>alert('Data tidak ditemukan!'); window.location='?page=perizinan_tampil';</script>";
        exit();
    }

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

$filepath = $row['upload_berkas']; // Sudah berupa path? atau perlu ditambah 'uploads/'?

if (!empty($filepath) && file_exists($filepath)) {
    if (unlink($filepath)) {
        echo "File berhasil dihapus.";
    } else {
        echo "Gagal menghapus file.";
    }
} else {
    echo "File tidak ditemukan: " . $filepath;
}

    // Hapus data dari database
    $sql = "DELETE FROM perizinan WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['hasil'] = true;
        $_SESSION['pesan'] = "Berhasil Hapus Data";
    } else {
        $_SESSION['hasil'] = false;
        $_SESSION['pesan'] = "Gagal Hapus Data";
    }

    echo "<meta http-equiv='refresh' content='0; url=?page=perizinan_tampil'>";
} catch (PDOException $e) {
    echo "<script>alert('Kesalahan: " . $e->getMessage() . "'); window.location='?page=perizinan_tampil';</script>";
}
?>
