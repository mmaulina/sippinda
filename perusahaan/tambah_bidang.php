<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "koneksi.php";

if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!'); window.location.href='login/login.php';</script>";
    exit();
}

$id_user = $_SESSION['id_user'];
$role = $_SESSION['role'];
$nama_perusahaan = '';

try {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "SELECT nama_perusahaan FROM profil_perusahaan WHERE id_user = :id_user";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    $stmt->execute();
    $profil = $stmt->fetch(PDO::FETCH_ASSOC);
    $nama_perusahaan = $profil['nama_perusahaan'] ?? '';
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    function sanitize_input($data)
    {
        return trim(strip_tags($data));
    }

    $bidang = sanitize_input($_POST['bidang']);

    try {
        if ($profil && !empty($profil['nama_perusahaan'])) {
            $nama_perusahaan = $profil['nama_perusahaan'];

            $sql = "INSERT INTO bidang_perusahaan (id_user, nama_perusahaan, bidang) 
                    VALUES (:id_user, :nama_perusahaan, :bidang)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
            $stmt->bindParam(':nama_perusahaan', $nama_perusahaan, PDO::PARAM_STR);
            $stmt->bindParam(':bidang', $bidang, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $redirect = ($role === 'superadmin') ? 'profil_admin' : 'profil_perusahaan';
                echo "<script>alert('Bidang Perusahaan berhasil ditambahkan!'); window.location.href='?page=$redirect';</script>";
            } else {
                echo "<script>alert('Gagal menambahkan Bidang Perusahaan.');</script>";
            }
        } else {
            echo "<script>alert('Profil perusahaan tidak ditemukan. Silakan lengkapi terlebih dahulu.');</script>";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>


<!-- TAMBAH PROFIL PERUSAHAAN -->
<?php
$role = $_SESSION['role'];
$page = ($role === 'superadmin') ? 'profil_admin' : 'profil_perusahaan';
?>
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Bidang Perusahaan</h6>
            <a href="?page=<?= htmlspecialchars($page); ?>" class="btn btn-primary btn-icon-split btn-sm">
                <span class="icon text-white-50">
                    <i class="fas fa-arrow-left" style="vertical-align: middle; margin-top: 5px;"></i>
                </span>
                <span class="text">Kembali</span>
            </a>
        </div>
        <div class="card shadow">
            <div class="card-body">
                <form method="POST">
                    <div class="form-group mb-2">
                        <label>Nama Perusahaan</label>
                        <input type="text" class="form-control" name="nama_perusahaan" placeholder="Masukkan nama perusahaan" required maxlength="100" value="<?= htmlspecialchars($nama_perusahaan) ?>" readonly>
                        <small class="text-muted">
                            Catatan: nama perusahaan sesuai perizinan
                        </small>
                    </div>
                    <div class="form-group mb-3">
                        <label>Bidang Perusahaan</label>
                        <input type="text" class="form-control" name="bidang" placeholder="Masukkan Bidang Perusahaan" required maxlength="200"></input>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <button type="reset" class="btn btn-secondary">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>