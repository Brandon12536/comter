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

if (!isset($_SESSION['id_proveedor'])) {
    echo 'La sesión no tiene el id_proveedor.';
    exit(); 
  }
  
$id_proveedor = $_SESSION['id_proveedor'];
$db = new Database();
$con = $db->conectar();

$sql_select = "SELECT * FROM PCBA";
$stmt_select = $con->prepare($sql_select);
$stmt_select->execute();
$records = $stmt_select->fetchAll(PDO::FETCH_ASSOC);

$sql_sum_goods = "SELECT SUM(goods) AS total_goods FROM PCBA";
$stmt_sum_goods = $con->prepare($sql_sum_goods);
$stmt_sum_goods->execute();
$row_sum_goods = $stmt_sum_goods->fetch(PDO::FETCH_ASSOC);
$total_goods = $row_sum_goods['total_goods'] ?? 0;

$sql_sum_fails_dedos_oro = "SELECT SUM(fails_dedos_oro) AS total_fails_dedos_oro FROM PCBA";
$stmt_sum_fails_dedos_oro = $con->prepare($sql_sum_fails_dedos_oro);
$stmt_sum_fails_dedos_oro->execute();
$row_sum_fails_dedos_oro = $stmt_sum_fails_dedos_oro->fetch(PDO::FETCH_ASSOC);
$total_fails_dedos_oro = $row_sum_fails_dedos_oro['total_fails_dedos_oro'] ?? 0;

$sql_sum_fails_mal_corte = "SELECT SUM(fails_mal_corte) AS total_fails_mal_corte FROM PCBA";
$stmt_sum_fails_mal_corte = $con->prepare($sql_sum_fails_mal_corte);
$stmt_sum_fails_mal_corte->execute();
$row_sum_fails_mal_corte = $stmt_sum_fails_mal_corte->fetch(PDO::FETCH_ASSOC);
$total_fails_mal_corte = $row_sum_fails_mal_corte['total_fails_mal_corte'] ?? 0;

$sql_sum_fails_contaminacion = "SELECT SUM(fails_contaminacion) AS total_fails_contaminacion FROM PCBA";
$stmt_sum_fails_contaminacion = $con->prepare($sql_sum_fails_contaminacion);
$stmt_sum_fails_contaminacion->execute();
$row_sum_fails_contaminacion = $stmt_sum_fails_contaminacion->fetch(PDO::FETCH_ASSOC);
$total_fails_contaminacion = $row_sum_fails_contaminacion['total_fails_contaminacion'] ?? 0;

$sql_sum_pd = "SELECT SUM(pd) AS total_pd FROM PCBA";
$stmt_sum_pd = $con->prepare($sql_sum_pd);
$stmt_sum_pd->execute();
$row_sum_pd = $stmt_sum_pd->fetch(PDO::FETCH_ASSOC);
$total_pd = $row_sum_pd['total_pd'] ?? 0;

$sql_sum_fails_desplazados = "SELECT SUM(fails_desplazados) AS total_fails_desplazados FROM PCBA";
$stmt_sum_fails_desplazados = $con->prepare($sql_sum_fails_desplazados);
$stmt_sum_fails_desplazados->execute();
$row_sum_fails_desplazados = $stmt_sum_fails_desplazados->fetch(PDO::FETCH_ASSOC);
$total_fails_desplazados = $row_sum_fails_desplazados['total_fails_desplazados'] ?? 0;

$sql_sum_fails_insuficiencias = "SELECT SUM(fails_insuficiencias) AS total_fails_insuficiencias FROM PCBA";
$stmt_sum_fails_insuficiencias = $con->prepare($sql_sum_fails_insuficiencias);
$stmt_sum_fails_insuficiencias->execute();
$row_sum_fails_insuficiencias = $stmt_sum_fails_insuficiencias->fetch(PDO::FETCH_ASSOC);
$total_fails_insuficiencias = $row_sum_fails_insuficiencias['total_fails_insuficiencias'] ?? 0;

$sql_sum_fails_despanelizados = "SELECT SUM(fails_despanelizados) AS total_fails_despanelizados FROM PCBA";
$stmt_sum_fails_despanelizados = $con->prepare($sql_sum_fails_despanelizados);
$stmt_sum_fails_despanelizados->execute();
$row_sum_fails_despanelizados = $stmt_sum_fails_despanelizados->fetch(PDO::FETCH_ASSOC);
$total_fails_despanelizados = $row_sum_fails_despanelizados['total_fails_despanelizados'] ?? 0;

$sql_sum_fails_desprendidos = "SELECT SUM(fails_desprendidos) AS total_fails_desprendidos FROM PCBA";
$stmt_sum_fails_desprendidos = $con->prepare($sql_sum_fails_desprendidos);
$stmt_sum_fails_desprendidos->execute();
$row_sum_fails_desprendidos = $stmt_sum_fails_desprendidos->fetch(PDO::FETCH_ASSOC);
$total_fails_desprendidos = $row_sum_fails_desprendidos['total_fails_desprendidos'] ?? 0;

$sql_sum_total_fails = "SELECT SUM(total_fails) AS total_total_fails FROM PCBA";
$stmt_sum_total_fails = $con->prepare($sql_sum_total_fails);
$stmt_sum_total_fails->execute();
$row_sum_total_fails = $stmt_sum_total_fails->fetch(PDO::FETCH_ASSOC);
$total_total_fails = $row_sum_total_fails['total_total_fails'] ?? 0;

$sql_sum_total = "SELECT SUM(total) AS total_total FROM PCBA";
$stmt_sum_total = $con->prepare($sql_sum_total);
$stmt_sum_total->execute();
$row_sum_total = $stmt_sum_total->fetch(PDO::FETCH_ASSOC);
$total_total = $row_sum_total['total_total'] ?? 0;

if ($total_total != 0) {
    $result_division = ($total_goods / $total_total) * 100;
} else {
    $result_division = 0;
}

$result_division = round($result_division);

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
    ->setCellValue('H8', 'MAL CORTE')
    ->setCellValue('I8', 'CONTAMINACION')
    ->setCellValue('J8', 'PD')
    ->setCellValue('K8', 'DESPLAZADOS')
    ->setCellValue('L8', 'INSUFICIENCIAS')
    ->setCellValue('M8', 'DESPANELIZADOS')
    ->setCellValue('N8', 'DESPRENDIDOS')
    ->setCellValue('O8', 'TOTAL')
    ->setCellValue('P8', 'TOTAL')
    ->setCellValue('Q8', 'YIELD')
    ->setCellValue('R8', 'COMMENTS');

$sheet->getStyle('B8:R8')->getFont()->setBold(true);
$sheet->getStyle('B8:R8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getStyle('B8:R8')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9DAD9');
$sheet->getStyle('B8:R8')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('000000');

$sheet->getStyle('F8')->getFont()->getColor()->setRGB('0FCB59');
$sheet->getStyle('P8')->getFont()->getColor()->setRGB('0FCB59');
$sheet->getStyle('Q8')->getFont()->getColor()->setRGB('0FCB59');

$row = 9;

$sheet->setCellValue("B$row", 'GRAN TOTAL / SEMANA 29')
    ->setCellValue("C$row", '...')
    ->setCellValue("D$row", '...')
    ->setCellValue("E$row", '...')
    ->setCellValue("F$row", ($total_goods == 0 ? '' : number_format($total_goods)))
    ->setCellValue("G$row", ($total_fails_dedos_oro == 0 ? '' : number_format($total_fails_dedos_oro)))
    ->setCellValue("H$row", ($total_fails_mal_corte == 0 ? '' : number_format($total_fails_mal_corte)))
    ->setCellValue("I$row", ($total_fails_contaminacion == 0 ? '' : number_format($total_fails_contaminacion)))
    ->setCellValue("J$row", ($total_pd == 0 ? '' : number_format($total_pd)))
    ->setCellValue("K$row", ($total_fails_desplazados == 0 ? '' : number_format($total_fails_desplazados)))
    ->setCellValue("L$row", ($total_fails_insuficiencias == 0 ? '' : number_format($total_fails_insuficiencias)))
    ->setCellValue("M$row", ($total_fails_despanelizados == 0 ? '' : number_format($total_fails_despanelizados)))
    ->setCellValue("N$row", ($total_fails_desprendidos == 0 ? '' : number_format($total_fails_desprendidos)))
    ->setCellValue("O$row", ($total_total_fails == 0 ? '' : number_format($total_total_fails)))
    ->setCellValue("P$row", ($total_total == 0 ? '' : number_format($total_total)))
    ->setCellValue("Q$row", ($result_division == 0 ? '' : $result_division . '%'))
    ->setCellValue("R$row", '');

$sheet->mergeCells("B$row:E$row");
$sheet->getStyle("B$row:E$row")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
$sheet->getStyle("B$row:E$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getStyle("B$row:E$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('626262');

$sheet->getStyle("B$row:R$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('000000');

$row++;
foreach ($records as $record) {
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

    $fails_dedos_oro = $record['fails_dedos_oro'];
    $sheet->setCellValue("G$row", ($fails_dedos_oro == 0 ? '' : htmlspecialchars($fails_dedos_oro)));
    if ($fails_dedos_oro > 0) {
        $sheet->getStyle("G$row")->getFont()->getColor()->setRGB('FF0000');
        $sheet->getStyle("G$row")->getFont()->setBold(true);
        $sheet->getStyle("G$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFCCCC');
    }


    $fails_mal_corte = $record['fails_mal_corte'];
    $sheet->setCellValue("H$row", ($fails_mal_corte == 0 ? '' : htmlspecialchars($fails_mal_corte)));
    if ($fails_mal_corte > 0) {
        $sheet->getStyle("H$row")->getFont()->getColor()->setRGB('FF0000');
        $sheet->getStyle("H$row")->getFont()->setBold(true);
        $sheet->getStyle("H$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFCCCC');
    }


    $fails_contaminacion = $record['fails_contaminacion'];
    $sheet->setCellValue("I$row", ($fails_contaminacion == 0 ? '' : htmlspecialchars($fails_contaminacion)));
    if ($fails_contaminacion > 0) {
        $sheet->getStyle("I$row")->getFont()->getColor()->setRGB('FF0000');
        $sheet->getStyle("I$row")->getFont()->setBold(true);
        $sheet->getStyle("I$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFCCCC');
    }


    $pd = $record['pd'];
    $sheet->setCellValue("J$row", ($pd == 0 ? '' : htmlspecialchars($pd)));
    if ($pd > 0) {
        $sheet->getStyle("J$row")->getFont()->getColor()->setRGB('FF0000');
        $sheet->getStyle("J$row")->getFont()->setBold(true);
        $sheet->getStyle("J$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFCCCC');
    }


    $fails_desplazados = $record['fails_desplazados'];
    $sheet->setCellValue("K$row", ($fails_desplazados == 0 ? '' : htmlspecialchars($fails_desplazados)));
    if ($fails_desplazados > 0) {
        $sheet->getStyle("K$row")->getFont()->getColor()->setRGB('FF0000');
        $sheet->getStyle("K$row")->getFont()->setBold(true);
        $sheet->getStyle("K$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFCCCC');
    }


    $fails_insuficiencias = $record['fails_insuficiencias'];
    $sheet->setCellValue("L$row", ($fails_insuficiencias == 0 ? '' : htmlspecialchars($fails_insuficiencias)));
    if ($fails_insuficiencias > 0) {
        $sheet->getStyle("L$row")->getFont()->getColor()->setRGB('FF0000');
        $sheet->getStyle("L$row")->getFont()->setBold(true);
        $sheet->getStyle("L$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFCCCC');
    }


    $fails_despanelizados = $record['fails_despanelizados'];
    $sheet->setCellValue("M$row", ($fails_despanelizados == 0 ? '' : htmlspecialchars($fails_despanelizados)));
    if ($fails_despanelizados > 0) {
        $sheet->getStyle("M$row")->getFont()->getColor()->setRGB('FF0000');
        $sheet->getStyle("M$row")->getFont()->setBold(true);
        $sheet->getStyle("M$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFCCCC');
    }


    $fails_desprendidos = $record['fails_desprendidos'];
    $sheet->setCellValue("N$row", ($fails_desprendidos == 0 ? '' : htmlspecialchars($fails_desprendidos)));
    if ($fails_desprendidos > 0) {
        $sheet->getStyle("N$row")->getFont()->getColor()->setRGB('FF0000');
        $sheet->getStyle("N$row")->getFont()->setBold(true);
        $sheet->getStyle("N$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFCCCC');
    }


    $total_fails = $record['total_fails'];
    $sheet->setCellValue("O$row", ($total_fails == 0 ? '' : htmlspecialchars($total_fails)));
    if ($total_fails > 0) {
        $sheet->getStyle("O$row")->getFont()->getColor()->setRGB('FF0000');
        $sheet->getStyle("O$row")->getFont()->setBold(true);
        $sheet->getStyle("O$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFCCCC');
    }

    $sheet->setCellValue("B$row", $formatted_date)
        ->setCellValue("C$row", htmlspecialchars($record['description']))
        ->setCellValue("D$row", htmlspecialchars($record['shift']))
        ->setCellValue("E$row", htmlspecialchars($record['operators']))
        ->setCellValue("F$row", ($record['goods'] == 0 ? '' : htmlspecialchars($record['goods'])))
        ->setCellValue("G$row", ($record['fails_dedos_oro'] == 0 ? '' : htmlspecialchars($record['fails_dedos_oro'])))
        ->setCellValue("H$row", ($record['fails_mal_corte'] == 0 ? '' : htmlspecialchars($record['fails_mal_corte'])))
        ->setCellValue("I$row", ($record['fails_contaminacion'] == 0 ? '' : htmlspecialchars($record['fails_contaminacion'])))
        ->setCellValue("J$row", ($record['pd'] == 0 ? '' : htmlspecialchars($record['pd'])))
        ->setCellValue("K$row", ($record['fails_desplazados'] == 0 ? '' : htmlspecialchars($record['fails_desplazados'])))
        ->setCellValue("L$row", ($record['fails_insuficiencias'] == 0 ? '' : htmlspecialchars($record['fails_insuficiencias'])))
        ->setCellValue("M$row", ($record['fails_despanelizados'] == 0 ? '' : htmlspecialchars($record['fails_despanelizados'])))
        ->setCellValue("N$row", ($record['fails_desprendidos'] == 0 ? '' : htmlspecialchars($record['fails_desprendidos'])))
        ->setCellValue("O$row", ($record['total_fails'] == 0 ? '' : htmlspecialchars($record['total_fails'])))
        ->setCellValue("P$row", ($record['total'] == 0 ? '' : htmlspecialchars($record['total'])))
        ->setCellValue("Q$row", ($record['yield'] == 0 ? '' : htmlspecialchars($record['yield'])))
        ->setCellValue("R$row", htmlspecialchars($record['comments']));

    $sheet->getStyle("B$row:R$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('000000');
    $row++;
}

foreach (range('B', 'R') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="MOLEX PCBA - Material Acumulado o de Almacén.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
