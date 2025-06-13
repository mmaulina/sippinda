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
$sql = "SELECT * FROM profil WHERE id_user = :id_user";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
$stmt->execute();
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fungsi untuk sanitasi input
    function sanitize_input($data)
    {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    $nama_perusahaan = sanitize_input($_POST['nama_perusahaan']);
    $kabupaten = sanitize_input($_POST['kabupaten']);
    $alamat = sanitize_input($_POST['alamat']);
    $jenis_usaha = sanitize_input($_POST['jenis_usaha']);
    $no_telp_kantor = sanitize_input($_POST['no_telp_kantor']);
    $no_hp_pimpinan = sanitize_input($_POST['no_hp_pimpinan']);
    $tenaga_teknik = sanitize_input($_POST['tenaga_teknik']);
    $no_hp_teknik = sanitize_input($_POST['no_hp_teknik']);
    $nama = sanitize_input($_POST['nama']);
    $no_hp = sanitize_input($_POST['no_hp']);
    $email = sanitize_input($_POST['email']);

    // Validasi nomor telepon hanya angka dan tanda +
    if (!preg_match('/^[0-9\+]+$/', $no_telp_kantor) || !preg_match('/^[0-9\+]+$/', $no_hp)) {
        echo "<script>alert('Kontak hanya boleh berisi angka dan tanda +!');</script>";
    } else {
        // Update data profil
        $sql = "UPDATE profil SET nama_perusahaan=:nama_perusahaan, kabupaten=:kabupaten, alamat=:alamat, jenis_usaha=:jenis_usaha, no_telp_kantor=:no_telp_kantor, no_hp_pimpinan=:no_hp_pimpinan, tenaga_teknik=:tenaga_teknik, no_hp_teknik=:no_hp_teknik, nama=:nama, no_hp=:no_hp, email=:email, status = 'Diajukan', keterangan = '-' WHERE id_user=:id_user";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nama_perusahaan', $nama_perusahaan);
        $stmt->bindParam(':kabupaten', $kabupaten);
        $stmt->bindParam(':alamat', $alamat);
        $stmt->bindParam(':jenis_usaha', $jenis_usaha);
        $stmt->bindParam(':no_telp_kantor', $no_telp_kantor);
        $stmt->bindParam(':no_hp_pimpinan', $no_hp_pimpinan);
        $stmt->bindParam(':tenaga_teknik', $tenaga_teknik);
        $stmt->bindParam(':no_hp_teknik', $no_hp_teknik);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':no_hp', $no_hp);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='?page=profil_perusahaan';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui profil. Silakan coba lagi.');</script>";
        }
    }
}
?>

<!-- UPDATE PROFIL PERUSAHAAN -->
<div class="container mt-4">
    <h3 class="text-center mb-3"><i class="fas fa-bolt" style="color: #ffc107;"></i> Update Profil Perusahaan <i class="fas fa-bolt" style="color: #ffc107;"></i></h3>
    <hr>
    <div class="card shadow" style="overflow-x: auto; max-height: calc(100vh - 150px); overflow-y: auto;">
        <div class="card-body">
            <form method="POST">
                <div class="form-group mb-2">
                    <label>Nama Perusahaan</label>
                    <input type="text" class="form-control" name="nama_perusahaan" placeholder="Masukkan nama perusahaan" required value="<?php echo $profil['nama_perusahaan']; ?>">
                    <small class="text-muted">
                        Catatan: nama perusahaan sesuai perizinan
                    </small>
                </div>

                <div class="form-group mb-2">
                    <label>Kabupaten/Kota</label>
                    <select class="form-control" name="kabupaten" required>
                        <option value="<?php echo $profil['kabupaten']; ?>" selected><?php echo $profil['kabupaten']; ?></option>
                        <option value="Balangan">Balangan</option>
                        <option value="Banjar">Banjar</option>
                        <option value="Barito Kuala">Barito Kuala</option>
                        <option value="Hulu Sungai Selatan">Hulu Sungai Selatan</option>
                        <option value="Hulu Sungai Tengah">Hulu Sungai Tengah</option>
                        <option value="Hulu Sungai Utara">Hulu Sungai Utara</option>
                        <option value="Kotabaru">Kotabaru</option>
                        <option value="Tabalong">Tabalong</option>
                        <option value="Tanah Bumbu">Tanah Bumbu</option>
                        <option value="Tanah Laut">Tanah Laut</option>
                        <option value="Tapin">Tapin</option>
                        <option value="Kota Banjarmasin">Banjarmasin (Kota)</option>
                        <option value="Kota Banjarbaru">Banjarbaru (Kota)</option>
                    </select>
                </div>

                <div class="form-group mb-2">
                    <label>Alamat</label>
                    <textarea class="form-control" name="alamat" placeholder="Masukkan alamat lengkap perusahaan" required><?php echo $profil['alamat']; ?></textarea>
                </div>

                <div class="form-group mb-2">
                    <label>Jenis Usaha</label>
                    <select class="form-control" name="jenis_usaha" required>
                        <option value="<?php echo $profil['jenis_usaha']; ?>" selected><?php echo $profil['jenis_usaha']; ?></option>
                        <option value="Kesehatan dan Rumah Sakit">Kesehatan dan Rumah Sakit</option>
                        <option value="Industri dan Manufaktur">Industri dan Manufaktur</option>
                        <option value="Restoran">Restoran</option>
                        <option value="Event Organizer">Event Organizer</option>
                        <option value="Pendidikan">Pendidikan</option>
                        <option value="Perdagangan">Perdagangan</option>
                        <option value="Telekomunikasi dan Teknologi">Telekomunikasi dan Teknologi</option>
                        <option value="Transportasi">Transportasi</option>
                        <option value="Perhotelan">Perhotelan</option>
                        <option value="Logistik">Logistik</option>
                        <option value="Pertanian">Pertanian</option>
                        <option value="Perikanan">Perikanan</option>
                        <option value="Perternakan">Perternakan</option>
                        <option value="Hiburan dan Wisata">Hiburan dan Wisata</option>
                        <option value="Lingkungan">Lingkungan</option>
                        <option value="Konstruksi dan Infrastruktur">Konstruksi dan Infrastruktur</option>
                        <option value="Jasa">Jasa</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="form-group mb-2">
                    <label>Nomor Telepon Kantor</label>
                    <input type="text" class="form-control" name="no_telp_kantor" placeholder="Contoh : 081234567890" required value="<?php echo $profil['no_telp_kantor']; ?>">
                </div>

                <div class="form-group mb-2">
                    <label>No HP Pimpinan</label>
                    <input type="text" class="form-control" name="no_hp_pimpinan" placeholder="Contoh : 081234567890" maxlength="15" pattern="[0-9]+" required value="<?php echo $profil['no_hp_pimpinan']; ?>">
                </div>

                <div class="form-group mb-2">
                    <label>Tenaga Teknik</label>
                    <input type="text" class="form-control" name="tenaga_teknik" placeholder="Masukkan nama tenaga teknik" required value="<?php echo $profil['tenaga_teknik']; ?>">
                </div>
                <div class="form-group mb-2">
                    <label>No HP Tenaga Teknik</label>
                    <input type="text" class="form-control" name="no_hp_teknik" placeholder="Contoh : 081234567890" maxlength="15" pattern="[0-9]+" required value="<?php echo $profil['no_hp_teknik']; ?>">
                </div>

                <div class="card-header mt-4">
                    <h6>Kontak Person</h6>
                </div>
                <div class="form-group mt-2 mb-2">
                    <label>Nama</label>
                    <input type="text" class="form-control" name="nama" placeholder="Masukkan nama" required value="<?php echo $profil['nama']; ?>">
                </div>

                <div class="form-group mb-2">
                    <label>Nomor HP</label>
                    <input type="text" class="form-control" name="no_hp" placeholder="Masukkan nomor handphone/whatsapp" required maxlength="15" pattern="[0-9]+" required value="<?php echo $profil['no_hp']; ?>">
                </div>

                <div class="form-group mb-2">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" placeholder="Masukkan email" required value="<?php echo $profil['email']; ?>">
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                    <a href="?page=profil_perusahaan" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>