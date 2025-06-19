<?php
include 'koneksi.php'; // Pastikan koneksi sudah tersedia

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('ID tidak valid!'); window.location='?page=konten_tampil';</script>";
    exit();
}

$id = intval($_GET['id']);

try {
    $db = new Database();
    $conn = $db->getConnection();
    // Periksa apakah data ada dalam database
    $sql = "SELECT konten FROM news WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        echo "<script>alert('Data tidak ditemukan!'); window.location='?page=konten_tampil';</script>";
        exit();
    }
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Hapus file jika ada
    $filePaths = [$row['konten']];
    foreach ($filePaths as $file) {
        if (!empty($file) && file_exists($file)) {
            unlink($file);
        }
    }
    // Hapus data dari database
    $sql = "DELETE FROM news WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        $_SESSION['hasil'] = true;
        $_SESSION['pesan'] = "Berhasil Menghapus Data";
    } else {
        $_SESSION['hasil'] = false;
        $_SESSION['pesan'] = "Gagal Menghapus Data";
    }
    echo "<meta http-equiv='refresh' content='0; url=?page=konten_tampil'>";
} catch (PDOException $e) {
    echo "<script>alert('Kesalahan: " . $e->getMessage() . "'); window.location='?page=konten_tampil';</script>";
}
?>
