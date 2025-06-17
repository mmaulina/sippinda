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
$sql = "SELECT * FROM data_khusus WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$data_khusus = $stmt->fetch(PDO::FETCH_ASSOC);

if (!isset($_GET['id'])) {
    echo "<script>alert('Data tidak ditemukan!');";

    if ($_SESSION['role'] == 'superadmin') {
        echo "window.location.href='?page=data_khusus_tampil';";
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
    $nama_penanda_tangan_laporan = sanitize_input($_POST['nama_penanda_tangan_laporan']);
    $jabatan = sanitize_input($_POST['jabatan']);
    $nama_perusahaan_induk = sanitize_input($_POST['nama_perusahaan_induk']);

    if (!empty($nama_perusahaan)) {
        $sql = "UPDATE data_khusus SET nama_perusahaan = ?, nama_penanda_tangan_laporan = ?, jabatan = ?, nama_perusahaan_induk = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$nama_perusahaan, $nama_penanda_tangan_laporan, $jabatan, $nama_perusahaan_induk, $id]);

        if ($success) {
            if ($role === 'superadmin') {
                echo "<script>alert('data berhasil diperbarui!'); window.location.href='?page=data_khusus_tampil';</script>";
            } else {
                echo "<script>alert('data berhasil diperbarui!'); window.location.href='?page=profil_perusahaan';</script>";
            }
        } else {
            echo "<script>alert('Gagal memperbarui data. Silakan coba lagi.');</script>";
        }
    } else {
        echo "<script>alert('Nama perusahaan dan data tidak boleh kosong!');</script>";
    }
}
?>

<?php
$role = $_SESSION['role'];
$page = ($role === 'superadmin') ? 'data_khusus_tampil' : 'profil_perusahaan';
?>
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Edit Data Khusus Perusahaan</h6>
            <a href="?page=<?= htmlspecialchars($page); ?>" class="btn btn-primary btn-icon-split btn-sm">
                <span class="icon text-white-50">
                    <i class="fas fa-arrow-left" style="vertical-align: middle; margin-top: 5px;"></i>
                </span>
                <span class="text">Kembali</span>
            </a>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group mb-2">
                    <label>Nama Perusahaan</label>
                    <input type="text" class="form-control" name="nama_perusahaan" placeholder="Masukkan nama perusahaan" required maxlength="100" value="<?php echo $data_khusus['nama_perusahaan']; ?>" readonly>
                    <small class="text-muted">
                        Catatan: nama perusahaan sesuai perizinan
                    </small>
                </div>
                <div class="form-group mb-2">
                    <label>Nama Penanda Tangan Laporan</label>
                    <input type="text" class="form-control" name="nama_penanda_tangan_laporan" placeholder="Masukkan Nama Penanda Tangan Laporan" required maxlength="200" value="<?php echo $data_khusus['nama_penanda_tangan_laporan']; ?>"></input>
                </div>
                <div class="form-group mb-2">
                    <label>Jabatan</label>
                    <input type="text" class="form-control" name="jabatan" placeholder="Masukkan Jabatan" required maxlength="200" value="<?php echo $data_khusus['jabatan']; ?>"></input>
                </div>
                <div class="form-group mb-2">
                    <label>Nama Perusahaan Induk</label>
                    <input type="text" class="form-control" name="nama_perusahaan_induk" placeholder="Masukkan Nama Perusahaan Induk" required maxlength="200" value="<?php echo $data_khusus['nama_perusahaan_induk']; ?>"></input>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="reset" class="btn btn-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>