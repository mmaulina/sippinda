
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once 'koneksi.php';

if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='login.php';</script>";
    exit;
}


$id_laporan = isset($_GET['id']) ? $_GET['id'] : null;
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();

    function sanitizeInput($input)
    {
        return strip_tags(trim($input));
    }


    $nama_perusahaan = sanitizeInput($_POST['nama_perusahaan']);
    $jenis_laporan = sanitizeInput($_POST['jenis_laporan']);
    $no_izin = sanitizeInput($_POST['no_izin']);
    $tgl_dokumen = sanitizeInput($_POST['tgl_dokumen']);
    $upload_berkas = uploadFile('upload_berkas', $jenis_laporan, $nama_perusahaan, $no_izin);

    if ($upload_berkas === null) {
    echo "<script>alert('Upload file gagal! Pastikan memilih file dengan format yang benar (pdf/doc/docx/xls/xlsx) dan ukuran maksimal 10MB.'); history.back();</script>";
    exit;
}

    $verifikasi = 'diajukan';
    $keterangan = '-';
    $tgl_verif = null;

    $updateSQL = "UPDATE perizinan SET 
    nama_perusahaan = :nama_perusahaan,
    jenis_laporan=:jenis_laporan, 
    no_izin=:no_izin,
    tgl_dokumen=:tgl_dokumen,
    verifikasi=:verifikasi,
    keterangan=:keterangan,
    tgl_verif=:tgl_verif";

    // Hanya tambahkan file_laporan ke query jika ada file yang diunggah
    if ($upload_berkas !== null) {
        $updateSQL .= ", upload_berkas = :upload_berkas";
    }

    $updateSQL .= " WHERE id = :id ";

    $stmt = $db->prepare($updateSQL);

    // Bind parameter yang wajib
    $stmt->bindParam(':id', $id_laporan);
    $stmt->bindParam(':nama_perusahaan', $nama_perusahaan, PDO::PARAM_STR);
    $stmt->bindParam(':jenis_laporan', $jenis_laporan, PDO::PARAM_STR);
    $stmt->bindParam(':no_izin', $no_izin, PDO::PARAM_STR);
    $stmt->bindParam(':tgl_dokumen', $tgl_dokumen, PDO::PARAM_STR);
    $stmt->bindParam(':verifikasi', $verifikasi, PDO::PARAM_STR);
    $stmt->bindParam(':keterangan', $keterangan, PDO::PARAM_STR);
    $stmt->bindParam(':tgl_verif', $tgl_verif, PDO::PARAM_STR);

    // Bind parameter hanya jika file diunggah
    if ($upload_berkas !== null) {
        $stmt->bindParam(':upload_berkas', $upload_berkas);
    }

    if ($stmt->execute()) {
        $_SESSION['hasil'] = true;
        $_SESSION['pesan'] = "Berhasil Update Data";
    } else {
        $_SESSION['hasil'] = false;
        $_SESSION['pesan'] = "Gagal Update Data";
    }

    echo "<meta http-equiv='refresh' content='0; url=?page=perizinan_tampil'>";
}

$database = new Database();
$db = $database->getConnection();
$query = "SELECT * FROM perizinan WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id_laporan);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='?page=perizinan_tampil';</script>";
    exit;
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
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Berkas Perizinan</h6>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group mb-2">
                    <label>Nama Perusahaan</label>
                    <input type="text" class="form-control" name="nama_perusahaan" required maxlength="100" value="<?= htmlspecialchars($data['nama_perusahaan']) ?>" readonly>
                    <small class="text-muted">Catatan: nama perusahaan sesuai perizinan</small>
                </div>

                <div class="form-group mb-2">
                    <label>Jenis Laporan</label>
                    <select name="jenis_laporan" id="jenis_laporan" class="form-control" required>
                        <option value="">-- Pilih Jenis Laporan--</option>
                        <?php
                        $jenis_list = [
                            "KKPR", "PERSETUJUAN", "SLF", "PBS", "NIIS", "NPWP",
                            "PERIZINAN BERUSAHA SEKTOR INDUSTRI", "SERTIFIKAT HALAL", "TKDN",
                            "SNI", "SERTIFIKAT INDUSTRI HIJAU", "PELAPORAN S1 S2 SINAS",
                            "KEPEMILIKAN AKUN SINAS", "KESESUAIAN KEGIATAN USAHA", "KESESUAIAN FASILITAS",
                            "IZIN USAHA INDSUTRI", "IZIN PERLUASAN INDUSTRI", "IZIN KAWASAN INDUSTRI",
                            "IZIN PERLUASAN KAWASAN INDUSTRI"
                        ];
                        foreach ($jenis_list as $j) {
                            $selected = ($data['jenis_laporan'] == $j) ? 'selected' : '';
                            echo "<option value='$j' $selected>$j</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group mb-2">
                    <label>No.Izin</label>
                    <input type="text" class="form-control" name="no_izin" required maxlength="200" value="<?= htmlspecialchars($data['no_izin']) ?>">
                </div>

                <div class="form-group mb-2">
                    <label>Tanggal Dokumen</label>
                    <input type="date" class="form-control" name="tgl_dokumen" required value="<?= $data['tgl_dokumen'] ?>">
                </div>

                <div class="form-group mb-2">
                    <label class="form-label">Upload Berkas (PDF, DOC, DOCX, XLS, XLSX)</label>
                    <input type="file" name="upload_berkas" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">
                    <?php if (!empty($data['upload_berkas'])): ?>
                        <small class="text-success">File saat ini: <a href="<?= $data['upload_berkas'] ?>" target="_blank">Lihat Berkas</a></small><br>
                    <?php endif; ?>
                    <small class="text-danger">Max File 10Mb. Kosongkan jika tidak ingin mengubah file.</small>
                </div>

                <!-- Tombol Simpan dan Batal -->
                <div class="mb-3">
                    <button type="submit" class="btn btn-success">Perbarui</button>
                    <a href="?page=perizinan_tampil" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
