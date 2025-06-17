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
$sql = "SELECT * FROM data_umum WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$data_umum = $stmt->fetch(PDO::FETCH_ASSOC);

if (!isset($_GET['id'])) {
    echo "<script>alert('Data tidak ditemukan!');";

    if ($_SESSION['role'] == 'superadmin') {
        echo "window.location.href='?page=data_umum_tampil';";
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
    $periode_laporan = sanitize_input($_POST['periode_laporan']);
    $nilai_investasi_mesin = sanitize_input($_POST['nilai_investasi_mesin']);
    $nilai_investasi_lainnya = sanitize_input($_POST['nilai_investasi_lainnya']);
    $modal_kerja = sanitize_input($_POST['modal_kerja']);
    $investasi_tanpa_tanah_bangunan = sanitize_input($_POST['investasi_tanpa_tanah_bangunan']);
    $status = sanitize_input($_POST['status']);
    $menggunakan_maklon = sanitize_input($_POST['menggunakan_maklon']);
    $menyediakan_makon = sanitize_input($_POST['menyediakan_maklon']);

    if (!empty($nama_perusahaan)) {
        $sql = "UPDATE data_umum SET nama_perusahaan = ?, periode_laporan = ?, nilai_investasi_mesin = ?, nilai_investasi_lainnya = ?, modal_kerja = ?, investasi_tanpa_tanah_bangunan = ?, status = ?, menggunakan_maklon = ?, menyediakan_maklon = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$nama_perusahaan, $periode_laporan, $nilai_investasi_mesin, $nilai_investasi_lainnya, $modal_kerja, $investasi_tanpa_tanah_bangunan, $status, $menggunakan_maklon, $menyediakan_makon, $id]);

        if ($success) {
            if ($role === 'superadmin') {
                echo "<script>alert('data berhasil diperbarui!'); window.location.href='?page=data_umum_tampil';</script>";
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

<!-- UPDATE DATA UMUM PERUSAHAAN -->
<?php
$role = $_SESSION['role'];
$page = ($role === 'superadmin') ? 'data_umum_tampil' : 'profil_perusahaan';
?>
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Edit Data Umum Perusahaan</h6>
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
                    <input type="text" class="form-control" name="nama_perusahaan" placeholder="Masukkan nama perusahaan" required maxlength="100" value="<?php echo $data_umum['nama_perusahaan']; ?>" readonly>
                    <small class="text-muted">
                        Catatan: nama perusahaan sesuai perizinan
                    </small>
                </div>
                <div class="form-group mb-2">
                    <label>Periode Laporan</label>
                    <input type="text" class="form-control" name="periode_laporan" placeholder="Masukkan Periode Laporan" required maxlength="200" value="<?php echo $data_umum['periode_laporan']; ?>"></input>
                </div>
                <div class="form-group mb-2">
                    <label>Nilai Investasi Mesin</label>
                    <input type="text" class="form-control" name="nilai_investasi_mesin" placeholder="Masukkan Nilai Investasi Mesin" required maxlength="200" value="<?php echo $data_umum['nilai_investasi_mesin']; ?>"></input>
                </div>
                <div class="form-group mb-2">
                    <label>Nilai Investasi lainnya</label>
                    <input type="text" class="form-control" name="nilai_investasi_lainnya" placeholder="Masukkan Nilai Investasi lainnya" required maxlength="200" value="<?php echo $data_umum['nilai_investasi_lainnya']; ?>"></input>
                </div>
                <div class="form-group mb-2">
                    <label>Modal Kerja</label>
                    <input type="text" class="form-control" name="modal_kerja" placeholder="Masukkan Modal Kerja" required maxlength="200" value="<?php echo $data_umum['modal_kerja']; ?>"></input>
                </div>
                <div class="form-group mb-2">
                    <label>Investasi Tanpa Tanah dan Bangunan</label>
                    <input type="text" class="form-control" name="investasi_tanpa_tanah_bangunan" placeholder="Masukkan Investasi Tanpa Tanah dan Bangunan" required maxlength="200" value="<?php echo $data_umum['investasi_tanpa_tanah_bangunan']; ?>"></input>
                </div>
                <div class="form-group mb-2">
                    <label>Status</label>
                    <input type="text" class="form-control" name="status" placeholder="Masukkan Status" required maxlength="200" value="<?php echo $data_umum['status']; ?>"></input>
                </div>
                <div class="mb-3">
                    <label class="form-label">Menggunakan Maklon</label>
                    <select name="menggunakan_maklon" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <option value="iya">iya</option>
                        <option value="tidak">tidak</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Menyediakan Maklon</label>
                    <select name="menyediakan_maklon" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <option value="iya">iya</option>
                        <option value="tidak">tidak</option>
                    </select>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="reset" class="btn btn-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>