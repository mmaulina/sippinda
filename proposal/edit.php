
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

$database = new Database();
$db = $database->getConnection();
$query = "SELECT * FROM proposal WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id_laporan);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='?page=proposal_tampil';</script>";
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();

    function sanitizeInput($input)
    {
        return strip_tags(trim($input));
    }


    $nama_perusahaan = sanitizeInput($_POST['nama_perusahaan']);
    $tahun = sanitizeInput($_POST['tahun']);
    $upload = uploadFile('upload', $nama_perusahaan);

    // Jika tidak ada file baru diunggah, gunakan file lama
    if ($upload === null && empty($_FILES['upload']['name'])) {
        $upload = $data['upload']; // Gunakan file lama
    } elseif ($upload === null && !empty($_FILES['upload']['name'])) {
        // Ada file tapi gagal upload
        echo "<script>alert('Upload file gagal! Pastikan memilih file dengan format yang benar (pdf/doc/docx/xls/xlsx) dan ukuran maksimal 10MB.'); history.back();</script>";
        exit;
    }

    $status = 'diajukan';
    $keterangan = '-';

    $updateSQL = "UPDATE proposal SET 
    nama_perusahaan = :nama_perusahaan,
    tahun=:tahun, 
    status=:status,
    keterangan=:keterangan";

    // Hanya tambahkan file_laporan ke query jika ada file yang diunggah
    if ($upload !== null) {
        $updateSQL .= ", upload = :upload";
    }

    $updateSQL .= " WHERE id = :id ";

    $stmt = $db->prepare($updateSQL);

    // Bind parameter yang wajib
            $stmt->bindParam(':id', $id_laporan);
            $stmt->bindParam(':nama_perusahaan', $nama_perusahaan, PDO::PARAM_STR);
            $stmt->bindParam(':tahun', $tahun, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':keterangan', $keterangan, PDO::PARAM_STR);

    // Bind parameter hanya jika file diunggah
    if ($upload !== null) {
        $stmt->bindParam(':upload', $upload);
    }

    if ($stmt->execute()) {
        $_SESSION['hasil'] = true;
        $_SESSION['pesan'] = "Berhasil Update Data";
    } else {
        $_SESSION['hasil'] = false;
        $_SESSION['pesan'] = "Gagal Update Data";
    }

    echo "<meta http-equiv='refresh' content='0; url=?page=proposal_tampil'>";
}




function uploadFile($input_name, $nama_perusahaan)
{
    if (!empty($_FILES[$input_name]['name'])) {
        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($_FILES[$input_name]['size'] > $maxSize) {
            $_SESSION['pesan'] = "File $input_name terlalu besar! Maksimal 10MB.";
            return null;
        }

        $target_dir = "uploads/";

        // Bersihkan nama-nama agar aman sebagai nama file
        $nama_perusahaan = preg_replace("/[^a-zA-Z0-9]/", "", $nama_perusahaan);

        $datetime = date('Ymd_His'); // Format: 20250617_153012
        $kode_unik = substr(md5(uniqid(rand(), true)), 0, 6); // 6 karakter acak

        $file_ext = pathinfo($_FILES[$input_name]["name"], PATHINFO_EXTENSION);
        $new_file_name = "{$nama_perusahaan}_{$datetime}_{$kode_unik}.{$file_ext}";

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
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Edit Proposal</h6>
            <a href="?page=proposal_tampil" class="btn btn-primary btn-icon-split btn-sm">
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
                    <input type="text" class="form-control" name="nama_perusahaan" required maxlength="100" value="<?= htmlspecialchars($data['nama_perusahaan']) ?>" readonly>
                    <small class="text-muted">Catatan: nama perusahaan sesuai proposal</small>
                </div>

                <div class="form-group mb-2">
                    <label class="form-label">Tahun Usulan Kegiatan</label>
                    <select class="form-control" name="tahun" id="tahun" required>
                        <option value="">-- Pilih Tahun --</option>
                    </select>
                </div>

                <div class="form-group mb-2">
                    <label class="form-label">Upload Berkas (PDF, DOC, DOCX, XLS, XLSX)</label>
                    <input type="file" name="upload" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">
                    <?php if (!empty($data['upload'])): ?>
                        <small class="text-success">File saat ini: <a href="<?= $data['upload'] ?>" target="_blank">Lihat Berkas</a></small><br>
                    <?php endif; ?>
                    <small class="text-danger">Max File 10Mb. Kosongkan jika tidak ingin mengubah file.</small>
                </div>

                <!-- Tombol Simpan dan Batal -->
                <div class="mb-3">
                    <button type="submit" class="btn btn-success">Perbarui</button>
                    <button type="reset" class="btn btn-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const tahunSelect = document.getElementById('tahun');
    const currentYear = new Date().getFullYear();
    const startYear = currentYear - 1;
    const endYear = currentYear + 10;
    const selectedYear = "<?= isset($data['tahun']) ? $data['tahun'] : '' ?>";

    for (let year = startYear; year <= endYear; year++) {
        const option = document.createElement("option");
        option.value = year;
        option.textContent = year;
        if (year == selectedYear) {
            option.selected = true;
        }
        tahunSelect.appendChild(option);
    }
</script>

