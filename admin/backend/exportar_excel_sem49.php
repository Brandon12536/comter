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

ob_start();

if (!isset($_SESSION['id_proveedor'])) {
    echo 'La sesión no tiene el id_proveedor.';
    exit();
}

$id_proveedor = $_SESSION['id_proveedor'];
$db = new Database();
$con = $db->conectar();

$nombre_version = 'sem49';

$query_version = "SELECT id_version FROM versiones_inspeccion WHERE nombre_version = :nombre_version";
$stmt_version = $con->prepare($query_version);
$stmt_version->bindParam(':nombre_version', $nombre_version, PDO::PARAM_STR);
$stmt_version->execute();

if ($stmt_version->rowCount() == 0) {
    echo 'Versión no encontrada.';
    exit();
} else {
    $version = $stmt_version->fetch(PDO::FETCH_ASSOC);
    $id_version = $version['id_version'];

    $query = "SELECT * FROM molex WHERE id_proveedor = :id_proveedor AND id_version = :id_version";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
    $stmt->bindParam(':id_version', $id_version, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        echo 'No se encontraron registros para la versión sem42.';
        exit();
    } else {
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->getStyle('A1:Z1000')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');

$drawing = new Drawing();
$drawing->setPath('../ico/comter.png');
$drawing->setHeight(90);
$drawing->setWidth(90);
$drawing->setCoordinates('B1');
$drawing->setOffsetX(10);
$drawing->setOffsetY(10);
$drawing->setWorksheet($sheet);

$headers = [
    'INSPECTION DATE',
    'DESCRIPTION',
    'OPERATORS',
    'PRIMER T',
    'SEGUNDO T',
    'TERCER T',
    'GOODS',
    'COUPLER',
    'DANO END FACE',
    'GOLPE TOP',
    'REBABA',
    'DANO EN LENTE',
    'FUERA DE SPC',
    'DAÑO FISICO',
    'WIREBOND CORTO',
    'WIREBOND CHUECO',
    'FISURA',
    'SILICON/CONTAMINACION',
    'CONTAMINACION END FACE',
    'TOTAL',
    'TOTAL FINAL',
    'COMMENTS'
];

$sheet->fromArray($headers, NULL, 'C7');

$row = 8;

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
        'J' => 'coupler',
        'K' => 'dano_end_face',
        'L' => 'golpe_top',
        'M' => 'rebaba',
        'N' => 'dano_en_lente',
        'O' => 'fuera_de_spc',
        'P' => 'dano_fisico',
        'Q' => 'wirebond_corto',
        'R' => 'wirebond_chueco',
        'S' => 'fisura',
        'T' => 'silicon_contaminacion',
        'U' => 'contaminacion_end_face',
        'V' => 'total'
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

    $formatted_date = $date->format('d/m/y') . " - " . $daysInSpanish[$dayOfWeek];
    $sheet->setCellValue('C' . $row, $formatted_date);
    $sheet->setCellValue('D' . $row, $record['descripcion']);
    $sheet->setCellValue('E' . $row, $record['operators']);
    $sheet->setCellValue('F' . $row, $record['primer_t']);
    $sheet->setCellValue('G' . $row, $record['segundo_t']);
    $sheet->setCellValue('H' . $row, $record['tercer_t']);
    $sheet->setCellValue('I' . $row, $record['goods']);
    $sheet->setCellValue('J' . $row, $record['coupler'] > 0 ? $record['coupler'] : '');
    $sheet->setCellValue('K' . $row, $record['dano_end_face'] > 0 ? $record['dano_end_face'] : '');
    $sheet->setCellValue('L' . $row, $record['golpe_top'] > 0 ? $record['golpe_top'] : '');
    $sheet->setCellValue('M' . $row, $record['rebaba'] > 0 ? $record['rebaba'] : '');
    $sheet->setCellValue('N' . $row, $record['dano_en_lente'] > 0 ? $record['dano_en_lente'] : '');
    $sheet->setCellValue('O' . $row, $record['fuera_de_spc'] > 0 ? $record['fuera_de_spc'] : '');
    $sheet->setCellValue('P' . $row, $record['dano_fisico'] > 0 ? $record['dano_fisico'] : '');
    $sheet->setCellValue('Q' . $row, $record['wirebond_corto'] > 0 ? $record['wirebond_corto'] : '');
    $sheet->setCellValue('R' . $row, $record['wirebond_chueco'] > 0 ? $record['wirebond_chueco'] : '');
    $sheet->setCellValue('S' . $row, $record['fisura'] > 0 ? $record['fisura'] : '');
    $sheet->setCellValue('T' . $row, $record['silicon_contaminacion'] > 0 ? $record['silicon_contaminacion'] : '');
    $sheet->setCellValue('U' . $row, $record['contaminacion_end_face'] > 0 ? $record['contaminacion_end_face'] : '');
    $sheet->setCellValue('V' . $row, $record['total'] > 0 ? $record['total'] : '');
    $sheet->setCellValue('W' . $row, $record['total_final'] > 0 ? $record['total_final'] : '');
    $sheet->setCellValue('X' . $row, $record['comments'] > 0 ? $record['comments'] : '');
    $sheet->getStyle('C' . $row . ':X' . $row)->applyFromArray($dataRowStyle);

    $row++;
}

$totalRowFormula = $row + 1;

$sheet->setCellValue('C' . $totalRowFormula, 'Total');
$sheet->mergeCells('C' . $totalRowFormula . ':H' . $totalRowFormula);
$sheet->getStyle('C' . $totalRowFormula . ':H' . $totalRowFormula)->applyFromArray($headerStyle);

$sheet->setCellValue('I' . $totalRowFormula, "=SUM(I8:I$row)");
$sheet->setCellValue('J' . $totalRowFormula, "=SUM(J8:J$row)");
$sheet->setCellValue('K' . $totalRowFormula, "=SUM(K8:K$row)");
$sheet->setCellValue('L' . $totalRowFormula, "=SUM(L8:L$row)");
$sheet->setCellValue('M' . $totalRowFormula, "=SUM(M8:M$row)");
$sheet->setCellValue('N' . $totalRowFormula, "=SUM(N8:N$row)");
$sheet->setCellValue('O' . $totalRowFormula, "=SUM(O8:O$row)");
$sheet->setCellValue('P' . $totalRowFormula, "=SUM(P8:P$row)");
$sheet->setCellValue('Q' . $totalRowFormula, "=SUM(Q8:Q$row)");
$sheet->setCellValue('R' . $totalRowFormula, "=SUM(R8:R$row)");
$sheet->setCellValue('S' . $totalRowFormula, "=SUM(S8:S$row)");
$sheet->setCellValue('T' . $totalRowFormula, "=SUM(T8:T$row)");
$sheet->setCellValue('U' . $totalRowFormula, "=SUM(U8:U$row)");
$sheet->setCellValue('V' . $totalRowFormula, "=SUM(V8:V$row)");
$sheet->setCellValue('W' . $totalRowFormula, "=SUM(W8:W$row)");

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
$fileName = 'Molex SEM49.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit();
