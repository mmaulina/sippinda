<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once 'koneksi.php';

if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='login.php';</script>";
    exit;
}

$id_user = $_SESSION['id_user'];
$role = $_SESSION['role'] ?? '';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Tandai semua konten baru sebagai dilihat
    $query = "INSERT IGNORE INTO djih_dilihat (id_user, konten_id) 
              SELECT :id_user, id FROM news";
    $stmt = $conn->prepare($query);
    $stmt->execute(['id_user' => $id_user]);

    // Ambil semua konten
    $sql = "SELECT * FROM djih ORDER BY tanggal DESC";
    $stmt = $conn->query($sql);
    $konten_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Kelompokkan berdasarkan id_title
    $grouped_konten = [];
    foreach ($konten_list as $konten) {
        $grouped_konten[$konten['id_title']][] = $konten;
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<div class="container-fluid mt-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Jaringan Dokumentasi dan Informasi Hukum</h6>
            <?php if ($role == 'admin' || $role == 'superadmin'): ?>
                <a href="?page=jdih_tabel" class="btn btn-primary btn-sm">
                    <i class="fas fa-table fa-sm text-white-50"></i> Daftar Konten
                </a>
            <?php endif; ?>
        </div>

        <div class="card-body" style="max-height: 500px; overflow-y: auto;">
            <div class="timeline position-relative">
                <?php foreach ($grouped_konten as $id_title => $kontens) : ?>
                    <div class="timeline-item d-flex flex-column align-items-center text-center position-relative">
                        <div class="circle bg-dark rounded-circle position-absolute" style="width: 15px; height: 15px; left: -10px; top: 50%; transform: translateY(-50%);"></div>
                        <div class="content w-75 ms-3 mb-3 p-4 border rounded shadow-sm bg-white">
                            <h5 class="card-text mt-3"><?php echo htmlspecialchars($kontens[0]['title']); ?></h5>
                            <div class="d-flex flex-column align-items-center mt-3">
                                <?php foreach ($kontens as $konten) : ?>
                                    <div class="text-center">
                                        <?php if ($konten['jenis_konten'] === 'gambar') : ?>
                                            <img src="<?php echo htmlspecialchars($konten['konten']); ?>"
                                                class="img-fluid rounded"
                                                style="width: 250px; height: auto; cursor: pointer;"
                                                alt="Konten Gambar"
                                                data-bs-toggle="modal"
                                                data-bs-target="#imageModal"
                                                data-img="<?php echo htmlspecialchars($konten['konten']); ?>">
                                        <?php elseif ($konten['jenis_konten'] === 'file') : ?>
                                            <a href="<?php echo htmlspecialchars($konten['konten']); ?>" class="btn btn-secondary" style="width: 150px;">Download File</a>
                                        <?php elseif ($konten['jenis_konten'] === 'link') : ?>
                                            <a href="<?php echo htmlspecialchars($konten['konten']); ?>" target="_blank" class="btn btn-info" style="width: 150px;">Lihat Link</a>
                                        <?php endif; ?>
                                        <p class="mt-2 small"><?php echo nl2br(htmlspecialchars($konten['caption'])); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <p class="card-text mt-3"><small class="text-muted">Diupload pada: <?php echo $kontens[0]['tanggal']; ?></small></p>
                        </div>
                        <div class="line position-absolute bg-dark" style="width: 2px; height: 100%; left: 0px; top: 0;"></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <img src="" id="modalImage" class="img-fluid rounded" alt="Preview Gambar">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const imageModal = document.getElementById('imageModal');
    imageModal.addEventListener('show.bs.modal', function(event) {
        const img = event.relatedTarget;
        const src = img.getAttribute('data-img');
        const modalImg = imageModal.querySelector('#modalImage');
        modalImg.src = src;
    });
</script>