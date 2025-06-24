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
    $tahun = sanitizeInput($_POST['tahun']);
    $triwulan_final = sanitizeInput($_POST['triwulan_final']);
    $upload = uploadFile('upload', $nama_perusahaan);

    if (!empty($_FILES['upload']['name'])) {
        if ($upload === null) {
            echo "<script>alert('Upload file gagal! Pastikan memilih file dengan format yang benar (pdf/jpg/png) dan ukuran maksimal 5MB.'); history.back();</script>";
            exit;
        }
    }


    $status = 'diajukan';
    $keterangan = '-';

    $updateSQL = "UPDATE data_sinas SET 
    nama_perusahaan = :nama_perusahaan,
    tahun=:tahun, 
    triwulan=:triwulan_final,
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
    $stmt->bindParam(':triwulan_final', $triwulan_final, PDO::PARAM_STR);
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

    echo "<meta http-equiv='refresh' content='0; url=?page=data_siinas_tampil'>";
}

$database = new Database();
$db = $database->getConnection();
$query = "SELECT * FROM data_sinas WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id_laporan);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='?page=data_siinas_tampil';</script>";
    exit;
}


function uploadFile($input_name, $nama_perusahaan)
{
    if (!empty($_FILES[$input_name]['name'])) {
        $maxSize = 5 * 1024 * 1024; // 10MB
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

        $allowed_types = ['pdf', 'jpg', 'png'];
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
            <h6 class="m-0 font-weight-bold text-primary">Edit Data Sistem Informasi Industri Nasional</h6>
            <a href="?page=siinas_tampil" class="btn btn-primary btn-icon-split btn-sm">
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
                    <small class="text-muted">Catatan: nama perusahaan sesuai perizinan</small>
                </div>

                <div class="form-group mb-2">
                    <label class="form-label">Upload Berkas (PDF, JPG, PNG)</label>
                    <input type="file" name="upload" class="form-control" accept=".pdf,.jpg,.png">
                    <?php if (!empty($data['upload'])): ?>
                        <small class="text-success">File saat ini: <a href="<?= $data['upload'] ?>" target="_blank">Lihat Berkas</a></small><br>
                    <?php endif; ?>
                    <small class="text-danger">Max File 5MB</small>
                </div>

                <div class="form-group mb-2">
                    <label class="form-label">Tahun</label>
                    <select class="form-control" name="tahun" id="tahun" required>
                        <option value="">-- Pilih Tahun --</option>
                        <?php
                        // Isi dropdown tahun dari currentYear sampai endYear
                        for ($year = 2025; $year <= 2035; $year++) {
                            $selected = ($data['tahun'] == $year) ? 'selected' : '';
                            echo "<option value='$year' $selected>$year</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group mb-2">
                    <label>Triwulan</label>
                    <select class="form-control" name="triwulan" id="triwulan" required>
                        <option value="">-- Pilih Triwulan --</option>
                        <option value="Triwulan I" <?php echo ($data['triwulan'] == 'Triwulan I') ? 'selected' : ''; ?> id="triwulan1">Triwulan I</option>
                        <option value="Triwulan II" <?php echo ($data['triwulan'] == 'Triwulan II') ? 'selected' : ''; ?> id="triwulan2">Triwulan II</option>
                        <option value="Triwulan III" <?php echo ($data['triwulan'] == 'Triwulan III') ? 'selected' : ''; ?> id="triwulan3">Triwulan III</option>
                        <option value="Triwulan IV" <?php echo ($data['triwulan'] == 'Triwulan IV') ? 'selected' : ''; ?> id="triwulan4">Triwulan IV</option>
                    </select>
                    <p style="color: red; font-size: 0.875em; margin-top: 5px;">
                        * Untuk Triwulan yang sudah terlewat, pengisian tidak dapat dilakukan
                    </p>
                </div>
                <input type="hidden" name="triwulan_final" id="triwulan_final">

                <div class="form-group mb-2">
                    <label class="form-label d-block">Pelaporan</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_pelaporan" id="tahap_konstruksi" value="Tahap Konstruksi"
                            <?= ($data['jenis_pelaporan'] == 'Tahap Konstruksi') ? 'checked' : '' ?> required>
                        <label class="form-check-label" for="tahap_konstruksi">Tahap Konstruksi</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_pelaporan" id="tahap_produksi" value="Tahap Produksi"
                            <?= ($data['jenis_pelaporan'] == 'Tahap Produksi') ? 'checked' : '' ?> required>
                        <label class="form-check-label" for="tahap_produksi">Tahap Produksi</label>
                    </div>
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
    const triwulan1 = document.getElementById('triwulan1');
    const triwulan2 = document.getElementById('triwulan2');
    const triwulan3 = document.getElementById('triwulan3');
    const triwulan4 = document.getElementById('triwulan4');

    const today = new Date();
    const currentYear = today.getFullYear();

    const batasTriwulan = {
        triwulan1: {
            mulai: new Date(`${currentYear}-01-01`),
            akhir: new Date(`${currentYear}-04-15`)
        },
        triwulan2: {
            mulai: new Date(`${currentYear}-04-01`),
            akhir: new Date(`${currentYear}-07-10`)
        },
        triwulan3: {
            mulai: new Date(`${currentYear}-07-01`),
            akhir: new Date(`${currentYear}-10-10`)
        },
        triwulan4: {
            mulai: new Date(`${currentYear}-10-01`),
            akhir: new Date(`${currentYear}-01-10`) // tahun depan
        }
    };

    function isInRange(date, start, end) {
        return date >= start && date <= end;
    }

    function updateTriwulanOption(optionEl, label, aktif) {
        optionEl.disabled = !aktif;
        optionEl.text = aktif ? label : `${label} 🔒`;
    }

    updateTriwulanOption(triwulan1, 'Triwulan I', isInRange(today, batasTriwulan.triwulan1.mulai, batasTriwulan.triwulan1.akhir));
    updateTriwulanOption(triwulan2, 'Triwulan II', isInRange(today, batasTriwulan.triwulan2.mulai, batasTriwulan.triwulan2.akhir));
    updateTriwulanOption(triwulan3, 'Triwulan III', isInRange(today, batasTriwulan.triwulan3.mulai, batasTriwulan.triwulan3.akhir));
    updateTriwulanOption(triwulan4, 'Triwulan IV', isInRange(today, batasTriwulan.triwulan4.mulai, batasTriwulan.triwulan4.akhir));

    // =================== Dropdown Tahun ===================
    const tahunSelect = document.getElementById('tahun');
    const triwulanSelect = document.getElementById('triwulan');
    const triwulanFinal = document.getElementById('triwulan_final');

    const startYear = currentYear - 1;
    const endYear = currentYear + 10;

    for (let year = startYear; year <= endYear; year++) {
        const option = document.createElement("option");
        option.value = year;
        option.text = year;
        tahunSelect.appendChild(option);
    }

    function updatetriwulanFinal() {
        const tahun = tahunSelect.value;
        const triwulan = triwulanSelect.value;
        if (tahun && triwulan) {
            triwulanFinal.value = `${triwulan} ${tahun}`;
        } else {
            triwulanFinal.value = "";
        }
    }

    tahunSelect.addEventListener('change', updatetriwulanFinal);
    triwulanSelect.addEventListener('change', updatetriwulanFinal);
</script>