<?php
session_start();

require '../../vendor/autoload.php';
require '../../config/connection.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

if (!isset($_SESSION['id_usuarios'])) {
    echo 'No has iniciado sesión o tu sesión ha expirado.';
    exit();
}

$id_usuario = $_SESSION['id_usuarios']; 
$db = new Database();
$con = $db->conectar();

$sql = "SELECT inspection_date, operators, descripcion, goods, primer_t, segundo_t, tercer_t, coupler, dano_end_face, golpe_top, rebaba, dano_en_lente, fuera_de_spc, dano_fisico, coupler_dano, hundimiento, fisura, silicon, contaminacion, total, comments 
        FROM inspection_data 
        WHERE id_usuarios = :id_usuario";

try {
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Ocultar líneas de cuadrícula y aplicar fondo blanco
        $sheet->setShowGridlines(false);
        $sheet->getStyle('A1:V1')->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFF');

        // Agregar el logo
        $logoPath = '../ico/comter.png';
        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo de la compañía');
            $drawing->setPath($logoPath);
            $drawing->setHeight(80); // Ajusta la altura del logo
            $drawing->setCoordinates('A1'); // Celda donde se colocará
            $drawing->setOffsetX(10); // Ajusta el margen horizontal
            $drawing->setOffsetY(10); // Ajusta el margen vertical
            $drawing->setWorksheet($sheet);
        }

        // Mover la tabla para que no se sobreponga al logo
        $startRow = 6; // Empieza en la fila 6 para dejar espacio para el logo

        // Estilos para el encabezado
        $headerStyle = [
            'font' => [
                'bold' => true,
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

        $sheet->getStyle("A$startRow:V$startRow")->applyFromArray($headerStyle);

        // Encabezados de las columnas
        $sheet->setCellValue("A$startRow", 'Inspection Date')
              ->setCellValue("B$startRow", 'Operators')
              ->setCellValue("C$startRow", 'Descripción')
              ->setCellValue("D$startRow", '1 er T')
              ->setCellValue("E$startRow", '2 do T')
              ->setCellValue("F$startRow", '3 er T')
              ->setCellValue("G$startRow", 'GOODS')
              ->setCellValue("H$startRow", 'Coupler')
              ->setCellValue("I$startRow", 'Daño End Face')
              ->setCellValue("J$startRow", 'Golpe Top')
              ->setCellValue("K$startRow", 'Rebaba')
              ->setCellValue("L$startRow", 'Daño en Lente')
              ->setCellValue("M$startRow", 'Fuera de SPC')
              ->setCellValue("N$startRow", 'Daño Fisico')
              ->setCellValue("O$startRow", 'Coupler Dañado')
              ->setCellValue("P$startRow", 'Hundimiento')
              ->setCellValue("Q$startRow", 'Fisura')
              ->setCellValue("R$startRow", 'Silicón / Contaminación')
              ->setCellValue("S$startRow", 'Contaminación / End Face')
              ->setCellValue("T$startRow", 'Total')
              ->setCellValue("U$startRow", 'Total Final')
              ->setCellValue("V$startRow", 'Comments');

        // Estilo para los datos
        $dataStyle = [
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

        // Rellenar los datos
        $row = $startRow + 1; // Fila siguiente después de los encabezados
        while ($row_data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Formatear la fecha
            $date = new DateTime($row_data['inspection_date']);
            $formattedDate = $date->format('d/m/Y'); // Fecha formateada como 'd/m/Y'
            $dayOfWeek = $date->format('l');  // Día de la semana en inglés
            $daysInSpanish = [
                'Monday' => 'Lunes',
                'Tuesday' => 'Martes',
                'Wednesday' => 'Miércoles',
                'Thursday' => 'Jueves',
                'Friday' => 'Viernes',
                'Saturday' => 'Sábado',
                'Sunday' => 'Domingo'
            ];
            $spanishDay = $daysInSpanish[$dayOfWeek] ?? $dayOfWeek;  // Convertir el día de la semana al español

            // Establece la fecha con el día de la semana en español
            $formattedDateWithDay = $spanishDay . ', ' . $formattedDate;

            // Verifica si los valores son 0 y asígnales una cadena vacía
            $total_final = ($row_data['goods'] == 0) ? '' : $row_data['goods'] + $row_data['total']; // Calcula el total_final

            // Establece los valores de las celdas
            $sheet->setCellValue('A' . $row, $formattedDateWithDay)
                  ->setCellValue('B' . $row, $row_data['operators'] ?: '')
                  ->setCellValue('C' . $row, $row_data['descripcion'] ?: '')
                  ->setCellValue('D' . $row, $row_data['primer_t'] == 0 ? '' : $row_data['primer_t'])
                  ->setCellValue('E' . $row, $row_data['segundo_t'] == 0 ? '' : $row_data['segundo_t'])
                  ->setCellValue('F' . $row, $row_data['tercer_t'] == 0 ? '' : $row_data['tercer_t'])
                  ->setCellValue('G' . $row, $row_data['goods'] == 0 ? '' : $row_data['goods'])
                  ->setCellValue('H' . $row, $row_data['coupler'] == 0 ? '' : $row_data['coupler'])
                  ->setCellValue('I' . $row, $row_data['dano_end_face'] == 0 ? '' : $row_data['dano_end_face'])
                  ->setCellValue('J' . $row, $row_data['golpe_top'] == 0 ? '' : $row_data['golpe_top'])
                  ->setCellValue('K' . $row, $row_data['rebaba'] == 0 ? '' : $row_data['rebaba'])
                  ->setCellValue('L' . $row, $row_data['dano_en_lente'] == 0 ? '' : $row_data['dano_en_lente'])
                  ->setCellValue('M' . $row, $row_data['fuera_de_spc'] == 0 ? '' : $row_data['fuera_de_spc'])
                  ->setCellValue('N' . $row, $row_data['dano_fisico'] == 0 ? '' : $row_data['dano_fisico'])
                  ->setCellValue('O' . $row, $row_data['coupler_dano'] == 0 ? '' : $row_data['coupler_dano'])
                  ->setCellValue('P' . $row, $row_data['hundimiento'] == 0 ? '' : $row_data['hundimiento'])
                  ->setCellValue('Q' . $row, $row_data['fisura'] == 0 ? '' : $row_data['fisura'])
                  ->setCellValue('R' . $row, $row_data['silicon'] == 0 ? '' : $row_data['silicon'])
                  ->setCellValue('S' . $row, $row_data['contaminacion'] == 0 ? '' : $row_data['contaminacion'])
                  ->setCellValue('T' . $row, $row_data['total'] == 0 ? '' : $row_data['total'])
                  ->setCellValue('U' . $row, $total_final ?: '')
                  ->setCellValue('V' . $row, $row_data['comments'] ?: '');

       // Establecer el color de texto verde y el fondo verde para los títulos de columnas específicas
$sheet->getStyle('G' . $startRow)->applyFromArray([
    'font' => [
        'color' => ['argb' => '008000'], // Color de texto verde
        'bold' => true, // Poner el texto en negrita si deseas
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'D9EAD3'], // Color de fondo verde claro
    ],
]);

$sheet->getStyle('U' . $startRow)->applyFromArray([
    'font' => [
        'color' => ['argb' => '008000'], // Color de texto verde
        'bold' => true, // Poner el texto en negrita si deseas
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'D9EAD3'], // Color de fondo verde claro
    ],
]);


// Aplicar color de texto rojo a las celdas con valores
$sheet->getStyle('H' . $row)->getFont()->getColor()->setARGB('FF0000'); // Rojo para coupler
$sheet->getStyle('I' . $row)->getFont()->getColor()->setARGB('FF0000'); // Rojo para daño end face
$sheet->getStyle('J' . $row)->getFont()->getColor()->setARGB('FF0000'); // Rojo para golpe top
$sheet->getStyle('K' . $row)->getFont()->getColor()->setARGB('FF0000'); // Rojo para rebaba
$sheet->getStyle('L' . $row)->getFont()->getColor()->setARGB('FF0000'); // Rojo para daño en lente
$sheet->getStyle('M' . $row)->getFont()->getColor()->setARGB('FF0000'); // Rojo para fuera de SPC
$sheet->getStyle('N' . $row)->getFont()->getColor()->setARGB('FF0000'); // Rojo para daño físico
$sheet->getStyle('O' . $row)->getFont()->getColor()->setARGB('FF0000'); // Rojo para coupler dañado
$sheet->getStyle('P' . $row)->getFont()->getColor()->setARGB('FF0000'); // Rojo para hundimiento
$sheet->getStyle('Q' . $row)->getFont()->getColor()->setARGB('FF0000'); // Rojo para fisura
$sheet->getStyle('R' . $row)->getFont()->getColor()->setARGB('FF0000'); // Rojo para silicón / contaminación
$sheet->getStyle('S' . $row)->getFont()->getColor()->setARGB('FF0000'); // Rojo para contaminación / end face
$sheet->getStyle('T' . $row)->getFont()->getColor()->setARGB('FF0000'); // Rojo para total



            // Aplicar el estilo de bordes y alineación
            $sheet->getStyle("A$row:V$row")->applyFromArray($dataStyle);

            // Ajustar el ancho de las columnas automáticamente
            foreach (range('A', 'V') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $row++;
        }

        // Generar el archivo Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'molex SEM46_' . date('Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    } else {
        echo 'No se encontraron datos para este usuario.';
    }
} catch (Exception $e) {
    echo 'Error al generar el reporte: ' . $e->getMessage();
}
?>
