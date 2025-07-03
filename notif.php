<?php
if (isset($_SESSION['hasil']) && isset($_SESSION['pesan'])) {
    if ($_SESSION['hasil']) {
        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                {$_SESSION['pesan']}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
    } else {
        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                {$_SESSION['pesan']}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
    }

    // Hapus session setelah ditampilkan
    unset($_SESSION['hasil']);
    unset($_SESSION['pesan']);
}
?>
