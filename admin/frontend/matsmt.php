<?php
session_start();
require '../../config/connection.php';
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

  $photo = '../../assets/img/avatars/1.png';
} else {
  echo 'Proveedor no encontrado o cuenta no válida.';
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_SESSION['id_proveedor'])) {
    $_SESSION['error_message_smt'] = 'El proveedor no está disponible. Asegúrate de estar logueado.';
    header('Location: matsmt.php');
    exit();
  }
  $id_proveedor = $_SESSION['id_proveedor'];
  $inspection_date = $_POST['inspection_date'];
  $description = isset($_POST['description']) ? $_POST['description'] : '';

  $shift = $_POST['shift'];
  $operators = $_POST['operators'];


  $goods = isset($_POST['goods']) && !empty($_POST['goods']) ? $_POST['goods'] : 0;

  $fails_dedos_oro_contaminados = $_POST['dedos_de_oro_contaminados'];
  $fails_faltante = $_POST['faltante'];
  $fails_desplazados = $_POST['desplazados'];
  $fails_insuficiencias = $_POST['insuficiencias'];
  $fails_desprendidos = $_POST['desprendidos'];
  $fails_despanelizados = isset($_POST['despanelizados']) ? (int) $_POST['despanelizados'] : 0;

  $total_fails = $fails_dedos_oro_contaminados + $fails_faltante + $fails_despanelizados + $fails_desplazados + $fails_insuficiencias + $fails_desprendidos;

  $total_final = $goods + $total_fails;

  if ($total_final > 0) {
    $yield = round(($goods / $total_final) * 100, 2);
  } else {
    $yield = 0;
  }

  $total = $_POST['total'];
  $comments = $_POST['comments'];


  $description_images = [];
  if (isset($_FILES['description_image']) && $_FILES['description_image']['error'][0] === UPLOAD_ERR_OK) {
    foreach ($_FILES['description_image']['tmp_name'] as $key => $tmp_name) {
      $image_data = file_get_contents($tmp_name);
      $description_images[] = $image_data;
    }
  }


  $comments_image = null;
  if (isset($_FILES['comments_image']) && $_FILES['comments_image']['error'] === UPLOAD_ERR_OK) {
    $comments_image = file_get_contents($_FILES['comments_image']['tmp_name']);
  }


  $missing_fields = [];
  if (empty($inspection_date)) {
    $missing_fields[] = 'Inspection Date';
  }
  if (empty($shift)) {
    $missing_fields[] = 'Shift';
  }
  if (empty($operators)) {
    $missing_fields[] = 'Operators';
  }

  if (!empty($missing_fields)) {
    $fields = implode(', ', $missing_fields);
    $_SESSION['error_message_smt'] = 'Por favor, rellene los siguientes campos obligatorios: ' . $fields . '.';
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
  }

  $sqlInsert = "INSERT INTO materiales 
    (inspection_date, descripcion, shift, operators, goods, dedos_de_oro_contaminados, faltante, desplazados, insuficiencias, despanelizados, desprendidos, total, yield, total_final, comments, id_proveedor, descripcion_image, comments_image) 
    VALUES 
    (:inspection_date, :description, :shift, :operators, :goods, :fails_dedos_oro_contaminados, :fails_faltante, :fails_desplazados, :fails_insuficiencias, :fails_despanelizados, :fails_desprendidos, :total_fails, :yield, :total_final, :comments, :id_proveedor, :descripcion_image, :comments_image)";


  $stmtInsert = $con->prepare($sqlInsert);


  $stmtInsert->bindParam(':inspection_date', $inspection_date, PDO::PARAM_STR);
  $stmtInsert->bindParam(':description', $description, PDO::PARAM_STR);
  $stmtInsert->bindParam(':shift', $shift, PDO::PARAM_STR);
  $stmtInsert->bindParam(':operators', $operators, PDO::PARAM_STR);
  $stmtInsert->bindParam(':goods', $goods, PDO::PARAM_INT);
  $stmtInsert->bindParam(':fails_dedos_oro_contaminados', $fails_dedos_oro_contaminados, PDO::PARAM_INT);
  $stmtInsert->bindParam(':fails_faltante', $fails_faltante, PDO::PARAM_INT);
  $stmtInsert->bindParam(':fails_desplazados', $fails_desplazados, PDO::PARAM_INT);
  $stmtInsert->bindParam(':fails_insuficiencias', $fails_insuficiencias, PDO::PARAM_INT);
  $stmtInsert->bindParam(':fails_despanelizados', $fails_despanelizados, PDO::PARAM_INT);
  $stmtInsert->bindParam(':fails_desprendidos', $fails_desprendidos, PDO::PARAM_INT);
  $stmtInsert->bindParam(':total_fails', $total_fails, PDO::PARAM_INT);
  $stmtInsert->bindParam(':total_final', $total_final, PDO::PARAM_INT);
  $stmtInsert->bindParam(':yield', $yield, PDO::PARAM_STR);
  $stmtInsert->bindParam(':comments', $comments, PDO::PARAM_STR);
  $stmtInsert->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);


  $descripcion_image_value = empty($description_images) ? 'N/A' : $description_images[0];
  $stmtInsert->bindParam(':descripcion_image', $descripcion_image_value, PDO::PARAM_STR);

  $comments_image_value = $comments_image === null ? 'N/A' : $comments_image;
  $stmtInsert->bindParam(':comments_image', $comments_image_value, PDO::PARAM_STR);


  $stmtInsert->execute();

  $_SESSION['success_message_smt'] = 'Registro guardado exitosamente.';
  header('Location: ' . $_SERVER['PHP_SELF']);
  exit();

}

$records_per_page = 100;
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

$total_records_sql = "SELECT COUNT(*) FROM materiales WHERE id_proveedor = :id_proveedor";
$total_records_stmt = $con->prepare($total_records_sql);
$total_records_stmt->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
$total_records_stmt->execute();
$total_records = $total_records_stmt->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);

$sql_sum_goods = "SELECT SUM(goods) AS total_goods FROM materiales WHERE id_proveedor = :id_proveedor";
$stmt_sum_goods = $con->prepare($sql_sum_goods);
$stmt_sum_goods->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
$stmt_sum_goods->execute();
$row_sum_goods = $stmt_sum_goods->fetch(PDO::FETCH_ASSOC);
$total_goods = $row_sum_goods['total_goods'] ?? 0;

$sql_sum_fails_dedos_oro = "SELECT SUM(dedos_de_oro_contaminados) AS total_fails_dedos_oro FROM materiales WHERE id_proveedor = :id_proveedor";
$stmt_sum_fails_dedos_oro = $con->prepare($sql_sum_fails_dedos_oro);
$stmt_sum_fails_dedos_oro->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
$stmt_sum_fails_dedos_oro->execute();
$row_sum_fails_dedos_oro = $stmt_sum_fails_dedos_oro->fetch(PDO::FETCH_ASSOC);
$total_fails_dedos_oro = $row_sum_fails_dedos_oro['total_fails_dedos_oro'] ?? 0;

$sql_sum_fails_faltante = "SELECT SUM(faltante) AS total_fails_faltante FROM materiales WHERE id_proveedor = :id_proveedor";
$stmt_sum_fails_faltante = $con->prepare($sql_sum_fails_faltante);
$stmt_sum_fails_faltante->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
$stmt_sum_fails_faltante->execute();
$row_sum_fails_faltante = $stmt_sum_fails_faltante->fetch(PDO::FETCH_ASSOC);
$faltante = $row_sum_fails_faltante['total_fails_faltante'] ?? 0;

$sql_sum_fails_desplazados = "SELECT SUM(desplazados) AS total_fails_desplazados FROM materiales WHERE id_proveedor = :id_proveedor";
$stmt_sum_fails_desplazados = $con->prepare($sql_sum_fails_desplazados);
$stmt_sum_fails_desplazados->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
$stmt_sum_fails_desplazados->execute();
$row_sum_fails_desplazados = $stmt_sum_fails_desplazados->fetch(PDO::FETCH_ASSOC);
$total_fails_desplazados = $row_sum_fails_desplazados['total_fails_desplazados'] ?? 0;

$sql_sum_fails_insuficiencias = "SELECT SUM(insuficiencias) AS total_fails_insuficiencias FROM materiales WHERE id_proveedor = :id_proveedor";
$stmt_sum_fails_insuficiencias = $con->prepare($sql_sum_fails_insuficiencias);
$stmt_sum_fails_insuficiencias->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
$stmt_sum_fails_insuficiencias->execute();
$row_sum_fails_insuficiencias = $stmt_sum_fails_insuficiencias->fetch(PDO::FETCH_ASSOC);
$total_fails_insuficiencias = $row_sum_fails_insuficiencias['total_fails_insuficiencias'] ?? 0;

$sql_sum_fails_despanelizados = "SELECT SUM(despanelizados) AS total_fails_despanelizados FROM materiales WHERE id_proveedor = :id_proveedor";
$stmt_sum_fails_despanelizados = $con->prepare($sql_sum_fails_despanelizados);
$stmt_sum_fails_despanelizados->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
$stmt_sum_fails_despanelizados->execute();
$row_sum_fails_despanelizados = $stmt_sum_fails_despanelizados->fetch(PDO::FETCH_ASSOC);
$total_fails_despanelizados = $row_sum_fails_despanelizados['total_fails_despanelizados'] ?? 0;

$sql_sum_fails_desprendidos = "SELECT SUM(desprendidos) AS total_fails_desprendidos FROM materiales WHERE id_proveedor = :id_proveedor";
$stmt_sum_fails_desprendidos = $con->prepare($sql_sum_fails_desprendidos);
$stmt_sum_fails_desprendidos->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
$stmt_sum_fails_desprendidos->execute();
$row_sum_fails_desprendidos = $stmt_sum_fails_desprendidos->fetch(PDO::FETCH_ASSOC);
$total_fails_desprendidos = $row_sum_fails_desprendidos['total_fails_desprendidos'] ?? 0;

$sql_sum_total_fails = "SELECT SUM(total) AS total_total_fails FROM materiales WHERE id_proveedor = :id_proveedor";
$stmt_sum_total_fails = $con->prepare($sql_sum_total_fails);
$stmt_sum_total_fails->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
$stmt_sum_total_fails->execute();
$row_sum_total_fails = $stmt_sum_total_fails->fetch(PDO::FETCH_ASSOC);
$total_total_fails = $row_sum_total_fails['total_total_fails'] ?? 0;

$sql_sum_total_final = "SELECT SUM(total_final) AS total_total_final FROM materiales WHERE id_proveedor = :id_proveedor";
$stmt_sum_total_final = $con->prepare($sql_sum_total_final);
$stmt_sum_total_final->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
$stmt_sum_total_final->execute();
$row_sum_total_final = $stmt_sum_total_final->fetch(PDO::FETCH_ASSOC);
$total_total_final = $row_sum_total_final['total_total_final'] ?? 0;

$sql_sum_total = "SELECT SUM(total) AS total_total FROM materiales WHERE id_proveedor = :id_proveedor";
$stmt_sum_total = $con->prepare($sql_sum_total);
$stmt_sum_total->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
$stmt_sum_total->execute();
$row_sum_total = $stmt_sum_total->fetch(PDO::FETCH_ASSOC);
$total_total = $row_sum_total['total_total'] ?? 0;


if ($total_total != 0) {
  $result_division = ($total_goods / $total_total) * 100;
} else {
  $result_division = 0;
}

$result_division = min(max($result_division, 0), 100);

$result_division = round($result_division);

$sql = "SELECT * FROM materiales WHERE id_proveedor = :id_proveedor ORDER BY inspection_date ASC, id_material ASC LIMIT :offset, :records_per_page";
$stmt = $con->prepare($sql);
$stmt->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->execute();

$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

function getUniqueValues($column, $con, $id_proveedor)
{
  $sql = "SELECT DISTINCT $column FROM materiales WHERE $column IS NOT NULL AND $column != '' AND id_proveedor = :id_proveedor ORDER BY $column";
  $stmt = $con->prepare($sql);
  $stmt->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
$descripcion_values = getUniqueValues('descripcion', $con, $id_proveedor);
$shift_values = getUniqueValues('shift', $con, $id_proveedor);
$operators_values = getUniqueValues('operators', $con, $id_proveedor);
$goods_values = getUniqueValues('goods', $con, $id_proveedor);
$dedos_oro_values = getUniqueValues('dedos_de_oro_contaminados', $con, $id_proveedor);
$faltante_values = getUniqueValues('faltante', $con, $id_proveedor);
$desplazados_values = getUniqueValues('desplazados', $con, $id_proveedor);
$insuficiencias_values = getUniqueValues('insuficiencias', $con, $id_proveedor);
$despanelizados_values = getUniqueValues('despanelizados', $con, $id_proveedor);
$desprendidos_values = getUniqueValues('desprendidos', $con, $id_proveedor);
$total_values = getUniqueValues('total', $con, $id_proveedor);
$total_final_values = getUniqueValues('total_final', $con, $id_proveedor);
$yield_values = getUniqueValues('yield', $con, $id_proveedor);


if (isset($id_proveedor) && is_numeric($id_proveedor)) {
  $sql = "SELECT permiso_editar, permiso_capturar FROM roles_permisos WHERE id_proveedor = :id_proveedor AND activo = 1";

  try {
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
    $stmt->execute();

    $permisos = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($permisos) {
      $puedeEditar = (bool) $permisos['permiso_editar'];
      $puedeCapturar = (bool) $permisos['permiso_capturar'];
    } else {
      $puedeEditar = false;
      $puedeCapturar = false;
    }
  } catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
  }
} else {
  $puedeEditar = false;
  $puedeCapturar = false;
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="../../assets/vendor/fonts/boxicons.css" />
  <link rel="stylesheet" href="../../assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../../assets/css/demo.css" />
  <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../../assets/vendor/libs/apex-charts/apex-charts.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="../../assets/vendor/js/helpers.js"></script>
  <script src="../../assets/js/config.js"></script>
  <link rel="stylesheet" href="../../css/styles.css">
  <link rel="shortcut icon" href="../../ico/comter.png" type="image/x-icon">

  <title>PCBA MATERIAL DE SMT</title>
</head>

<body>



  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

      <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme" >
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

          <!--<li class="menu-item active">
            <a href="../admin_panel.php" class="menu-link">
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
                <a href="molexpcba.php" class="menu-link">
                  <div data-i18n="Without menu">MOLEX PCBA - Material Acumulado o de Almacén</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="matsmt.php" class="menu-link">
                  <div data-i18n="Without menu">MATERIAL DE SMT (MAT.FRESCO)</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="molexsem42.php" class="menu-link">
                  <div data-i18n="Without navbar">Molex SEM42</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="molexsem43.php" class="menu-link">
                  <div data-i18n="Without navbar">Molex SEM43</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="molexsem44.php" class="menu-link">
                  <div data-i18n="Without navbar">Molex SEM44</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="molexsem45.php" class="menu-link">
                  <div data-i18n="Without navbar">Molex SEM45</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="molexsem46.php" class="menu-link">
                  <div data-i18n="Without navbar">Molex SEM46</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="molexsem47.php" class="menu-link">
                  <div data-i18n="Without navbar">Molex SEM47</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="molexsem48.php" class="menu-link">
                  <div data-i18n="Without navbar">Molex SEM48</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="molexsem49.php" class="menu-link">
                  <div data-i18n="Without navbar">Molex SEM49</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="molexsem50.php" class="menu-link">
                  <div data-i18n="Without navbar">Molex SEM50</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="molexsem51.php" class="menu-link">
                  <div data-i18n="Without navbar">Molex SEM51</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="molexsem52.php" class="menu-link">
                  <div data-i18n="Without navbar">Molex SEM52</div>
                </a>
              </li>
            </ul>
          </li>

          <li class="mt-2">
            <ul>
              <div>
                <form action="../backend/exportar_excel_smt.php" method="post">
                  <button type="submit" class="btn btn-success mb-4" id="exportButton"  style="width: 100%;">
                    <i class="bx bxs-file"></i> Exportar a Excel
                  </button>
                </form>
              </div>
              <div>
                <button type="button" class="btn btn-primary mb-4 add_smt" data-bs-toggle="modal"
                  data-bs-target="#addRecordModalsmt" style="width: 100%;">
                  <i class="bx bx-plus"></i> Agregar registro
                </button>
              </div>
              <button id="resetFilters" class="btn btn-danger mb-3" style="width: 100%;">
                <i class="bx bx-reset"></i> Reiniciar Filtros
              </button>
            </ul>
          </li>

        </ul>
        </li>


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
</style>
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
                <!--<form method="GET" action="search_molex_pcba.php" class="d-flex align-items-center">
                  <input type="text" name="search" class="form-control border-0 shadow-none"
                    placeholder="Buscar por Operador..." aria-label="Search..."
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
                  <button type="submit" class="btn border-0 bg-transparent p-0 ms-2">
                    <i class="bx bx-search fs-4 lh-0"></i>
                  </button>

                  <a href="molexpcba.php" class="btn border-0 bg-transparent p-0 ms-2">
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
                          <!--<span
                                                        class="fw-semibold d-block"><?php echo $nombre . ' ' . $apellido; ?></span>-->
                          <small class="text-muted">Rol: <?php echo $role; ?></small><br>
                          <small class="text-muted">Puesto: <?php echo $puesto; ?></small>
                        </div>



                      </div>
                    </a>
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
        <style>
          .logo-style {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
          }
        </style>

        <div class="content-wrapper">
          <div class="container my-5">
            <?php if (isset($_SESSION['success_message_smt'])): ?>
              <div class="alert alert-success"><?= $_SESSION['success_message_smt'];
              unset($_SESSION['success_message_smt']); ?>
              </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message_smt'])): ?>
              <div class="alert alert-danger"><?= $_SESSION['error_message_smt'];
              unset($_SESSION['error_message_smt']); ?></div>
            <?php endif; ?>

            <div class="container-desktop">
              <h3 class="text-center mt-3">Records Material de SMT</h3>
              <div class="mt-5 d-flex justify-content-between">
                <div>
                  <!--<form action="../backend/exportar_excel_smt.php" method="post">
                    <button type="submit" class="btn btn-success mb-4" id="exportButton" disabled>
                      <i class="bx bxs-file"></i> Export to Excel
                    </button>
                  </form>-->
                </div>
                <!--<div>
                  <button type="button" class="btn btn-primary mb-4 add_smt" data-bs-toggle="modal"
                    data-bs-target="#addRecordModalsmt">
                    <i class="bx bx-plus"></i> Add Record
                  </button>
                </div>-->
              </div>
              <div class="table-responsive">

                <!--<button id="resetFilters" class="btn btn-danger mb-3"><i class="bx bx-reset"></i> Reset Filters</button>-->


                <div style="overflow-y: auto; max-height: 600px;">
                  <table class="table table-bordered text-center" id="inspectionTable" style="border-color: black;">
                    <thead style="background-color: #D9DAD9; position: sticky; top: 0; z-index: 10;">
                      <tr>
                        <th colspan="5" style="color:#000000; border-color: black;"> </th>
                        <th colspan="7"
                          style="background-color:#000000; color:#ffffff; text-align:center; border-color: black;">Fails
                          Report</th>
                        <th colspan="8" style="color:#000000; border-color: black;"></th>
                      </tr>
                      <tr>
                        <th class="text-center" style="color:#000000; border-color: black;">
                          INSPECTION DATE
                          <input id="filter_date" type="text" class="form-control form-control-sm mt-2"
                            placeholder="Select Date" />
                        </th>
                        <th class="text-center" style="color:#000000; border-color: black;">
                          DESCRIPTION
                          <select id="filter_description" class="form-select form-select-sm mt-2">
                            <option value="">All</option>
                            <?php
                            if (!empty($descripcion_values)) {
                              foreach ($descripcion_values as $descripcion) {
                                if (!empty($descripcion)) {
                                  echo "<option value=\"" . htmlspecialchars($descripcion) . "\">" . htmlspecialchars($descripcion) . "</option>";
                                }
                              }
                            } else {
                              echo "<option value=\"\">No descriptions available</option>";
                            }
                            ?>
                          </select>
                        </th>
                        <th class="text-center" style="color:#000000; border-color: black;">
                          SHIFT
                          <select id="filter_shift" class="form-select form-select-sm mt-2" name="shift">
                            <option value="all" selected>All</option>
                            <?php
                            if ($id_turno) {
                              echo "<option value=\"$id_turno\" selected>$nombre_turno</option>";
                            } else {
                              for ($i = 1; $i <= 10; $i++) {
                                echo "<option value=\"$i\">$i</option>";
                              }
                            }
                            ?>
                          </select>
                        </th>
                        <th class="text-center" style="color:#000000; border-color: black;">
                          OPERATORS
                          <select id="filter_operators" class="form-select form-select-sm mt-2">
                            <option value="">All</option>
                            <?php
                            foreach ($operators_values as $value) {
                              echo "<option value=\"$value\">$value</option>";
                            }
                            ?>
                          </select>
                        </th>
                        <th class="text-center border border-dark" style="color:#0fcb59;">
                          GOODS
                          <select id="filter_goods" class="form-select form-select-sm">
                            <option value="">All</option>
                            <?php
                            foreach ($goods_values as $value) {
                              echo "<option value=\"$value\">$value</option>";
                            }
                            ?>
                          </select>
                        </th>
                        <th class="text-center border border-dark" style="color:#000000;">
                          DEDOS DE ORO CONTAMINADOS
                          <select id="filter_dedos_oro" class="form-select form-select-sm">
                            <option value="">All</option>
                            <?php
                            foreach ($dedos_oro_values as $value) {
                              echo "<option value=\"$value\">$value</option>";
                            }
                            ?>
                          </select>
                        </th>
                        <th class="text-center border border-dark" style="color:#000000;">
                          FALTANTE
                          <select id="filter_faltante" class="form-select form-select-sm">
                            <option value="">All</option>
                            <?php
                            foreach ($faltante_values as $value) {
                              echo "<option value=\"$value\">$value</option>";
                            }
                            ?>
                          </select>
                        </th>
                        <th class="text-center border border-dark" style="color:#000000;">
                          DESPLAZADOS
                          <select id="filter_desplazados" class="form-select form-select-sm">
                            <option value="">All</option>
                            <?php
                            foreach ($desplazados_values as $value) {
                              echo "<option value=\"$value\">$value</option>";
                            }
                            ?>
                          </select>
                        </th>
                        <th class="text-center border border-dark" style="color:#000000;">
                          INSUFICIENCIAS
                          <select id="filter_insuficiencias" class="form-select form-select-sm">
                            <option value="">All</option>
                            <?php
                            foreach ($insuficiencias_values as $value) {
                              echo "<option value=\"$value\">$value</option>";
                            }
                            ?>
                          </select>
                        </th>
                        <th class="text-center border border-dark" style="color:#000000;">
                          DESPANELIZADOS
                          <select id="filter_despanelizados" class="form-select form-select-sm">
                            <option value="">All</option>
                            <?php
                            foreach ($despanelizados_values as $value) {
                              echo "<option value=\"$value\">$value</option>";
                            }
                            ?>
                          </select>
                        </th>
                        <th class="text-center border border-dark" style="color:#000000;">
                          DESPRENDIDOS
                          <select id="filter_desprendidos" class="form-select form-select-sm">
                            <option value="">All</option>
                            <?php
                            foreach ($desprendidos_values as $value) {
                              echo "<option value=\"$value\">$value</option>";
                            }
                            ?>
                          </select>
                        </th>
                        <th class="text-center border border-dark" style="color:#0fcb59;">
                          TOTAL
                          <select id="filter_total" class="form-select form-select-sm">
                            <option value="">All</option>
                            <?php
                            foreach ($total_values as $value) {
                              echo "<option value=\"$value\">$value</option>";
                            }
                            ?>
                          </select>
                        </th>
                        <th class="text-center border border-dark" style="color:#0fcb59;">
                          TOTAL FINAL
                          <select id="filter_total_final" class="form-select form-select-sm">
                            <option value="">All</option>
                            <?php
                            foreach ($total_final_values as $value) {
                              echo "<option value=\"$value\">$value</option>";
                            }
                            ?>
                          </select>
                        </th>
                        <th class="text-center border border-dark" style="color:#0fcb59;">
                          YIELD
                          <select id="filter_yield" class="form-select form-select-sm">
                            <option value="">All</option>
                            <?php
                            foreach ($yield_values as $value) {
                              echo "<option value=\"$value\">$value</option>";
                            }
                            ?>
                          </select>
                        </th>
                        <th class="text-center border border-dark" style="color:#000000;">
                          COMMENTS
                          <select id="filter_comments" class="form-select form-select-sm">
                            <option value="">All</option>
                          </select>
                        </th>
                        <th style="color:#000000; border-color: black;">Acciones</th>
                      </tr>
                      <tr>
                        <th colspan="4"
                          style="background-color:#626262; color:#ffffff; text-align:center; border-color: black;">GRAN
                          TOTAL / SEMANA 29</th>
                        <th style="color:#000000; border-color: black;"><?php echo number_format($total_goods); ?></th>
                        <th style="color:#000000; border-color: black;"><?php echo $total_fails_dedos_oro; ?></th>
                        <th style="color:#000000; border-color: black;"><?php echo $faltante; ?></th>
                        <th style="color:#000000; border-color: black;"><?php echo $total_fails_desplazados; ?></th>
                        <th style="color:#000000; border-color: black;"><?php echo $total_fails_insuficiencias; ?></th>
                        <th style="color:#000000; border-color: black;"><?php echo $total_fails_despanelizados; ?></th>
                        <th style="color:#000000; border-color: black;"><?php echo $total_fails_desprendidos; ?></th>
                        <th style="color:#000000; border-color: black;"><?php echo $total_total_fails; ?></th>
                        <th style="color:#000000; border-color: black;"><?php echo number_format($total_total_final); ?>
                        </th>
                        <th style="color:#000000; border-color: black;"><?php echo $result_division . '%'; ?></th>
                        <th style="color:#000000; border-color: black;"></th>
                        <th style="color:#000000; border-color: black;"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (empty($records)): ?>
                        <tr>
                          <td colspan="16" class="fw-bold text-danger" style="border-color: black;">No records available.
                          </td>
                        </tr>
                      <?php else: ?>
                        <?php foreach ($records as $record): ?>
                          <tr data-inspection-date="<?= $record['inspection_date']; ?>"
                            data-description="<?= htmlspecialchars($record['descripcion']); ?>"
                            data-shift="<?= htmlspecialchars($record['shift']); ?>"
                            data-operators="<?= htmlspecialchars($record['operators']); ?>"
                            data-goods="<?= htmlspecialchars($record['goods']); ?>"
                            data-dedos-oro="<?= htmlspecialchars($record['dedos_de_oro_contaminados']); ?>"
                            data-faltante="<?= htmlspecialchars($record['faltante']); ?>"
                            data-desplazados="<?= htmlspecialchars($record['desplazados']); ?>"
                            data-insuficiencias="<?= htmlspecialchars($record['insuficiencias']); ?>"
                            data-despanelizados="<?= htmlspecialchars($record['despanelizados']); ?>"
                            data-desprendidos="<?= htmlspecialchars($record['desprendidos']); ?>"
                            data-total="<?= htmlspecialchars($record['total']); ?>"
                            data-total-final="<?= htmlspecialchars($record['total_final']); ?>"
                            data-yield="<?= htmlspecialchars($record['yield']); ?>"
                            data-comments="<?= htmlspecialchars($record['comments']); ?>">

                            <td style="border-color: black; color:#000000;">
                              <?php
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
                              echo $date->format('d/m/y') . " - " . $daysInSpanish[$dayOfWeek];
                              ?>
                            </td>
                            <td style="border-color: black; color:#000000;">
                              <?php
                              if (!empty($record['descripcion']) && !empty($record['descripcion_image'])) {
                                echo htmlspecialchars($record['descripcion']);
                                echo '<br>';
                                echo '<img src="data:image/jpeg;base64,' . base64_encode($record['descripcion_image']) . '" alt="" style="max-width: 200px; max-height: 100px;" />';
                              } elseif (!empty($record['descripcion'])) {
                                echo htmlspecialchars($record['descripcion']);
                              } elseif (!empty($record['descripcion_image'])) {
                                echo '<img src="data:image/jpeg;base64,' . base64_encode($record['descripcion_image']) . '" alt="" style="max-width: 200px; max-height: 100px;" />';
                              } else {
                                echo 'N/A';
                              }
                              ?>
                            </td>
                            <td style="border-color: black; color:#000000;"><?= htmlspecialchars($record['shift']) ?></td>
                            <td style="border-color: black; color:#000000;"><?= htmlspecialchars($record['operators']) ?>
                            </td>
                            <td style="border-color: black; color:#000000;"><?= htmlspecialchars($record['goods']) ?></td>
                            <td
                              style="border-color: black; <?= $record['dedos_de_oro_contaminados'] != 0 ? 'color:#FF0000; background-color: #FFCCCC;' : ''; ?>">
                              <?= $record['dedos_de_oro_contaminados'] != 0 ? '<b>' . htmlspecialchars($record['dedos_de_oro_contaminados']) . '</b>' : htmlspecialchars($record['dedos_de_oro_contaminados']) ?>
                            </td>
                            <td
                              style="border-color: black; <?= $record['faltante'] != 0 ? 'color:#FF0000; background-color: #FFCCCC;' : ''; ?>">
                              <?= $record['faltante'] != 0 ? '<b>' . htmlspecialchars($record['faltante']) . '</b>' : htmlspecialchars($record['faltante']) ?>
                            </td>
                            <td
                              style="border-color: black; <?= $record['desplazados'] != 0 ? 'color:#FF0000; background-color: #FFCCCC;' : ''; ?>">
                              <?= $record['desplazados'] != 0 ? '<b>' . htmlspecialchars($record['desplazados']) . '</b>' : htmlspecialchars($record['desplazados']) ?>
                            </td>
                            <td
                              style="border-color: black; <?= $record['insuficiencias'] != 0 ? 'color:#FF0000; background-color: #FFCCCC;' : ''; ?>">
                              <?= $record['insuficiencias'] != 0 ? '<b>' . htmlspecialchars($record['insuficiencias']) . '</b>' : htmlspecialchars($record['insuficiencias']) ?>
                            </td>
                            <td
                              style="border-color: black; <?= $record['despanelizados'] != 0 ? 'color:#FF0000; background-color: #FFCCCC;' : ''; ?>">
                              <?= $record['despanelizados'] != 0 ? '<b>' . htmlspecialchars($record['despanelizados']) . '</b>' : htmlspecialchars($record['despanelizados']) ?>
                            </td>
                            <td
                              style="border-color: black; <?= $record['desprendidos'] != 0 ? 'color:#FF0000; background-color: #FFCCCC;' : ''; ?>">
                              <?= $record['desprendidos'] != 0 ? '<b>' . htmlspecialchars($record['desprendidos']) . '</b>' : htmlspecialchars($record['desprendidos']) ?>
                            </td>
                            <td
                              style="border-color: black; <?= $record['total'] != 0 ? 'color:#FF0000; background-color: #FFCCCC;' : ''; ?>">
                              <?= $record['total'] != 0 ? '<b>' . htmlspecialchars($record['total']) . '</b>' : htmlspecialchars($record['total']) ?>
                            </td>
                            <td style="border-color: black;"><?= htmlspecialchars($record['total_final']) ?></td>
                            <td style="border-color: black;"><?= htmlspecialchars($record['yield']) ?>%</td>
                            <td style="border-color: black;">
                              <?php
                              if (!empty($record['comments']) && !empty($record['comments_image'])) {
                                echo htmlspecialchars($record['comments']);
                                echo '<br>';
                                echo '<img src="data:image/jpeg;base64,' . base64_encode($record['comments_image']) . '" alt="" style="max-width: 200px; max-height: 100px;" />';
                              } elseif (!empty($record['comments'])) {
                                echo htmlspecialchars($record['comments']);
                              } elseif (!empty($record['comments_image'])) {
                                echo '<img src="data:image/jpeg;base64,' . base64_encode($record['comments_image']) . '" alt="" style="max-width: 200px; max-height: 100px;" />';
                              } else {
                                echo 'N/A';
                              }
                              ?>
                            </td>
                            <td style="border-color: black; text-align:center;">
                              <a href="edit_record_smt.php?id_material=<?= $record['id_material']; ?>"
                                class="btn btn-warning btn-sm edit_smt <?php echo !$puedeEditar ? 'disabled-btn' : ''; ?>"><i
                                  class="fas fa-edit"></i> Edit</a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </tbody>
                  </table>

                </div>





                <div class="pagination"
                  style="display: flex; justify-content: center; align-items: center; text-align: center; margin-top: 20px;">
                  <?php if ($current_page > 1): ?>
                    <a href="?page=<?= $current_page - 1 ?>" class="btn btn-primary"
                      style="margin-right: 5px;">Previous</a>
                  <?php endif; ?>

                  <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="btn <?= $i === $current_page ? 'btn-info' : 'btn-secondary' ?>"
                      style="margin-right: 5px;">
                      <?= $i ?>
                    </a>
                  <?php endfor; ?>

                  <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?= $current_page + 1 ?>" class="btn btn-primary" style="margin-right: 5px;">Next</a>
                  <?php endif; ?>
                </div>



                <div class="modal fade" id="addRecordModalsmt" tabindex="-1" aria-labelledby="addRecordModalLabel"
                  aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="addRecordModalLabel">Add Record</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <form action="matsmt.php" method="post" enctype="multipart/form-data">
                          <?php

                          date_default_timezone_set('America/Mexico_City');
                          ?>

                          <div class="mb-3">
                            <label for="inspection_date" class="form-label">INSPECTION DATE</label>
                            <input type="date" class="form-control" id="inspection_date" name="inspection_date"
                              value="<?php echo date('Y-m-d'); ?>">
                          </div>


                          <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control" id="description"
                              name="description"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                          </div>

                          <div class="mb-3">
                            <label for="description_image" class="form-label">Imagen Descripción</label>
                            <input type="file" class="form-control" id="description_image" name="description_image[]"
                              multiple>
                          </div>


                          <div class="mb-3">
                            <label for="shift" class="form-label">SHIFT</label>
                            <select class="form-control" id="shift" name="shift">
                              <option value="" disabled selected>Selecciona un turno</option>
                              <?php

                              $sql_turno = "SELECT p.id_turno 
                      FROM proveedores p
                      WHERE p.id_proveedor = :id_proveedor";

                              $stmt_turno = $con->prepare($sql_turno);
                              $stmt_turno->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
                              $stmt_turno->execute();


                              $turno = $stmt_turno->fetch(PDO::FETCH_ASSOC);
                              $id_turno = $turno ? $turno['id_turno'] : null;


                              if ($id_turno) {
                                $sql_turno_nombre = "SELECT nombre_turno FROM turnos WHERE id_turno = :id_turno";
                                $stmt_turno_nombre = $con->prepare($sql_turno_nombre);
                                $stmt_turno_nombre->bindParam(':id_turno', $id_turno, PDO::PARAM_INT);
                                $stmt_turno_nombre->execute();

                                $turno_nombre = $stmt_turno_nombre->fetch(PDO::FETCH_ASSOC);
                                $nombre_turno = $turno_nombre ? $turno_nombre['nombre_turno'] : 'No asignado';


                                echo "<option value=\"$nombre_turno\" selected>$nombre_turno</option>";
                              } else {

                                echo "<option value=\"\" selected>No asignado</option>";
                              }


                              if (!$id_turno) {
                                for ($i = 1; $i <= 10; $i++) {
                                  echo "<option value=\"Turno $i\">Turno $i</option>";
                                }
                              }
                              ?>
                            </select>
                          </div>



                          <div class="mb-3">
                            <label for="operators" class="form-label">OPERATORS</label>
                            <textarea class="form-control" id="operators" name="operators">
        <?php echo htmlspecialchars($nombre . ' ' . $apellido); ?>
    </textarea>
                          </div>

                          <div class="mb-3">
                            <label for="goods" class="form-label">GOODS</label>
                            <select class="form-control" id="goods" name="goods">
                              <option value="" disabled selected>Selecciona un valor para GOODS</option>
                              <?php for ($i = 0; $i <= 7000; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                              <?php endfor; ?>
                            </select>
                          </div>

                          <div class="mb-3">
                            <label for="dedos_de_oro_contaminados" class="form-label">DEDOS DE ORO CONTAMINADOS</label>
                            <select class="form-control" id="dedos_de_oro_contaminados"
                              name="dedos_de_oro_contaminados">
                              <option value="0" selected>0</option>
                              <?php for ($i = 1; $i <= 1000; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                              <?php endfor; ?>
                            </select>
                          </div>

                          <div class="mb-3">
                            <label for="faltante" class="form-label">FALTANTE</label>
                            <select class="form-control" id="faltante" name="faltante">
                              <option value="0" selected>0</option>
                              <?php for ($i = 1; $i <= 1000; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                              <?php endfor; ?>
                            </select>
                          </div>

                          <div class="mb-3">
                            <label for="desplazados" class="form-label">DESPLAZADOS</label>
                            <select class="form-control" id="desplazados" name="desplazados">
                              <option value="0" selected>0</option>
                              <?php for ($i = 1; $i <= 1000; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                              <?php endfor; ?>
                            </select>
                          </div>

                          <div class="mb-3">
                            <label for="insuficiencias" class="form-label">INSUFICIENCIAS</label>
                            <select class="form-control" id="insuficiencias" name="insuficiencias">
                              <option value="0" selected>0</option>
                              <?php for ($i = 1; $i <= 1000; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                              <?php endfor; ?>
                            </select>
                          </div>

                          <div class="mb-3">
                            <label for="despanelizados" class="form-label">DESPANELIZADOS</label>
                            <select class="form-control" id="despanelizados" name="despanelizados">
                              <option value="0" selected>0</option>
                              <?php for ($i = 1; $i <= 1000; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                              <?php endfor; ?>
                            </select>
                          </div>

                          <div class="mb-3">
                            <label for="desprendidos" class="form-label">DESPRENDIDOS</label>
                            <select class="form-control" id="desprendidos" name="desprendidos">
                              <option value="0" selected>0</option>
                              <?php for ($i = 1; $i <= 1000; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                              <?php endfor; ?>
                            </select>
                          </div>

                          <div class="mb-3">
                            <label for="total" class="form-label">Total</label>
                            <input type="number" class="form-control" id="total" name="total" required readonly>
                          </div>
                          <div class="mb-3">
                            <label for="total_final" class="form-label">Total Final</label>
                            <input type="number" class="form-control" id="total_final" name="total_final" required
                              readonly>
                          </div>
                          <div class="mb-3">
                            <label for="yield" class="form-label">Yield (%)</label>
                            <input type="text" class="form-control" id="yield" name="yield" required readonly>
                          </div>
                          <div class="mb-3">
                            <label for="comments" class="form-label">COMMENTS</label>
                            <textarea class="form-control" id="comments" name="comments"></textarea>
                          </div>
                          <div class="mb-3">
                            <label for="comments_image" class="form-label">Imagen Comentarios</label>
                            <input type="file" class="form-control" id="comments_image" name="comments_image">
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>





                <br><br><br><br><br><br><br><br>
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
        <script>
          function updateTotalInspectedAndYield() {

            const dedosDeOro = parseInt(document.getElementById('dedos_de_oro_contaminados').value) || 0;
            const faltante = parseInt(document.getElementById('faltante').value) || 0;
            const desplazados = parseInt(document.getElementById('desplazados').value) || 0;
            const insuficiencias = parseInt(document.getElementById('insuficiencias').value) || 0;
            const despanelizados = parseInt(document.getElementById('despanelizados').value) || 0;
            const desprendidos = parseInt(document.getElementById('desprendidos').value) || 0;
            const goods = parseInt(document.getElementById('goods').value) || 0;


            const totalFails = dedosDeOro + faltante + desplazados + insuficiencias + despanelizados + desprendidos;


            document.getElementById('total').value = totalFails;

            const totalFinal = totalFails + goods;


            document.getElementById('total_final').value = totalFinal;


            let yieldPercentage = 0;
            if (totalFinal > 0) {
              yieldPercentage = Math.round((goods / totalFinal) * 100);
            }


            document.getElementById('yield').value = yieldPercentage + '%';
          }


          document.getElementById('dedos_de_oro_contaminados').addEventListener('input', updateTotalInspectedAndYield);
          document.getElementById('faltante').addEventListener('input', updateTotalInspectedAndYield);
          document.getElementById('desplazados').addEventListener('input', updateTotalInspectedAndYield);
          document.getElementById('insuficiencias').addEventListener('input', updateTotalInspectedAndYield);
          document.getElementById('despanelizados').addEventListener('input', updateTotalInspectedAndYield);
          document.getElementById('desprendidos').addEventListener('input', updateTotalInspectedAndYield);
          document.getElementById('goods').addEventListener('input', updateTotalInspectedAndYield);


          updateTotalInspectedAndYield();
        </script>





        <script>
         /* document.addEventListener('DOMContentLoaded', function () {
            var table = document.getElementById('inspectionTable');
            var exportButton = document.getElementById('exportButton');

            var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            var hasRecords = rows.length > 0 && rows[0].getElementsByTagName('td').length > 1;

            exportButton.disabled = !hasRecords;
          });*/
        </script>



        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
          document.addEventListener('DOMContentLoaded', function () {


            flatpickr("#filter_date", {
              dateFormat: "Y-m-d",
              onChange: function () {
                filterTable();
              }
            });


            const filters = [
              "filter_date", "filter_description", "filter_shift", "filter_operators",
              "filter_goods", "filter_dedos_oro", "filter_faltante", "filter_desplazados",
              "filter_insuficiencias", "filter_despanelizados", "filter_desprendidos",
              "filter_total", "filter_total_final", "filter_yield", "filter_comments"
            ];


            filters.forEach(function (filterId) {
              const filterElement = document.getElementById(filterId);
              filterElement.addEventListener("input", filterTable);
            });


            function filterTable() {
              const rows = document.querySelectorAll("#inspectionTable tbody tr");
              let firstVisibleRow = null;

              rows.forEach(function (row) {
                let showRow = true;


                const filterDate = document.getElementById("filter_date").value.trim();
                const filterDescription = document.getElementById("filter_description").value.trim().toLowerCase();
                const filterShift = document.getElementById("filter_shift").value.trim().toLowerCase();
                const filterOperators = document.getElementById("filter_operators").value.trim().toLowerCase();
                const filterGoods = document.getElementById("filter_goods").value.trim().toLowerCase();
                const filterDedosOro = document.getElementById("filter_dedos_oro").value.trim().toLowerCase();
                const filterFaltante = document.getElementById("filter_faltante").value.trim().toLowerCase();
                const filterDesplazados = document.getElementById("filter_desplazados").value.trim().toLowerCase();
                const filterInsuficiencias = document.getElementById("filter_insuficiencias").value.trim().toLowerCase();
                const filterDespanelizados = document.getElementById("filter_despanelizados").value.trim().toLowerCase();
                const filterDesprendidos = document.getElementById("filter_desprendidos").value.trim().toLowerCase();
                const filterTotal = document.getElementById("filter_total").value.trim().toLowerCase();
                const filterTotalFinal = document.getElementById("filter_total_final").value.trim().toLowerCase();
                const filterYield = document.getElementById("filter_yield").value.trim().toLowerCase();
                const filterComments = document.getElementById("filter_comments").value.trim().toLowerCase();


                if (filterDate && !row.dataset.inspectionDate.includes(filterDate)) showRow = false;
                if (filterDescription && !row.dataset.description.toLowerCase().includes(filterDescription)) showRow = false;
                if (filterShift && !row.dataset.shift.toLowerCase().includes(filterShift)) showRow = false;
                if (filterOperators && !row.dataset.operators.toLowerCase().includes(filterOperators)) showRow = false;
                if (filterGoods && !row.dataset.goods.toLowerCase().includes(filterGoods)) showRow = false;
                if (filterDedosOro && !row.dataset.dedosOro.toLowerCase().includes(filterDedosOro)) showRow = false;
                if (filterFaltante && !row.dataset.faltante.toLowerCase().includes(filterFaltante)) showRow = false;
                if (filterDesplazados && !row.dataset.desplazados.toLowerCase().includes(filterDesplazados)) showRow = false;
                if (filterInsuficiencias && !row.dataset.insuficiencias.toLowerCase().includes(filterInsuficiencias)) showRow = false;
                if (filterDespanelizados && !row.dataset.despanelizados.toLowerCase().includes(filterDespanelizados)) showRow = false;
                if (filterDesprendidos && !row.dataset.desprendidos.toLowerCase().includes(filterDesprendidos)) showRow = false;
                if (filterTotal && !row.dataset.total.toLowerCase().includes(filterTotal)) showRow = false;
                if (filterTotalFinal && !row.dataset.totalFinal.toLowerCase().includes(filterTotalFinal)) showRow = false;
                if (filterYield && !row.dataset.yield.toLowerCase().includes(filterYield)) showRow = false;
                if (filterComments && !row.dataset.comments.toLowerCase().includes(filterComments)) showRow = false;


                if (showRow) {
                  row.classList.remove('hidden');
                  if (!firstVisibleRow) firstVisibleRow = row;
                } else {
                  row.classList.add('hidden');
                }
              });


              if (firstVisibleRow) {
                const tableBody = document.querySelector("#inspectionTable tbody");
                tableBody.scrollTop = firstVisibleRow.offsetTop;
              }
            }


            document.getElementById('resetFilters').addEventListener('click', function () {

              filters.forEach(function (filterId) {
                document.getElementById(filterId).value = '';
              });


              filterTable();
            });

          });
        </script>



        <style>
          table tbody tr {
            opacity: 1;
            transition: opacity 0.5s ease-out;

          }


          table tbody tr.hidden {
            opacity: 0;
            pointer-events: none;

          }

          .disabled-btn {
            pointer-events: none;
            opacity: 0.5;
          }
        </style>
        <script>
          document.addEventListener('DOMContentLoaded', function () {

            const puedeEditar = <?php echo json_encode($puedeEditar); ?>;
            const puedeCapturar = <?php echo json_encode($puedeCapturar); ?>;


            const addRecordBtn = document.querySelector('.add_smt');
            if (!puedeCapturar && addRecordBtn) {
              addRecordBtn.classList.add('disabled-btn');
            }


            const editBtns = document.querySelectorAll('.edit_smt');
            editBtns.forEach(function (editBtn) {
              if (!puedeEditar) {
                editBtn.classList.add('disabled-btn');
              }
            });
          });

        </script>


</body>

</html>