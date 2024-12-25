<?php
session_start();
require '../config/connection.php';

$id_usuario = $_SESSION['id_usuarios'];
$db = new Database();
$con = $db->conectar();

$sql = "SELECT firstname, lastname, role, photo, active FROM usuarios WHERE id_usuarios = :id_usuario";
$stmt = $con->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $firstname = $row['firstname'];
  $lastname = $row['lastname'];
  $role = $row['role'];
  $photo = $row['photo'];
  $active = $row['active'];

  if ((int) $active === 0) {
    echo 'Tu cuenta ha sido desactivada. Por favor, contacta con el administrador.';
    header('Location: ../index.php');
    exit();
  }

  if ($role !== 'Proveedor') {
    echo 'No tienes permiso para acceder a esta página.';
    header('Location: ../index.php');
    exit();
  }


  if ($photo !== null) {
    $base64Image = base64_encode($photo);
    $imageSrc = 'data:image/jpeg;base64,' . $base64Image;
  } else {
    $imageSrc = '../assets/img/avatars/1.png';
  }
} else {
  echo 'Usuario no encontrado o cuenta no válida.';
  header('Location: ../index.php');
  exit();
}



$sql = "SELECT * FROM wire_failures";
$stmt = $con->prepare($sql);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

$wireFailuresData = [];
$totalGeneral = 0;

foreach ($records as $record) {
  $a = isset($record['a']) && $record['a'] !== '' ? (int) $record['a'] : 0;
  $b = isset($record['b']) && $record['b'] !== '' ? (int) $record['b'] : 0;
  $c = isset($record['c']) && $record['c'] !== '' ? (int) $record['c'] : 0;
  $a_and_b = isset($record['a_and_b']) && $record['a_and_b'] !== '' ? (int) $record['a_and_b'] : 0;
  $goods = isset($record['goods']) && $record['goods'] !== '' ? (int) $record['goods'] : 0;
  $total = $a + $b + $c + $a_and_b + $goods;

  $totalGeneral += $total;
  $wireFailuresData[] = [
    'box' => $record['box'],
    'a' => $a,
    'b' => $b,
    'c' => $c,
    'a_and_b' => $a_and_b,
    'goods' => $goods,
    'total' => $total,
  ];
}

$jsonData = json_encode($wireFailuresData);




$sql = "SELECT * FROM reporte";
$stmt = $con->prepare($sql);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

$reporteData = [];

foreach ($records as $record) {
  $reporteData[] = [
    'folio_captura' => $record['folio_captura'],
    'folio_requisicion' => $record['folio_requisicion'],
    'cliente_fabricante' => $record['cliente_fabricante'],
    'fecha_reporte' => $record['fecha_reporte'],
    'caja' => $record['caja'],
    'po_skid' => $record['po_skid'],
    'num_parte' => $record['num_parte'],
    'date_code' => $record['date_code'],
    'nombre_operador' => $record['nombre_operador'],
    'horario' => $record['horario'],
    'productividad_a' => $record['productividad_a'],
    'productividad_b' => $record['productividad_b'],
    'total_inspeccionadas' => $record['total_inspeccionadas'],
    'total_defectos' => $record['total_defectos'],
    'buenas' => $record['buenas'],
  ];
}

$jsonData = json_encode($reporteData);




$sql = "SELECT * FROM report_fails";
$stmt = $con->prepare($sql);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

$reportFailsData = [];

foreach ($records as $record) {
  $reportFailsData[] = [
    'inspection_date' => isset($record['inspection_date']) ? $record['inspection_date'] : '',
    'operators' => isset($record['operators']) ? $record['operators'] : '',
    'descripcion' => isset($record['descripcion']) ? $record['descripcion'] : '',
    'primer_t' => isset($record['primer_t']) ? $record['primer_t'] : 0,
    'segundo_t' => isset($record['segundo_t']) ? $record['segundo_t'] : 0,
    'tercer_t' => isset($record['tercer_t']) ? $record['tercer_t'] : 0,
    'comments' => isset($record['comments']) ? $record['comments'] : '',
    'burr' => isset($record['burr']) ? $record['burr'] : 0,
    'blockend_hole' => isset($record['blockend_hole']) ? $record['blockend_hole'] : 0,
    'non_flat_edge' => isset($record['non_flat_edge']) ? $record['non_flat_edge'] : 0
  ];
}

$jsonData = json_encode($reportFailsData);








$sql = "SELECT * FROM inspection_data";
$stmt = $con->prepare($sql);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

$inspectionData = [];

foreach ($records as $record) {
  $inspectionData[] = [
    'goods' => isset($record['goods']) ? $record['goods'] : 0,
    'primer_t' => isset($record['primer_t']) ? $record['primer_t'] : 0,
    'segundo_t' => isset($record['segundo_t']) ? $record['segundo_t'] : 0,
    'tercer_t' => isset($record['tercer_t']) ? $record['tercer_t'] : 0,
    'coupler' => isset($record['coupler']) ? $record['coupler'] : 0,
    'dano_end_face' => isset($record['dano_end_face']) ? $record['dano_end_face'] : 0,
    'golpe_top' => isset($record['golpe_top']) ? $record['golpe_top'] : 0,
    'rebaba' => isset($record['rebaba']) ? $record['rebaba'] : 0,
    'dano_en_lente' => isset($record['dano_en_lente']) ? $record['dano_en_lente'] : 0,
    'fuera_de_spc' => isset($record['fuera_de_spc']) ? $record['fuera_de_spc'] : 0,
    'dano_fisico' => isset($record['dano_fisico']) ? $record['dano_fisico'] : 0,
    'coupler_dano' => isset($record['coupler_dano']) ? $record['coupler_dano'] : 0,
    'hundimiento' => isset($record['hundimiento']) ? $record['hundimiento'] : 0,
    'fisura' => isset($record['fisura']) ? $record['fisura'] : 0,
    'silicon' => isset($record['silicon']) ? $record['silicon'] : 0,
    'contaminacion' => isset($record['contaminacion']) ? $record['contaminacion'] : 0,
    'total' => isset($record['total']) ? $record['total'] : 0
  ];
}

$jsonData = json_encode($inspectionData);
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
  <style>
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
  </style>
  <script src="../assets/vendor/js/helpers.js"></script>
  <script src="../assets/js/config.js"></script>
  <link rel="shortcut icon" href="ico/comter.png" type="image/x-icon">
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

  <script type="text/javascript">
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

      var rawData = <?php echo json_encode($wireFailuresData); ?>;


      if (!rawData.length) {
        document.getElementById('chart_div').innerHTML = '<p>No hay datos disponibles para graficar.</p>';
        return;
      }

      var data = google.visualization.arrayToDataTable([
        ['Box', 'A', 'B', 'C', 'A & B', 'Goods', 'Total'],
        <?php
        foreach ($wireFailuresData as $row) {
          echo "['" . $row['box'] . "', " . $row['a'] . ", " . $row['b'] . ", " . $row['c'] . ", " . $row['a_and_b'] . ", " . $row['goods'] . ", " . $row['total'] . "],";
        }
        ?>
      ]);

      data.addRow(['Total General', null, null, null, null, null, <?= $totalGeneral ?>]);

      var options = {
        title: 'INFINEX',
        hAxis: { title: 'Box' },
        vAxis: { title: 'Count' },
        legend: { position: 'top' }
      };

      var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));

      chart.draw(data, options);
    }
  </script>


  <script type="text/javascript">
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

      var rawData = <?php echo $jsonData; ?>;


      if (!rawData.length) {
        document.getElementById('chart_div_molex').innerHTML = '<p>No hay datos disponibles para graficar.</p>';
        return;
      }

      var data = google.visualization.arrayToDataTable([
        ['Campo', 'Total'],
        ['Folio Captura', <?= count($reporteData) ?>],
        ['Folio Requisicion', <?= count($reporteData) ?>],
        ['Cliente/Fabricante', <?= count(array_unique(array_column($reporteData, 'cliente_fabricante'))) ?>],
        ['Fecha Reporte', <?= count(array_unique(array_column($reporteData, 'fecha_reporte'))) ?>],
        ['Caja', <?= count(array_unique(array_column($reporteData, 'caja'))) ?>],
        ['PO/SKID', <?= count(array_unique(array_column($reporteData, 'po_skid'))) ?>],
        ['Número de Parte', <?= count(array_unique(array_column($reporteData, 'num_parte'))) ?>],
        ['Date Code', <?= count(array_unique(array_column($reporteData, 'date_code'))) ?>],
        ['Nombre del Operador', <?= count(array_unique(array_column($reporteData, 'nombre_operador'))) ?>],
        ['Horario', <?= count(array_unique(array_column($reporteData, 'horario'))) ?>],
        ['Productividad A', <?= array_sum(array_column($reporteData, 'productividad_a')) ?>],
        ['Productividad B', <?= array_sum(array_column($reporteData, 'productividad_b')) ?>],
        ['Total Inspeccionadas', <?= array_sum(array_column($reporteData, 'total_inspeccionadas')) ?>],
        ['Total Defectos', <?= array_sum(array_column($reporteData, 'total_defectos')) ?>],
        ['Buenas', <?= array_sum(array_column($reporteData, 'buenas')) ?>]
      ]);

      var options = {
        title: 'MOLEX SEM46',
        pieHole: 0.4,
        legend: { position: 'top' }
      };

      var chart = new google.visualization.PieChart(document.getElementById('chart_div_molex'));

      chart.draw(data, options);
    }
  </script>


  <script type="text/javascript">
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

      var rawData = [
        ['Campo', 'Total'],
        ['Primer T', <?= array_sum(array_column($reportFailsData, 'primer_t')) ?>],
        ['Segundo T', <?= array_sum(array_column($reportFailsData, 'segundo_t')) ?>],
        ['Tercer T', <?= array_sum(array_column($reportFailsData, 'tercer_t')) ?>],
        ['Burr', <?= array_sum(array_column($reportFailsData, 'burr')) ?>],
        ['Blockend Hole', <?= array_sum(array_column($reportFailsData, 'blockend_hole')) ?>],
        ['Non Flat Edge', <?= array_sum(array_column($reportFailsData, 'non_flat_edge')) ?>]
      ];


      if (rawData.length <= 1) {
        document.getElementById('chart_div_report_fails').innerHTML = '<p>No hay datos disponibles para graficar.</p>';
        return;
      }

      var data = google.visualization.arrayToDataTable(rawData);

      var options = {
        title: 'Reporte de Fallas',
        pieHole: 0.4,
        legend: { position: 'top' }
      };

      var chart = new google.visualization.PieChart(document.getElementById('chart_div_report_fails'));

      chart.draw(data, options);
    }
  </script>

  <script type="text/javascript">
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      var rawData = <?php echo json_encode($inspectionData); ?>;

      if (!rawData.length) {
        document.getElementById('chart_div_inspection_data').innerHTML = '<p>No hay datos disponibles para graficar.</p>';
        return;
      }

      var data = google.visualization.arrayToDataTable([
        ['Element', 'Goods', 'Primer T', 'Segundo T', 'Tercer T', 'Coupler', 'Daño End Face', 'Golpe Top', 'Rebaba', 'Daño En Lente', 'Fuera De SPC', 'Daño Físico', 'Coupler Daño', 'Hundimiento', 'Fisura', 'Silicón', 'Contaminación', 'Total'],
        <?php
        foreach ($inspectionData as $row) {
          echo "['', " . implode(", ", $row) . "],";
        }
        ?>
      ]);

      var options = {
        title: 'Inspección de Datos',
        hAxis: {
          title: 'Elementos',
          format: 'string',
        },
        vAxis: {
          title: 'Cantidad'
        },
        legend: { position: 'top', maxLines: 3 },
        bar: { groupWidth: '100%' },
        isStacked: false,
        chartArea: { width: '90%', height: '70%' },
        colors: ['#1b9e77', '#d95f02', '#7570b3', '#e7298a', '#66a61e', '#e6ab02', '#a6761d', '#666666']
      };

      var chart = new google.visualization.ColumnChart(document.getElementById('chart_div_inspection_data'));

      chart.draw(data, options);
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
                    <g id="Triangle"
                      transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) ">
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

          <li class="menu-item active">
            <a href="admin_panel.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-home-circle"></i>
              <div data-i18n="Analytics">Dashboard</div>
            </a>
          </li>


          <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons bx bx-layout"></i>
              <div data-i18n="Layouts">Reportes</div>
            </a>

            <ul class="menu-sub">
              <li class="menu-item">
                <a href="frontend/infinex.php" class="menu-link">
                  <div data-i18n="Without menu">Infinex</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="frontend/molex.php" class="menu-link">
                  <div data-i18n="Without navbar">Molex SEM46</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="frontend/reporte.php" class="menu-link">
                  <div data-i18n="Container">Reporte</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="frontend/scc.php" class="menu-link">
                  <div data-i18n="Fluid">SCC</div>
                </a>
              </li>
              <!--<li class="menu-item">
                <a href="layouts-blank.html" class="menu-link">
                  <div data-i18n="Blank">Blank</div>
                </a>
              </li>-->
            </ul>
          </li>


      </aside>

      <div class="layout-page">

        <nav
          class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
          id="layout-navbar">
          <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
              <i class="bx bx-menu bx-sm"></i>
            </a>
          </div>

          <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <div class="navbar-nav align-items-center">
              <div class="nav-item d-flex align-items-center">
                <form method="GET" action="frontend/search_molex.php" class="d-flex align-items-center">
                  <input type="text" name="search" class="form-control border-0 shadow-none"
                    placeholder="Buscar por Operador..." aria-label="Search..."
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
                  <button type="submit" class="btn border-0 bg-transparent p-0 ms-2">
                    <i class="bx bx-search fs-4 lh-0"></i>
                  </button>
                </form>
              </div>
            </div>
            <ul class="navbar-nav flex-row align-items-center ms-auto">




              <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                  <div class="avatar avatar-online">
                    <img src="<?php echo $imageSrc; ?>" alt="" class="w-px-40 h-auto rounded-circle" />
                  </div>



                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item" href="#">
                      <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                          <div class="avatar avatar-online">
                            <img src="<?php echo $imageSrc; ?>" alt="" class="w-px-40 h-auto rounded-circle" />
                          </div>
                        </div>
                        <div class="flex-grow-1">
                          <span class="fw-semibold d-block"><?php echo $firstname . ' ' . $lastname; ?></span>
                          <small class="text-muted"><?php echo $role; ?></small>
                        </div>


                      </div>
                    </a>
                  </li>
                  <li>
                    <div class="dropdown-divider"></div>
                  </li>
                  <!--<li>
                    <a class="dropdown-item" href="#">
                      <i class="bx bx-user me-2"></i>
                      <span class="align-middle">My Profile</span>
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="#">
                      <i class="bx bx-cog me-2"></i>
                      <span class="align-middle">Settings</span>
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="#">
                      <span class="d-flex align-items-center align-middle">
                        <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
                        <span class="flex-grow-1 align-middle">Billing</span>
                        <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
                      </span>
                    </a>
                  </li>
                  <li>
                    <div class="dropdown-divider"></div>
                  </li>-->
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


          <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
              <div class="col-12 mb-4">
                <div class="card">
                  <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                      <div class="card-body">
                        <h5 class="card-title text-primary">Bienvenido <?php echo $firstname . ' ' . $lastname; ?>! 🎉
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
          </div>



          <div class="container">
            <div id="chart_div" style="width: 100%; height: 500px;"></div>
          </div>


          <div class="container">

            <hr>
          </div>


          <div class="container">
            <div id="chart_div_molex" style="width: 100%; height: 500px;"></div>

          </div>

          <div class="container">

            <hr>
          </div>

          <div class="container">
            <div id="chart_div_report_fails" style="width: 100%; height: 500px;"></div>
          </div>


          <div class="container">

            <hr>
          </div>



          <div class="container">
            <div id="chart_div_inspection_data" style="width: 100%; height: 500px;"></div>
          </div>



          <footer class="content-footer footer bg-footer-theme">
    <div class="container-xxl d-flex flex-column flex-md-row justify-content-center py-2">
        <div class="mb-2 mb-md-0 text-center text-md-start">
            ©
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