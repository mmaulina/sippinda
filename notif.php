<?php
if (isset($_SESSION['hasil']) && isset($_SESSION['pesan'])) {
    if ($_SESSION['hasil']) {
        echo "<div class='alert alert-success alert-dismissible auto-dismiss' role='alert' style='position:relative; padding-right:3rem;'>
                {$_SESSION['pesan']}
                <button type='button' onclick='this.parentElement.style.display=\"none\";' style='position:absolute; right:10px; top:10px; background:none; border:none; font-size:1.2rem;'>&times;</button>
              </div>";
    } else {
        echo "<div class='alert alert-danger alert-dismissible auto-dismiss' role='alert' style='position:relative; padding-right:3rem;'>
                {$_SESSION['pesan']}
                <button type='button' onclick='this.parentElement.style.display=\"none\";' style='position:absolute; right:10px; top:10px; background:none; border:none; font-size:1.2rem;'>&times;</button>
              </div>";
    }

    // Hapus session setelah ditampilkan
    unset($_SESSION['hasil']);
    unset($_SESSION['pesan']);
}
?>

<!-- JavaScript untuk menyembunyikan alert setelah 3 detik -->
<script>
    setTimeout(function () {
        var alert = document.querySelector('.auto-dismiss');
        if (alert) {
            alert.style.transition = "opacity 0.5s ease";
            alert.style.opacity = "0";
            setTimeout(function () {
                alert.style.display = "none";
            }, 500); // waktu animasi fade-out
        }
    }, 2000);
</script>
