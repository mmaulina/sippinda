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

$id_user = $_SESSION['id_user'];

$db = new Database();
$conn = $db->getConnection();
// Ambil data profil perusahaan
$sql = "SELECT * FROM profil_perusahaan WHERE id_user = :id_user";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
$stmt->execute();
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fungsi untuk sanitasi input
function sanitize_input($data)
{
    return trim(strip_tags($data)); // Tidak mengubah karakter seperti &
}

    $nama_perusahaan = sanitize_input($_POST['nama_perusahaan']);
    $alamat_kantor = sanitize_input($_POST['alamat_kantor']);
    $alamat_pabrik = sanitize_input($_POST['alamat_pabrik']);
    $no_telpon = sanitize_input($_POST['no_telpon']);
    $no_fax = sanitize_input($_POST['no_fax']);
    $jenis_lokasi_pabrik = sanitize_input($_POST['jenis_lokasi_pabrik']);
    $jenis_kuisioner = sanitize_input($_POST['jenis_kuisioner']);

    // Validasi nomor telepon hanya angka dan tanda +
     if (!preg_match('/^[0-9\+]+$/', $no_telpon && $no_fax)) {
        echo "<script>alert('Kontak hanya boleh berisi angka dan tanda +!');</script>";
    } else {
        $sql = "UPDATE profil_perusahaan SET nama_perusahaan=?, alamat_kantor=?, alamat_pabrik=?, no_telpon=?, no_fax=?, jenis_lokasi_pabrik=?, jenis_kuisioner=? WHERE id_user=?";
        $stmt = $conn->prepare($sql);
        $success = $stmt->execute([$nama_perusahaan, $alamat_kantor, $alamat_pabrik, $no_telpon, $no_fax, $jenis_lokasi_pabrik, $jenis_kuisioner, $id_user]);

        if ($success) {
            echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='?page=profil_perusahaan';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui profil. Silakan coba lagi.');</script>";
        }
    }
}
?>

<!-- UPDATE PROFIL PERUSAHAAN -->
<div class="container mt-4">
    <h3 class="text-center mb-3"></i> Update Profil Perusahaan </i></h3>
    <hr>
    <div class="card shadow" style="overflow-x: auto; max-height: calc(100vh - 150px); overflow-y: auto;">
        <div class="card-body">
            <form method="POST">
                <div class="form-group mb-2">
                    <label>Nama Perusahaan</label>
                    <input type="text" class="form-control" name="nama_perusahaan" placeholder="Masukkan nama perusahaan" required maxlength="100" value="<?php echo $profil['nama_perusahaan']; ?>">
                    <small class="text-muted">
                        Catatan: nama perusahaan sesuai perizinan
                    </small>
                </div>

                <div class="form-group mb-2">
                    <label>Alamat Kantor</label>
                    <textarea class="form-control" name="alamat_kantor" placeholder="Masukkan alamat lengkap kantor" required maxlength="200"><?php echo htmlspecialchars($profil['alamat_kantor']); ?></textarea>
                </div>

                <div class="form-group mb-2">
                    <label>Alamat Pabrik</label>
                    <textarea class="form-control" name="alamat_pabrik" placeholder="Masukkan alamat lengkap pabrik" required maxlength="200"><?php echo htmlspecialchars($profil['alamat_pabrik']); ?></textarea>
                </div>

                <div class="form-group mb-2">
                    <label>Nomor Telepon</label>
                    <input type="text" class="form-control" name="no_telpon" placeholder="Contoh : 081234567890" maxlength="15" pattern="[0-9]+" value="<?php echo $profil['no_telpon']; ?>">
                </div>
                <div class="form-group mb-2">
                    <label>Nomor Fax</label>
                    <input type="text" class="form-control" name="no_fax" placeholder="Contoh : 081234567890" maxlength="15" pattern="[0-9]+" value="<?php echo $profil['no_fax']; ?>">
                </div>
                <div class="form-group mb-2">
                    <label>Jenis Lokasi Pabrik</label>
                    <input type="text" class="form-control" name="jenis_lokasi_pabrik" placeholder="Masukkan Jenis Lokasi Pabrik" required maxlength="100" value="<?php echo $profil['jenis_lokasi_pabrik']; ?>">
                </div>
                <div class="form-group mb-2">
                    <label>Jenis Kuisioner</label>
                    <input type="text" class="form-control" name="jenis_kuisioner" placeholder="Masukkan Jenis Kuisioner" required maxlength="100" value="<?php echo $profil['jenis_kuisioner']; ?>">
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                    <a href="?page=profil_perusahaan" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>