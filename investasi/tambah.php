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
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Data Investasi</h6>
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
                            <input type="text" class="form-control" name="periode_laporan" placeholder="Masukkan Pemerintah Pusat" required maxlength="200">
                        </div>
                        <div class="form-group mb-2">
                            <label>Pemerintah Daerah</label>
                            <input type="text" class="form-control" name="nilai_investasi_mesin" placeholder="Masukkan Pemerintah Daerah" required maxlength="200">
                        </div>
                        <div class="form-group mb-2">
                            <label>Swasta Nasional</label>
                            <input type="text" class="form-control" name="nilai_investasi_lainnya" placeholder="Masukkan Swasta Nasional" required maxlength="200">
                        </div>
                        <div class="form-group mb-2">
                            <label>Asing</label>
                            <input type="text" class="form-control" name="modal_kerja" placeholder="Masukkan Asing" required maxlength="200">
                        </div>
                        <div class="form-group mb-2">
                            <label>Negara Asal</label>
                            <input type="text" class="form-control" name="investasi_tanpa_tanah_bangunan" placeholder="Masukkan Negara Asal" required maxlength="200">
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
                    <?php
                    $role = $_SESSION['role'];
                    $page = ($role === 'superadmin') ? 'profil_admin' : 'profil_perusahaan';
                    ?>
                    <a href="?page=<?= htmlspecialchars($page); ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>