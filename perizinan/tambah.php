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

    $jenis_laporan = sanitize_input($_POST['jenis_laporan']);
    $no_izin = sanitize_input($_POST['no_izin']);
    $tgl_dokumen = sanitize_input($_POST['tgl_dokumen']);
    $upload_berkas = uploadFile('upload_berkas', $jenis_laporan, $nama_perusahaan, $no_izin);

    if ($upload_berkas === null) {
        echo "<script>alert('Upload file gagal! Pastikan memilih file dengan format yang benar (pdf/doc/docx/xls/xlsx) dan ukuran maksimal 10MB.'); history.back();</script>";
        exit;
    }

    $verifikasi = 'diajukan';
    $keterangan = '-';
    $tgl_verif = null;

    try {
        if ($profil && !empty($profil['nama_perusahaan'])) {
            $nama_perusahaan = $profil['nama_perusahaan'];

            $sql = "INSERT INTO perizinan (id_user, nama_perusahaan, jenis_laporan, no_izin, tgl_dokumen, upload_berkas, verifikasi, keterangan, tgl_verif) 
                    VALUES (:id_user, :nama_perusahaan, :jenis_laporan, :no_izin, :tgl_dokumen, :upload_berkas, :verifikasi, :keterangan, :tgl_verif)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
            $stmt->bindParam(':nama_perusahaan', $nama_perusahaan, PDO::PARAM_STR);
            $stmt->bindParam(':jenis_laporan', $jenis_laporan, PDO::PARAM_STR);
            $stmt->bindParam(':no_izin', $no_izin, PDO::PARAM_STR);
            $stmt->bindParam(':tgl_dokumen', $tgl_dokumen, PDO::PARAM_STR);
            $stmt->bindParam(':upload_berkas', $upload_berkas, PDO::PARAM_STR);
            $stmt->bindParam(':verifikasi', $verifikasi, PDO::PARAM_STR);
            $stmt->bindParam(':keterangan', $keterangan, PDO::PARAM_STR);
            $stmt->bindParam(':tgl_verif', $tgl_verif, PDO::PARAM_STR);

            if ($stmt->execute()) {
                echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='?page=perizinan_tampil';</script>";
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


function uploadFile($input_name, $jenis_laporan, $nama_perusahaan, $no_izin)
{
    if (!empty($_FILES[$input_name]['name'])) {
        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($_FILES[$input_name]['size'] > $maxSize) {
            $_SESSION['pesan'] = "File $input_name terlalu besar! Maksimal 10MB.";
            return null;
        }

        $target_dir = "uploads/";

        // Bersihkan nama-nama agar aman sebagai nama file
        $jenis_laporan = preg_replace("/[^a-zA-Z0-9]/", "", $jenis_laporan);
        $nama_perusahaan = preg_replace("/[^a-zA-Z0-9]/", "", $nama_perusahaan);
        $no_izin = preg_replace("/[^a-zA-Z0-9]/", "", $no_izin);

        $datetime = date('Ymd_His'); // Format: 20250617_153012
        $kode_unik = substr(md5(uniqid(rand(), true)), 0, 6); // 6 karakter acak

        $file_ext = pathinfo($_FILES[$input_name]["name"], PATHINFO_EXTENSION);
        $new_file_name = "{$jenis_laporan}_{$nama_perusahaan}_{$no_izin}_{$datetime}_{$kode_unik}.{$file_ext}";

        $target_file = $target_dir . $new_file_name;

        $allowed_types = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
        if (!in_array(strtolower($file_ext), $allowed_types)) {
            $_SESSION['pesan'] = "Format file tidak diizinkan!";
            return null;
        }

        if (move_uploaded_file($_FILES[$input_name]["tmp_name"], $target_file)) {
            return $target_file;
        }
    }
    return null;
}
?>

<!-- TAMBAH PERIZINAN -->
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Perizinan</h6>
            <a href="?page=perizinan_tampil" class="btn btn-primary btn-icon-split btn-sm">
                <span class="icon text-white-50">
                    <i class="fas fa-arrow-left" style="vertical-align: middle; margin-top: 5px;"></i>
                </span>
                <span class="text">Kembali</span>
            </a>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group mb-2">
                    <label>Nama Perusahaan</label>
                    <input type="text" class="form-control" name="nama_perusahaan" placeholder="Masukkan nama perusahaan" required maxlength="100" value="<?= htmlspecialchars($nama_perusahaan) ?>" readonly>
                    <small class="text-muted">
                        Catatan: nama perusahaan sesuai perizinan
                    </small>
                </div>
                <div class="form-group mb-2">
                    <label>Jenis Laporan</label>
                    <select name="jenis_laporan" id="jenis_laporan" class="form-control" required>
                        <option value="">-- Pilih Jenis Laporan--</option>
                        <option value="KKPR">KKPR</option>
                        <option value="PERSETUJUAN LINGKUNGAN">PERSETUJUAN LINGKUNGAN</option>
                        <option value="SURAT LAIK FUNGSI">SURAT LAIK FUNGSI</option>
                        <option value="PERSETUJUAN BANGUNAN DAN GEDUNG">PERSETUJUAN BANGUNAN DAN GEDUNG</option>
                        <option value="NOMOR INDUK BERUSAHA">NOMOR INDUK BERUSAHA</option>
                        <option value="NPWP">NPWP(SESUAI DOMISILI)</option>
                        <option value="PERIZINAN USAHA SEKTOR INDUSTRI">PERIZINAN USAHA (SEKTOR INDUSTRI/SERTIFIKAT STANDART)</option>
                        <option value="SERTIFIKAT HALAL">SERTIFIKAT HALAL</option>
                        <option value="TKDN">TKDN</option>
                        <option value="SNI">SNI</option>
                        <option value="ISO">ISO</option>
                        <option value="SERTIFIKAT INDUSTRI HIJAU">SERTIFIKAT INDUSTRI HIJAU</option>
                        <option value="PELAPORAN S1 S2 SINAS">PELAPORAN S.11/S.2(SINAS)</option>
                        <option value="KEPEMILIKAN AKUN SINAS">KEPEMILIKAN AKUN SINAS</option>
                        <option value="KESESUAIAN KEGIATAN USAHA DENGAN BIDANG USAHA PERIZINAN PERUSAHAAN">KESESUAIAN KEGIATAN USAHA DENGAN BIDANG USAHA PERIZINAN PERUSAHAAN</option>
                        <option value="KESESUAIAN FASILITAS PRODUKSI DAN KAPASITAS SESUAI DENGAN PERIZINAN PERUSAHAAN">KESESUAIAN FASILITAS PRODUKSI DAN KAPASITAS SESUAI DENGAN PERIZINAN PERUSAHAAN</option>
                        <option value="IZIN USAHA INDSUTRI">IZIN USAHA INDSUTRI</option>
                        <option value="IZIN PERLUASAN INDUSTRI">IZIN PERLUASAN INDUSTRI</option>
                        <option value="IZIN KAWASAN INDUSTRI">IZIN KAWASAN INDUSTRI</option>
                        <option value="IZIN PERLUASAN KAWASAN INDUSTRI">IZIN PERLUASAN KAWASAN INDUSTRI</option>
                        <option value="IZIN EDAR (BPOM)">IZIN EDAR (BPOM)</option>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label>No. Izin</label>
                    <input type="text" class="form-control" name="no_izin" placeholder="Masukkan No. Izin" required maxlength="200"></input>
                </div>
                <div class="form-group mb-2">
                    <label>Tanggal Dokumen</label>
                    <input type="date" class="form-control" name="tgl_dokumen" required></input>
                </div>
                <div class="form-group mb-2">
                    <label for="upload_berkas">Upload Berkas (PDF, DOC , DOCX, XLS, XLSX)</label>
                    <input type="file" name="upload_berkas" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">
                    <small class="text-danger">Max File 10Mb</small>
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