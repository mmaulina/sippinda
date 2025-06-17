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
        echo "window.location.href='?page=investasi_tampil';";
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
$sql = "SELECT * FROM investasi WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$investasi = $stmt->fetch(PDO::FETCH_ASSOC);

if (!isset($_GET['id'])) {
    echo "<script>alert('Data tidak ditemukan!');";

    if ($_SESSION['role'] == 'superadmin') {
        echo "window.location.href='?page=investasi_tampil';";
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
    $pemerintah_pusat = sanitize_input($_POST['pemerintah_pusat']);
    $pemerintah_daerah = sanitize_input($_POST['pemerintah_daerah']);
    $swasta_nasional = sanitize_input($_POST['swasta_nasional']);
    $asing = sanitize_input($_POST['asing']);
    $negara_asal = sanitize_input($_POST['negara_asal']);
    $nilai_investasi_tanah = sanitize_input($_POST['nilai_investasi_tanah']);
    $nilai_investasi_bangunan = sanitize_input($_POST['nilai_investasi_bangunan']);

    if (!empty($nama_perusahaan)) {
        $sql = "UPDATE investasi SET nama_perusahaan = ?, pemerintah_pusat = ?, pemerintah_daerah = ?, swasta_nasional = ?, asing = ?, negara_asal = ?, nilai_investasi_tanah = ?, nilai_investasi_bangunan = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$nama_perusahaan, $pemerintah_pusat, $pemerintah_daerah, $swasta_nasional, $asing, $negara_asal, $nilai_investasi_tanah, $nilai_investasi_bangunan, $id]);

        if ($success) {
            if ($role === 'superadmin') {
                echo "<script>alert('data berhasil diperbarui!'); window.location.href='?page=investasi_tampil';</script>";
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

<!-- UPDATE DATA INVESTASI -->
<?php
$role = $_SESSION['role'];
$page = ($role === 'superadmin') ? 'investasi_tampil' : 'profil_perusahaan';
?>
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Edit Data Investasi</h6>
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
                    <input type="text" class="form-control" name="nama_perusahaan" placeholder="Masukkan nama perusahaan" required maxlength="100" value="<?php echo $investasi['nama_perusahaan']; ?>" readonly>
                    <small class="text-muted">
                        Catatan: nama perusahaan sesuai perizinan
                    </small>
                </div>
                <!-- Persentase Kepemilikan -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold">Persentase Kepemilikan</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-2">
                            <label>Pemerintah Pusat</label>
                            <input type="text" class="form-control" name="pemerintah_pusat" placeholder="Masukkan Pemerintah Pusat" required maxlength="200" value="<?php echo $investasi['pemerintah_pusat']; ?>"></input>
                        </div>
                        <div class="form-group mb-2">
                            <label>Pemerintah Daerah</label>
                            <input type="text" class="form-control" name="pemerintah_daerah" placeholder="Masukkan Pemerintah Daerah" required maxlength="200" value="<?php echo $investasi['pemerintah_daerah']; ?>"></input>
                        </div>
                        <div class="form-group mb-2">
                            <label>Swasta Nasional</label>
                            <input type="text" class="form-control" name="swasta_nasional" placeholder="Masukkan Swasta Nasional" required maxlength="200" value="<?php echo $investasi['swasta_nasional']; ?>"></input>
                        </div>
                        <div class="form-group mb-2">
                            <label>Asing</label>
                            <input type="text" class="form-control" name="asing" placeholder="Masukkan Asing" required maxlength="200" value="<?php echo $investasi['asing']; ?>"></input>
                        </div>
                        <div class="form-group mb-2">
                            <label>Negara Asal</label>
                            <input type="text" class="form-control" name="negara_asal" placeholder="Masukkan Negara Asal" required maxlength="200" value="<?php echo $investasi['negara_asal']; ?>"></input>
                        </div>
                        <div class="form-group mb-2">
                            <label>Nilai Investasi Tanah</label>
                            <input type="text" class="form-control" name="nilai_investasi_tanah" placeholder="Masukkan Nilai Investasi Tanah" required maxlength="200" value="<?php echo $investasi['nilai_investasi_tanah']; ?>"></input>
                        </div>
                        <div class="form-group mb-2">
                            <label>Nilai Investasi Bangunan</label>
                            <input type="text" class="form-control" name="nilai_investasi_bangunan" placeholder="Masukkan Nilai Investasi Bangunan" required maxlength="200" value="<?php echo $investasi['nilai_investasi_bangunan']; ?>"></input>
                        </div>
                    </div>
                </div>
                <!-- Tombol simpan & batal -->
                <div class="mb-0">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="reset" class="btn btn-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>