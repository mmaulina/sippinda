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

    $upload_berkas = uploadFile('upload', $nama_perusahaan);
    $tahun = sanitize_input($_POST['tahun']);
    $triwulan_final = sanitize_input($_POST['triwulan_final']);

    if ($upload_berkas === null) {
        echo "<script>alert('Upload file gagal! Pastikan memilih file dengan format yang benar (pdf/jpg/png) dan ukuran maksimal 5MB.'); history.back();</script>";
        exit;
    }

    $status = 'diajukan';
    $keterangan = '-';

    try {
        if ($profil && !empty($profil['nama_perusahaan'])) {
            $nama_perusahaan = $profil['nama_perusahaan'];

            $sql = "INSERT INTO data_sinas (id_user, nama_perusahaan, upload, tahun, triwulan, status, keterangan) 
                    VALUES (:id_user, :nama_perusahaan, :upload, :tahun, :triwulan, :status, :keterangan)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
            $stmt->bindParam(':nama_perusahaan', $nama_perusahaan, PDO::PARAM_STR);
            $stmt->bindParam(':upload', $upload_berkas, PDO::PARAM_STR);
            $stmt->bindParam(':tahun', $tahun);
            $stmt->bindParam(':triwulan', $triwulan_final);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':keterangan', $keterangan, PDO::PARAM_STR);


            if ($stmt->execute()) {
                echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='?page=data_siinas_tampil';</script>";
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

<!-- TAMBAH PERIZINAN -->
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Data Sistem Informasi Industri Nasional</h6>
            <a href="?page=data_siinas_tampil" class="btn btn-primary btn-icon-split btn-sm">
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
                    <label for="upload_berkas">Upload Berkas (PDF, JPG, PNG)</label>
                    <input type="file" name="upload" class="form-control" accept=".pdf,.jpg,.png">
                    <small class="text-danger">Max File 5Mb</small>
                </div>

                <div class="form-group mb-2">
                    <label class="form-label">Tahun</label>
                    <select class="form-control" name="tahun" id="tahun" required>
                        <option value="">-- Pilih Tahun --</option>
                    </select>
                </div>

                <div class="form-group mb-2">
                    <label>Triwulan</label>
                    <select class="form-control" name="triwulan" id="triwulan" required>
                        <option value="">-- Pilih Triwulan --</option>
                        <option value="Triwulan I" id="triwulan1">Triwulan I</option>
                        <option value="Triwulan II" id="triwulan2">Triwulan II</option>
                        <option value="Triwulan III" id="triwulan3">Triwulan III</option>
                        <option value="Triwulan IV" id="triwulan4">Triwulan IV</option>
                    </select>
                    <p style="color: red; font-size: 0.875em; margin-top: 5px;">
                        * Untuk Triwulan yang sudah terlewat, pengisian tidak dapat dilakukan
                    </p>
                </div>
                <input type="hidden" name="triwulan_final" id="triwulan_final">

                <div class="form-group mb-3">
                    <label class="form-label d-block">Jenis Pelaporan</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_pelaporan" id="tahap_konstruksi" value="Tahap Konstruksi" required>
                        <label class="form-check-label" for="tahap_konstruksi">Tahap Konstruksi</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_pelaporan" id="tahap_produksi" value="Tahap Produksi" required>
                        <label class="form-check-label" for="tahap_produksi">Tahap Produksi</label>
                    </div>
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