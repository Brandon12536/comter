<?php
require '../../vendor/autoload.php';
require_once '../../config/connection.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();


$sheet->getStyle('A1:Z1000')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');


$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
$drawing->setName('Logo');
$drawing->setDescription('Logo Comter');
$drawing->setPath(dirname(dirname(__DIR__)) . '/ico/comter.png');
$drawing->setCoordinates('B1');
$drawing->setWidth(150);
$drawing->setHeight(50);
$drawing->setOffsetX(5);
$drawing->setOffsetY(5);
$drawing->setWorksheet($spreadsheet->getActiveSheet());


$sheet->getRowDimension(1)->setRowHeight(60);
$sheet->getRowDimension(2)->setRowHeight(20);


$sheet->getColumnDimension('A')->setWidth(5);
$sheet->getColumnDimension('B')->setWidth(10);
$sheet->getColumnDimension('C')->setWidth(30);
$sheet->getColumnDimension('D')->setWidth(35);
$sheet->getColumnDimension('E')->setWidth(20);
$sheet->getColumnDimension('F')->setWidth(25);
$sheet->getColumnDimension('G')->setWidth(25);
$sheet->getColumnDimension('H')->setWidth(20);
$sheet->getColumnDimension('I')->setWidth(20);



$sheet->setCellValue('B3', 'NO.');
$sheet->setCellValue('C3', 'NOMBRE');
$sheet->setCellValue('D3', 'CORREO ELECTRONICO');
$sheet->setCellValue('E3', 'TELEFONO PROPIO O DE CONTACTO');
$sheet->setCellValue('F3', 'COMPAÑIA');
$sheet->setCellValue('G3', 'FECHA ALTA');
$sheet->setCellValue('H3', 'DEPARTAMENTO');
$sheet->setCellValue('I3', 'PUESTO');
$sheet->setCellValue('J3', 'TURNO');


$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => '000000'],
        'size' => 11,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];


$sheet->getStyle('B3:C3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('90EE90');
$sheet->getStyle('D3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('ADD8E6');
$sheet->getStyle('E3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D3D3D3');
$sheet->getStyle('F3:G3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('90EE90');
$sheet->getStyle('H3:J3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('ADD8E6');


$sheet->getStyle('B3:J3')->applyFromArray($headerStyle);


$sheet->getStyle('B3:J3')->getFont()->setBold(true);
$sheet->getStyle('B3:J3')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('000000'));


$db = new Database();
$con = $db->conectar();

$query = "SELECT 
            ROW_NUMBER() OVER (ORDER BY p.id_proveedor) as numero,
            CONCAT(p.nombre, ' ', p.apellido) as nombre_completo,
            p.correo,
            p.telefono,
            p.compania,
            p.created_at,
            p.departamento,
            p.puesto,
            t.turno_completo as turno
          FROM proveedores p
          LEFT JOIN turnos t ON p.id_turno = t.id_turno
          ORDER BY p.id_proveedor";

$stmt = $con->prepare($query);
$stmt->execute();
$row = 4;

while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (!empty($data['nombre_completo'])) {

        setlocale(LC_TIME, 'es_ES.UTF-8', 'spanish');
        $fecha = strtotime($data['created_at']);
        $fecha_formateada = strftime("%A %d %B %Y", $fecha);

        $sheet->setCellValue('B' . $row, $data['numero']);
        $sheet->setCellValue('C' . $row, $data['nombre_completo']);
        $sheet->setCellValue('D' . $row, $data['correo']);
        $sheet->setCellValue('E' . $row, $data['telefono']);
        $sheet->setCellValue('F' . $row, $data['compania']);
        $sheet->setCellValue('G' . $row, ucfirst($fecha_formateada));
        $sheet->setCellValue('H' . $row, $data['departamento']);
        $sheet->setCellValue('I' . $row, $data['puesto']);
        $sheet->setCellValue('J' . $row, $data['turno']);


        $sheet->getStyle('B' . $row . ':J' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


        $sheet->getRowDimension($row)->setRowHeight(-1);

        $row++;
    }
}

if ($sheet->getCell('C' . ($row - 1))->getValue() === null) {
    $sheet->removeRow($row - 1);
    $row--;
}


$sheet->getStyle('B4:J' . $row)->getAlignment()->setWrapText(true);
$sheet->getStyle('B4:J' . $row)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);


foreach (range('B', 'J') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}


$sheet->getStyle('B4:J' . $row)->applyFromArray([
    'alignment' => [
        'vertical' => Alignment::VERTICAL_CENTER,
        'horizontal' => Alignment::HORIZONTAL_LEFT,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
]);


$sheet->getStyle('B4:B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // NO.
$sheet->getStyle('E4:E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // TELEFONO
$sheet->getStyle('J4:J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // TURNO


$sheet->getStyle('B3:J3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="SEM-07.xlsx"');
header('Cache-Control: max-age=0');


$writer->setPreCalculateFormulas(false);

$writer->save('php://output');
exit;
?>