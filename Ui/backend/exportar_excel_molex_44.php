<?php
require '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

session_start();
require '../../config/connection.php';

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

    $nombre_version = 'sem44';
    $query_version = "SELECT id_version FROM versiones_inspeccion WHERE nombre_version = :nombre_version";
    $stmt_version = $con->prepare($query_version);
    $stmt_version->bindParam(':nombre_version', $nombre_version, PDO::PARAM_STR);
    $stmt_version->execute();

    if ($stmt_version->rowCount() == 0) {
        throw new Exception("Versión no encontrada.");
    }

    $version = $stmt_version->fetch(PDO::FETCH_ASSOC);
    $id_version = $version['id_version'];

    $query = "SELECT COUNT(*) FROM inspecciones 
              WHERE id_version = :id_version 
              AND inspection_date BETWEEN :fecha_inicio AND :fecha_fin";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':id_version', $id_version, PDO::PARAM_INT);
    $stmt->bindParam(':fecha_inicio', $fechaInicio, PDO::PARAM_STR);
    $stmt->bindParam(':fecha_fin', $fechaFin, PDO::PARAM_STR);
    $stmt->execute();
    
    if ($stmt->fetchColumn() == 0) {
        throw new Exception("No se encontraron registros para la versión $nombre_version en el rango de fechas.");
    }

    if (isset($_GET['validar'])) {
        echo json_encode(["status" => "success", "message" => "Datos disponibles para exportar"]);
        exit;
    }

  
    ob_clean();
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Molex_SEM44.xlsx"');
    header('Cache-Control: max-age=0');

    $query = "SELECT * FROM inspecciones 
              WHERE id_version = :id_version 
              AND inspection_date BETWEEN :fecha_inicio AND :fecha_fin";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':id_version', $id_version, PDO::PARAM_INT);
    $stmt->bindParam(':fecha_inicio', $fechaInicio, PDO::PARAM_STR);
    $stmt->bindParam(':fecha_fin', $fechaFin, PDO::PARAM_STR);
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $logoPath = '../../ico/comter.png';
    if (file_exists($logoPath)) {
        $drawing = new Drawing();
        $drawing->setPath($logoPath);
        $drawing->setHeight(90);
        $drawing->setWidth(90);
        $drawing->setCoordinates('B1');
        $drawing->setOffsetX(10);
        $drawing->setOffsetY(10);
        $drawing->setWorksheet($sheet);
    }

    $sheet->getStyle('A1:Z1000')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');

    $sheet->setCellValue('J6', 'Fails Report');
    $sheet->mergeCells('J6:V6');

    $failsReportStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
            'size' => 12,
            'name' => 'Arial'
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => [
                'rgb' => '000000',
            ],
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
            ],
        ],
    ];

    $sheet->getStyle('J6:V6')->applyFromArray($failsReportStyle);

    $headers = [
        'INSPECTION DATE', 'DESCRIPTION', 'OPERATORS', 'PRIMER T', 'SEGUNDO T', 'TERCER T',
        'GOODS', 'COUPLER', 'DANO END FACE', 'GOLPE TOP', 'REBABA', 'DANO EN LENTE',
        'FUERA DE SPC', 'DAÑO FISICO', 'COUPLER DAÑADO', 'HUNDIMIENTO', 'FISURA',
        'SILICON/CONTAMINACION', 'CONTAMINACION END FACE', 'TOTAL', 'TOTAL FINAL', 'COMMENTS'
    ];

    $sheet->fromArray($headers, NULL, 'C7');

    $headerStyle = [
        'font' => [
            'bold' => true,
            'size' => 12,
            'name' => 'Arial'
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
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => [
                'rgb' => 'D9EAD3',
            ],
        ],
    ];

    $sheet->getStyle('C7:X7')->applyFromArray($headerStyle);

    $dataRowStyle = [
        'font' => [
            'size' => 10,
            'name' => 'Arial',
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

    $row = 8;
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
        $sheet->setCellValue('C' . $row, $formatted_date);
        $sheet->setCellValue('D' . $row, $record['descripcion']);
        $sheet->setCellValue('E' . $row, $record['operators']);

        if ($record['primer_t'] > 0) {
            $sheet->setCellValue('F' . $row, $record['primer_t']);
            $sheet->getStyle('F' . $row)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'B6D7A8',
                    ],
                ],
                'font' => [
                    'bold' => true,
                ],
            ]);
        } else {
            $sheet->setCellValue('F' . $row, '');
        }

        if ($record['segundo_t'] > 0) {
            $sheet->setCellValue('G' . $row, $record['segundo_t']);
            $sheet->getStyle('G' . $row)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'B6D7A8',
                    ],
                ],
                'font' => [
                    'bold' => true,
                ],
            ]);
        } else {
            $sheet->setCellValue('G' . $row, '');
        }

        if ($record['tercer_t'] > 0) {
            $sheet->setCellValue('H' . $row, $record['tercer_t']);
            $sheet->getStyle('H' . $row)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'B6D7A8',
                    ],
                ],
                'font' => [
                    'bold' => true,
                ],
            ]);
        } else {
            $sheet->setCellValue('H' . $row, '');
        }

        if ($record['goods'] > 0) {
            $sheet->setCellValue('I' . $row, $record['goods']);
            $sheet->getStyle('I' . $row)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'B6D7A8',
                    ],
                ],
                'font' => [
                    'bold' => true,
                ],
            ]);
        } else {
            $sheet->setCellValue('I' . $row, '');
        }

        $columns = [
            'J' => 'coupler', 'K' => 'dano_end_face', 'L' => 'golpe_top', 'M' => 'rebaba',
            'N' => 'dano_en_lente', 'O' => 'fuera_de_spc', 'P' => 'dano_fisico', 'Q' => 'coupler_danado',
            'R' => 'hundimiento', 'S' => 'fisura', 'T' => 'silicon_contaminacion',
            'U' => 'contaminacion_end_face', 'V' => 'total'
        ];

        foreach ($columns as $column => $field) {
            if ($record[$field] > 0) {
                $sheet->setCellValue($column . $row, $record[$field]);
                $sheet->getStyle($column . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'FFCCCC',
                        ],
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => [
                            'rgb' => 'FF0000',
                        ],
                    ],
                ]);
            } else {
                $sheet->setCellValue($column . $row, '');
            }
        }

        $sheet->setCellValue('W' . $row, $record['total_final']);
        $sheet->setCellValue('X' . $row, $record['comments']);
        $sheet->getStyle('C' . $row . ':X' . $row)->applyFromArray($dataRowStyle);

        $row++;
    }

    $totalRowFormula = $row;
    $sheet->setCellValue('C' . $totalRowFormula, 'Total');
    $sheet->mergeCells('C' . $totalRowFormula . ':H' . $totalRowFormula);
    $sheet->getStyle('C' . $totalRowFormula . ':H' . $totalRowFormula)->applyFromArray($headerStyle);

    $sheet->setCellValue('I' . $totalRowFormula, "=SUM(I8:I" . ($row-1) . ")");
    $sheet->setCellValue('J' . $totalRowFormula, "=SUM(J8:J" . ($row-1) . ")");
    $sheet->setCellValue('K' . $totalRowFormula, "=SUM(K8:K" . ($row-1) . ")");
    $sheet->setCellValue('L' . $totalRowFormula, "=SUM(L8:L" . ($row-1) . ")");
    $sheet->setCellValue('M' . $totalRowFormula, "=SUM(M8:M" . ($row-1) . ")");
    $sheet->setCellValue('N' . $totalRowFormula, "=SUM(N8:N" . ($row-1) . ")");
    $sheet->setCellValue('O' . $totalRowFormula, "=SUM(O8:O" . ($row-1) . ")");
    $sheet->setCellValue('P' . $totalRowFormula, "=SUM(P8:P" . ($row-1) . ")");
    $sheet->setCellValue('Q' . $totalRowFormula, "=SUM(Q8:Q" . ($row-1) . ")");
    $sheet->setCellValue('R' . $totalRowFormula, "=SUM(R8:R" . ($row-1) . ")");
    $sheet->setCellValue('S' . $totalRowFormula, "=SUM(S8:S" . ($row-1) . ")");
    $sheet->setCellValue('T' . $totalRowFormula, "=SUM(T8:T" . ($row-1) . ")");
    $sheet->setCellValue('U' . $totalRowFormula, "=SUM(U8:U" . ($row-1) . ")");
    $sheet->setCellValue('V' . $totalRowFormula, "=SUM(V8:V" . ($row-1) . ")");
    $sheet->setCellValue('W' . $totalRowFormula, "=SUM(W8:W" . ($row-1) . ")");

    $sheet->getStyle('C' . $totalRowFormula . ':W' . $totalRowFormula)->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THICK,
                'color' => ['rgb' => '000000']
            ]
        ]
    ]);

    foreach (range('C', 'X') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    exit;
}
