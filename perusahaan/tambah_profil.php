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
        return trim(strip_tags($data)); // Tidak mengubah karakter seperti &
    }


    // Menyimpan (tidak mengubah &)
    $nama_perusahaan = sanitize_input($_POST['nama_perusahaan']);
    $alamat_kantor = sanitize_input($_POST['alamat_kantor']);
    $alamat_pabrik = sanitize_input($_POST['alamat_pabrik']);
    $no_telpon = sanitize_input($_POST['no_telpon']);
    $no_fax = sanitize_input($_POST['no_fax']);
    $jenis_lokasi_pabrik = sanitize_input($_POST['jenis_lokasi_pabrik']);
    $jenis_kuisioner = sanitize_input($_POST['jenis_kuisioner']);

    // Validasi nomor telepon hanya angka dan tanda +
    // Nomor telepon: wajib diisi & hanya angka dan +
    if (empty($no_telpon) || !preg_match('/^[0-9\+]+$/', $no_telpon)) {
        echo "<script>
        alert('Nomor telepon wajib diisi dan hanya boleh berisi angka dan tanda +!');
        window.history.back();
    </script>";
        exit();
    }

    // Nomor fax: boleh kosong, tapi kalau diisi hanya angka dan +
    if (!empty($no_fax) && !preg_match('/^[0-9\+]+$/', $no_fax)) {
        echo "<script>
        alert('Nomor fax hanya boleh berisi angka dan tanda +!');
        window.history.back();
    </script>";
        exit();
    }


    try {
        $db = new Database();
        $conn = $db->getConnection();
        // Query menggunakan prepared statement dengan PDO
        $sql = "INSERT INTO profil_perusahaan (id_user, nama_perusahaan, alamat_kantor, alamat_pabrik, no_telpon, no_fax, jenis_lokasi_pabrik, jenis_kuisioner) 
                VALUES (:id_user, :nama_perusahaan, :alamat_kantor, :alamat_pabrik, :no_telpon, :no_fax, :jenis_lokasi_pabrik, :jenis_kuisioner)";
        $stmt = $conn->prepare($sql);

        // Bind parameter
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->bindParam(':nama_perusahaan', $nama_perusahaan, PDO::PARAM_STR);
        $stmt->bindParam(':alamat_kantor', $alamat_kantor, PDO::PARAM_STR);
        $stmt->bindParam(':alamat_pabrik', $alamat_pabrik, PDO::PARAM_STR);
        $stmt->bindParam(':no_telpon', $no_telpon, PDO::PARAM_STR);
        $stmt->bindParam(':no_fax', $no_fax, PDO::PARAM_STR);
        $stmt->bindParam(':jenis_lokasi_pabrik', $jenis_lokasi_pabrik, PDO::PARAM_STR);
        $stmt->bindParam(':jenis_kuisioner', $jenis_kuisioner, PDO::PARAM_STR);

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
<?php
$role = $_SESSION['role'];
$page = ($role === 'superadmin') ? 'profil_admin' : 'profil_perusahaan';
?>
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Profil Perusahaan</h6>
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
                    <input type="text" class="form-control" name="nama_perusahaan" placeholder="Masukkan nama perusahaan" required maxlength="100">
                    <small class="text-muted">
                        Catatan: nama perusahaan sesuai perizinan
                    </small>
                </div>
                <div class="form-group mb-2">
                    <label>Alamat Kantor</label>
                    <textarea class="form-control" name="alamat_kantor" placeholder="Masukkan alamat lengkap kantor" required maxlength="200"></textarea>
                </div>
                <div class="form-group mb-2">
                    <label>Alamat Pabrik</label>
                    <textarea class="form-control" name="alamat_pabrik" placeholder="Masukkan alamat lengkap pabrik" required maxlength="200"></textarea>
                </div>
                <div class="form-group mb-2">
                    <label>Nomor Telepon</label>
                    <input type="text" class="form-control" name="no_telpon" placeholder="Contoh : 081234567890" required maxlength="15" pattern="[0-9]+">
                </div>
                <div class="form-group mb-2">
                    <label>Nomor Fax</label>
                    <input type="text" class="form-control" name="no_fax" placeholder="Contoh : 081234567890" maxlength="15" pattern="[0-9]+">
                    <small class="text-muted">Jika tidak memiliki nomor fax, kosongkan saja.</small>
                </div>
                <div class="form-group mb-2">
                    <label>Jenis Lokasi Pabrik</label>
                    <input type="text" class="form-control" name="jenis_lokasi_pabrik" placeholder="Masukkan Jenis Lokasi Pabrik" required maxlength="100">
                </div>
                <div class="form-group mb-3">
                    <label>Jenis Kuisioner</label>
                    <input type="text" class="form-control" name="jenis_kuisioner" placeholder="Masukkan Jenis Kuisioner" required maxlength="100">
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="reset" class="btn btn-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>