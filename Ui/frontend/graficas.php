<?php
session_start();
require '../../config/connection.php';

if (!isset($_SESSION['id_administrador'])) {
    echo 'La sesión no tiene el id_administrador.';
    exit();
}

$id_administrador = $_SESSION['id_administrador'];

$db = new Database();
$con = $db->conectar();


$sql = "SELECT nombre, apellido, compania, business_unit, telefono, correo, role, fecha_registro
        FROM administrador 
        WHERE id_administrador = :id_administrador";
$stmt = $con->prepare($sql);
$stmt->bindParam(':id_administrador', $id_administrador, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $nombre = $row['nombre'];
    $apellido = $row['apellido'];
    $compania = $row['compania'];
    $business_unit = $row['business_unit'];
    $telefono = $row['telefono'];
    $correo = $row['correo'];
    $role = $row['role'];
    $created_at = $row['fecha_registro'];


    $photo = '../../assets/img/avatars/1.png';
} else {
    echo 'Administrador no encontrado o cuenta no válida.';
    exit();
}

$sql = "SELECT MIN(inspection_date) AS start_date, MAX(inspection_date) AS end_date FROM PCBA";
$stmt = $con->prepare($sql);
$stmt->execute();

$dateRange = $stmt->fetch(PDO::FETCH_ASSOC);
$start_date = $dateRange['start_date'] ?? '2025-01-01';
$end_date = $dateRange['end_date'] ?? date('Y-m-d');


if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
  $start_date = $_GET['start_date'];
  $end_date = $_GET['end_date'];
}

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
    inspection_date, operators
  ORDER BY
    inspection_date DESC";

$stmt = $con->prepare($sql);
$stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
$stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);


$chartData = [];
foreach ($data as $row) {
  $chartData[] = [
    'inspection_date' => $row['inspection_date'],
    'total_goods' => $row['total_goods'],
    'total_fails_dedos_oro' => $row['total_fails_dedos_oro'],
    'total_fails_mal_corte' => $row['total_fails_mal_corte'],
    'total_fails_contaminacion' => $row['total_fails_contaminacion'],
    'total_pd' => $row['total_pd'],
    'total_fails_desplazados' => $row['total_fails_desplazados'],
    'total_fails_insuficiencias' => $row['total_fails_insuficiencias'],
    'total_fails_despanelizados' => $row['total_fails_despanelizados'],
    'total_fails_desprendidos' => $row['total_fails_desprendidos'],
    'total_total_fails' => $row['total_total_fails'],
    'total_total' => $row['total_total']
  ];
}






$sql = "SELECT MIN(inspection_date) AS start_date, MAX(inspection_date) AS end_date FROM materiales";
$stmt = $con->prepare($sql);
$stmt->execute();
$dateRangeMateriales = $stmt->fetch(PDO::FETCH_ASSOC);
$start_date_materiales = $dateRangeMateriales['start_date'] ?? '2025-01-01';
$end_date_materiales = $dateRangeMateriales['end_date'] ?? date('Y-m-d');

if (isset($_GET['start_date_materiales']) && isset($_GET['end_date_inspeccion'])) {
  $start_date_materiales = $_GET['start_date_inspeccion'];
  $end_date_materiales = $_GET['end_date_materiales'];
}


$sql_materiales = "
  SELECT
    inspection_date,
    shift,
    operators,
    SUM(goods) AS total_goods,
    SUM(dedos_de_oro_contaminados) AS total_dedos_de_oro_contaminados,
    SUM(faltante) AS total_faltante,
    SUM(desplazados) AS total_desplazados,
    SUM(insuficiencias) AS total_insuficiencias,
    SUM(despanelizados) AS total_despanelizados,
    SUM(desprendidos) AS total_desprendidos,
    SUM(total) AS total_total,
    AVG(yield) AS average_yield,
    SUM(total_final) AS total_final
  FROM
    materiales
  WHERE
    inspection_date BETWEEN :start_date_materiales AND :end_date_materiales
  GROUP BY
    inspection_date, shift, operators
  ORDER BY
    inspection_date DESC";

$stmt_materiales = $con->prepare($sql_materiales);
$stmt_materiales->bindParam(':start_date_materiales', $start_date_materiales, PDO::PARAM_STR);
$stmt_materiales->bindParam(':end_date_materiales', $end_date_materiales, PDO::PARAM_STR);
$stmt_materiales->execute();
$data_materiales = $stmt_materiales->fetchAll(PDO::FETCH_ASSOC);


$chartDataMateriales = [];
foreach ($data_materiales as $row) {
  $chartDataMateriales[] = [
    'inspection_date' => $row['inspection_date'],
    'shift' => $row['shift'],
    'operators' => $row['operators'],
    'total_goods' => $row['total_goods'],
    'total_dedos_de_oro_contaminados' => $row['total_dedos_de_oro_contaminados'],
    'total_faltante' => $row['total_faltante'],
    'total_desplazados' => $row['total_desplazados'],
    'total_insuficiencias' => $row['total_insuficiencias'],
    'total_despanelizados' => $row['total_despanelizados'],
    'total_desprendidos' => $row['total_desprendidos'],
    'total_total' => $row['total_total'],
    'average_yield' => $row['average_yield'],
    'total_final' => $row['total_final']
  ];
}




$sql_inspecciones = "SELECT MIN(inspection_date) AS start_date, MAX(inspection_date) AS end_date FROM inspecciones";
$stmt_inspecciones = $con->prepare($sql_inspecciones);
$stmt_inspecciones->execute();
$dateRangeInspecciones = $stmt_inspecciones->fetch(PDO::FETCH_ASSOC);

$start_date_inspecciones = $dateRangeInspecciones['start_date'] ?? '2025-01-01';
$end_date_inspecciones = $dateRangeInspecciones['end_date'] ?? date('Y-m-d');


if (isset($_GET['start_date_inspecciones']) && isset($_GET['end_date_inspecciones'])) {
  $start_date_inspecciones = $_GET['start_date_inspecciones'];
  $end_date_inspecciones = $_GET['end_date_inspecciones'];
}


function obtenerDatosInspecciones($con, $version, $start_date_inspecciones, $end_date_inspecciones)
{
  $sql = "
        SELECT
            inspection_date,
            operators,
            descripcion,
            SUM(primer_t) AS total_primer_t,
            SUM(segundo_t) AS total_segundo_t,
            SUM(tercer_t) AS total_tercer_t,
            SUM(goods) AS total_goods,
            SUM(coupler) AS total_coupler,
            SUM(dano_end_face) AS total_dano_end_face,
            SUM(golpe_top) AS total_golpe_top,
            SUM(rebaba) AS total_rebaba,
            SUM(dano_en_lente) AS total_dano_en_lente,
            SUM(fuera_de_spc) AS total_fuera_de_spc,
            SUM(dano_fisico) AS total_dano_fisico,
            SUM(coupler_danado) AS total_coupler_danado,
            SUM(hundimiento) AS total_hundimiento,
            SUM(fisura) AS total_fisura,
            SUM(silicon_contaminacion) AS total_silicon_contaminacion,
            SUM(contaminacion_end_face) AS total_contaminacion_end_face,
            SUM(total) AS total_total,
            SUM(total_final) AS total_final
        FROM
            inspecciones
        INNER JOIN
            versiones_inspeccion ON inspecciones.id_version = versiones_inspeccion.id_version
        WHERE
            versiones_inspeccion.nombre_version = :version
            AND inspection_date BETWEEN :start_date_inspecciones AND :end_date_inspecciones
        GROUP BY
            inspection_date, operators, descripcion
        ORDER BY
            inspection_date DESC";

  $stmt = $con->prepare($sql);
  $stmt->bindParam(':version', $version, PDO::PARAM_STR);
  $stmt->bindParam(':start_date_inspecciones', $start_date_inspecciones, PDO::PARAM_STR);
  $stmt->bindParam(':end_date_inspecciones', $end_date_inspecciones, PDO::PARAM_STR);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


$versions = ['sem42', 'sem43', 'sem44', 'sem45', 'sem46', 'sem47', 'sem48'];
$chartDataInspecciones = [];

foreach ($versions as $version) {
  $chartDataInspecciones[$version] = obtenerDatosInspecciones($con, $version, $start_date_inspecciones, $end_date_inspecciones);
}




$sql_inspecciones = "SELECT MIN(inspection_date) AS start_date, MAX(inspection_date) AS end_date FROM molex";
$stmt_inspecciones = $con->prepare($sql_inspecciones);
$stmt_inspecciones->execute();
$dateRangeMolex = $stmt_inspecciones->fetch(PDO::FETCH_ASSOC);


$start_date_molex = $dateRangeMolex['start_date'] ?? '2025-01-01';
$end_date_molex = $dateRangeMolex['end_date'] ?? date('Y-m-d');


if (isset($_GET['start_date_molex']) && isset($_GET['end_date_molex'])) {
  $start_date_molex = $_GET['start_date_molex'];
  $end_date_molex = $_GET['end_date_molex'];
}


function obtenerDatosMolex($con, $version, $start_date_molex, $end_date_molex)
{

  if ($start_date_molex === '2025-01-01' && $end_date_molex === date('Y-m-d')) {

    $sql = "
            SELECT
                inspection_date,
                operators,
                descripcion,
                SUM(primer_t) AS total_primer_t,
                SUM(segundo_t) AS total_segundo_t,
                SUM(tercer_t) AS total_tercer_t,
                SUM(goods) AS total_goods,
                SUM(coupler) AS total_coupler,
                SUM(dano_end_face) AS total_dano_end_face,
                SUM(golpe_top) AS total_golpe_top,
                SUM(rebaba) AS total_rebaba,
                SUM(dano_en_lente) AS total_dano_en_lente,
                SUM(fuera_de_spc) AS total_fuera_de_spc,
                SUM(dano_fisico) AS total_dano_fisico,
                SUM(wirebond_corto) AS total_wirebond_corto,
                SUM(wirebond_chueco) AS total_wirebond_chueco,
                SUM(fisura) AS total_fisura,
                SUM(silicon_contaminacion) AS total_silicon_contaminacion,
                SUM(contaminacion_end_face) AS total_contaminacion_end_face,
                SUM(total) AS total_total,
                SUM(total_final) AS total_final
            FROM
                molex
            INNER JOIN
                versiones_inspeccion ON molex.id_version = versiones_inspeccion.id_version
            WHERE
                versiones_inspeccion.nombre_version = :version
            GROUP BY
                inspection_date, operators, descripcion
            ORDER BY
                inspection_date DESC";
  } else {

    $sql = "
            SELECT
                inspection_date,
                operators,
                descripcion,
                SUM(primer_t) AS total_primer_t,
                SUM(segundo_t) AS total_segundo_t,
                SUM(tercer_t) AS total_tercer_t,
                SUM(goods) AS total_goods,
                SUM(coupler) AS total_coupler,
                SUM(dano_end_face) AS total_dano_end_face,
                SUM(golpe_top) AS total_golpe_top,
                SUM(rebaba) AS total_rebaba,
                SUM(dano_en_lente) AS total_dano_en_lente,
                SUM(fuera_de_spc) AS total_fuera_de_spc,
                SUM(dano_fisico) AS total_dano_fisico,
                SUM(wirebond_corto) AS total_wirebond_corto,
                SUM(wirebond_chueco) AS total_wirebond_chueco,
                SUM(fisura) AS total_fisura,
                SUM(silicon_contaminacion) AS total_silicon_contaminacion,
                SUM(contaminacion_end_face) AS total_contaminacion_end_face,
                SUM(total) AS total_total,
                SUM(total_final) AS total_final
            FROM
                molex
            INNER JOIN
                versiones_inspeccion ON molex.id_version = versiones_inspeccion.id_version
            WHERE
                versiones_inspeccion.nombre_version = :version
                AND inspection_date BETWEEN :start_date_molex AND :end_date_molex
            GROUP BY
                inspection_date, operators, descripcion
            ORDER BY
                inspection_date DESC";
  }


  $stmt = $con->prepare($sql);


  $stmt->bindParam(':version', $version, PDO::PARAM_STR);


  if ($start_date_molex !== '2025-01-01' || $end_date_molex !== date('Y-m-d')) {
    $stmt->bindParam(':start_date_molex', $start_date_molex, PDO::PARAM_STR);
    $stmt->bindParam(':end_date_molex', $end_date_molex, PDO::PARAM_STR);
  }


  $stmt->execute();


  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


$versions = ['sem49', 'sem50', 'sem51', 'sem52'];

$chartDataMolex = [];


foreach ($versions as $version) {
  $chartDataMolex[$version] = obtenerDatosMolex($con, $version, $start_date_molex, $end_date_molex);
}

?>



<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../ico/comter.png" type="image/x-icon">
    <link rel="stylesheet" href="../../css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="../../css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="../../css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="../../css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="../../css/styles_administrador.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>


<script type="text/javascript">
  google.charts.load('current', { 'packages': ['corechart', 'bar'] });
  google.charts.setOnLoadCallback(drawChart);

  var rawData = <?php echo json_encode($chartData); ?>;
  var filteredData = rawData;

  function drawChart() {
    if (!filteredData.length) {
      document.getElementById('chart_div').innerHTML = '<p>No hay datos disponibles para graficar.</p>';
      return;
    }

    var data = google.visualization.arrayToDataTable([
      ['Fecha', 'Goods', 'Fails Dedos Oro', 'Fails Mal Corte', 'Fails Contaminacion', 'PD', 'Fails Desplazados', 'Fails Insuficiencias', 'Fails Despanelizados', 'Fails Desprendidos', 'Total Fails', 'Total'],
      ...filteredData.map(row => [
        row.inspection_date,
        parseInt(row.total_goods),
        parseInt(row.total_fails_dedos_oro),
        parseInt(row.total_fails_mal_corte),
        parseInt(row.total_fails_contaminacion),
        parseInt(row.total_pd),
        parseInt(row.total_fails_desplazados),
        parseInt(row.total_fails_insuficiencias),
        parseInt(row.total_fails_despanelizados),
        parseInt(row.total_fails_desprendidos),
        parseInt(row.total_total_fails),
        parseInt(row.total_total)
      ])
    ]);

    var options = {
      title: 'MOLEX PCBA - Material Acumulado o de Almacén',
      chartArea: { width: '50%' },
      hAxis: { title: 'Cantidad', minValue: 0 },
      vAxis: { title: 'Fecha' },
      isStacked: true,
    };

    var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
    chart.draw(data, options);
  }

  function filterData() {
    var startDate = document.getElementById('start_date').value;
    var endDate = document.getElementById('end_date').value;

    if (!startDate || !endDate) {
      filteredData = rawData;
    } else {
      var startDateObj = new Date(startDate);
      var endDateObj = new Date(endDate);

      filteredData = rawData.filter(function (row) {
        var rowDate = new Date(row['inspection_date']);
        return rowDate >= startDateObj && rowDate <= endDateObj;
      });
    }

    console.log('Datos filtrados:', filteredData);
    drawChart();
  }

  function resetFilters() {
    document.getElementById('start_date').value;
    document.getElementById('end_date').value;

    filteredData = rawData;
    drawChart();
  }



</script>

<script type="text/javascript">
  google.charts.load('current', { 'packages': ['corechart', 'pie'] });
  google.charts.setOnLoadCallback(drawMaterialesChart);

  google.charts.load('current', { 'packages': ['corechart', 'bar'] });
  google.charts.setOnLoadCallback(drawMaterialesChart);

  var rawMaterialesData = <?php echo json_encode($chartDataMateriales); ?>;
  var filteredMaterialesData = rawMaterialesData;


  function drawMaterialesChart() {
    if (!filteredMaterialesData.length) {
      document.getElementById('chart_div_materiales').innerHTML = '<p>No hay datos disponibles para graficar.</p>';
      return;
    }

    var data = google.visualization.arrayToDataTable([
      ['Fecha', 'Goods', 'Fails Dedos Oro', 'Fails Faltante', 'Fails Desplazados', 'Fails Insuficiencias', 'Fails Despanelizados', 'Fails Desprendidos', 'Total Fails', 'Total', 'Average Yield'],
      ...filteredMaterialesData.map(row => [
        row.inspection_date,
        parseInt(row.total_goods),
        parseInt(row.total_dedos_de_oro_contaminados),
        parseInt(row.total_faltante),
        parseInt(row.total_desplazados),
        parseInt(row.total_insuficiencias),
        parseInt(row.total_despanelizados),
        parseInt(row.total_desprendidos),
        parseInt(row.total_total),
        parseInt(row.total_final),
        parseFloat(row.average_yield)
      ])
    ]);

    var options = {
      title: 'MATERIAL DE SMT (MAT.FRESCO)',
      chartArea: { width: '50%' },
      hAxis: { title: 'Cantidad', minValue: 0 },
      vAxis: { title: 'Fecha' },
      isStacked: true,
    };

    var chart = new google.visualization.BarChart(document.getElementById('chart_div_materiales'));
    chart.draw(data, options);
  }


  function filterDataMolex(version) {
    var startDate = document.getElementById('start_date_molex_' + version).value;
    var endDate = document.getElementById('end_date_molex_' + version).value;

    if (!startDate || !endDate) {
      filteredMolexData[version] = rawMolexData[version];
    } else {
      var startDateObj = new Date(startDate);
      var endDateObj = new Date(endDate);

      filteredMolexData[version] = rawMolexData[version].filter(function (row) {
        var rowDate = new Date(row['inspection_date']);
        return rowDate >= startDateObj && rowDate <= endDateObj;
      });
    }

    drawMolexChart(version);
  }

  function resetFiltersMolex(version) {
    document.getElementById('start_date_molex_' + version).value = '';
    document.getElementById('end_date_molex_' + version).value = '';

    filteredMolexData[version] = rawMolexData[version];

    drawMolexChart(version);
  }


  window.onload = function () {
    var startDate = document.getElementById('start_date_materiales').value;
    var endDate = document.getElementById('end_date_materiales').value;

    if (startDate && endDate) {
      filterDataMateriales();
    } else {
      drawMaterialesChart();
    }
  }

</script>

<script type="text/javascript">
  google.charts.load('current', { 'packages': ['corechart', 'bar'] });
  google.charts.setOnLoadCallback(drawAllInspeccionesCharts);

  var rawInspeccionesData = <?php echo json_encode($chartDataInspecciones); ?>;
  var filteredInspeccionesData = {
    sem42: rawInspeccionesData['sem42'],
    sem43: rawInspeccionesData['sem43'],
    sem44: rawInspeccionesData['sem44'],
    sem45: rawInspeccionesData['sem45'],
    sem46: rawInspeccionesData['sem46'],
    sem47: rawInspeccionesData['sem47'],
    sem48: rawInspeccionesData['sem48']
  };

  function drawAllInspeccionesCharts() {
    drawInspeccionesChart('sem42');
    drawInspeccionesChart('sem43');
    drawInspeccionesChart('sem44');
    drawInspeccionesChart('sem45');
    drawInspeccionesChart('sem46');
    drawInspeccionesChart('sem47');
    drawInspeccionesChart('sem48');
  }

  function drawInspeccionesChart(version) {
    if (!filteredInspeccionesData[version].length) {
      document.getElementById('chart_div_inspecciones_' + version).innerHTML = '<p>No hay datos disponibles para graficar.</p>';
      return;
    }

    var data = google.visualization.arrayToDataTable([
      ['Fecha', 'Primer T', 'Segundo T', 'Tercer T', 'Goods', 'Coupler', 'Dano End Face', 'Golpe Top', 'Rebaba', 'Dano en Lente', 'Fuera de SPC', 'Dano Fisico', 'Coupler Danado', 'Hundimiento', 'Fisura', 'Silicon Contaminacion', 'Contaminacion End Face', 'Total', 'Total Final'],
      ...filteredInspeccionesData[version].map(row => [
        row.inspection_date,
        parseInt(row.total_primer_t),
        parseInt(row.total_segundo_t),
        parseInt(row.total_tercer_t),
        parseInt(row.total_goods),
        parseInt(row.total_coupler),
        parseInt(row.total_dano_end_face),
        parseInt(row.total_golpe_top),
        parseInt(row.total_rebaba),
        parseInt(row.total_dano_en_lente),
        parseInt(row.total_fuera_de_spc),
        parseInt(row.total_dano_fisico),
        parseInt(row.total_coupler_danado),
        parseInt(row.total_hundimiento),
        parseInt(row.total_fisura),
        parseInt(row.total_silicon_contaminacion),
        parseInt(row.total_contaminacion_end_face),
        parseInt(row.total_total),
        parseInt(row.total_final)
      ])
    ]);

    var options = {
      title: 'Molex - ' + version.toUpperCase(),
      chartArea: { width: '70%' },
      hAxis: { title: 'Cantidad', minValue: 0 },
      vAxis: { title: 'Fecha' },
      isStacked: true,
      colors: ['#FF5733', '#33FF57', '#337FFF', '#FF33A6', '#FFD700', '#FF8C00', '#8A2BE2', '#00FA9A', '#FF1493', '#7FFF00', '#FFD700', '#DC143C', '#B22222', '#FF6347', '#DAA520', '#C71585', '#808080', '#20B2AA', '#90EE90'],
      legend: { position: 'top' },
      bars: 'vertical'
    };

    var chart = new google.visualization.BarChart(document.getElementById('chart_div_inspecciones_' + version));
    chart.draw(data, options);
  }

  function filterDataInspecciones(version) {
    var startDate = document.getElementById('start_date_inspecciones_' + version).value;
    var endDate = document.getElementById('end_date_inspecciones_' + version).value;

    if (!startDate || !endDate) {
      filteredInspeccionesData[version] = rawInspeccionesData[version];
    } else {
      var startDateObj = new Date(startDate);
      var endDateObj = new Date(endDate);

      filteredInspeccionesData[version] = rawInspeccionesData[version].filter(function (row) {
        var rowDate = new Date(row['inspection_date']);
        return rowDate >= startDateObj && rowDate <= endDateObj;
      });
    }

    drawInspeccionesChart(version);
  }

  function resetFiltersInspecciones(version) {
    document.getElementById('start_date_inspecciones_' + version).value;
    document.getElementById('end_date_inspecciones_' + version).value;

    filteredInspeccionesData[version] = rawInspeccionesData[version];
    drawInspeccionesChart(version);
  }
</script>



<script type="text/javascript">
  google.charts.load('current', { 'packages': ['corechart', 'bar'] });
  google.charts.setOnLoadCallback(drawAllMolexCharts);

  var rawMolexData = <?php echo json_encode($chartDataMolex); ?>;

  var filteredMolexData = {
    sem49: rawMolexData['sem49'],
    sem50: rawMolexData['sem50'],
    sem51: rawMolexData['sem51'],
    sem52: rawMolexData['sem52']
  };

  function drawAllMolexCharts() {
    drawMolexChart('sem49');
    drawMolexChart('sem50');
    drawMolexChart('sem51');
    drawMolexChart('sem52');
  }

  function drawMolexChart(version) {
    if (!filteredMolexData[version].length) {
      document.getElementById('chart_div_molex_' + version).innerHTML = '<p>No hay datos disponibles para graficar.</p>';
      return;
    }

    var data = google.visualization.arrayToDataTable([
      ['Fecha', 'Primer T', 'Segundo T', 'Tercer T', 'Goods', 'Coupler', 'Dano End Face', 'Golpe Top', 'Rebaba', 'Dano en Lente', 'Fuera de SPC', 'Dano Fisico', 'Wirebond Corto', 'Wirebond Chueco', 'Fisura', 'Silicon Contaminacion', 'Contaminacion End Face', 'Total', 'Total Final'],
      ...filteredMolexData[version].map(row => [
        row.inspection_date,
        parseInt(row.total_primer_t),
        parseInt(row.total_segundo_t),
        parseInt(row.total_tercer_t),
        parseInt(row.total_goods),
        parseInt(row.total_coupler),
        parseInt(row.total_dano_end_face),
        parseInt(row.total_golpe_top),
        parseInt(row.total_rebaba),
        parseInt(row.total_dano_en_lente),
        parseInt(row.total_fuera_de_spc),
        parseInt(row.total_dano_fisico),
        parseInt(row.total_wirebond_corto),
        parseInt(row.total_wirebond_chueco),
        parseInt(row.total_fisura),
        parseInt(row.total_silicon_contaminacion),
        parseInt(row.total_contaminacion_end_face),
        parseInt(row.total_total),
        parseInt(row.total_final)
      ])
    ]);

    var options = {
      title: 'Molex - ' + version.toUpperCase(),
      chartArea: { width: '70%' },
      hAxis: { title: 'Cantidad', minValue: 0 },
      vAxis: { title: 'Fecha' },
      isStacked: true,
      colors: ['#FF5733', '#33FF57', '#337FFF', '#FF33A6', '#FFD700', '#FF8C00', '#8A2BE2', '#00FA9A', '#FF1493', '#7FFF00', '#FFD700', '#DC143C', '#B22222', '#FF6347', '#DAA520', '#C71585', '#808080', '#20B2AA', '#90EE90'],
      legend: { position: 'top' },
      bars: 'vertical'
    };

    var chart = new google.visualization.BarChart(document.getElementById('chart_div_molex_' + version));
    chart.draw(data, options);
  }

  function filterDataMolex(version) {
    var startDate = document.getElementById('start_date_molex_' + version).value;
    var endDate = document.getElementById('end_date_molex_' + version).value;

    if (!startDate || !endDate) {
      filteredMolexData[version] = rawMolexData[version];
    } else {
      var startDateObj = new Date(startDate);
      var endDateObj = new Date(endDate);

      filteredMolexData[version] = rawMolexData[version].filter(function (row) {
        var rowDate = new Date(row['inspection_date']);
        return rowDate >= startDateObj && rowDate <= endDateObj;
      });
    }

    drawMolexChart(version);
  }

  function resetFiltersMolex(version) {
    document.getElementById('start_date_molex_' + version).value;
    document.getElementById('end_date_molex_' + version).value;

    filteredMolexData[version] = rawMolexData[version];
    drawMolexChart(version);
  }

</script>

    <title>Comter</title>

</head>

<body>



    <header class="header fixed-top" style="background-color:#1B419B;">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="header__logo d-flex align-items-center">
                        <a href="../administrador.php"><img src="../../ico/comter.png" alt="" style="width:50px"></a>
                        <button class="navbar-toggler d-lg-none ms-auto" type="button" data-bs-toggle="collapse"
                            data-bs-target="#sidebarMenu">
                            <i class="fas fa-bars text-white"></i>
                        </button>
                    </div>
                </div>
                <div class="col-lg-10">
                    <div class="header__nav__option">
                        <nav class="header__nav__menu mobile-menu">
                            <ul>

                                <small class="text-muted" style="text-transform:uppercase;"><span
                                        style="color: #fff; font-weight: bold;">Bienvenido</span>
                                    <span
                                        style="color: #fff; font-weight: bold;"><?php echo $role; ?></span></small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <li>
                                    <a href="#" class="no-decoration">
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $photo; ?>" alt="" class="user-image" />
                                            <span
                                                class="fw-semibold d-block ms-2 no-decoration"><?php echo $nombre . ' ' . $apellido; ?></span>
                                        </div>
                                    </a>
                                    <ul class="dropdown">

                                        <li> <a href="#" class="no-decoration"> <small class="text-muted">Rol:
                                                    <?php echo $role; ?></small></a>
                                            <hr>

                                        <li><a href="#" class="no-decoration" onclick="confirmLogout()">Cerrar
                                                sesión</a></li>
                                    </ul>
                                </li>

                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>
    <br><br><br><br><br><br>

    <div class="container-fluid">
        <div class="row">

            <div class="col-lg-2 sidebar collapse d-lg-block" id="sidebarMenu">
                <div class="d-flex justify-content-between align-items-center px-3 mb-3">
                    <div class="d-lg-none text-center pt-3">
                        <a href="../administrador.php">
                            <img src="../../ico/comter.png" alt="Comter" style="width:50px; z-index: 1031;"
                                class="img-fluid">
                        </a>
                    </div>
                    <button id="toggleSidebar" class="btn d-none d-lg-block">
                        <i class="fas fa-bars text-white"></i>
                    </button>
                </div>
                <div class="sidebar-menu">
                    <ul class="nav flex-column">
                    <li class="nav-item mt-1">
                        <button type="button" class="btn btn-primary nav-link" data-bs-toggle="modal" 
                                data-bs-target="#nuevoModal">
                                <i class="fas fa-plus"></i> Registro Comter
                            </button>
                        </li>
                      <li class="nav-item mt-1">
                                  <button type="button" class="btn btn-primary nav-link" data-bs-toggle="modal" 
                                          data-bs-target="#modalCliente">
                                          <i class="fas fa-plus"></i> Registro Cliente
                                      </button>
                                  </li>
                                  <li class="nav-item">
                            <a class="nav-link" href="molex.php">
                            <i class="fas fa-file-alt"></i> Reportes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="graficas.php">
                            <i class="fas fa-chart-bar"></i> Gráficas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="respaldos.php">
                                <i class="fas fa-database"></i> Respaldos
                            </a>
                        </li>

                       
                        

                        <!--<li class="nav-item">
                        <a href="../backend/exportar_excel.php" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Exportar a Excel
                                </a>
                        </li>-->
                        
                       
<li class="nav-item mt-1">
    <button class="btn btn-success" onclick="mostrarSweetAlert()">
        <i class="fas fa-file-excel"></i> Exportar a Excel
    </button>
</li>

<!--<li class="nav-item mt-1">
                            <a class="nav-link" href="clientes.php">
                                <i class="fas fa-users"></i> Clientes
                            </a>
                        </li>-->
                        <!--<li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#semanas">
                                <i class="fas fa-calendar-week"></i> MOLEX Semanas
                            </a>
                            <div class="collapse" id="semanas">
                                <ul class="nav flex-column ms-3">
                                    <?php
                                    for ($i = 42; $i <= 52; $i++) {
                                        echo '<li class="nav-item">
                                                <a class="nav-link" href="sem' . $i . '.php">SEM' . $i . '</a>
                                              </li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </li>-->
                    </ul>
                </div>
            </div>

            <div class="col-lg-10 main-content" style="background-color: #fff;">
                
        
        
        <div class="container mt-5" >
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="start_date" class="form-label">Fecha de inicio:</label>
                      <input type="date" id="start_date" class="form-control" value="<?php echo $start_date; ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="end_date" class="form-label">Fecha de fin:</label>
                      <input type="date" id="end_date" class="form-control" value="<?php echo $end_date; ?>">
                    </div>
                  </div>
                </div>
    
                <div class="row">
                  <div class="col-md-6">
                    <button class="btn btn-primary" onclick="filterData()"> <i class="bx bx-filter-alt"></i> Aplicar
                      Filtro</button>
                    <button class="btn btn-secondary ms-2" onclick="resetFilters()"><i class="bx bx-reset"></i> Reset
                      Filters</button>
                  </div>
                </div>
    
                <div id="chart_div" style="width: 100%; height: 450px; margin-top: 60px;"></div>
              </div>
    
    
    
              <div class="container mt-5">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="start_date_materiales" class="form-label">Fecha de inicio:</label>
                      <input type="date" id="start_date_materiales" class="form-control"
                        value="<?php echo $start_date_materiales; ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="end_date_materiales" class="form-label">Fecha de fin:</label>
                      <input type="date" id="end_date_materiales" class="form-control"
                        value="<?php echo $end_date_materiales; ?>">
                    </div>
                  </div>
                </div>
    
                <div class="row">
                  <div class="col-md-6">
                    <button class="btn btn-primary" onclick="filterDataMateriales()"> <i class="bx bx-filter-alt"></i>
                      Aplicar Filtro</button>
                    <button class="btn btn-secondary ms-2" onclick="resetFiltersMateriales()"><i class="bx bx-reset"></i>
                      Reset Filters</button>
                  </div>
                </div>
    
                <div id="chart_div_materiales" style="width: 100%; height: 450px; margin-top: 60px;"></div>
              </div>
    
    
    
    
    
    
              <div class="container mt-5">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="start_date_inspecciones_sem42" class="form-label">Fecha de inicio:</label>
                      <input type="date" id="start_date_inspecciones_sem42" class="form-control"
                        value="<?php echo $start_date_inspecciones; ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="end_date_inspecciones_sem42" class="form-label">Fecha de fin:</label>
                      <input type="date" id="end_date_inspecciones_sem42" class="form-control"
                        value="<?php echo $end_date_inspecciones; ?>">
                    </div>
                  </div>
                </div>
    
                <div class="row">
                  <div class="col-md-6">
                    <button class="btn btn-primary" onclick="filterDataInspecciones('sem42')"> <i
                        class="bx bx-filter-alt"></i> Aplicar Filtro</button>
                    <button class="btn btn-secondary ms-2" onclick="resetFiltersInspecciones('sem42')"><i
                        class="bx bx-reset"></i> Reset Filters</button>
                  </div>
                </div>
    
                <div id="chart_div_inspecciones_sem42" style="width: 100%; height: 450px; margin-top: 60px;"></div>
              </div>
    
              <div class="container mt-5" style="background-color: #fff;">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="start_date_inspecciones_sem43" class="form-label">Fecha de inicio:</label>
                      <input type="date" id="start_date_inspecciones_sem43" class="form-control"
                        value="<?php echo $start_date_inspecciones; ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="end_date_inspecciones_sem43" class="form-label">Fecha de fin:</label>
                      <input type="date" id="end_date_inspecciones_sem43" class="form-control"
                        value="<?php echo $end_date_inspecciones; ?>">
                    </div>
                  </div>
                </div>
    
                <div class="row">
                  <div class="col-md-6">
                    <button class="btn btn-primary" onclick="filterDataInspecciones('sem43')"> <i
                        class="bx bx-filter-alt"></i> Aplicar Filtro</button>
                    <button class="btn btn-secondary ms-2" onclick="resetFiltersInspecciones('sem43')"><i
                        class="bx bx-reset"></i> Reset Filters</button>
                  </div>
                </div>
    
                <div id="chart_div_inspecciones_sem43" style="width: 100%; height: 450px; margin-top: 60px;"></div>
              </div>
    
              <div class="container mt-5" style="background-color: #fff;">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="start_date_inspecciones_sem44" class="form-label">Fecha de inicio:</label>
                      <input type="date" id="start_date_inspecciones_sem44" class="form-control"
                        value="<?php echo $start_date_inspecciones; ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="end_date_inspecciones_sem44" class="form-label">Fecha de fin:</label>
                      <input type="date" id="end_date_inspecciones_sem44" class="form-control"
                        value="<?php echo $end_date_inspecciones; ?>">
                    </div>
                  </div>
                </div>
    
                <div class="row">
                  <div class="col-md-6">
                    <button class="btn btn-primary" onclick="filterDataInspecciones('sem44')"> <i
                        class="bx bx-filter-alt"></i> Aplicar Filtro</button>
                    <button class="btn btn-secondary ms-2" onclick="resetFiltersInspecciones('sem44')"><i
                        class="bx bx-reset"></i> Reset Filters</button>
                  </div>
                </div>
    
                <div id="chart_div_inspecciones_sem44" style="width: 100%; height: 450px; margin-top: 60px;"></div>
              </div>
    
              <div class="container mt-5">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="start_date_inspecciones_sem45" class="form-label">Fecha de inicio:</label>
                      <input type="date" id="start_date_inspecciones_sem45" class="form-control"
                        value="<?php echo $start_date_inspecciones; ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="end_date_inspecciones_sem45" class="form-label">Fecha de fin:</label>
                      <input type="date" id="end_date_inspecciones_sem45" class="form-control"
                        value="<?php echo $end_date_inspecciones; ?>">
                    </div>
                  </div>
                </div>
    
                <div class="row">
                  <div class="col-md-6">
                    <button class="btn btn-primary" onclick="filterDataInspecciones('sem45')"> <i
                        class="bx bx-filter-alt"></i> Aplicar Filtro</button>
                    <button class="btn btn-secondary ms-2" onclick="resetFiltersInspecciones('sem45')"><i
                        class="bx bx-reset"></i> Reset Filters</button>
                  </div>
                </div>
    
                <div id="chart_div_inspecciones_sem45" style="width: 100%; height: 450px; margin-top: 60px;"></div>
              </div>
    
              <div class="container mt-5">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="start_date_inspecciones_sem46" class="form-label">Fecha de inicio:</label>
                      <input type="date" id="start_date_inspecciones_sem46" class="form-control"
                        value="<?php echo $start_date_inspecciones; ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="end_date_inspecciones_sem46" class="form-label">Fecha de fin:</label>
                      <input type="date" id="end_date_inspecciones_sem46" class="form-control"
                        value="<?php echo $end_date_inspecciones; ?>">
                    </div>
                  </div>
                </div>
    
                <div class="row">
                  <div class="col-md-6">
                    <button class="btn btn-primary" onclick="filterDataInspecciones('sem46')"> <i
                        class="bx bx-filter-alt"></i> Aplicar Filtro</button>
                    <button class="btn btn-secondary ms-2" onclick="resetFiltersInspecciones('sem46')"><i
                        class="bx bx-reset"></i> Reset Filters</button>
                  </div>
                </div>
    
                <div id="chart_div_inspecciones_sem46" style="width: 100%; height: 450px; margin-top: 60px;"></div>
              </div>
    
              <div class="container mt-5">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="start_date_inspecciones_sem47" class="form-label">Fecha de inicio:</label>
                      <input type="date" id="start_date_inspecciones_sem47" class="form-control"
                        value="<?php echo $start_date_inspecciones; ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="end_date_inspecciones_sem47" class="form-label">Fecha de fin:</label>
                      <input type="date" id="end_date_inspecciones_sem47" class="form-control"
                        value="<?php echo $end_date_inspecciones; ?>">
                    </div>
                  </div>
                </div>
    
                <div class="row">
                  <div class="col-md-6">
                    <button class="btn btn-primary" onclick="filterDataInspecciones('sem47')"> <i
                        class="bx bx-filter-alt"></i> Aplicar Filtro</button>
                    <button class="btn btn-secondary ms-2" onclick="resetFiltersInspecciones('sem47')"><i
                        class="bx bx-reset"></i> Reset Filters</button>
                  </div>
                </div>
    
                <div id="chart_div_inspecciones_sem47" style="width: 100%; height: 450px; margin-top: 60px;"></div>
              </div>
    
    
    
              <div class="container mt-5">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="start_date_inspecciones_sem48" class="form-label">Fecha de inicio:</label>
                      <input type="date" id="start_date_inspecciones_sem48" class="form-control"
                        value="<?php echo $start_date_inspecciones; ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="end_date_inspecciones_sem48" class="form-label">Fecha de fin:</label>
                      <input type="date" id="end_date_inspecciones_sem48" class="form-control"
                        value="<?php echo $end_date_inspecciones; ?>">
                    </div>
                  </div>
                </div>
    
                <div class="row">
                  <div class="col-md-6">
                    <button class="btn btn-primary" onclick="filterDataInspecciones('sem48')"> <i
                        class="bx bx-filter-alt"></i> Aplicar Filtro</button>
                    <button class="btn btn-secondary ms-2" onclick="resetFiltersInspecciones('sem48')"><i
                        class="bx bx-reset"></i> Reset Filters</button>
                  </div>
                </div>
    
                <div id="chart_div_inspecciones_sem48" style="width: 100%; height: 450px; margin-top: 60px;"></div>
              </div>
    
              <div class="container mt-5">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="start_date_molex_sem49" class="form-label">Fecha de inicio:</label>
                      <input type="date" id="start_date_molex_sem49" class="form-control"
                        value="<?php echo $start_date_molex['sem49'] ?? '2025-01-01'; ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="end_date_molex_sem49" class="form-label">Fecha de fin:</label>
                      <input type="date" id="end_date_molex_sem49" class="form-control"
                        value="<?php echo $end_date_molex['sem49'] ?? date('Y-m-d'); ?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <button class="btn btn-primary" onclick="filterDataMolex('sem49')"> <i class="bx bx-filter-alt"></i>
                      Aplicar Filtro</button>
                    <button class="btn btn-secondary ms-2" onclick="resetFiltersMolex('sem49')"><i class="bx bx-reset"></i>
                      Reset Filters</button>
                  </div>
                </div>
                <div id="chart_div_molex_sem49" style="width: 100%; height: 450px; margin-top: 60px;"></div>
              </div>
    
              <div class="container mt-5">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="start_date_molex_sem50" class="form-label">Fecha de inicio:</label>
                      <input type="date" id="start_date_molex_sem50" class="form-control"
                        value="<?php echo $start_date_molex['sem50'] ?? '2025-01-01'; ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="end_date_molex_sem50" class="form-label">Fecha de fin:</label>
                      <input type="date" id="end_date_molex_sem50" class="form-control"
                        value="<?php echo $end_date_molex['sem50'] ?? date('Y-m-d'); ?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <button class="btn btn-primary" onclick="filterDataMolex('sem50')"> <i class="bx bx-filter-alt"></i>
                      Aplicar Filtro</button>
                    <button class="btn btn-secondary ms-2" onclick="resetFiltersMolex('sem50')"><i class="bx bx-reset"></i>
                      Reset Filters</button>
                  </div>
                </div>
                <div id="chart_div_molex_sem50" style="width: 100%; height: 450px; margin-top: 60px;"></div>
              </div>
    
              <div class="container mt-5">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="start_date_molex_sem51" class="form-label">Fecha de inicio:</label>
                      <input type="date" id="start_date_molex_sem51" class="form-control"
                        value="<?php echo $start_date_molex['sem51'] ?? '2025-01-01'; ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="end_date_molex_sem51" class="form-label">Fecha de fin:</label>
                      <input type="date" id="end_date_molex_sem51" class="form-control"
                        value="<?php echo $end_date_molex['sem51'] ?? date('Y-m-d'); ?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <button class="btn btn-primary" onclick="filterDataMolex('sem51')"> <i class="bx bx-filter-alt"></i>
                      Aplicar Filtro</button>
                    <button class="btn btn-secondary ms-2" onclick="resetFiltersMolex('sem51')"><i class="bx bx-reset"></i>
                      Reset Filters</button>
                  </div>
                </div>
                <div id="chart_div_molex_sem51" style="width: 100%; height: 450px; margin-top: 60px;"></div>
              </div>
    
              <div class="container mt-5">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="start_date_molex_sem52" class="form-label">Fecha de inicio:</label>
                      <input type="date" id="start_date_molex_sem52" class="form-control"
                        value="<?php echo $start_date_molex['sem52'] ?? '2025-01-01'; ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="end_date_molex_sem52" class="form-label">Fecha de fin:</label>
                      <input type="date" id="end_date_molex_sem52" class="form-control"
                        value="<?php echo $end_date_molex['sem52'] ?? date('Y-m-d'); ?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <button class="btn btn-primary" onclick="filterDataMolex('sem52')"> <i class="bx bx-filter-alt"></i>
                      Aplicar Filtro</button>
                    <button class="btn btn-secondary ms-2" onclick="resetFiltersMolex('sem52')"><i class="bx bx-reset"></i>
                      Reset Filters</button>
                  </div>
                </div>
                <div id="chart_div_molex_sem52" style="width: 100%; height: 450px; margin-top: 60px;"></div>
              </div>
            </div>





            <div class="modal fade" id="respaldoModal" tabindex="-1" aria-labelledby="respaldoModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="respaldoModalLabel">Crear Respaldo</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="respaldos.php" method="POST">
                                <div class="mb-3">
                                    <label for="tipo_respaldo" class="form-label">Tipo de Respaldo</label>
                                    <select class="form-select" id="tipo_respaldo" name="tipo_respaldo" required>
                                        <option value="Individual">Individual</option>
                                        <option value="Completo">Completo</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="respaldo_automatico" class="form-label">Respaldo Completo</label>
                                    <select class="form-select" id="respaldo_automatico" name="respaldo_automatico"
                                        required>
                                        <option value="0">No</option>
                                        <option value="1">Sí</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Selecciona las Tablas a Respaldar</label>
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="usuarios"
                                                    id="usuarios" name="tablas[]">
                                                <label class="form-check-label" for="usuarios">Usuarios</label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="proveedores"
                                                    id="proveedores" name="tablas[]">
                                                <label class="form-check-label" for="proveedores">Proveedores</label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="turnos"
                                                    id="turnos" name="tablas[]">
                                                <label class="form-check-label" for="turnos">Turnos</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="inspecciones"
                                                    id="inspecciones" name="tablas[]">
                                                <label class="form-check-label" for="inspecciones">Inspecciones</label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="materiales"
                                                    id="materiales" name="tablas[]">
                                                <label class="form-check-label" for="materiales">Materiales</label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="PCBA" id="PCBA"
                                                    name="tablas[]">
                                                <label class="form-check-label" for="PCBA">PCBA</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="molex" id="molex"
                                                    name="tablas[]">
                                                <label class="form-check-label" for="molex">Molex</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion"
                                        rows="3"></textarea>
                                </div>

                                <div class="mb-3 text-center">
                                    <button type="submit" class="btn btn-primary">Realizar Respaldo</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>





        </div>
    </div>


    </div>
    </div>
    </div>
    </div>
    </div>


    <div class="modal fade" id="nuevoModal" tabindex="-1" aria-labelledby="nuevoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: linear-gradient(to right, #f8f9fa, #e9ecef);">
            <div class="modal-header" style="background-color: #0d6efd; color: white;">
                <h5 class="modal-title" id="nuevoModalLabel">Registro Comter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background-color: rgba(255, 255, 255, 0.9);">
                <form id="formNuevoRegistro" method="POST" action="../administrador.php">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Compañía</label>
                            <input type="text" class="form-control" name="compania">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Business Unit</label>
                            <input type="text" class="form-control" name="business_unit">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellido</label>
                            <input type="text" class="form-control" name="apellido">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="correo">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" name="telefono">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Departamento</label>
                            <select class="form-select" name="departamento">
                                <option value="">Seleccione un departamento</option>
                                <option value="ADMINISTRACION">ADMINISTRACION</option>
                                <option value="PRODUCCION">PRODUCCION</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Puesto</label>
                            <select class="form-select" name="puesto">
                                <option value="">Seleccione un puesto</option>
                                <option value="GERENTE GENERAL">GERENTE GENERAL</option>
                                <option value="SUPERVISOR">SUPERVISOR</option>
                                <option value="PRODUCCION">PRODUCCION</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Turno</label>
                            <select class="form-select" name="turno_completo">
                                <option value="">Seleccione un turno</option>
                                <option value="1er.">1er.</option>
                                <option value="2do.">2do.</option>
                                <option value="3er.">3er.</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password" id="password">
                                <span class="input-group-text" id="togglePassword">
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary" onclick="sugerirContraseña()">
                                <i class="fas fa-key"></i> Sugerir Contraseña
                            </button>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hora de Inicio</label>
                            <input type="time" class="form-control" name="hora_inicio">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hora de Fin</label>
                            <input type="time" class="form-control" name="hora_fin">
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <h4>Permisos</h4>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permiso_ver" id="permiso_ver">
                                    <label class="form-check-label" for="permiso_ver">Permiso para ver</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permiso_editar" id="permiso_editar">
                                    <label class="form-check-label" for="permiso_editar">Permiso para editar</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permiso_capturar" id="permiso_capturar">
                                    <label class="form-check-label" for="permiso_capturar">Permiso para capturar</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="cerrarModalNuevo()">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="guardarBtn">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>




<script>
 
 function generarContraseña(longitud) {
     const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
     let contraseña = '';
     for (let i = 0; i < longitud; i++) {
         const randomIndex = Math.floor(Math.random() * caracteres.length);
         contraseña += caracteres.charAt(randomIndex);
     }
     return contraseña;
 }


 function sugerirContraseñaCliente() {
     const contraseñaSugerida = generarContraseña(12);
     document.getElementById('password_cliente').value = contraseñaSugerida;
 }


 const togglePasswordCliente = document.querySelector("#togglePasswordCliente");
 const passwordFieldCliente = document.querySelector("#password_cliente");
 const eyeIconCliente = document.querySelector("#eyeIconCliente");

 togglePasswordCliente.addEventListener("click", function () {
     if (passwordFieldCliente.type === "password") {
         passwordFieldCliente.type = "text";
         eyeIconCliente.classList.remove("fa-eye");
         eyeIconCliente.classList.add("fa-eye-slash");
     } else {
         passwordFieldCliente.type = "password";
         eyeIconCliente.classList.remove("fa-eye-slash");
         eyeIconCliente.classList.add("fa-eye");
     }
 });
</script>

<div class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="nuevoModalLabelCliente" aria-hidden="true">
 <div class="modal-dialog modal-lg">
     <div class="modal-content">
         <div class="modal-header" style="background-color: #28a745; color: white;">
             <h5 class="modal-title" id="nuevoModalLabelCliente">Registro de Cliente</h5>
             <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
             <form id="formNuevoRegistroCliente" method="POST" action="clientes.php">
                 <div class="row g-3">
                     <div class="col-md-6">
                         <label class="form-label">Compañía</label>
                         <input type="text" class="form-control" name="compania" required>
                     </div>
                     
                     <div class="col-md-6">
                         <label class="form-label">Business Unit</label>
                         <input type="text" class="form-control" name="business_unit" required>
                     </div>
                    
                     <div class="col-md-6">
                         <label class="form-label">Nombre</label>
                         <input type="text" class="form-control" name="nombre" required>
                     </div>
                     
                     <div class="col-md-6">
                         <label class="form-label">Apellido</label>
                         <input type="text" class="form-control" name="apellido" required>
                     </div>
                    
                     <div class="col-md-6">
                         <label class="form-label">Correo Electrónico</label>
                         <input type="email" class="form-control" name="correo" required>
                     </div>
                    
                     <div class="col-md-6">
                         <label class="form-label">Teléfono</label>
                         <input type="tel" class="form-control" name="telefono" required>
                     </div>
                    
                     <div class="col-md-6">
                         <label class="form-label">Contraseña</label>
                         <div class="input-group">
                             <input type="password" class="form-control" name="password" id="password_cliente" required>
                             <span class="input-group-text" id="togglePasswordCliente">
                                 <i class="fas fa-eye" id="eyeIconCliente"></i>
                             </span>
                         </div>
                     </div>
                 
                     <div class="col-md-6">
                         <button type="button" class="btn btn-primary" onclick="sugerirContraseñaCliente()">
                             <i class="fas fa-key"></i> Sugerir Contraseña
                         </button>
                     </div>
                    
                     <div class="row mb-3">
                         <div class="col-12">
                             <h4>Permisos</h4>
                         </div>
                         <div class="col-md-4">
                             <div class="form-check">
                                 <input class="form-check-input" type="checkbox" name="permiso_ver" id="permiso_ver">
                                 <label class="form-check-label" for="permiso_ver">Permiso para ver</label>
                             </div>
                         </div>
                         <div class="col-md-4">
                             <div class="form-check">
                                 <input class="form-check-input" type="checkbox" name="permiso_editar" id="permiso_editar">
                                 <label class="form-check-label" for="permiso_editar">Permiso para editar</label>
                             </div>
                         </div>
                         <div class="col-md-4">
                             <div class="form-check">
                                 <input class="form-check-input" type="checkbox" name="permiso_capturar" id="permiso_capturar">
                                 <label class="form-check-label" for="permiso_capturar">Permiso para capturar</label>
                             </div>
                         </div>
                     </div>
                 </div>
                 <div class="modal-footer">
                   <button type="button" class="btn btn-secondary" onclick="cerrarModalCliente()">Cancelar</button>
                     <button type="submit" class="btn btn-primary" id="guardarBtnCliente" disabled>Guardar</button>
                 </div>
             </form>
         </div>
     </div>
 </div>
</div>
</div>
<script>
function cerrarModalCliente() {
    location.reload();  

   
}
</script>
<script>
    const form = document.getElementById('formNuevoRegistroCliente');
    const guardarBtn = document.getElementById('guardarBtnCliente');

    function checkFormCompletion() {
       
        const inputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], input[type="password"], select');
        let allFilled = true;

        inputs.forEach(input => {
           
            if (input.value.trim() === '') {
                allFilled = false;
            }
        });

     
        guardarBtn.disabled = !allFilled;

        console.log('Estado del botón de guardar: ', guardarBtn.disabled);
    }

 
    form.addEventListener('input', checkFormCompletion);
    form.addEventListener('change', checkFormCompletion);

    
    checkFormCompletion();
</script>



</div>

    <br><br><br><br><br>
    <footer class="content-footer footer" style="background-color:#edebea">
        <div class="container-xxl d-flex flex-wrap justify-content-center py-2 flex-md-row flex-column text-center"
            style="color:#838383;">
            <div class="mb-2 mb-md-0 fw-bolder">
                ©
                <script>
                    document.write(new Date().getFullYear());
                </script>
                , Comter |
                <a href="#" class="footer-link fw-bolder no-decoration" style="color: #6c757d;">Osdems Digital Group</a>
            </div>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmLogout() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Quieres cerrar la sesión?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../../admin/backend/home/logout.php';
                }
            });
        }
    </script>

   

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            const toggleBtn = document.getElementById('toggleSidebar');


            if (window.innerWidth >= 992) {
                toggleBtn.addEventListener('click', function () {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');


                    const isCollapsed = sidebar.classList.contains('collapsed');
                    localStorage.setItem('sidebarCollapsed', isCollapsed);
                });


                const savedState = localStorage.getItem('sidebarCollapsed');
                if (savedState === 'true') {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                }
            }
        });
    </script>

<script>

function generarContraseña(longitud) {
    const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
    let contraseña = '';
    for (let i = 0; i < longitud; i++) {
        const randomIndex = Math.floor(Math.random() * caracteres.length);
        contraseña += caracteres.charAt(randomIndex);
    }
    return contraseña;
}


function sugerirContraseña() {
    const contraseñaSugerida = generarContraseña(12);
    document.getElementById('password').value = contraseñaSugerida;
}


const togglePassword = document.querySelector("#togglePassword");
const passwordField = document.querySelector("#password");
const eyeIcon = document.querySelector("#eyeIcon");

togglePassword.addEventListener("click", function () {

    if (passwordField.type === "password") {
        passwordField.type = "text";
        eyeIcon.classList.remove("fa-eye");
        eyeIcon.classList.add("fa-eye-slash");
    } else {
        passwordField.type = "password";
        eyeIcon.classList.remove("fa-eye-slash");
        eyeIcon.classList.add("fa-eye");
    }
});
</script>

<script>
function mostrarSweetAlert() {
    Swal.fire({
        title: 'Seleccionar Rango de Fechas y Tipo de Exportación',
        html: `
            <label for="fechaInicio">Fecha Inicio:</label>
            <input type="date" id="fechaInicio" class="swal2-input">

            <label for="fechaFin">Fecha Fin:</label>
            <input type="date" id="fechaFin" class="swal2-input">

            <label for="tipoExportacion">Seleccionar Exportación:</label>
            <select id="tipoExportacion" class="swal2-select" style="width: 70%; padding: 8px;">
                <option value="" disabled selected>Seleccione una opción</option>
                <option value="../backend/exportar_excel_pcba.php">Exportar PCBA</option>
                <option value="../backend/exportar_excel_materiales.php">Exportar Materiales</option>
                <option value="../backend/exportar_excel_molex_42.php">Exportar Molex SEM 42</option>
                <option value="../backend/exportar_excel_molex_43.php">Exportar Molex SEM 43</option>
                <option value="../backend/exportar_excel_molex_44.php">Exportar Molex SEM 44</option>
                <option value="../backend/exportar_excel_molex_45.php">Exportar Molex SEM 45</option>
                <option value="../backend/exportar_excel_molex_46.php">Exportar Molex SEM 46</option>
                <option value="../backend/exportar_excel_molex_47.php">Exportar Molex SEM 47</option>
                <option value="../backend/exportar_excel_molex_48.php">Exportar Molex SEM 48</option>
                <option value="../backend/exportar_excel_molex_49.php">Exportar Molex SEM 49</option>
                <option value="../backend/exportar_excel_molex_50.php">Exportar Molex SEM 50</option>
                <option value="../backend/exportar_excel_molex_51.php">Exportar Molex SEM 51</option>
                <option value="../backend/exportar_excel_molex_52.php">Exportar Molex SEM 52</option>
            </select>
        `,
        showCancelButton: true,
        confirmButtonText: 'Exportar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            let fechaInicio = document.getElementById("fechaInicio").value;
            let fechaFin = document.getElementById("fechaFin").value;
            let tipoExportacion = document.getElementById("tipoExportacion").value;

            if (!fechaInicio || !fechaFin || !tipoExportacion) {
                Swal.showValidationMessage("Por favor, selecciona un rango de fechas y un tipo de exportación.");
                return false;
            }

            Swal.fire({
                title: 'Validando...',
                text: 'Por favor, espera mientras validamos los datos.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            return fetch(`${tipoExportacion}?fechaInicio=${encodeURIComponent(fechaInicio)}&fechaFin=${encodeURIComponent(fechaFin)}&validar=1`)
    .then(response => response.json())
    .then(data => {
        if (data.status === "error") {
            throw new Error(data.message);
        }

        Swal.close();
        window.location.href = `${tipoExportacion}?fechaInicio=${encodeURIComponent(fechaInicio)}&fechaFin=${encodeURIComponent(fechaFin)}`;
    })
    .catch(error => {
        Swal.fire({
            title: 'Error',
            text: error.message || "Ocurrió un error inesperado.",
            icon: 'error'
        });
    });

        }
    });
}

    </script>

</body>

</html>