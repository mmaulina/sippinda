<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_user']) || !isset($_SESSION['role'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='login.php';</script>";
    exit;
}

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$id_user = $_SESSION['id_user'];
$role = $_SESSION['role'];

try {
    require_once 'koneksi.php';
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT * FROM profil");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Judul
    $sheet->setCellValue('A1', 'DATA PERUSAHAAN LIST');
    $sheet->mergeCells('A1:L1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Header
    $headers = [
        'No.',
        'Nama Perusahaan',
        'Kabupaten/Kota',
        'Alamat',
        'Jenis Usaha',
        'Email'
    ];

    if ($role === 'superadmin') {
        $headers[] = 'No. Telp. Kantor';
        $headers[] = 'No. HP Pimpinan';
    }

    $headers[] = 'Tenaga Teknik';

    if ($role === 'superadmin') {
        $headers[] = 'No. HP Tenaga Teknik';
        $headers[] = 'Nomor HP Admin';
    }

    $headers[] = 'Nama Admin';

    // Tulis header
    $column = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($column . '3', $header);
        $sheet->getStyle($column . '3')->getFont()->setBold(true);
        $column++;
    }

    // Terapkan style header hanya jika ada data
    if (!empty($data)) {
        $lastCol = chr(ord('A') + count($headers) - 1);
        $sheet->getStyle("A3:{$lastCol}3")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
    }

    // Tulis data
    $rowNum = 4;
    $no = 1;
    foreach ($data as $row) {
        $col = 'A';
        $sheet->setCellValue($col++ . $rowNum, $no++);
        $sheet->setCellValue($col++ . $rowNum, $row['nama_perusahaan']);
        $sheet->setCellValue($col++ . $rowNum, $row['kabupaten']);
        $sheet->setCellValue($col++ . $rowNum, $row['alamat']);
        $sheet->setCellValue($col++ . $rowNum, $row['jenis_usaha']);
        $sheet->setCellValue($col++ . $rowNum, $row['email']);

        if ($role === 'superadmin') {
            $sheet->setCellValue($col++ . $rowNum, $row['no_telp_kantor']);
            $sheet->setCellValue($col++ . $rowNum, $row['no_hp_pimpinan']);
        }

        $sheet->setCellValue($col++ . $rowNum, $row['tenaga_teknik']);

        if ($role === 'superadmin') {
            $sheet->setCellValue($col++ . $rowNum, $row['no_hp_teknik']);
            $sheet->setCellValue($col++ . $rowNum, $row['no_hp']);
        }

        $sheet->setCellValue($col++ . $rowNum, $row['nama']);
        $rowNum++;
    }

    // Border & Auto Size jika ada data
    if (!empty($data)) {
        $lastCol = chr(ord('A') + count($headers) - 1);
        $sheet->getStyle("A3:{$lastCol}" . ($rowNum - 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    $fileName = 'data_perusahaan_' . time() . '.xlsx';
    $filePath = 'exports/' . $fileName;

    if (!file_exists('exports')) {
        mkdir('exports', 0777, true);
    }

    foreach (glob('exports/*.xlsx') as $file) {
        if (filemtime($file) < time() - 86400) {
            unlink($file);
        }
    }

    $writer = new Xlsx($spreadsheet);
    $writer->save($filePath);

    echo "
    <html>
    <head>
        <script>
            function startDownload() {
                document.getElementById('loading').style.display = 'block';
                setTimeout(() => {
                    window.location.href = '$filePath';
                    setTimeout(() => {
                        document.getElementById('loading').innerHTML = 'Download selesai, kembali ke halaman...';
                        setTimeout(() => {
                            window.location.href = '?page=profil_admin';
                        }, 2000);
                    }, 2000);
                }, 1000);
            }
        </script>
        <style>
            #loading {
                display: none;
                width: 100%;
                height: 100vh;
                position: fixed;
                top: 0;
                left: 0;
                background: rgba(0, 0, 0, 0.7);
                color: white;
                font-size: 20px;
                text-align: center;
                line-height: 100vh;
                z-index: 9999;
            }
        </style>
    </head>
    <body onload='startDownload()'>
        <div id='loading'>Sedang mengunduh, harap tunggu...</div>
    </body>
    </html>";
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
