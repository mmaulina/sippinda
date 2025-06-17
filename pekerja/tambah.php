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

    $laki_laki_pro_tetap = sanitize_input($_POST['laki_laki_pro_tetap']);
    $perempuan_pro_tetap = sanitize_input($_POST['perempuan_pro_tetap']);
    $laki_laki_pro_tidak_tetap = sanitize_input($_POST['laki_laki_pro_tidak_tetap']);
    $perempuan_pro_tidak_tetap = sanitize_input($_POST['perempuan_pro_tidak_tetap']);
    $laki_laki_lainnya = sanitize_input($_POST['laki_laki_lainnya']);
    $perempuan_lainnya = sanitize_input($_POST['perempuan_lainnya']);
    $sd = sanitize_input($_POST['sd']);
    $smp = sanitize_input($_POST['smp']);
    $sma = sanitize_input($_POST['sma']);
    $d1_d2_d3 = sanitize_input($_POST['d1_d2_d3']);
    $s1_d4 = sanitize_input($_POST['s1_d4']);
    $s2 = sanitize_input($_POST['s2']);
    $s3 = sanitize_input($_POST['s3']);

    try {
        if ($profil && !empty($profil['nama_perusahaan'])) {
            $nama_perusahaan = $profil['nama_perusahaan'];

            $sql = "INSERT INTO pekerja (id_user, nama_perusahaan, laki_laki_pro_tetap, perempuan_pro_tetap, laki_laki_pro_tidak_tetap, perempuan_pro_tidak_tetap, laki_laki_lainnya, perempuan_lainnya, sd, smp, sma, d1_d2_d3, s1_d4, s2, s3) 
                    VALUES (:id_user, :nama_perusahaan, :laki_laki_pro_tetap, :perempuan_pro_tetap, :laki_laki_pro_tidak_tetap, :perempuan_pro_tidak_tetap, :laki_laki_lainnya, :perempuan_lainnya, :sd, :smp, :sma, :d1_d2_d3, :s1_d4, :s2, :s3)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
            $stmt->bindParam(':nama_perusahaan', $nama_perusahaan, PDO::PARAM_STR);
            $stmt->bindParam(':laki_laki_pro_tetap', $laki_laki_pro_tetap, PDO::PARAM_STR);
            $stmt->bindParam(':perempuan_pro_tetap', $perempuan_pro_tetap, PDO::PARAM_STR);
            $stmt->bindParam(':laki_laki_pro_tidak_tetap', $laki_laki_pro_tidak_tetap, PDO::PARAM_STR);
            $stmt->bindParam(':perempuan_pro_tidak_tetap', $perempuan_pro_tidak_tetap, PDO::PARAM_STR);
            $stmt->bindParam(':laki_laki_lainnya', $laki_laki_lainnya, PDO::PARAM_STR);
            $stmt->bindParam(':perempuan_lainnya', $perempuan_lainnya, PDO::PARAM_STR);
            $stmt->bindParam(':sd', $sd, PDO::PARAM_STR);
            $stmt->bindParam(':smp', $smp, PDO::PARAM_STR);
            $stmt->bindParam(':sma', $sma, PDO::PARAM_STR);
            $stmt->bindParam(':d1_d2_d3', $d1_d2_d3, PDO::PARAM_STR);
            $stmt->bindParam(':s1_d4', $s1_d4, PDO::PARAM_STR);
            $stmt->bindParam(':s2', $s2, PDO::PARAM_STR);
            $stmt->bindParam(':s3', $s3, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $redirect = ($role === 'superadmin') ? 'pekerja_tampil' : 'profil_perusahaan';
                echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='?page=$redirect';</script>";
            } else {
                echo "<script>alert('Gagal menambahkan Data.');</script>";
            }
        } else {
            echo "<script>alert('Pekerja tidak ditemukan. Silakan lengkapi terlebih dahulu.');</script>";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>


<!-- TAMBAH PROFIL PERUSAHAAN -->
<?php
$role = $_SESSION['role'];
$page = ($role === 'superadmin') ? 'pekerja_tampil' : 'profil_perusahaan';
?>
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Data Pekerja Per Hari</h6>
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
                <!-- Produksi (Tetap) -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold">Produksi (Tetap)</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-2">
                            <label>Laki-Laki</label>
                            <input type="number" class="form-control" name="laki_laki_pro_tetap" placeholder="Masukkan Jumlah Laki-Laki" required maxlength="200">
                        </div>
                        <div class="form-group mb-2">
                            <label>Perempuan</label>
                            <input type="number" class="form-control" name="perempuan_pro_tetap" placeholder="Masukkan Jumlah Perempuan" required maxlength="200">
                        </div>
                    </div>
                </div>
                <!-- Produksi (Tidak Tetap) -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold">Produksi (Tidak Tetap)</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-2">
                            <label>Laki-Laki</label>
                            <input type="number" class="form-control" name="laki_laki_pro_tidak_tetap" placeholder="Masukkan Jumlah Laki-Laki" required maxlength="200">
                        </div>
                        <div class="form-group mb-2">
                            <label>Perempuan</label>
                            <input type="number" class="form-control" name="perempuan_pro_tidak_tetap" placeholder="Masukkan Jumlah Perempuan" required maxlength="200">
                        </div>
                    </div>
                </div>
                <!-- Lainnya (Tidak Tetap) -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold">Lainnya (Tidak Tetap)</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-2">
                            <label>Laki-Laki</label>
                            <input type="number" class="form-control" name="laki_laki_lainnya" placeholder="Masukkan Jumlah Laki-Laki" required maxlength="200">
                        </div>
                        <div class="form-group mb-2">
                            <label>Perempuan</label>
                            <input type="number" class="form-control" name="perempuan_lainnya" placeholder="Masukkan Jumlah Perempuan" required maxlength="200">
                        </div>
                    </div>
                </div>
                <!-- Berdasarkan Tingkat Pendidikan -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold">Berdasarkan Tingkat Pendidikan</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-2">
                            <label>SD</label>
                            <input type="number" class="form-control" name="sd" placeholder="Masukkan Jumlah SD" required maxlength="200">
                        </div>
                        <div class="form-group mb-2">
                            <label>SMP</label>
                            <input type="number" class="form-control" name="smp" placeholder="Masukkan Jumlah SMP" required maxlength="200">
                        </div>
                        <div class="form-group mb-2">
                            <label>SMA</label>
                            <input type="number" class="form-control" name="sma" placeholder="Masukkan Jumlah SMA" required maxlength="200">
                        </div>
                        <div class="form-group mb-2">
                            <label>D1 sampai D3</label>
                            <input type="number" class="form-control" name="d1_d2_d3" placeholder="Masukkan Jumlah D1 sampai D3" required maxlength="200">
                        </div>
                        <div class="form-group mb-2">
                            <label>S1/D4</label>
                            <input type="number" class="form-control" name="s1_d4" placeholder="Masukkan Jumlah S1/D4" required maxlength="200">
                        </div>
                        <div class="form-group mb-2">
                            <label>S2</label>
                            <input type="number" class="form-control" name="s2" placeholder="Masukkan Jumlah S2" required maxlength="200">
                        </div>
                        <div class="form-group mb-2">
                            <label>S3</label>
                            <input type="number" class="form-control" name="s3" placeholder="Masukkan Jumlah S3" required maxlength="200">
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