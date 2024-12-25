<?php
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
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

$sql = "SELECT r.*, u.firstname, u.lastname, u.email
        FROM reporte r
        JOIN usuarios u ON r.id_usuario = u.id_usuarios";
$stmt = $con->prepare($sql);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$logoPath = '../ico/comter.png';
if (file_exists($logoPath)) {
    $drawing = new Drawing();
    $drawing->setName('Logo');
    $drawing->setDescription('Logo de Infinex');
    $drawing->setPath($logoPath);
    $drawing->setWidth(250);
    $drawing->setHeight(100);
    $drawing->setCoordinates('A1');
    $drawing->setWorksheet($sheet);
} else {
    echo 'El archivo de imagen no se encuentra en la ruta especificada.';
    exit();
}

$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size' => 12,
        'name' => 'Arial'
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '1F4E78'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => 'FFFFFF'],
        ],
    ],
];

$dataStyle = [
    'font' => [
        'size' => 11,
        'name' => 'Arial',
        'color' => ['rgb' => '000000'],
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => 'DDDDDD'],
        ],
    ],
    'alignment' => [
        'vertical' => Alignment::VERTICAL_CENTER,
        'horizontal' => Alignment::HORIZONTAL_LEFT,
    ],
];

$headers = [
    'Folio Captura', 'Folio Requerimiento de Servicio', 'Cliente/Fabricante', 
    'Fecha Reporte', 'Caja', 'PO/Skid', 'Número de Parte', 
    'Date Code. Lote o Fecha de Fabricación', 'Descripción', 'Nombre del Operador', 
    'Horario', 'Horas o Minutos', 'Rate Meta', 'Rate Real', 
    'Diferencia Rate', 'Total Defectos', 'Buenas', 
    'Total Inspeccionadas', 'Comentarios Defecto', 'Total Inspeccionadas C', 'Comentarios Sorteo'
];

$column = 'A';
$rowHeader = 7;
foreach ($headers as $header) {
    $sheet->setCellValue($column . $rowHeader, $header);
    $sheet->getStyle($column . $rowHeader)->applyFromArray($headerStyle);
    $column++;
}

$row = $rowHeader + 1;
foreach ($records as $record) {
    $sheet->setCellValue('A' . $row, $record['folio_captura']);
    $sheet->setCellValue('B' . $row, $record['folio_requisicion']);
    $sheet->setCellValue('C' . $row, $record['cliente_fabricante']);
    $date = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($record['fecha_reporte']));
    $sheet->setCellValue('D' . $row, $date);
    $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
    $sheet->setCellValue('E' . $row, $record['caja']);
    $sheet->setCellValue('F' . $row, $record['po_skid']);
    $sheet->setCellValue('G' . $row, $record['num_parte']);
    $sheet->setCellValue('H' . $row, $record['date_code']);
    $sheet->setCellValue('I' . $row, $record['descripcion']);
    $sheet->setCellValue('J' . $row, $record['nombre_operador']);
    $sheet->setCellValue('K' . $row, $record['horario']);
    $sheet->setCellValue('L' . $row, $record['productividad_a']);
    $sheet->setCellValue('M' . $row, $record['productividad_b']);
    $sheet->setCellValue('N' . $row, $record['total_inspeccionadas']);
    $sheet->setCellValue('O' . $row, $record['defectos_y_descripcion']);
    $sheet->setCellValue('P' . $row, $record['total_defectos']);
    $sheet->setCellValue('Q' . $row, $record['buenas']);
    $sheet->setCellValue('R' . $row, $record['total_inspeccionadas']);
    $sheet->setCellValue('S' . $row, $record['defectos_y_descripcion']);
    $sheet->setCellValue('T' . $row, $record['total_inspeccionadas_c']);
    $sheet->setCellValue('U' . $row, $record['comentarios_descripcion_sorteo']);
    $sheet->getStyle("A$row:U$row")->applyFromArray($dataStyle);
    $row++;
}

foreach (range('A', 'U') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$filename = 'reporte_estilizado_con_logo.xlsx';

if (ob_get_length()) {
    ob_end_clean();
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
