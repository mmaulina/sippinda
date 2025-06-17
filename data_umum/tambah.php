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

    $periode_laporan = sanitize_input($_POST['periode_laporan']);
    $nilai_investasi_mesin = sanitize_input($_POST['nilai_investasi_mesin']);
    $nilai_investasi_lainnya = sanitize_input($_POST['nilai_investasi_lainnya']);
    $modal_kerja = sanitize_input($_POST['modal_kerja']);
    $investasi_tanpa_tanah_bangunan = sanitize_input($_POST['investasi_tanpa_tanah_bangunan']);
    $status = sanitize_input($_POST['status']);
    $menggunakan_maklon = sanitize_input($_POST['menggunakan_maklon']);
    $menyediakan_maklon = sanitize_input($_POST['menyediakan_maklon']);

    try {
        if ($profil && !empty($profil['nama_perusahaan'])) {
            $nama_perusahaan = $profil['nama_perusahaan'];

            $sql = "INSERT INTO data_umum (id_user, nama_perusahaan, periode_laporan, nilai_investasi_mesin, nilai_investasi_lainnya, modal_kerja, investasi_tanpa_tanah_bangunan, status, menggunakan_maklon, menyediakan_maklon) 
                    VALUES (:id_user, :nama_perusahaan, :periode_laporan, :nilai_investasi_mesin, :nilai_investasi_lainnya, :modal_kerja, :investasi_tanpa_tanah_bangunan, :status, :menggunakan_maklon, :menyediakan_maklon)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
            $stmt->bindParam(':nama_perusahaan', $nama_perusahaan, PDO::PARAM_STR);
            $stmt->bindParam(':periode_laporan', $periode_laporan, PDO::PARAM_STR);
            $stmt->bindParam(':nilai_investasi_mesin', $nilai_investasi_mesin, PDO::PARAM_STR);
            $stmt->bindParam(':nilai_investasi_lainnya', $nilai_investasi_lainnya, PDO::PARAM_STR);
            $stmt->bindParam(':modal_kerja', $modal_kerja, PDO::PARAM_STR);
            $stmt->bindParam(':investasi_tanpa_tanah_bangunan', $investasi_tanpa_tanah_bangunan, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':menggunakan_maklon', $menggunakan_maklon, PDO::PARAM_STR);
            $stmt->bindParam(':menyediakan_maklon', $menyediakan_maklon, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $redirect = ($role === 'superadmin') ? 'data_umum_tampil' : 'profil_perusahaan';
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
$page = ($role === 'superadmin') ? 'data_umum_tampil' : 'profil_perusahaan';
?>
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Data Umum Perusahaan</h6>
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
                <div class="form-group mb-2">
                    <label>Periode Laporan</label>
                    <input type="text" class="form-control" name="periode_laporan" placeholder="Masukkan Periode Laporan" required maxlength="200"></input>
                </div>
                <div class="form-group mb-2">
                    <label>Nilai Investasi Mesin</label>
                    <input type="text" class="form-control" name="nilai_investasi_mesin" placeholder="Masukkan Nilai Investasi Mesin" required maxlength="200"></input>
                </div>
                <div class="form-group mb-2">
                    <label>Nilai Investasi lainnya</label>
                    <input type="text" class="form-control" name="nilai_investasi_lainnya" placeholder="Masukkan Nilai Investasi lainnya" required maxlength="200"></input>
                </div>
                <div class="form-group mb-2">
                    <label>Modal Kerja</label>
                    <input type="text" class="form-control" name="modal_kerja" placeholder="Masukkan Modal Kerja" required maxlength="200"></input>
                </div>
                <div class="form-group mb-2">
                    <label>Investasi Tanpa Tanah dan Bangunan</label>
                    <input type="text" class="form-control" name="investasi_tanpa_tanah_bangunan" placeholder="Masukkan Investasi Tanpa Tanah dan Bangunan" required maxlength="200"></input>
                </div>
                <div class="form-group mb-2">
                    <label>Status</label>
                    <input type="text" class="form-control" name="status" placeholder="Masukkan Status" required maxlength="200"></input>
                </div>
                <div class="form-group mb-3">
                    <label for="menggunakan_maklon">Menggunakan Maklon</label>
                    <select name="menggunakan_maklon" id="menggunakan_maklon" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <option value="iya">Iya</option>
                        <option value="tidak">Tidak</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="menyediakan_maklon">Menyediakan Maklon</label>
                    <select name="menyediakan_maklon" id="menyediakan_maklon" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <option value="iya">Iya</option>
                        <option value="tidak">Tidak</option>
                    </select>
                </div>
                <!-- Tombol Simpan dan Batal -->
                <div class="mb-3">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="reset" class="btn btn-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>