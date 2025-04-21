<?php
session_start();
require '../config/connection.php';


if (!isset($_SESSION['id_proveedor'])) {
  echo 'La sesión no tiene el id_proveedor.';
  exit();
}


$id_proveedor = $_SESSION['id_proveedor'];


$db = new Database();
$con = $db->conectar();


$sql = "SELECT nombre, apellido, compania, business_unit, telefono, correo, role, puesto, created_at, updated_at
        FROM proveedores 
        WHERE id_proveedor = :id_proveedor";
$stmt = $con->prepare($sql);
$stmt->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
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
  $puesto = $row['puesto'];
  $created_at = $row['created_at'];
  $updated_at = $row['updated_at'];


  $photo = '../assets/img/avatars/1.png';

} else {
  echo 'Proveedor no encontrado o cuenta no válida.';
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
  $params = [':version' => $version];
  
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
    
    $params[':start_date_molex'] = $start_date_molex;
    $params[':end_date_molex'] = $end_date_molex;
  }


  $stmt = $con->prepare($sql);
  $stmt->execute($params);
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
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
  <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />
  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />
  <link rel="stylesheet" href="css/styles.css">

  <script src="../assets/vendor/js/helpers.js"></script>
  <script src="../assets/js/config.js"></script>
  <link rel="shortcut icon" href="ico/comter.png" type="image/x-icon">
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

  <title>Proveedor</title>
</head>

<body>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">


    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="admin_panel.php" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="ico/comter.png" alt="" width="50">
                <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                        <g id="Icon" transform="translate(27.000000, 15.000000)">
                            <g id="Mask" transform="translate(0.000000, 8.000000)">
                                <mask id="mask-2" fill="white">
                                    <use xlink:href="#path-1"></use>
                                </mask>
                                <use fill="#696cff" xlink:href="#path-1"></use>
                                <g id="Path-3" mask="url(#mask-2)">
                                    <use fill="#696cff" xlink:href="#path-3"></use>
                                    <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                                </g>
                                <g id="Path-4" mask="url(#mask-2)">
                                    <use fill="#696cff" xlink:href="#path-4"></use>
                                    <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                                </g>
                            </g>
                            <g id="Triangle" transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000)">
                                <use fill="#696cff" xlink:href="#path-5"></use>
                                <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                            </g>
                        </g>
                    </g>
                </g>
                </svg>
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">COMTER</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>
    
    <ul class="menu-inner py-1">
      
        <!--<li class="menu-item active">
            <a href="admin_panel.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>-->
        <li class="menu-item active">
    <a href="semanas.php" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar-week"></i>
        <div data-i18n="Analytics">Nuevo Reporte</div>
    </a>
</li>


        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Layouts">Reportes</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="frontend/molexpcba.php" class="menu-link">
                        <div data-i18n="Without menu">MOLEX PCBA - Material Acumulado o de Almacén</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="frontend/matsmt.php" class="menu-link">
                        <div data-i18n="Without menu">MATERIAL DE SMT (MAT.FRESCO)</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="frontend/molexsem42.php" class="menu-link">
                        <div data-i18n="Without navbar">Molex SEM42</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="frontend/molexsem43.php" class="menu-link">
                        <div data-i18n="Without navbar">Molex SEM43</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="frontend/molexsem44.php" class="menu-link">
                        <div data-i18n="Without navbar">Molex SEM44</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="frontend/molexsem45.php" class="menu-link">
                        <div data-i18n="Without navbar">Molex SEM45</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="frontend/molexsem46.php" class="menu-link">
                        <div data-i18n="Without navbar">Molex SEM46</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="frontend/molexsem47.php" class="menu-link">
                        <div data-i18n="Without navbar">Molex SEM47</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="frontend/molexsem48.php" class="menu-link">
                        <div data-i18n="Without navbar">Molex SEM48</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="frontend/molexsem49.php" class="menu-link">
                        <div data-i18n="Without navbar">Molex SEM49</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="frontend/molexsem50.php" class="menu-link">
                        <div data-i18n="Without navbar">Molex SEM50</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="frontend/molexsem51.php" class="menu-link">
                        <div data-i18n="Without navbar">Molex SEM51</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="frontend/molexsem52.php" class="menu-link">
                        <div data-i18n="Without navbar">Molex SEM52</div>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</aside>


<style>
    @media (min-width: 769px) { 
        .layout-page {
            margin-left: 250px;
            padding: 20px;
        }

        #layout-menu {
            position: fixed; 
            top: 0; 
            left: 0;
            width: 250px;
            height: 100%; 
            overflow-y: auto; 
            z-index: 1000; 
        }
    }

    @media (max-width: 768px) { 
        .layout-page {
            margin-left: 0;
            padding: 20px;
        }

        #layout-menu {
            position: relative; 
            width: 100%; 
            height: auto;
        }
    }
    .filter-buttons {
    display: flex; 
    justify-content: flex-start; 
    gap: 10px;
    margin-top: 20px; 
}

.apply-filter, .reset-filters {
    height: 40px;
    padding: 0 15px; 
    font-size: 16px; 
}


@media (max-width: 768px) {
    .filter-buttons {
        flex-direction: row; 
    }
}
</style>
      
      <div class="layout-page">

      <nav class="fixed-top layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <div class="navbar-nav align-items-center">
            <div class="nav-item d-flex align-items-center">
                <!--<form method="GET" action="frontend/search_molex_pcba.php" class="d-flex align-items-center">
                    <input type="text" name="search" class="form-control border-0 shadow-none"
                        placeholder="Buscar por Operador..." aria-label="Search..."
                        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
                    <button type="submit" class="btn border-0 bg-transparent p-0 ms-2">
                        <i class="bx bx-search fs-4 lh-0"></i>
                    </button>

                    <a href="frontend/molexpcba.php" class="btn border-0 bg-transparent p-0 ms-2">
                        <i class="bx bx-x fs-4 lh-0"></i>
                    </a>
                </form>-->
            </div>
        </div>

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <li class="fw-semibold d-block">BIENVENIDO COMTER </li> &nbsp;&nbsp;&nbsp;<span
                    class="fw-semibold d-block"><?php echo $nombre . ' ' . $apellido; ?></span>
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="<?php echo $photo; ?>" alt="" class="w-px-40 h-auto rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="<?php echo $photo; ?>" alt="" class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <!--<span class="fw-semibold d-block"><?php echo $nombre . ' ' . $apellido; ?></span>-->
                                    <small class="text-muted">Rol: <br> <?php echo $role; ?></small><br>
                                    <small class="text-muted">Puesto:<br> <?php echo $puesto; ?></small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>

                    <li>
                        <a class="dropdown-item" href="#" onclick="confirmLogout()">
                            <i class="bx bx-power-off me-2"></i>
                            <span class="align-middle">Cerrar sesión</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

        <div class="content-wrapper">


          <!--<div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
              <div class="col-12 mb-4">
                <div class="card">
                  <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                      <div class="card-body">
                        <h5 class="card-title text-primary">Bienvenido <?php echo $nombre . ' ' . $apellido; ?>! 🎉
                        </h5>
                      </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                      <div class="card-body pb-0 px-0 px-md-4">
                        <img src="../assets/img/illustrations/man-with-laptop-light.png" height="140"
                          alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png"
                          data-app-light-img="illustrations/man-with-laptop-light.png" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>-->


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
              <div class="filter-buttons">
                <button class="btn btn-primary" onclick="filterData()"> <i class="bx bx-filter-alt"></i> Aplicar
                  Filtro</button>
                <button class="btn btn-secondary ms-2" onclick="resetFilters()"><i class="bx bx-reset"></i> Reset
                  Filters</button>
              </div>
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
              <div class="filter-buttons">
                <button class="btn btn-primary" onclick="filterDataMateriales()"> <i class="bx bx-filter-alt"></i>
                  Aplicar Filtro</button>
                <button class="btn btn-secondary ms-2" onclick="resetFiltersMateriales()"><i class="bx bx-reset"></i>
                  Reset Filters</button>
              </div>
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
              <div class="filter-buttons">
                <button class="btn btn-primary" onclick="filterDataInspecciones('sem42')"> <i
                    class="bx bx-filter-alt"></i> Aplicar Filtro</button>
                <button class="btn btn-secondary ms-2" onclick="resetFiltersInspecciones('sem42')"><i
                    class="bx bx-reset"></i> Reset Filters</button>
                    </div>
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
              <div class="filter-buttons">
                <button class="btn btn-primary" onclick="filterDataInspecciones('sem43')"> <i
                    class="bx bx-filter-alt"></i> Aplicar Filtro</button>
                <button class="btn btn-secondary ms-2" onclick="resetFiltersInspecciones('sem43')"><i
                    class="bx bx-reset"></i> Reset Filters</button>
              </div>
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
              <div class="filter-buttons">
                <button class="btn btn-primary" onclick="filterDataInspecciones('sem44')"> <i
                    class="bx bx-filter-alt"></i> Aplicar Filtro</button>
                <button class="btn btn-secondary ms-2" onclick="resetFiltersInspecciones('sem44')"><i
                    class="bx bx-reset"></i> Reset Filters</button>
                    </div>
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
              <div class="filter-buttons">
                <button class="btn btn-primary" onclick="filterDataInspecciones('sem45')"> <i
                    class="bx bx-filter-alt"></i> Aplicar Filtro</button>
                <button class="btn btn-secondary ms-2" onclick="resetFiltersInspecciones('sem45')"><i
                    class="bx bx-reset"></i> Reset Filters</button>
              </div>
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
              <div class="filter-buttons">
                <button class="btn btn-primary" onclick="filterDataInspecciones('sem46')"> <i
                    class="bx bx-filter-alt"></i> Aplicar Filtro</button>
                <button class="btn btn-secondary ms-2" onclick="resetFiltersInspecciones('sem46')"><i
                    class="bx bx-reset"></i> Reset Filters</button>
                    </div>
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
              <div class="filter-buttons">
                <button class="btn btn-primary" onclick="filterDataInspecciones('sem47')"> <i
                    class="bx bx-filter-alt"></i> Aplicar Filtro</button>
                <button class="btn btn-secondary ms-2" onclick="resetFiltersInspecciones('sem47')"><i
                    class="bx bx-reset"></i> Reset Filters</button>
                    </div>
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
              <div class="filter-buttons">
                <button class="btn btn-primary" onclick="filterDataInspecciones('sem48')"> <i
                    class="bx bx-filter-alt"></i> Aplicar Filtro</button>
                <button class="btn btn-secondary ms-2" onclick="resetFiltersInspecciones('sem48')"><i
                    class="bx bx-reset"></i> Reset Filters</button>
                    </div>
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
              <div class="filter-buttons">
                <button class="btn btn-primary" onclick="filterDataMolex('sem49')"> <i class="bx bx-filter-alt"></i>
                  Aplicar Filtro</button>
                <button class="btn btn-secondary ms-2" onclick="resetFiltersMolex('sem49')"><i class="bx bx-reset"></i>
                  Reset Filters</button>
                  </div>
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
              <div class="filter-buttons">
                <button class="btn btn-primary" onclick="filterDataMolex('sem50')"> <i class="bx bx-filter-alt"></i>
                  Aplicar Filtro</button>
                <button class="btn btn-secondary ms-2" onclick="resetFiltersMolex('sem50')"><i class="bx bx-reset"></i>
                  Reset Filters</button>
                  </div>
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
              <div class="filter-buttons">
                <button class="btn btn-primary" onclick="filterDataMolex('sem51')"> <i class="bx bx-filter-alt"></i>
                  Aplicar Filtro</button>
                <button class="btn btn-secondary ms-2" onclick="resetFiltersMolex('sem51')"><i class="bx bx-reset"></i>
                  Reset Filters</button>
                  </div>
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
              <div class="filter-buttons">
                <button class="btn btn-primary" onclick="filterDataMolex('sem52')"> <i class="bx bx-filter-alt"></i>
                  Aplicar Filtro</button>
                <button class="btn btn-secondary ms-2" onclick="resetFiltersMolex('sem52')"><i class="bx bx-reset"></i>
                  Reset Filters</button>
                  </div>
              </div>
            </div>
            <div id="chart_div_molex_sem52" style="width: 100%; height: 450px; margin-top: 60px;"></div>
          </div>

          <footer class="content-footer footer bg-footer-theme">
            <div class="container-xxl d-flex flex-column flex-md-row justify-content-center py-2">
              <div class="mb-2 mb-md-0 text-center text-md-start">
                &copy;
                <script>
                  document.write(new Date().getFullYear());
                </script>
                , Comter
                <a href="#" class="footer-link fw-bolder">Osdems Digital Group</a>
              </div>
            </div>
          </footer>


          <div class="content-backdrop fade"></div>
        </div>

      </div>

    </div>


    <div class="layout-overlay layout-menu-toggle"></div>
  </div>


  <script src="../assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../assets/vendor/libs/popper/popper.js"></script>
  <script src="../assets/vendor/js/bootstrap.js"></script>
  <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../assets/vendor/js/menu.js"></script>
  <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>
  <script src="../assets/js/main.js"></script>
  <!--<script src="../assets/js/dashboards-analytics.js"></script>-->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

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
          window.location.href = 'backend/home/logout.php';
        }
      });
    }
  </script>


</body>

</html>