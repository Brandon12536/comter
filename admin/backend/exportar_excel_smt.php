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

ob_start();

$db = new Database();
$con = $db->conectar();

// Consulta para obtener todos los registros sin importar el id_proveedor
$sql_select = "SELECT * FROM materiales";
$stmt_select = $con->prepare($sql_select);
$stmt_select->execute();
$records = $stmt_select->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener los totales sin filtrar por id_proveedor
$sql_totals = "
    SELECT
        SUM(goods) AS total_goods,
        SUM(dedos_de_oro_contaminados) AS total_dedos_oro,
        SUM(faltante) AS total_faltante,
        SUM(desplazados) AS total_desplazados,
        SUM(insuficiencias) AS total_insuficiencias,
        SUM(despanelizados) AS total_despanelizados,
        SUM(desprendidos) AS total_despedidos,
        SUM(total) AS total_total,
        SUM(total_final) AS total_final
    FROM materiales";
$stmt_totals = $con->prepare($sql_totals);
$stmt_totals->execute();
$totals = $stmt_totals->fetch(PDO::FETCH_ASSOC);

$yield = ($totals['total_total'] != 0) ? round(($totals['total_goods'] / $totals['total_total']) * 100, 2) : 0;
$yield = min(max($yield, 0), 100);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->insertNewColumnBefore('A', 1);

$sheet->getStyle('A1:Z1000')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');

$drawing = new Drawing();
$drawing->setPath('../ico/comter.png');
$drawing->setHeight(90);
$drawing->setWidth(90);
$drawing->setCoordinates('B1');
$drawing->setOffsetX(10);
$drawing->setOffsetY(10);
$drawing->setWorksheet($sheet);

$sheet->setCellValue('G7', 'Fails Report');
$sheet->mergeCells('G7:M7');
$sheet->getStyle('G7:M7')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
$sheet->getStyle('G7:M7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getStyle('G7:M7')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('000000');
$sheet->getStyle('G7:M7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('000000');

$sheet->setCellValue('B8', 'INSPECTION DATE')
    ->setCellValue('C8', 'DESCRIPTION')
    ->setCellValue('D8', 'SHIFT')
    ->setCellValue('E8', 'OPERATORS')
    ->setCellValue('F8', 'GOODS')
    ->setCellValue('G8', 'DEDOS DE ORO CONTAMINADOS')
    ->setCellValue('H8', 'FALTANTE')
    ->setCellValue('I8', 'DESPLAZADOS')
    ->setCellValue('J8', 'INSUFICIENCIAS')
    ->setCellValue('K8', 'DESPANELIZADOS')
    ->setCellValue('L8', 'DESPRENDIDOS')
    ->setCellValue('M8', 'TOTAL')
    ->setCellValue('N8', 'TOTAL FINAL')
    ->setCellValue('O8', 'YIELD')
    ->setCellValue('P8', 'COMMENTS');

$sheet->getStyle('B8:P8')->getFont()->setBold(true);
$sheet->getStyle('B8:P8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getStyle('B8:P8')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9DAD9');
$sheet->getStyle('B8:P8')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('000000');

$sheet->getStyle('F8')->getFont()->getColor()->setRGB('0FCB59');
$sheet->getStyle('P8')->getFont()->getColor()->setRGB('0FCB59');
$sheet->getStyle('O8')->getFont()->getColor()->setRGB('0FCB59');

$row = 9;
$sheet->setCellValue("B$row", "GRAN TOTAL")
    ->setCellValue("F$row", number_format($totals['total_goods']))
    ->setCellValue("G$row", number_format($totals['total_dedos_oro']))
    ->setCellValue("H$row", number_format($totals['total_faltante']))
    ->setCellValue("I$row", number_format($totals['total_desplazados']))
    ->setCellValue("J$row", number_format($totals['total_insuficiencias']))
    ->setCellValue("K$row", number_format($totals['total_despanelizados']))
    ->setCellValue("L$row", number_format($totals['total_despedidos']))
    ->setCellValue("M$row", number_format($totals['total_total']))
    ->setCellValue("N$row", isset($totals['total_final']) ? number_format($totals['total_final']) : 0)
    ->setCellValue("O$row", $yield . "%");

$sheet->getStyle("B$row:P$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('000000');
$sheet->getStyle("B$row:P$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("B$row:P$row")->getFont()->setBold(true);
$sheet->getStyle("B$row:P$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9DAD9');

$row = 10;
foreach ($records as $record) {
    $descriptionImage = $record['descripcion_image'];
    $descriptionText = $record['descripcion'];

    $date = new DateTime($record['inspection_date']);
    $dayOfWeek = $date->format('l');
    $daysInSpanish = [
        'Monday' => 'Lunes',
        'Tuesday' => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves',
        'Friday' => 'Viernes',
        'Saturday' => 'Sábado',
        'Sunday' => 'Domingo'
    ];
    $formatted_date = $date->format('d/m/y') . " - " . $daysInSpanish[$dayOfWeek];

    $sheet->setCellValue("B$row", $formatted_date)
        ->setCellValue("C$row", $descriptionText ? $descriptionText : '')
        ->setCellValue("D$row", $record['shift'])
        ->setCellValue("E$row", $record['operators'])
        ->setCellValue("F$row", $record['goods'])
        ->setCellValue("G$row", $record['dedos_de_oro_contaminados'] != 0 ? $record['dedos_de_oro_contaminados'] : '')
        ->setCellValue("H$row", $record['faltante'] != 0 ? $record['faltante'] : '')
        ->setCellValue("I$row", $record['desplazados'] != 0 ? $record['desplazados'] : '')
        ->setCellValue("J$row", $record['insuficiencias'] != 0 ? $record['insuficiencias'] : '')
        ->setCellValue("K$row", $record['despanelizados'] != 0 ? $record['despanelizados'] : '')
        ->setCellValue("L$row", $record['desprendidos'] != 0 ? $record['desprendidos'] : '')
        ->setCellValue("M$row", $record['total'] != 0 ? $record['total'] : '')
        ->setCellValue("N$row", isset($record['total_final']) ? $record['total_final'] : 0)
        ->setCellValue("O$row", isset($record['yield']) ? number_format($record['yield'], 2) . "%" : "0%")
        ->setCellValue("P$row", $record['comments']);

    $fields = ['G', 'H', 'I', 'J', 'K', 'L', 'M'];
    foreach ($fields as $field) {
        if ($sheet->getCell("$field$row")->getValue() != '') {
            $sheet->getStyle("$field$row")->getFont()->getColor()->setRGB('FF0000');
            $sheet->getStyle("$field$row")->getFont()->setBold(true);
            $sheet->getStyle("$field$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFCCCC');
        }
    }

    if ($descriptionImage) {
        $img = @imagecreatefromstring($descriptionImage);

        if ($img === false) {
            continue;
        }

        $imageFile = tempnam(sys_get_temp_dir(), 'img');
        imagepng($img, $imageFile);
        imagedestroy($img);

        $drawing = new Drawing();
        $drawing->setPath($imageFile);
        $drawing->setHeight(100);
        $drawing->setWidth(100);
        $drawing->setCoordinates("C$row");
        $drawing->setWorksheet($sheet);

        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getRowDimension($row)->setRowHeight(100);
    } elseif ($descriptionText) {
        $sheet->setCellValue("C$row", $descriptionText);
        $sheet->getColumnDimension('C')->setWidth(30);
    } else {
        $sheet->setCellValue("C$row", '');
    }

    $sheet->getStyle("B$row:P$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("B$row:P$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('000000');
    $sheet->getStyle("B$row:P$row")->getFont()->setSize(10);
    $row++;
}

if ($sheet->getCell("B$row")->getValue() == '') {
    $sheet->removeRow($row);
}

$sheet->getStyle("B$row:P$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('000000');
$sheet->getStyle("B$row:P$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("B$row:P$row")->getFont()->setBold(true);
$sheet->getStyle("B$row:P$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9DAD9');

foreach (range('B', 'P') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="MATERIAL DE SMT.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
ob_end_flush();
exit;
