<?php
ob_start();

require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

session_start();
require '../../config/connection.php';

if (!isset($_SESSION['id_usuarios'])) {
    echo 'No has iniciado sesión o tu sesión ha expirado.';
    header('Location: ../../login.php');
    exit();
}

$id_usuario = $_SESSION['id_usuarios'];
$db = new Database();
$con = $db->conectar();

$sql = "SELECT * FROM wire_failures";
$stmt = $con->prepare($sql);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->getStyle('A1:Z1000')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
$sheet->getStyle('A1:Z1000')->getFill()->getStartColor()->setRGB('FFFFFF');

$logoPath = '../../img/infinex.png';
if (file_exists($logoPath)) {
    $drawing = new Drawing();
    $drawing->setName('Logo');
    $drawing->setDescription('Logo de Infinex');
    $drawing->setPath($logoPath);
    $drawing->setWidth(500);
    $drawing->setHeight(150);
    $drawing->setCoordinates('A1');
    $drawing->setWorksheet($sheet);
} else {
    echo 'El archivo de imagen no se encuentra en la ruta especificada.';
    exit();
}

// Añadir el encabezado superior
$sheet->mergeCells('B9:H9');
$sheet->setCellValue('B9', 'THE WIRE PRESENT THE FAILURE AT POSITION');
$sheet->getStyle('B9')->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 14],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '000000']],
]);

// Establecer los títulos con colores específicos
$sheet->setCellValue('B10', 'Box');
$sheet->setCellValue('C10', 'A');
$sheet->setCellValue('D10', 'B');
$sheet->setCellValue('E10', 'C');
$sheet->setCellValue('F10', 'A & B');
$sheet->setCellValue('G10', 'Goods');
$sheet->setCellValue('H10', 'Total');

// Estilo para las celdas del encabezado
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 14],
    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '000000']],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
    'borders' => [
        'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
    ],
];

// Estilo específico para los títulos en azul
$blueTitleStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => '0f7ecb'], 'size' => 14],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
    'borders' => [
        'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
    ],
];

// Estilo específico para el título en verde
$greenTitleStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => '0fcb59'], 'size' => 14],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
    'borders' => [
        'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
    ],
];

$sheet->getStyle('B10:F10')->applyFromArray($blueTitleStyle);
$sheet->getStyle('G10')->applyFromArray($greenTitleStyle);
$sheet->getStyle('H10')->applyFromArray($headerStyle);

$row = 11;
$totalGeneral = 0;
foreach ($records as $record) {
    $sheet->setCellValue('B' . $row, $record['box'] == 0 ? '' : $record['box']);
    $sheet->setCellValue('C' . $row, $record['a'] == 0 ? '' : $record['a']);
    $sheet->setCellValue('D' . $row, $record['b'] == 0 ? '' : $record['b']);
    $sheet->setCellValue('E' . $row, $record['c'] == 0 ? '' : $record['c']);
    $sheet->setCellValue('F' . $row, $record['a_and_b'] == 0 ? '' : $record['a_and_b']);
    $sheet->setCellValue('G' . $row, $record['goods'] == 0 ? '' : $record['goods']);
    $sheet->setCellValue('H' . $row, $record['total'] == 0 ? '' : $record['total']);

    $totalGeneral += $record['total'];
    $row++;
}

$dataStyle = [
    'font' => ['color' => ['rgb' => '000000'], 'size' => 12],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
    'borders' => [
        'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
    ],
];
$sheet->getStyle('B11:H' . ($row - 1))->applyFromArray($dataStyle);

$sheet->setCellValue('F' . $row, 'TOTAL GENERAL:');
$sheet->setCellValue('H' . $row, $totalGeneral);

$totalStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff'], 'size' => 14],
    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '000000']],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
    'borders' => [
        'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
    ],
];
$sheet->getStyle('F' . $row . ':H' . $row)->applyFromArray($totalStyle);

foreach (range('B', 'H') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$writer = new Xlsx($spreadsheet);

$filename = 'infinex.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

ob_end_clean();

$writer->save('php://output');
exit();
