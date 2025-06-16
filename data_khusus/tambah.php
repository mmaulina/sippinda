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

    $nama_penanda_tangan_laporan = sanitize_input($_POST['nama_penanda_tangan_laporan']);
    $jabatan = sanitize_input($_POST['jabatan']);
    $nama_perusahaan_induk = sanitize_input($_POST['nama_perusahaan_induk']);

    try {
        if ($profil && !empty($profil['nama_perusahaan'])) {
            $nama_perusahaan = $profil['nama_perusahaan'];

            $sql = "INSERT INTO data_khusus (id_user, nama_perusahaan, nama_penanda_tangan_laporan, jabatan, nama_perusahaan_induk) 
                    VALUES (:id_user, :nama_perusahaan, :nama_penanda_tangan_laporan, :jabatan, :nama_perusahaan_induk)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
            $stmt->bindParam(':nama_perusahaan', $nama_perusahaan, PDO::PARAM_STR);
            $stmt->bindParam(':nama_penanda_tangan_laporan', $nama_penanda_tangan_laporan, PDO::PARAM_STR);
            $stmt->bindParam(':jabatan', $jabatan, PDO::PARAM_STR);
            $stmt->bindParam(':nama_perusahaan_induk', $nama_perusahaan_induk, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $redirect = ($role === 'superadmin') ? 'data_khusus_tampil' : 'profil_perusahaan';
                echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='?page=$redirect';</script>";
            } else {
                echo "<script>alert('Gagal menambahkan Data.');</script>";
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
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Data Khusus Perusahaan</h6>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group mb-2">
                    <label>Nama Perusahaan</label>
                    <input type="text" class="form-control" name="nama_perusahaan" placeholder="Masukkan nama perusahaan" required maxlength="100" value="<?= htmlspecialchars($nama_perusahaan) ?>" readonly>
                    <small class="text-muted">
                        Catatan: nama perusahaan sesuai perizinan
                    </small>
                </div>
                <div class="form-group mb-2">
                    <label>Nama Penanda Tangan Laporan</label>
                    <input type="text" class="form-control" name="nama_penanda_tangan_laporan" placeholder="Masukkan Nama Penanda Tangan" required maxlength="200"></input>
                </div>
                <div class="form-group mb-2">
                    <label>Jabatan</label>
                    <input type="text" class="form-control" name="jabatan" placeholder="Masukkan Jabatan" required maxlength="200"></input>
                </div>
                <div class="form-group mb-3">
                    <label>Nama Perusahaan Induk</label>
                    <input type="text" class="form-control" name="nama_perusahaan_induk" placeholder="Masukkan Nama Perusahaan Induk" required maxlength="200"></input>
                </div>
                <!-- Tombol Simpan dan Batal -->
                <div class="mb-0">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <?php
                    $role = $_SESSION['role'];
                    $page = ($role === 'superadmin') ? 'data_khusus_tampil' : 'profil_perusahaan';
                    ?>
                    <a href="?page=<?= htmlspecialchars($page); ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>