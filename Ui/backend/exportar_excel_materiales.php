<?php
require '../../config/connection.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

session_start();
header('Content-Type: application/json');

$db = new Database();
$con = $db->conectar();

try {
    if (!isset($_GET['fechaInicio']) || !isset($_GET['fechaFin'])) {
        throw new Exception("Faltan parámetros de fecha.");
    }

    $fechaInicio = $_GET['fechaInicio'];
    $fechaFin = $_GET['fechaFin'];

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaInicio) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaFin)) {
        throw new Exception("Formato de fecha inválido.");
    }

    
    $consulta = "SELECT COUNT(*) FROM materiales WHERE inspection_date BETWEEN :fechaInicio AND :fechaFin";
    $stmt = $con->prepare($consulta);
    $stmt->bindParam(':fechaInicio', $fechaInicio);
    $stmt->bindParam(':fechaFin', $fechaFin);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        throw new Exception("No se encontraron registros para el rango de fechas seleccionado.");
    }

    if (isset($_GET['validar'])) {
        echo json_encode(["status" => "success", "message" => "Datos disponibles para exportar"]);
        exit;
    }

   
    $consulta = "SELECT * FROM materiales WHERE inspection_date BETWEEN :fechaInicio AND :fechaFin";
    $stmt = $con->prepare($consulta);
    $stmt->bindParam(':fechaInicio', $fechaInicio);
    $stmt->bindParam(':fechaFin', $fechaFin);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->getStyle('A1:Z1000')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');

   
    $drawing = new Drawing();
    $drawing->setPath('../../ico/comter.png');
    $drawing->setHeight(90);
    $drawing->setWidth(90);
    $drawing->setCoordinates('B1');
    $drawing->setOffsetX(10);
    $drawing->setOffsetY(10);
    $drawing->setWorksheet($sheet);

    
    $sheet->setCellValue('G7', 'Fails Report');
    $sheet->mergeCells('G7:M7');
    $sheet->getStyle('G7:M7')->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '000000']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
    ]);

  
    $encabezados = [
        'INSPECTION DATE', 'DESCRIPTION', 'SHIFT', 'OPERATORS', 'GOODS', 'DEDOS DE ORO CONTAMINADOS', 'FALTANTE',
        'DESPLAZADOS', 'INSUFICIENCIAS', 'DESPANELIZADOS', 'DESPRENDIDOS', 'TOTAL', 'YIELD', 'TOTAL FINAL', 'COMMENTS'
    ];

    $columna = 'B';
    foreach ($encabezados as $encabezado) {
        $sheet->setCellValue($columna . '8', $encabezado);
        $columna++;
    }

    
    $sheet->getStyle('B8:P8')->applyFromArray([
        'font' => ['bold' => true],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9DAD9']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
    ]);

    foreach (range('B', 'P') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    $fila = 9;
    foreach ($resultado as $row) {
        $fecha = new DateTime($row['inspection_date']);
        $fechaFormateada = $fecha->format('d/m/y');
        $diaSemanaIngles = $fecha->format('l');
        $diasSemana = [
            'Sunday' => 'Domingo',
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado'
        ];
        $diaSemana = $diasSemana[$diaSemanaIngles];

        $sheet->setCellValue("B$fila", "$fechaFormateada ($diaSemana)");
        $sheet->setCellValue("C$fila", $row['descripcion']);
        $sheet->setCellValue("D$fila", $row['shift']);
        $sheet->setCellValue("E$fila", $row['operators']);
        $sheet->setCellValue("F$fila", $row['goods']);
        $sheet->setCellValue("G$fila", $row['dedos_de_oro_contaminados']);
        $sheet->setCellValue("H$fila", $row['faltante']);
        $sheet->setCellValue("I$fila", $row['desplazados']);
        $sheet->setCellValue("J$fila", $row['insuficiencias']);
        $sheet->setCellValue("K$fila", $row['despanelizados']);
        $sheet->setCellValue("L$fila", $row['desprendidos']);
        $sheet->setCellValue("M$fila", $row['total']);
        $sheet->setCellValue("N$fila", $row['yield']);
        $sheet->setCellValue("O$fila", $row['total_final']);
        $sheet->setCellValue("P$fila", $row['comments']);

        $sheet->getStyle("B$fila:P$fila")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('000000');
        $fila++;
    }

 
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Reporte_Materiales.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    exit;
}

