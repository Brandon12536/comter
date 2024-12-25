<?php
session_start();

require '../../vendor/autoload.php';
require '../../config/connection.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

if (!isset($_SESSION['id_usuarios'])) {
    echo 'No has iniciado sesión o tu sesión ha expirado.';
    exit();
}

$id_usuario = $_SESSION['id_usuarios'];
$db = new Database();
$con = $db->conectar();

$sql = "SELECT rf.inspection_date, rf.operators, rf.descripcion, rf.primer_t, rf.segundo_t, rf.tercer_t, 
               rf.burr, rf.blockend_hole, rf.non_flat_edge, rf.comments, ri.image 
        FROM report_fails rf
        LEFT JOIN report_images ri ON rf.id_report_fails = ri.id_report_fails
        WHERE rf.id_usuarios = :id_usuario";

try {
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setShowGridlines(false);
        $sheet->getStyle('A1:V1')->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFF');



        $logoPath = '../ico/comter.png';
        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo de la compañía');
            $drawing->setPath($logoPath);
            $drawing->setHeight(80);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(10);
            $drawing->setOffsetY(10);
            $drawing->setWorksheet($sheet);
        }


        $startRow = 6;


        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['argb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '000000']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFFFFF']
                ]
            ]
        ];

        $sheet->getStyle("A$startRow:O$startRow")->applyFromArray($headerStyle);


        $sheet->setCellValue("A$startRow", 'Inspection Date')
            ->setCellValue("B$startRow", 'Operators')
            ->setCellValue("C$startRow", 'Descripción')
            ->setCellValue("D$startRow", '1 er T')
            ->setCellValue("E$startRow", '2 do T')
            ->setCellValue("F$startRow", '3 er T')
            ->setCellValue("G$startRow", 'GOODS')
            ->setCellValue("H$startRow", 'Burr')
            ->setCellValue("I$startRow", 'Blockend Hole')
            ->setCellValue("J$startRow", 'Non Flat Edge')
            ->setCellValue("K$startRow", 'Total')
            ->setCellValue("L$startRow", 'Total Final')
            ->setCellValue("M$startRow", 'Yield')
            ->setCellValue("N$startRow", 'Comments')
            ->setCellValue("O$startRow", 'Imagen');

        $sheet->getStyle("G$startRow")->applyFromArray([
            'font' => [
                'color' => ['rgb' => '008000'],
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'e2f4e2'],
            ]
        ]);

        $sheet->getStyle("L$startRow")->applyFromArray([
            'font' => [
                'color' => ['rgb' => '008000'],
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'e2f4e2'],
            ]
        ]);

        $sheet->getStyle("M$startRow")->applyFromArray([
            'font' => [
                'color' => ['rgb' => '008000'],
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'e2f4e2'],
            ]
        ]);
        $dataStyle = [
            'font' => [
                'size' => 12
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'CCCCCC']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ];

        $row = $startRow + 1;
        while ($row_data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $date = new DateTime($row_data['inspection_date']);
            $formattedDate = $date->format('d/m/Y');
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
            $spanishDay = $daysInSpanish[$dayOfWeek] ?? $dayOfWeek;

            $formattedDateWithDay = $spanishDay . ', ' . $formattedDate;

            $goods = $row_data['primer_t'] + $row_data['segundo_t'] + $row_data['tercer_t'];
            $burr = $row_data['burr'];
            $blockendHole = $row_data['blockend_hole'];
            $nonFlatEdge = $row_data['non_flat_edge'];
            $total = $burr + $blockendHole + $nonFlatEdge;
            $totalFinal = $goods + $total;
            $yield = $totalFinal != 0 ? ($goods / $totalFinal) * 100 : 'N/A';

            $sheet->setCellValue('A' . $row, $formattedDateWithDay)
                ->setCellValue('B' . $row, $row_data['operators'] ?: ' ')
                ->setCellValue('C' . $row, $row_data['descripcion'] ?: ' ')
                ->setCellValue('D' . $row, $row_data['primer_t'] == 0 ? '' : $row_data['primer_t'])
                ->setCellValue('E' . $row, $row_data['segundo_t'] == 0 ? '' : $row_data['segundo_t'])
                ->setCellValue('F' . $row, $row_data['tercer_t'] == 0 ? '' : $row_data['tercer_t'])
                ->setCellValue('G' . $row, $goods == 0 ? '' : $goods)
                ->setCellValue('H' . $row, $burr == 0 ? '' : $burr)
                ->setCellValue('I' . $row, $blockendHole == 0 ? '' : $blockendHole)
                ->setCellValue('J' . $row, $nonFlatEdge == 0 ? '' : $nonFlatEdge)
                ->setCellValue('K' . $row, $total == 0 ? '' : $total)
                ->setCellValue('L' . $row, $totalFinal == 0 ? '' : $totalFinal)
                ->setCellValue('M' . $row, is_numeric($yield) && $yield == 0 ? '' : (is_numeric($yield) ? round($yield) . '%' : $yield))
                ->setCellValue('N' . $row, $row_data['comments'] ?: ' ');

            if ($row_data['image']) {
                $imageData = $row_data['image'];
                $imageResource = imagecreatefromstring($imageData);
                if ($imageResource !== false) {

                    $imageFile = tempnam(sys_get_temp_dir(), 'image');
                    imagepng($imageResource, $imageFile);
                    imagedestroy($imageResource);


                    $drawing = new Drawing();
                    $drawing->setName('Imagen');
                    $drawing->setDescription('Imagen report fail');
                    $drawing->setPath($imageFile);
                    $drawing->setCoordinates('O' . $row);


                    $drawing->setWidth(25);
                    $drawing->setHeight(25);


                    $drawing->setWorksheet($sheet);
                }
            } else {
                $sheet->setCellValue('O' . $row, 'No hay imagen');
            }

            $sheet->getStyle("A$row:O$row")->applyFromArray($dataStyle);


            foreach (range('A', 'O') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $row++;
        }


        $writer = new Xlsx($spreadsheet);
        $filename = 'SCC_' . date('Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    } else {
        echo 'No se encontraron registros para este usuario.';
    }
} catch (PDOException $e) {
    echo 'Error en la base de datos: ' . $e->getMessage();
} catch (Exception $e) {
    echo 'Error al generar el archivo Excel: ' . $e->getMessage();
}

