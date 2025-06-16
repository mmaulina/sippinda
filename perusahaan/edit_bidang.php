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
    echo "<script>alert('Data tidak ditemukan!');";

    if ($_SESSION['role'] == 'superadmin') {
        echo "window.location.href='?page=profil_admin';";
    } elseif ($_SESSION['role'] == 'umum') {
        echo "window.location.href='?page=profil_perusahaan';";
    } else {
        // Role lain, misalnya kominfo atau instansi, bisa diarahkan ke halaman umum
        echo "window.location.href='?page=beranda';";
    }

    echo "</script>";
    exit();
}


$id = $_GET['id'];
$id_user = $_SESSION['id_user'];
$role = $_SESSION['role'] ?? 'umum'; // default fallback ke 'umum' jika tidak tersedia

$database = new Database();
$pdo = $database->getConnection();

// Ambil data bidang berdasarkan ID
$sql = "SELECT * FROM bidang_perusahaan WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$data_bidang = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data_bidang) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='?page=profil_admin';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    function sanitize_input($data)
    {
        return trim(strip_tags($data));
    }

    $nama_perusahaan = sanitize_input($_POST['nama_perusahaan']);
    $bidang_baru = sanitize_input($_POST['bidang']);

    if (!empty($nama_perusahaan) && !empty($bidang_baru)) {
        $sql = "UPDATE bidang_perusahaan SET nama_perusahaan = ?, bidang = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$nama_perusahaan, $bidang_baru, $id]);

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


<!-- UPDATE BIDANG PERUSAHAAN -->
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Update Bidang Perusahaan</h6>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group mb-2">
                    <label>Nama Perusahaan</label>
                    <input type="text" class="form-control" name="nama_perusahaan" placeholder="Masukkan nama perusahaan" required maxlength="100" value="<?php echo $data_bidang['nama_perusahaan']; ?>">
                    <small class="text-muted">
                        Catatan: nama perusahaan sesuai perizinan
                    </small>
                </div>

                <div class="form-group mb-2">
                    <label>Bidang Perusahaan</label>
                    <input type="text" class="form-control" name="bidang" placeholder="Masukkan bidang perusahaan" required maxlength="100" value="<?php echo $data_bidang['bidang']; ?>">
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                    <a href="javascript:window.history.back();" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>