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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_user = $_SESSION['id_user'];
    $role = $_SESSION['role'];

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
    $status = 'diajukan'; // Status diisi otomatis
    $keterangan = '-'; // Keterangan diisi otomatis

    // Validasi nomor telepon hanya angka dan tanda +
    if (!preg_match('/^[0-9\+]+$/', $no_telp_kantor) || !preg_match('/^[0-9\+]+$/', $no_hp)) {
        echo "<script>alert('Kontak hanya boleh berisi angka dan tanda +!');</script>";
        exit();
    }

    // Validasi Kabupaten hanya dari daftar yang diperbolehkan
    $valid_kabupaten = [
        "Balangan",
        "Banjar",
        "Barito Kuala",
        "Hulu Sungai Selatan",
        "Hulu Sungai Tengah",
        "Hulu Sungai Utara",
        "Kotabaru",
        "Tabalong",
        "Tanah Bumbu",
        "Tanah Laut",
        "Tapin",
        "Kota Banjarmasin",
        "Kota Banjarbaru"
    ];
    if (!in_array($kabupaten, $valid_kabupaten)) {
        echo "<script>alert('Kabupaten tidak valid!');</script>";
        exit();
    }

    try {
        $db = new Database();
        $conn = $db->getConnection();
        // Query menggunakan prepared statement dengan PDO
        $sql = "INSERT INTO profil (id_user, nama_perusahaan, kabupaten, alamat, jenis_usaha, no_telp_kantor, no_hp_pimpinan, tenaga_teknik, no_hp_teknik, nama, no_hp, email, status, keterangan) 
                VALUES (:id_user, :nama_perusahaan, :kabupaten, :alamat, :jenis_usaha, :no_telp_kantor, :no_hp_pimpinan, :tenaga_teknik, :no_hp_teknik, :nama, :no_hp, :email, :status, :keterangan)";
        $stmt = $conn->prepare($sql);

        // Bind parameter
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->bindParam(':nama_perusahaan', $nama_perusahaan, PDO::PARAM_STR);
        $stmt->bindParam(':kabupaten', $kabupaten, PDO::PARAM_STR);
        $stmt->bindParam(':alamat', $alamat, PDO::PARAM_STR);
        $stmt->bindParam(':jenis_usaha', $jenis_usaha, PDO::PARAM_STR);
        $stmt->bindParam(':no_telp_kantor', $no_telp_kantor, PDO::PARAM_STR);
        $stmt->bindParam(':no_hp_pimpinan', $no_hp_pimpinan, PDO::PARAM_STR);
        $stmt->bindParam(':tenaga_teknik', $tenaga_teknik, PDO::PARAM_STR);
        $stmt->bindParam(':no_hp_teknik', $no_hp_teknik, PDO::PARAM_STR);
        $stmt->bindParam(':nama', $nama, PDO::PARAM_STR);
        $stmt->bindParam(':no_hp', $no_hp, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':keterangan', $keterangan);

        // Eksekusi statement
        if ($stmt->execute()) {
            if ($role === 'superadmin') {
                echo "<script>alert('Profil berhasil ditambahkan!'); window.location.href='?page=profil_admin';</script>";
            } else {
                echo "<script>alert('Profil berhasil ditambahkan!'); window.location.href='?page=profil_perusahaan';</script>";
            }
        } else {
            echo "<script>alert('Gagal menambahkan profil. Silakan coba lagi.');</script>";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>

<!-- TAMBAH PROFIL PERUSAHAAN -->
<div class="container mt-4">
    <h3 class="text-center"><i class="fas fa-bolt" style="color: #ffc107;"></i> Tambah Profil Perusahaan <i class="fas fa-bolt" style="color: #ffc107;"></i></h3>
    <hr>
    <div class="card shadow" style="overflow-x: auto; max-height: calc(100vh - 150px); overflow-y: auto;">
        <div class="card-body">
            <form method="POST">
                <div class="form-group mb-2">
                    <label>Nama Perusahaan</label>
                    <input type="text" class="form-control" name="nama_perusahaan" placeholder="Masukkan nama perusahaan" required maxlength="100">
                    <small class="text-muted">
                        Catatan: nama perusahaan sesuai perizinan
                    </small>
                </div>

                <div class="form-group mb-2">
                    <label>Kabupaten/Kota</label>
                    <select class="form-control" name="kabupaten" required>
                        <option value="">-- Pilih Kabupaten/Kota --</option>
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
                    <textarea class="form-control" name="alamat" placeholder="Masukkan alamat lengkap perusahaan" required maxlength="200"></textarea>
                </div>

                <div class="form-group mb-2">
                    <label>Jenis Usaha</label>
                    <select class="form-control" name="jenis_usaha" required>
                        <option value="">-- Pilih Jenis Usaha --</option>
                        <option value="Perkantoran">Perkantoran</option>
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
                    <input type="text" class="form-control" name="no_telp_kantor" placeholder="Contoh : 081234567890" maxlength="15" pattern="[0-9]+">
                </div>
                <div class="form-group mb-2">
                    <label>Nomor Hp Pimpinan</label>
                    <input type="text" class="form-control" name="no_hp_pimpinan" placeholder="Contoh : 081234567890" maxlength="15" pattern="[0-9]+">
                </div>
                <div class="form-group mb-2">
                    <label>Email</label>
                    <input type="text" class="form-control" name="email" placeholder="Masukkan email" required maxlength="100">
                </div>

                <div class="form-group mb-2">
                    <label>Tenaga Teknik</label>
                    <input type="text" class="form-control" name="tenaga_teknik" placeholder="Masukkan nama tenaga teknik" required maxlength="100">
                </div>
                <div class="form-group mb-2">
                    <label>Nomor HP Tenaga Tenik</label>
                    <input type="text" class="form-control" name="no_hp_teknik" placeholder="Masukkan nomor handphone/whatsapp" required maxlength="15" pattern="[0-9]+">
                </div>
                <div class="card-header mt-4">
                    <h6>Kontak Person Admin</h6>
                </div>
                <div class="form-group mb-2">
                    <label>Nama</label>
                    <input type="text" class="form-control" name="nama" placeholder="Masukkan nama" required maxlength="100">
                </div>
                <div class="form-group mb-2">
                    <label>Nomor HP</label>
                    <input type="text" class="form-control" name="no_hp" placeholder="Masukkan nomor handphone/whatsapp" required maxlength="15" pattern="[0-9]+">
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <?php
                    $role = $_SESSION['role'];
                    $page = ($role === 'superadmin') ? 'profil_admin' : 'profil_perusahaan';
                    ?>
                    <a href="?page=<?php echo htmlspecialchars($page); ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>