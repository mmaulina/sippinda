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

    $pemerintah_pusat = sanitize_input($_POST['pemerintah_pusat']);
    $pemerintah_daerah = sanitize_input($_POST['pemerintah_daerah']);
    $swasta_nasional = sanitize_input($_POST['swasta_nasional']);
    $asing = sanitize_input($_POST['asing']);
    $negara_asal = sanitize_input($_POST['negara_asal']);
    $nilai_investasi_tanah = sanitize_input($_POST['nilai_investasi_tanah']);
    $nilai_investasi_bangunan = sanitize_input($_POST['nilai_investasi_bangunan']);

    try {
        if ($profil && !empty($profil['nama_perusahaan'])) {
            $nama_perusahaan = $profil['nama_perusahaan'];

            $sql = "INSERT INTO investasi (id_user, nama_perusahaan, pemerintah_pusat, pemerintah_daerah, swasta_nasional, asing, negara_asal, nilai_investasi_tanah, nilai_investasi_bangunan) 
                    VALUES (:id_user, :nama_perusahaan, :pemerintah_pusat, :pemerintah_daerah, :swasta_nasional, :asing, :negara_asal, :nilai_investasi_tanah, :nilai_investasi_bangunan)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
            $stmt->bindParam(':nama_perusahaan', $nama_perusahaan, PDO::PARAM_STR);
            $stmt->bindParam(':pemerintah_pusat', $pemerintah_pusat, PDO::PARAM_STR);
            $stmt->bindParam(':pemerintah_daerah', $pemerintah_daerah, PDO::PARAM_STR);
            $stmt->bindParam(':swasta_nasional', $swasta_nasional, PDO::PARAM_STR);
            $stmt->bindParam(':asing', $asing, PDO::PARAM_STR);
            $stmt->bindParam(':negara_asal', $negara_asal, PDO::PARAM_STR);
            $stmt->bindParam(':nilai_investasi_tanah', $nilai_investasi_tanah, PDO::PARAM_STR);
            $stmt->bindParam(':nilai_investasi_bangunan', $nilai_investasi_bangunan, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $redirect = ($role === 'superadmin') ? 'investasi_tampil' : 'profil_perusahaan';
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
<?php
$role = $_SESSION['role'];
$page = ($role === 'superadmin') ? 'investasi_tampil' : 'profil_perusahaan';
?>
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Data Investasi</h6>
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
                    <input type="text" class="form-control" name="nama_perusahaan" placeholder="Masukkan nama perusahaan" required maxlength="100" value="<?= htmlspecialchars($nama_perusahaan) ?>" readonly>
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
                            <input type="text" class="form-control" name="pemerintah_pusat" placeholder="Masukkan Pemerintah Pusat" required maxlength="200">
                        </div>
                        <div class="form-group mb-2">
                            <label>Pemerintah Daerah</label>
                            <input type="text" class="form-control" name="pemerintah_daerah" placeholder="Masukkan Pemerintah Daerah" required maxlength="200">
                        </div>
                        <div class="form-group mb-2">
                            <label>Swasta Nasional</label>
                            <input type="text" class="form-control" name="swasta_nasional" placeholder="Masukkan Swasta Nasional" required maxlength="200">
                        </div>
                        <div class="form-group mb-2">
                            <label>Asing</label>
                            <input type="text" class="form-control" name="asing" placeholder="Masukkan Asing" required maxlength="200">
                        </div>
                        <div class="form-group mb-2">
                            <label>Negara Asal</label>
                            <input type="text" class="form-control" name="negara_asal" placeholder="Masukkan Negara Asal" required maxlength="200">
                        </div>
                        <div class="form-group mb-2">
                            <label>Nilai Investasi Tanah</label>
                            <input type="text" class="form-control" name="nilai_investasi_tanah" placeholder="Masukkan Nilai Investasi Tanah" required maxlength="200">
                        </div>
                        <div class="form-group mb-2">
                            <label>Nilai Investasi Bangunan</label>
                            <input type="text" class="form-control" name="nilai_investasi_bangunan" placeholder="Masukkan Nilai Investasi Bangunan" required maxlength="200">
                        </div>
                    </div>
                </div>

                <!-- Tombol Simpan dan Batal -->
                <div class="mb-0">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="reset" class="btn btn-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>