<?php
session_start();
require '../config/connection.php';

// Obtener la conexión a la base de datos
$db = new Database();
$con = $db->conectar();

// Inicializar las fechas de inicio y fin, con valores predeterminados
$start_date = '2025-01-01';
$end_date = date('Y-m-d');

// Si las fechas están en los parámetros GET, usarlas
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
}

// Consulta SQL para obtener el rango de fechas
$sql = "SELECT MIN(inspection_date) AS start_date, MAX(inspection_date) AS end_date FROM PCBA";
$stmt = $con->prepare($sql);
$stmt->execute();

// Obtener el rango de fechas de la base de datos
$dateRange = $stmt->fetch(PDO::FETCH_ASSOC);
$start_date = $dateRange['start_date'] ?? $start_date;
$end_date = $dateRange['end_date'] ?? $end_date;

// Consulta SQL para obtener los datos de PCBA en el rango de fechas seleccionado
$sql = "
    SELECT
        inspection_date,
        operators,
        SUM(goods) AS total_goods,
        SUM(fails_dedos_oro) AS total_fails_dedos_oro,
        SUM(fails_mal_corte) AS total_fails_mal_corte,
        SUM(fails_contaminacion) AS total_fails_contaminacion,
        SUM(pd) AS total_pd,
        SUM(fails_desplazados) AS total_fails_desplazados,
        SUM(fails_insuficiencias) AS total_fails_insuficiencias,
        SUM(fails_despanelizados) AS total_fails_despanelizados,
        SUM(fails_desprendidos) AS total_fails_desprendidos,
        SUM(total_fails) AS total_total_fails,
        SUM(total) AS total_total
    FROM
        PCBA
    WHERE
        inspection_date BETWEEN :start_date AND :end_date
    GROUP BY
        inspection_date
    ORDER BY
        inspection_date DESC
";

// Preparar y ejecutar la consulta
$stmt = $con->prepare($sql);
$stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
$stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
$stmt->execute();

// Obtener los datos
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Formatear los datos para el gráfico
$chartData = [];
foreach ($data as $row) {
    // Datos de prueba
$chartData = [
    [
        'inspection_date' => '2024-12-05',
        'total_goods' => 100,
        'total_fails_dedos_oro' => 5,
        'total_fails_mal_corte' => 3,
        'total_fails_contaminacion' => 2,
        'total_pd' => 1,
        'total_fails_desplazados' => 0,
        'total_fails_insuficiencias' => 0,
        'total_fails_despanelizados' => 0,
        'total_fails_desprendidos' => 1,
        'total_total_fails' => 5,
        'total_total' => 100
    ],
    [
        'inspection_date' => '2024-12-06',
        'total_goods' => 120,
        'total_fails_dedos_oro' => 6,
        'total_fails_mal_corte' => 4,
        'total_fails_contaminacion' => 3,
        'total_pd' => 2,
        'total_fails_desplazados' => 0,
        'total_fails_insuficiencias' => 0,
        'total_fails_despanelizados' => 1,
        'total_fails_desprendidos' => 2,
        'total_total_fails' => 7,
        'total_total' => 120
    ]
];

// Devolver los datos como JSON
echo json_encode($chartData);

}

// Devolver los datos como JSON
header('Content-Type: application/json');
echo json_encode($chartData);
?>
