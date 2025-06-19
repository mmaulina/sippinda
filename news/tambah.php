<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once 'koneksi.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='login.php';</script>";
    exit;
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();

    function sanitizeInput($input)
    {
        return strip_tags(trim($input));
    }

    $title = sanitizeInput($_POST['title']);
    $caption = $_POST['caption']; // array caption
    $jenis_konten = $_POST['jenis_konten']; // Array
    $konten = $_POST['konten']; // Array
    $tanggal = date('Y-m-d H:i:s'); // Tambahkan datetime

    // Buat ID Title otomatis
    $query = "SELECT id_title FROM news ORDER BY id_title DESC LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && isset($row['id_title'])) {
        // Ambil angka terakhir dan increment
        $lastNumber = (int)substr($row['id_title'], 4);
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1; // Jika belum ada data
    }
    $id_title = "TTL-" . str_pad($newNumber, 3, "0", STR_PAD_LEFT);

    foreach ($jenis_konten as $index => $jenis) {
        // Handle berbagai jenis konten
        if ($jenis == 'gambar' || $jenis == 'file') {
            $konten_value = uploadFile('konten_' . $index);
        } elseif ($jenis == 'link') {
            $konten_value = sanitizeInput($konten[$index]);
        }
        $captionValue = isset($caption[$index]) ? sanitizeInput($caption[$index]) : '';

        // Simpan ke database
        $insertSQL = "INSERT INTO news (id_title, title, caption, jenis_konten, konten, tanggal) 
                              VALUES (:id_title, :title, :caption, :jenis_konten, :konten, :tanggal)";
        $stmt = $db->prepare($insertSQL);

        $stmt->bindParam(':id_title', $id_title);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':caption', $captionValue);
        $stmt->bindParam(':jenis_konten', $jenis);
        $stmt->bindParam(':konten', $konten_value);
        $stmt->bindParam(':tanggal', $tanggal);

        if (!$stmt->execute()) {
            $_SESSION['hasil'] = false;
            $_SESSION['pesan'] = "Gagal Simpan Data";
            break;
        }
    }
    $_SESSION['hasil'] = true;
    $_SESSION['pesan'] = "Berhasil Simpan Data";
    echo "<meta http-equiv='refresh' content='0; url=?page=konten_tampil'>";
}

// Fungsi untuk upload file
function uploadFile($input_name)
{
    if (!empty($_FILES[$input_name]['name'])) {
        $target_dir = "uploads/";
        $file_name = basename($_FILES[$input_name]["name"]);
        $file_name = preg_replace("/[^a-zA-Z0-9.\-_]/", "", $file_name);
        $target_file = $target_dir . time() . "_" . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
        if (!in_array($file_type, $allowed_types)) {
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
            <h6 class="m-0 font-weight-bold text-primary">Tambah Konten</h6>
            <a href="?page=konten_tampil" class="btn btn-primary btn-icon-split btn-sm">
                <span class="icon text-white-50">
                    <i class="fas fa-arrow-left" style="vertical-align: middle; margin-top: 5px;"></i>
                </span>
                <span class="text">Kembali</span>
            </a>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group mb-2">
                    <label>Title</label>
                    <input type="text" class="form-control" name="title" placeholder="Masukkan Title Konten" required maxlength="100">
                </div>
                <div id="kontenInputs">
                <div class="form-group mb-2 konten-group">
                    <Label>Jenis Konten</Label>
                <select class="form-control" name="jenis_konten[]" required>
                        <option value="">-- Pilih Jenis Konten --</option>
                        <option value="gambar">Gambar</option>
                        <option value="file">File</option>
                        <option value="link">Link</option>
                        <option value="kosong">Kosong</option>
                    </select>

                    <!-- Input konten -->
                    <div class="mb-2">
                        <input type="file" name="konten_0" class="form-control file-input" style="display: none;" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx">
                        <input type="text" name="konten[]" class="form-control link-input" style="display: none;" placeholder="Masukkan link di sini, contoh: https://google.com/">
                    </div>

                    <!-- Caption per konten -->
                    <div class="mb-2">
                        <label>Caption Konten</label>
                        <textarea name="caption[]" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                </div>
                <!-- Tombol Simpan dan Batal -->
                <div class="mb-0">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="reset" class="btn btn-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelector('select[name="jenis_konten[]"]').addEventListener('change', function () {
    let jenis = this.value;
    let kontenGroup = this.closest('.konten-group');
    kontenGroup.querySelector('input[type="file"]').style.display = (jenis === 'gambar' || jenis === 'file') ? 'block' : 'none';
    kontenGroup.querySelector('input[type="text"]').style.display = (jenis === 'link') ? 'block' : 'none';
});

document.getElementById('addContent').addEventListener('click', function () {
    let kontenInputs = document.getElementById('kontenInputs');
    let index = kontenInputs.children.length;

    let newGroup = document.createElement('div');
    newGroup.className = 'form-group mb-3 konten-group';
    newGroup.innerHTML = `
        <label>Jenis Konten</label>
        <select class="form-control" name="jenis_konten[]" required>
            <option value="">-- Pilih Jenis Konten --</option>
            <option value="gambar">Gambar</option>
            <option value="file">File</option>
            <option value="link">Link</option>
            <option value="kosong">Kosong</option>
        </select>
        
        <div class="mb-2">
            <input type="file" name="konten_${index}" class="form-control file-input" style="display: none;" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx">
            <input type="text" name="konten[]" class="form-control link-input" style="display: none;" placeholder="Masukkan link di sini, contoh: https://desdm.kalselprov.go.id/">
        </div>

        <div class="mb-2">
            <label>Caption Konten</label>
            <textarea name="caption[]" class="form-control" rows="2" placeholder="Tulis caption..."></textarea>
        </div>
    `;

    kontenInputs.appendChild(newGroup);

    // Event listener untuk select yang baru ditambahkan
    newGroup.querySelector('select').addEventListener('change', function () {
        let jenis = this.value;
        let group = this.closest('.konten-group');
        group.querySelector('.file-input').style.display = (jenis === 'gambar' || jenis === 'file') ? 'block' : 'none';
        group.querySelector('.link-input').style.display = (jenis === 'link') ? 'block' : 'none';
    });
});

</script>