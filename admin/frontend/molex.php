<?php
session_start();
require '../../config/connection.php';

if (!isset($_SESSION['id_usuarios'])) {
  echo 'No has iniciado sesión o tu sesión ha expirado.';
  header('Location: ../../login.php');
  exit();
}

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
    header('Location: ../../index.php');
    exit();
  }

  if ($role !== 'Proveedor') {
    echo 'No tienes permiso para acceder a esta página.';
    header('Location: ../../index.php');
    exit();
  }

  if ($photo !== null) {
    $base64Image = base64_encode($photo);
    $imageSrc = 'data:image/jpeg;base64,' . $base64Image;
  } else {
    $imageSrc = '../../assets/img/avatars/1.png';
  }
} else {
  echo 'Usuario no encontrado o cuenta no válida.';
  header('Location: ../../index.php');
  exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  try {

    $inspection_date = $_POST['inspection_date'];
    $operators = $_POST['operators'];
    $descripcion = $_POST['descripcion'];
    $goods = $_POST['goods'] ?? 0;
    $primer_t = $_POST['primer_t'] ?? 0;
    $segundo_t = $_POST['segundo_t'] ?? 0;
    $tercer_t = $_POST['tercer_t'] ?? 0;
    $coupler = $_POST['coupler'] ?? 0;
    $dano_end_face = $_POST['dano_end_face'] ?? 0;
    $golpe_top = $_POST['golpe_top'] ?? 0;
    $rebaba = $_POST['rebaba'] ?? 0;
    $dano_en_lente = $_POST['dano_en_lente'] ?? 0;
    $fuera_de_spc = $_POST['fuera_de_spc'] ?? 0;
    $dano_fisico = $_POST['dano_fisico'] ?? 0;
    $coupler_dano = $_POST['coupler_dano'] ?? 0;
    $hundimiento = $_POST['hundimiento'] ?? 0;
    $fisura = $_POST['fisura'] ?? 0;
    $silicon = $_POST['silicon'] ?? 0;
    $contaminacion = $_POST['contaminacion'] ?? 0;
    $total = $_POST['total'] ?? 0;
    $total_final = $_POST['total_final'] ?? 0;
    $comments = $_POST['comments'] ?? '';


    if (empty($inspection_date) || empty($operators) || empty($descripcion)) {
      $_SESSION['error_message'] = "Los campos 'Fecha de Inspección', 'Operadores' y 'Descripción' son obligatorios.";
    } else {

      $sql = "INSERT INTO inspection_data (
                      inspection_date, operators, descripcion, goods, primer_t, segundo_t, tercer_t, coupler, dano_end_face, golpe_top, 
                      rebaba, dano_en_lente, fuera_de_spc, dano_fisico, coupler_dano, hundimiento, 
                      fisura, silicon, contaminacion, total,comments, id_usuarios
                  ) VALUES (
                      :inspection_date, :operators, :descripcion, :goods, :primer_t, :segundo_t, :tercer_t, :coupler, :dano_end_face, :golpe_top, 
                      :rebaba, :dano_en_lente, :fuera_de_spc, :dano_fisico, :coupler_dano, :hundimiento, 
                      :fisura, :silicon, :contaminacion, :total, :comments, :id_usuarios
                  )";

      $stmt = $con->prepare($sql);
      $stmt->execute([
        ':inspection_date' => $inspection_date,
        ':operators' => $operators,
        ':descripcion' => $descripcion,
        ':goods' => $goods,
        ':primer_t' => $primer_t,
        ':segundo_t' => $segundo_t,
        ':tercer_t' => $tercer_t,
        ':coupler' => $coupler,
        ':dano_end_face' => $dano_end_face,
        ':golpe_top' => $golpe_top,
        ':rebaba' => $rebaba,
        ':dano_en_lente' => $dano_en_lente,
        ':fuera_de_spc' => $fuera_de_spc,
        ':dano_fisico' => $dano_fisico,
        ':coupler_dano' => $coupler_dano,
        ':hundimiento' => $hundimiento,
        ':fisura' => $fisura,
        ':silicon' => $silicon,
        ':contaminacion' => $contaminacion,
        ':total' => $total,

        ':comments' => $comments,
        ':id_usuarios' => $id_usuario
      ]);


      $_SESSION['message'] = "Datos insertados exitosamente.";
    }
  } catch (PDOException $e) {

    $_SESSION['error_message'] = "Error al insertar los datos: " . $e->getMessage();
  }
}


$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;


$sqlCount = "SELECT COUNT(*) FROM inspection_data";
$stmtCount = $con->query($sqlCount);
$totalRecords = $stmtCount->fetchColumn();


$totalPages = ceil($totalRecords / $limit);


$sql = "SELECT i.*, u.firstname, u.lastname, u.email 
      FROM inspection_data i
      JOIN usuarios u ON i.id_usuarios = u.id_usuarios
      LIMIT :limit OFFSET :offset";
$stmt = $con->prepare($sql);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
  <link rel="stylesheet" href="../../assets/vendor/fonts/boxicons.css" />
  <link rel="stylesheet" href="../../assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../../assets/css/demo.css" />
  <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../../assets/vendor/libs/apex-charts/apex-charts.css" />
  <script src="../../assets/vendor/js/helpers.js"></script>
  <script src="../../assets/js/config.js"></script>
  <link rel="shortcut icon" href="../ico/comter.png" type="image/x-icon">
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
  <title>Proveedor</title>
</head>

<body>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">


      <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <div class="app-brand demo">
          <a href="../admin_panel.php" class="app-brand-link">
            <span class="app-brand-logo demo">
              <img src="../ico/comter.png" alt="" width="50">
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
            <a href="../admin_panel.php" class="menu-link">
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
                <a href="infinex.php" class="menu-link">
                  <div data-i18n="Without menu">Infinex</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="molex.php" class="menu-link">
                  <div data-i18n="Without navbar">Molex SEM46</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="reporte.php" class="menu-link">
                  <div data-i18n="Container">Reporte</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="scc.php" class="menu-link">
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
                <form method="GET" action="search_molex.php" class="d-flex align-items-center">
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
          <div class="container mt-5">
            <h1 class="text-center">Fails Report</h1>
            <?php

            date_default_timezone_set('America/Mexico_City');


            $current_date = date('Y-m-d');
            ?>
            <form method="POST" class="mb-3">
              <div class="row g-2">
                <div class="col-md-2 d-flex flex-column">
                  <label for="inspection_date" class="form-label mb-1">Inspection Date</label>
                  <input type="date" name="inspection_date" class="form-control" required
                    value="<?php echo $current_date; ?>" id="inspection_date">
                </div>

                <div class="col-md-2 d-flex flex-column">
                  <label for="operators" class="form-label mb-1">Operators</label>
                  <input type="text" name="operators" class="form-control">
                </div>

                <div class="col-md-2 d-flex flex-column">
                  <label for="descripcion" class="form-label mb-1">Descripción</label>
                  <textarea name="descripcion" class="form-control"></textarea>
                </div>

                <div class="col-md-1 d-flex flex-column">
                  <label for="primer_t" class="form-label mb-1">1er T</label>
                  <input type="number" name="primer_t" class="form-control">
                </div>
                <div class="col-md-1 d-flex flex-column">
                  <label for="segundo_t" class="form-label mb-1">2do T</label>
                  <input type="number" name="segundo_t" class="form-control">
                </div>
                <div class="col-md-1 d-flex flex-column">
                  <label for="tercer_t" class="form-label mb-1">3er T</label>
                  <input type="number" name="tercer_t" class="form-control">
                </div>

                <div class="col-md-1 d-flex flex-column">
                  <label for="goods" class="form-label mb-1">Goods</label>
                  <input type="number" name="goods" class="form-control">
                </div>

                <div class="col-md-1 d-flex flex-column">
                  <label for="coupler" class="form-label mb-1">Coupler</label>
                  <input type="number" name="coupler" class="form-control">
                </div>
                <div class="col-md-1 d-flex flex-column">
                  <label for="dano_end_face" class="form-label mb-1">Daño End Face</label>
                  <input type="number" name="dano_end_face" class="form-control">
                </div>
                <div class="col-md-1 d-flex flex-column">
                  <label for="golpe_top" class="form-label mb-1">Golpe Top</label>
                  <input type="number" name="golpe_top" class="form-control">
                </div>
                <div class="col-md-1 d-flex flex-column">
                  <label for="rebaba" class="form-label mb-1">Rebaba</label>
                  <input type="number" name="rebaba" class="form-control">
                </div>
                <div class="col-md-1 d-flex flex-column">
                  <label for="dano_en_lente" class="form-label mb-1">Daño en Lente</label>
                  <input type="number" name="dano_en_lente" class="form-control">
                </div>
                <div class="col-md-1 d-flex flex-column">
                  <label for="fuera_de_spc" class="form-label mb-1">Fuera de SPC</label>
                  <input type="number" name="fuera_de_spc" class="form-control">
                </div>
                <div class="col-md-1 d-flex flex-column">
                  <label for="dano_fisico" class="form-label mb-1">Daño Fisico</label>
                  <input type="number" name="dano_fisico" class="form-control">
                </div>
                <div class="col-md-1 d-flex flex-column">
                  <label for="coupler_dano" class="form-label mb-1">Coupler Dañado</label>
                  <input type="number" name="coupler_dano" class="form-control">
                </div>
                <div class="col-md-1 d-flex flex-column">
                  <label for="hundimiento" class="form-label mb-1">Hundimiento</label>
                  <input type="number" name="hundimiento" class="form-control">
                </div>
                <div class="col-md-1 d-flex flex-column">
                  <label for="fisura" class="form-label mb-1">Fisura</label>
                  <input type="number" name="fisura" class="form-control">
                </div>
                <div class="col-md-1 d-flex flex-column">
                  <label for="silicon" class="form-label mb-1">Silicón</label>
                  <input type="number" name="silicon" class="form-control">
                </div>
                <div class="col-md-1 d-flex flex-column">
                  <label for="contaminacion" class="form-label mb-1">Contaminación</label>
                  <input type="number" name="contaminacion" class="form-control">
                </div>
                <div class="col-md-1 d-flex flex-column">
                  <label for="total" class="form-label mb-1">Total</label>
                  <input type="number" name="total" class="form-control">
                </div>

                <div class="col-md-12 mt-2">
                  <label for="comments" class="form-label mb-1">Comentarios</label>
                  <textarea name="comments" class="form-control"></textarea>
                </div>
              </div>
              <button type="submit" class="btn btn-primary mt-2"><i class="bx bx-plus"></i> Agregar Fila</button>
            </form>



            <div>
              <form action="../backend/exportar_excel.php" method="post">
                <button type="submit" class="btn btn-success" id="exportButton" disabled>
                  <i class="bx bxs-file"></i> Exportar a Excel
                </button>
              </form>
            </div>

            <div class="table-responsive mt-3">
              <?php
              if (isset($_SESSION['message'])) {
                echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
                unset($_SESSION['message']);
              }

              if (isset($_SESSION['error_message'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                unset($_SESSION['error_message']);
              }
              ?>

              <table class="table table-bordered" id="inspectionTable">
                <thead class="table-light">
                  <tr>
                    <th>Inspection Date</th>
                    <th>Operators</th>
                    <th>Descripción</th>
                    <th>1 er T</th>
                    <th>2 do T</th>
                    <th>3 er T</th>
                    <th>GOODS</th>
                    <th>Coupler</th>
                    <th>Daño End Face</th>
                    <th>Golpe Top</th>
                    <th>Rebaba</th>
                    <th>Daño en Lente</th>
                    <th>Fuera de SPC</th>
                    <th>Daño Fisico</th>
                    <th>Coupler Dañado</th>
                    <th>Hundimiento</th>
                    <th>Fisura</th>
                    <th>Silicón / Contaminación</th>
                    <th>Contaminación / End Face</th>
                    <th>Total</th>
                    <th>Total Final</th>
                    <th>Comments</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if (count($rows) > 0):
                    $previousDate = null;

                    foreach ($rows as $row):
                      $date = new DateTime($row['inspection_date']);
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
                      $dayInSpanish = $daysInSpanish[$dayOfWeek];

                      $total_final = (float) $row['goods'] + (float) $row['total'];

                      if ($previousDate !== null && $previousDate !== $formattedDate): ?>
                        <tr class="date-separator">
                          <td colspan="22" style="background-color: #f2f2f2; text-align: center; font-weight: bold;">
                            Cambio de fecha: <?= $formattedDate ?>
                          </td>
                        </tr>
                      <?php endif; ?>

                      <tr>
                        <td><?= $formattedDate . ' ' . $dayInSpanish ?></td>
                        <td><?= htmlspecialchars($row['operators']) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['descripcion'])) ?></td>
                        <td><?= htmlspecialchars($row['primer_t']) ?></td>
                        <td><?= htmlspecialchars($row['segundo_t']) ?></td>
                        <td><?= htmlspecialchars($row['tercer_t']) ?></td>
                        <td><?= htmlspecialchars($row['goods']) ?></td>
                        <td><?= htmlspecialchars($row['coupler']) ?></td>
                        <td><?= htmlspecialchars($row['dano_end_face']) ?></td>
                        <td><?= htmlspecialchars($row['golpe_top']) ?></td>
                        <td><?= htmlspecialchars($row['rebaba']) ?></td>
                        <td><?= htmlspecialchars($row['dano_en_lente']) ?></td>
                        <td><?= htmlspecialchars($row['fuera_de_spc']) ?></td>
                        <td><?= htmlspecialchars($row['dano_fisico']) ?></td>
                        <td><?= htmlspecialchars($row['coupler_dano']) ?></td>
                        <td><?= htmlspecialchars($row['hundimiento']) ?></td>
                        <td><?= htmlspecialchars($row['fisura']) ?></td>
                        <td><?= htmlspecialchars($row['silicon']) ?></td>
                        <td><?= htmlspecialchars($row['contaminacion']) ?></td>
                        <td><?= htmlspecialchars($row['total']) ?></td>
                        <td><?= htmlspecialchars($total_final) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['comments'])) ?></td>
                      </tr>
                      <?php
                      $previousDate = $formattedDate;
                    endforeach;
                  else: ?>
                    <tr>
                      <td colspan="22" class="text-center">No hay registros disponibles</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>


              <div class="pagination">
                <ul class="pagination">
                  <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">Anterior</a>
                  </li>
                  <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                      <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                  <?php endfor; ?>
                  <li class="page-item <?= $page == $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">Siguiente</a>
                  </li>
                </ul>
              </div>
            </div>


            <style>
              .date-separator td {
                background-color: #f2f2f2;
                text-align: center;
                font-weight: bold;
                color: #333;
              }
            </style>



            <script>
              document.addEventListener('DOMContentLoaded', function () {
                var table = document.getElementById('inspectionTable');
                var exportButton = document.getElementById('exportButton');


                var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
                var hasRecords = rows.length > 0 && rows[0].getElementsByTagName('td').length > 1;


                exportButton.disabled = !hasRecords;
              });
            </script>





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




    <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../assets/vendor/libs/popper/popper.js"></script>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../../assets/vendor/js/menu.js"></script>
    <script src="../../assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/dashboards-analytics.js"></script>
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
            window.location.href = '../backend/home/logout.php';
          }
        });
      }
    </script>
</body>

</html>