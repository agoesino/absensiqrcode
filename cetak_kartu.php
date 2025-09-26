<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

include 'config.php';
require 'fpdf/fpdf.php';

// Ambil logo sekolah
$profil = mysqli_fetch_assoc(mysqli_query($conn, "SELECT logo FROM profil_sekolah LIMIT 1"));
$logo_path = null;
if ($profil && !empty($profil['logo'])) {
    $logo_path = __DIR__ . '/uploads/' . $profil['logo'];
}

// Ambil data siswa urutkan berdasarkan kelas dan nama
$result = mysqli_query($conn, "SELECT * FROM siswa ORDER BY kelas ASC, nama ASC");

class PDF extends FPDF
{
    public $logo_path;

    // Footer halaman
    function Footer()
    {
        // Posisi 15 mm dari bawah
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Teks footer
        $this->Cell(0, 10, 'Aplikasi lainnya unduh di: www.tasadmin.id', 0, 0, 'C');
    }
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->logo_path = $logo_path;
$pdf->SetAutoPageBreak(false);

$card_width = 95;
$card_height = 50;
$margin_x = 7;
$margin_y = 10;
$spacing_x = 5;
$spacing_y = 5;

$x = $margin_x;
$y = $margin_y;
$count = 0;

while ($data = mysqli_fetch_assoc($result)) {
    if ($count % 10 == 0) {
        $pdf->AddPage();
        $x = $margin_x;
        $y = $margin_y;
    }

    $pdf->Rect($x, $y, $card_width, $card_height);

    if ($logo_path && file_exists($logo_path)) {
        $pdf->Image($logo_path, $x + 2, $y + 2, 12, 12);
    }
    $pdf->SetXY($x + 16, $y + 4);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 5, 'Kartu Pelajar', 0, 1);

    $pdf->SetXY($x + 2, $y + 16);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(50, 4, 'NIS : ' . $data['nis'], 0, 1);
    $pdf->SetX($x + 2);
    $pdf->Cell(50, 4, 'NISN: ' . $data['nisn'], 0, 1);
    $pdf->SetX($x + 2);
    $pdf->Cell(50, 4, 'Nama: ' . $data['nama'], 0, 1);
    $pdf->SetX($x + 2);
    $pdf->Cell(50, 4, 'Kelas: ' . $data['kelas'], 0, 1);

    $qr_path = "assets/qr/" . $data['nisn'] . ".png";
    if (file_exists($qr_path)) {
        $pdf->Image($qr_path, $x + $card_width - 22, $y + $card_height - 22, 20, 20);
    } else {
        $pdf->SetXY($x + $card_width - 23, $y + $card_height - 10);
        $pdf->Cell(18, 5, 'QR Missing', 0, 1, 'C');
    }

    if ($x + $card_width + $spacing_x > 210 - $margin_x) {
        $x = $margin_x;
        $y += $card_height + $spacing_y;
    } else {
        $x += $card_width + $spacing_x;
    }

    $count++;
}

$pdf->Output('I', 'kartu_pelajar.pdf');
