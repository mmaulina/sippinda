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
        echo "window.location.href='?page=pekerja_tampil';";
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
$sql = "SELECT * FROM pekerja WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$pekerja = $stmt->fetch(PDO::FETCH_ASSOC);

if (!isset($_GET['id'])) {
    echo "<script>alert('Data tidak ditemukan!');";

    if ($_SESSION['role'] == 'superadmin') {
        echo "window.location.href='?page=pekerja_tampil';";
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

    if (!empty($nama_perusahaan)) {
        $sql = "UPDATE pekerja SET nama_perusahaan = ?, laki_laki_pro_tetap = ?, perempuan_pro_tetap = ?, laki_laki_pro_tidak_tetap = ?, perempuan_pro_tidak_tetap = ?, laki_laki_lainnya = ?, perempuan_lainnya = ?, sd = ?, smp = ?, sma = ?, d1_d2_d3 = ?, s1_d4 = ?, s2 = ?, s3 = ?, WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$nama_perusahaan, $laki_laki_pro_tetap, $perempuan_pro_tetap, $laki_laki_pro_tidak_tetap, $perempuan_pro_tidak_tetap, $laki_laki_lainnya, $perempuan_lainnya, $sd, $smp, $sma, $d1_d2_d3, $s1_d4, $s2, $s3, $id]);

        if ($success) {
            if ($role === 'superadmin') {
                echo "<script>alert('data berhasil diperbarui!'); window.location.href='?page=dpekerja_tampil';</script>";
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

<!-- UPDATE PROFIL PERUSAHAAN -->
<?php
$role = $_SESSION['role'];
$page = ($role === 'superadmin') ? 'pekerja_tampil' : 'profil_perusahaan';
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
                    <input type="text" class="form-control" name="nama_perusahaan" placeholder="Masukkan nama perusahaan" required maxlength="100" value="<?php echo $pekerja['nama_perusahaan']; ?>" readonly>
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
                            <input type="number" class="form-control" name="laki_laki_pro_tetap" placeholder="Masukkan Jumlah Laki-Laki" required maxlength="200" value="<?php echo $pekerja['laki_laki_pro_tetap']; ?>"></input>
                        </div>
                        <div class="form-group mb-2">
                            <label>Perempuan</label>
                            <input type="number" class="form-control" name="perempuan_pro_tetap" placeholder="Masukkan Jumlah Perempuan" required maxlength="200" value="<?php echo $pekerja['perempuan_pro_tetap']; ?>"></input>
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
                            <input type="number" class="form-control" name="laki_laki_pro_tidak_tetap" placeholder="Masukkan Jumlah Laki-Laki" required maxlength="200" value="<?php echo $pekerja['laki_laki_pro_tidak_tetap']; ?>"></input>
                        </div>
                        <div class="form-group mb-2">
                            <label>Perempuan</label>
                            <input type="number" class="form-control" name="perempuan_pro_tidak_tetap" placeholder="Masukkan Jumlah Perempuan" required maxlength="200" value="<?php echo $pekerja['perempuan_pro_tidak_tetap']; ?>"></input>
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
                            <input type="number" class="form-control" name="laki_laki_lainnya" placeholder="Masukkan Jumlah Laki-Laki" required maxlength="200" value="<?php echo $pekerja['laki_laki_lainnya']; ?>"></input>
                        </div>
                        <div class="form-group mb-2">
                            <label>Perempuan</label>
                            <input type="number" class="form-control" name="perempuan_lainnya" placeholder="Masukkan Jumlah Perempuan" required maxlength="200" value="<?php echo $pekerja['perempuan_lainnya']; ?>"></input>
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
                            <input type="number" class="form-control" name="sd" placeholder="Masukkan Jumlah SD" required maxlength="200" value="<?php echo $pekerja['sd']; ?>"></input>
                        </div>
                        <div class="form-group mb-2">
                            <label>SMP</label>
                            <input type="number" class="form-control" name="smp" placeholder="Masukkan Jumlah SMP" required maxlength="200" value="<?php echo $pekerja['smp']; ?>"></input>
                        </div>
                        <div class="form-group mb-2">
                            <label>SMA</label>
                            <input type="number" class="form-control" name="sma" placeholder="Masukkan Jumlah SMA" required maxlength="200" value="<?php echo $pekerja['sma']; ?>"></input>
                        </div>
                        <div class="form-group mb-2">
                            <label>D1 sampai D3</label>
                            <input type="number" class="form-control" name="d1_d2_d3" placeholder="Masukkan Jumlah D1 sampai D3" required maxlength="200" value="<?php echo $pekerja['d1_d2_d3']; ?>"></input>
                        </div>
                        <div class="form-group mb-2">
                            <label>S1/D4</label>
                            <input type="number" class="form-control" name="s1_d4" placeholder="Masukkan Jumlah S1/D4" required maxlength="200" value="<?php echo $pekerja['s1_d4']; ?>"></input>
                        </div>
                        <div class="form-group mb-2">
                            <label>S2</label>
                            <input type="number" class="form-control" name="s2" placeholder="Masukkan Jumlah S2" required maxlength="200" value="<?php echo $pekerja['s2']; ?>"></input>
                        </div>
                        <div class="form-group mb-2">
                            <label>S3</label>
                            <input type="number" class="form-control" name="s3" placeholder="Masukkan Jumlah S3" required maxlength="200" value="<?php echo $pekerja['s3']; ?>"></input>
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