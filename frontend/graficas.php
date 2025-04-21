<?php
session_start();
require '../config/connection.php';

$id_usuario = $_SESSION['id_usuarios'];
$db = new Database();
$con = $db->conectar();

$sql = "SELECT nombre, apellido, role, verificado FROM usuarios WHERE id_usuarios = :id_usuario";
$stmt = $con->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $nombre = $row['nombre'];
    $apellido = $row['apellido'];
    $role = $row['role'];
    $verificado = $row['verificado'];

    if ((int) $verificado === 0) {
        echo 'Tu cuenta no está verificada. Por favor, contacta con el administrador.';
        header('Location: ../index.php');
        exit();
    }


    if ($role !== 'Cliente') {
        echo 'No tienes permiso para acceder a esta página.';
        header('Location: ../index.php');
        exit();
    }


    $imageSrc = '../assets/img/avatars/1.png';

} else {
    echo 'Usuario no encontrado o cuenta no válida.';
    header('Location: ../index.php');
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
  
 
  if (strpos($sql, ':start_date_molex') !== false) {
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
    <link rel="shortcut icon" href="../ico/comter.png" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="../css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="../css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="../css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="../css/style.css" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>


    <title>Comter</title>
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

</head>

<body>



    <header class="header fixed-top" style="background-color:#1B419B;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-6 col-md-2">
                    <div class="header__logo">
                        <a href="cliente_panel.php"><img src="../ico/comter.png" alt="" style="width:50px"></a>
                    </div>
                </div>
                <div class="col-6 col-md-10">
                    <div class="d-flex justify-content-end">

                        <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse"
                            data-bs-target="#mobileMenu" aria-controls="mobileMenu" aria-expanded="false"
                            aria-label="Toggle navigation">
                            <i class="fas fa-bars" style="color: white;"></i>
                        </button>
                    </div>


                    <div class="header__nav__option d-none d-md-block">
                        <nav class="header__nav__menu">
                            <ul>
                                <li>
                                    <select class="form-select transparent-select"
                                        onchange="window.location.href=this.value">
                                        <option value="#" selected disabled>Seleccione una opción</option>
                                        <option value="cliente_panel.php">PCBA</option>
                                        <option value="materiales.php">Materiales Acumulados o de Almacén</option>
                                        <option value="sem42.php">MOLEX SEM42</option>
                                        <option value="sem43.php">MOLEX SEM43</option>
                                        <option value="sem44.php">MOLEX SEM44</option>
                                        <option value="sem45.php">MOLEX SEM45</option>
                                        <option value="sem46.php">MOLEX SEM46</option>
                                        <option value="sem47.php">MOLEX SEM47</option>
                                        <option value="sem48.php">MOLEX SEM48</option>
                                        <option value="sem49.php">MOLEX SEM49</option>
                                        <option value="sem50.php">MOLEX SEM50</option>
                                        <option value="sem51.php">MOLEX SEM51</option>
                                        <option value="sem52.php">MOLEX SEM52</option>
                                    </select>
                                </li>
                                <li>
                                    <a href="graficas.php" class="d-flex align-items-center">
                                        <i class="fas fa-chart-pie"></i> &nbsp;&nbsp;Gráficas
                                    </a>
                                </li>

                                <li>
                                    <div class="d-flex align-items-center">
                                        <!--<button id="btnGraficar" class="btn btn-primary" onclick="drawChart()">Graficar</button>-->
                                        <span class="fw-semibold d-block ms-2"
                                            style="color:#fff; text-transform: uppercase;"><span
                                                style="text-transform: uppercase;">Bienvenido </span>
                                            <?php echo $role; ?></span>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </div>
                                </li>

                                <li>
                                    <a href="#">
                                        <div class="d-flex align-items-center">

                                            <img src="<?php echo $imageSrc; ?>" alt="" class="user-image" />
                                            <span
                                                class="fw-semibold d-block ms-2"><?php echo $nombre . ' ' . $apellido; ?></span>
                                        </div>
                                    </a>
                                    <ul class="dropdown">
                                        <li><a href="#"><small class="text-muted">Rol: <?php echo $role; ?></small></a>
                                        </li>
                                        <hr>
                                        <li><a href="#" onclick="confirmLogout()">Cerrar sesión</a></li>
                                    </ul>
                                </li>
                            </ul>

                        </nav>
                    </div>
                </div>
            </div>


            <div class="collapse navbar-collapse" id="mobileMenu">
                <div class="p-3 bg-white rounded shadow-sm mt-2">
                    <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                        <img src="<?php echo $imageSrc; ?>" alt="" class="user-image me-2" />
                        <div>
                            <span class="fw-semibold"><?php echo $nombre . ' ' . $apellido; ?></span>
                            <small class="d-block text-muted">Rol: <?php echo $role; ?></small>
                        </div>
                    </div>
                    <select class="form-select mb-3" onchange="window.location.href=this.value">
                        <option value="#" selected disabled>Seleccione una opción</option>
                        <option value="cliente_panel.php">PCBA</option>
                        <option value="materiales.php">Materiales Acumulados o de Almacén</option>
                        <option value="sem42.php">MOLEX SEM42</option>
                        <option value="sem43.php">MOLEX SEM43</option>
                        <option value="sem44.php">MOLEX SEM44</option>
                        <option value="sem45.php">MOLEX SEM45</option>
                        <option value="sem46.php">MOLEX SEM46</option>
                        <option value="sem47.php">MOLEX SEM47</option>
                        <option value="sem48.php">MOLEX SEM48</option>
                        <option value="sem49.php">MOLEX SEM49</option>
                        <option value="sem50.php">MOLEX SEM50</option>
                        <option value="sem51.php">MOLEX SEM51</option>
                        <option value="sem52.php">MOLEX SEM52</option>
                    </select>
                    <button onclick="confirmLogout()" class="btn btn-danger w-100">Cerrar sesión</button>
                </div>
            </div>
        </div>
    </header>
    <br><br><br><br><br><br>




    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;600;700;800&display=swap');

        html {
            height: 100%;
            background-color: #E5E5E6;
        }



        a {
            text-decoration: none;
            color: inherit;
        }

        * {
            box-sizing: border-box;
        }

        .user-image {
            width: 25px;
            height: 25px;
            border-radius: 50%;
        }


        .d-flex {
            display: flex;
            align-items: center;
        }


        .ms-2 {
            margin-left: 0.5rem;
        }

        body {
            background-color: #E5E5E6;
        }


        ::-webkit-scrollbar {
            width: 12px;
            height: 12px;
            transition: all 0.3s ease;
        }

        ::-webkit-scrollbar-thumb {
            background-color: rgb(27, 114, 155);
            border-radius: 10px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            margin: 4px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }


        ::-webkit-scrollbar-thumb:hover {
            background-color: rgb(27, 114, 155);
            transform: scale(1.2);
        }


        ::-webkit-scrollbar-track {
            background-color: #f1f1f1;
            transition: background-color 0.3s ease;
            border-radius: 10px;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1);
        }


        ::-webkit-scrollbar-track:hover {
            background-color: #e0e0e0;
        }


        html {
            scrollbar-width: thin;
            scrollbar-color: rgb(27, 114, 155)B #f1f1f1;
            transition: scrollbar-color 0.3s ease;
        }


        html:hover {
            scrollbar-color: rgb(27, 114, 155) #e0e0e0;
        }

        .transparent-select {
            background-color: transparent;
            border: none;
            color: #fff;
            box-shadow: none;
        }

        .transparent-select option {
            background-color: transparent;
            color: #000;
        }


        .transparent-select option:disabled {
            color: #fff;
        }


        @media (max-width: 767.98px) {
            .navbar-toggler {
                border: none;
                padding: 0;
                font-size: 1.5rem;
            }

            .navbar-toggler:focus {
                box-shadow: none;
            }

            #mobileMenu {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                z-index: 1000;
                padding: 0 15px;
            }

            .header {
                padding: 10px 0;
            }
        }


        @media (max-width: 767.98px) {
            .accordion-button {
                background-color: #f8f9fa;
                font-size: 0.9rem;
                padding: 0.75rem 1rem;
            }

            .accordion-button:not(.collapsed) {
                background-color: #e9ecef;
                color: #1B419B;
            }

            .accordion-body {
                padding: 1rem;
                background-color: #fff;
            }

            .form-label {
                font-size: 0.9rem;
                font-weight: 500;
                color: #666;
                margin-bottom: 0.3rem;
            }

            .form-control,
            .form-select {
                font-size: 0.9rem;
                margin-bottom: 0.5rem;
            }

            .mobile-card {
                transition: opacity 0.3s ease;
            }

            .mobile-card[style*="display: none"] {
                opacity: 0;
            }
        }

        @media (min-width: 768px) {
            #inspectionTable tbody tr {
                transition: opacity 0.3s ease, transform 0.3s ease;
            }

            #inspectionTable tbody tr[style*="display: none"] {
                opacity: 0;
                transform: translateY(-10px);
            }

            .table-responsive {
                transition: height 0.3s ease;
            }
        }


        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.2rem rgba(27, 65, 155, 0.25);
            border-color: #1B419B;
        }

        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(27, 65, 155, 0.5);
        }


        .accordion {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .accordion-item {
            border: none;
        }

        .accordion-button {
            border: none;
            font-weight: 500;
        }

        .accordion-button:not(.collapsed)::after {
            transform: rotate(-180deg);
            transition: transform 0.3s ease;
        }
    </style>


    <!--<div class="container">
        <div id="chart_div" style="width: 100%; height: 500px;"></div>
    </div>-->

    <style>
                    input.form-control,
                    select.form-select {
                        transition: all 0.3s ease-in-out;
                        border-radius: 5px;
                        padding: 5px;
                        border: 1px solid #ccc;
                    }


                    input.form-control:focus,
                    select.form-select:focus {
                        border-color: #6c757d;
                        box-shadow: 0 0 5px rgba(108, 117, 125, 0.5);
                    }


                    input.form-control:hover,
                    select.form-select:hover {
                        border-color: #007bff;
                        box-shadow: 0 0 8px rgba(0, 123, 255, 0.4);
                    }

                    button#resetFilters {
                        transition: background-color 0.3s ease-in-out, transform 0.2s ease;
                        border-radius: 5px;
                        padding: 8px 15px;
                    }

                    button#resetFilters:hover {
                        background-color: #dc3545;
                        transform: scale(1.05);
                    }

                    button#resetFilters:focus {
                        outline: none;
                    }


                    table.table-bordered thead {
                        background-color: #e9ecef;
                    }


                    table.table-bordered td.fw-bold.text-danger {
                        color: #ff4d4d;
                        font-weight: bold;
                    }



                    button#resetFilters {
                        background-color: #f44336;
                        color: white;
                        border: none;
                        font-size: 14px;
                        cursor: pointer;
                        padding: 10px 20px;
                        border-radius: 5px;
                        transition: background-color 0.3s ease, transform 0.3s ease;
                    }


                    button#resetFilters:hover {
                        background-color: #e53935;
                        transform: scale(1.05);
                    }


                    button#resetFilters:focus {
                        outline: none;
                        box-shadow: 0 0 10px rgba(244, 67, 54, 0.5);
                    }
                </style>


    <div class="content-wrapper">
        <div class="container mt-5">
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

          <div class="container mt-5">
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

          <div class="container mt-5">
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
        <br><br>
        <footer class="content-footer footer" style="background-color:#edebea">
            <div class="container-xxl d-flex flex-wrap justify-content-center py-2 flex-md-row flex-column text-center"
                style="color:#838383;">
                <div class="mb-2 mb-md-0 fw-bolder">
                    ©
                    <script>
                        document.write(new Date().getFullYear());
                    </script>
                    , Comter |
                    <a href="#" class="footer-link fw-bolder" style="color: #6c757d;">Osdems Digital Group</a>
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
                        window.location.href = '../admin/backend/home/logout.php';
                    }
                });
            }
        </script>
       

        <style>
            @media (max-width: 767.98px) {
                .mobile-card {
                    transition: opacity 0.3s ease, transform 0.3s ease;
                }

                #mobileResultsCount {
                    background-color: #f8f9fa;
                    padding: 0.5rem;
                    border-radius: 4px;
                    font-size: 0.9rem;
                }

                .alert {
                    transition: opacity 0.3s ease;
                }

                #noResultsMessage {
                    margin-top: 1rem;
                    margin-bottom: 1rem;
                }
            }
        </style>


</body>

</html>