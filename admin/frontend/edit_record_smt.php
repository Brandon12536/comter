<?php
session_start();
require '../../config/connection.php';

$id_material = isset($_GET['id_material']) ? intval($_GET['id_material']) : null;
if (!$id_material) {
  $_SESSION['error_message_smt'] = 'ID Material no encontrado.';
  header('Location: matsmt.php');
  exit();
}

if (!isset($_SESSION['id_proveedor'])) {
  $_SESSION['error_message_smt'] = 'Proveedor no encontrado o cuenta no válida.';
  header('Location: matsmt.php');
  exit();
}

$id_proveedor = $_SESSION['id_proveedor'];
$db = new Database();
$con = $db->conectar();

$sql = "SELECT nombre, apellido, compania, business_unit, telefono, correo, role, puesto, created_at, updated_at FROM proveedores WHERE id_proveedor = :id_proveedor";
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

if ($id_material) {
  $sql_material = "SELECT * FROM materiales WHERE id_material = :id_material AND id_proveedor = :id_proveedor";
  $stmt_material = $con->prepare($sql_material);
  $stmt_material->bindParam(':id_material', $id_material, PDO::PARAM_INT);
  $stmt_material->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
  $stmt_material->execute();

  if ($stmt_material->rowCount() > 0) {
    $material_row = $stmt_material->fetch(PDO::FETCH_ASSOC);
    $inspection_date = $material_row['inspection_date'];
    $descripcion = $material_row['descripcion'];
    $goods = $material_row['goods'];
    $dedos_de_oro_contaminados = $material_row['dedos_de_oro_contaminados'];
    $faltante = $material_row['faltante'];
    $desplazados = $material_row['desplazados'];
    $insuficiencias = $material_row['insuficiencias'];
    $despanelizados = $material_row['despanelizados'];
    $desprendidos = $material_row['desprendidos'];
    $total = $material_row['total'];
    $total_final = $material_row['total_final'];
    $yield = $material_row['yield'];
    $comments = $material_row['comments'];
    $descripcion_image = $material_row['descripcion_image'];
    $comments_image = $material_row['comments_image'];
  } else {
    $_SESSION['error_message'] = 'Material no encontrado o no autorizado.';
    header('Location: matsmt.php');
    exit();
  }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $inspection_date = $_POST['inspection_date'];
  $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : null;
  $shift = $_POST['shift'];
  $operators = $_POST['operators'];
  $goods = $_POST['goods'];
  $dedos_de_oro_contaminados = $_POST['dedos_de_oro_contaminados'];
  $faltante = $_POST['faltante'];
  $desplazados = $_POST['desplazados'];
  $insuficiencias = $_POST['insuficiencias'];
  $despanelizados = $_POST['despanelizados'];
  $desprendidos = $_POST['desprendidos'];
  $total = $_POST['total'];
  $total_final = $_POST['total_final'];
  $yield = isset($_POST['yield']) ? floatval(str_replace('%', '', $_POST['yield'])) : null;
  $comments = $_POST['comments'];


  $descripcion_image = $material_row['descripcion_image'];
  $comments_image = $material_row['comments_image'];


  if (isset($_POST['delete_comments_image']) && $_POST['delete_comments_image'] == 'on') {
    $comments_image = null;
  }


  if (isset($_POST['delete_descripcion_image']) && $_POST['delete_descripcion_image'] == 'on') {
    $descripcion_image = null;
  }


  if (isset($_FILES['descripcion_image']) && $_FILES['descripcion_image']['error'] === UPLOAD_ERR_OK) {
    $fileType = mime_content_type($_FILES['descripcion_image']['tmp_name']);
    if ($fileType == 'image/jpeg' || $fileType == 'image/png') {
      $descripcion_image = file_get_contents($_FILES['descripcion_image']['tmp_name']);
    } else {
      $_SESSION['error_message_smt'] = 'Solo se permiten imágenes en formato JPG o PNG para la descripción.';
      header('Location: ' . $_SERVER['PHP_SELF'] . '?id_material=' . $id_material);
      exit();
    }
  }

  if (isset($_FILES['comments_image']) && $_FILES['comments_image']['error'] === UPLOAD_ERR_OK) {
    $fileType = mime_content_type($_FILES['comments_image']['tmp_name']);
    if ($fileType == 'image/jpeg' || $fileType == 'image/png') {
      $comments_image = file_get_contents($_FILES['comments_image']['tmp_name']);
    } else {
      $_SESSION['error_message_smt'] = 'Solo se permiten imágenes en formato JPG o PNG para los comentarios.';
      header('Location: ' . $_SERVER['PHP_SELF'] . '?id_material=' . $id_material);
      exit();
    }
  }


  $sql_update = "UPDATE materiales SET 
                  inspection_date = :inspection_date, 
                  descripcion = :descripcion, 
                  shift = :shift, 
                  operators = :operators, 
                  goods = :goods, 
                  dedos_de_oro_contaminados = :dedos_de_oro_contaminados, 
                  faltante = :faltante, 
                  desplazados = :desplazados, 
                  insuficiencias = :insuficiencias, 
                  despanelizados = :despanelizados, 
                  desprendidos = :desprendidos, 
                  total = :total, 
                  total_final = :total_final, 
                  yield = :yield, 
                  comments = :comments, 
                  descripcion_image = :descripcion_image, 
                  comments_image = :comments_image, 
                  id_proveedor = :id_proveedor 
                WHERE id_material = :id_material";

  $stmt_update = $con->prepare($sql_update);
  $stmt_update->bindParam(':inspection_date', $inspection_date);
  $stmt_update->bindParam(':descripcion', $descripcion);
  $stmt_update->bindParam(':shift', $shift);
  $stmt_update->bindParam(':operators', $operators);
  $stmt_update->bindParam(':goods', $goods);
  $stmt_update->bindParam(':dedos_de_oro_contaminados', $dedos_de_oro_contaminados);
  $stmt_update->bindParam(':faltante', $faltante);
  $stmt_update->bindParam(':desplazados', $desplazados);
  $stmt_update->bindParam(':insuficiencias', $insuficiencias);
  $stmt_update->bindParam(':despanelizados', $despanelizados);
  $stmt_update->bindParam(':desprendidos', $desprendidos);
  $stmt_update->bindParam(':total', $total);
  $stmt_update->bindParam(':total_final', $total_final);
  $stmt_update->bindParam(':yield', $yield, PDO::PARAM_STR);
  $stmt_update->bindParam(':comments', $comments);
  $stmt_update->bindParam(':descripcion_image', $descripcion_image, PDO::PARAM_LOB);
  $stmt_update->bindParam(':comments_image', $comments_image, PDO::PARAM_LOB);
  $stmt_update->bindParam(':id_material', $id_material, PDO::PARAM_INT);
  $stmt_update->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);

  if ($stmt_update->execute()) {
    $_SESSION['success_message_smt'] = 'Datos actualizados correctamente.';
    header('Location: matsmt.php');
    exit();
  } else {
    $errorInfo = $stmt_update->errorInfo();
    $_SESSION['error_message_smt'] = 'Error al actualizar los datos: ' . $errorInfo[2];
    header('Location: matsmt.php');
    exit();
  }
}


?>




<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />
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
                          <!--<span class="fw-semibold d-block"><?php echo $nombre . ' ' . $apellido; ?></span>-->
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
            <h3 class="text-center mt-3">Editar PCBA Material de SMT</h3>
            <form action="edit_record_smt.php?id_material=<?php echo $id_material; ?>" method="post"
              enctype="multipart/form-data">
              <?php
              date_default_timezone_set('America/Mexico_City');
              ?>

              <div class="mb-3">
                <label for="inspection_date" class="form-label">INSPECTION DATE</label>
                <input type="date" class="form-control" id="inspection_date" name="inspection_date"
                  value="<?php echo isset($inspection_date) ? $inspection_date : date('Y-m-d'); ?>">
              </div>

              <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion"
                  name="descripcion"><?= isset($descripcion) ? htmlspecialchars($descripcion) : ''; ?></textarea>
              </div>

              <div class="mb-3">
                <label for="descripcion_image" class="form-label">Imagen Descripción</label>
                <input type="file" class="form-control" id="descripcion_image" name="descripcion_image">

                <?php
                if ($descripcion_image) {
                  echo '<br><img src="data:image/jpeg;base64,' . base64_encode($descripcion_image) . '" alt="" style="max-width: 200px; max-height: 100px;">';
                  echo '<div class="form-check mt-2">';
                  echo '<input type="checkbox" class="form-check-input" id="delete_descripcion_image" name="delete_descripcion_image">';
                  echo '<label class="form-check-label" for="delete_descripcion_image">Eliminar imagen de descripción</label>';
                  echo '</div>';
                }
                ?>
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
                      echo "<option value=\"Turno $i\" " . ($nombre_turno == "Turno $i" ? 'selected' : '') . ">Turno $i</option>";
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
                  <?php
                  for ($i = 0; $i <= 7000; $i++) {
                    $selected = ($goods == $i) ? 'selected' : '';
                    echo "<option value=\"$i\" $selected>$i</option>";
                  }
                  ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="dedos_de_oro_contaminados" class="form-label">DEDOS DE ORO CONTAMINADOS</label>
                <select class="form-control" id="dedos_de_oro_contaminados" name="dedos_de_oro_contaminados">
                  <?php
                  for ($i = 0; $i <= 1000; $i++) {
                    $selected = ($dedos_de_oro_contaminados == $i) ? 'selected' : '';
                    echo "<option value=\"$i\" $selected>$i</option>";
                  }
                  ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="faltante" class="form-label">FALTANTE</label>
                <select class="form-control" id="faltante" name="faltante">
                  <?php
                  for ($i = 0; $i <= 1000; $i++) {
                    $selected = ($faltante == $i) ? 'selected' : '';
                    echo "<option value=\"$i\" $selected>$i</option>";
                  }
                  ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="desplazados" class="form-label">DESPLAZADOS</label>
                <select class="form-control" id="desplazados" name="desplazados">
                  <?php
                  for ($i = 0; $i <= 1000; $i++) {
                    $selected = ($desplazados == $i) ? 'selected' : '';
                    echo "<option value=\"$i\" $selected>$i</option>";
                  }
                  ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="insuficiencias" class="form-label">INSUFICIENCIAS</label>
                <select class="form-control" id="insuficiencias" name="insuficiencias">
                  <?php
                  for ($i = 0; $i <= 1000; $i++) {
                    $selected = ($insuficiencias == $i) ? 'selected' : '';
                    echo "<option value=\"$i\" $selected>$i</option>";
                  }
                  ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="despanelizados" class="form-label">DESPANELIZADOS</label>
                <select class="form-control" id="despanelizados" name="despanelizados">
                  <?php
                  for ($i = 0; $i <= 1000; $i++) {
                    $selected = ($despanelizados == $i) ? 'selected' : '';
                    echo "<option value=\"$i\" $selected>$i</option>";
                  }
                  ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="desprendidos" class="form-label">DESPRENDIDOS</label>
                <select class="form-control" id="desprendidos" name="desprendidos">
                  <?php
                  for ($i = 0; $i <= 1000; $i++) {
                    $selected = ($desprendidos == $i) ? 'selected' : '';
                    echo "<option value=\"$i\" $selected>$i</option>";
                  }
                  ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="total" class="form-label">Total</label>
                <input type="number" class="form-control" id="total" name="total"
                  value="<?= isset($total) ? $total : ''; ?>" required readonly>
              </div>

              <div class="mb-3">
                <label for="total_final" class="form-label">Total Final</label>
                <input type="number" class="form-control" id="total_final" name="total_final"
                  value="<?= isset($total_final) ? $total_final : ''; ?>" required readonly>
              </div>

              <div class="mb-3">
                <label for="yield" class="form-label">Yield (%)</label>
                <input type="text" class="form-control" id="yield" name="yield"
                  value="<?= isset($yield) ? $yield : ''; ?>" required readonly>
              </div>

              <div class="mb-3">
                <label for="comments" class="form-label">COMMENTS</label>
                <textarea class="form-control" id="comments"
                  name="comments"><?= isset($comments) ? htmlspecialchars($comments) : ''; ?></textarea>
              </div>

              <div class="mb-3">
                <label for="comments_image" class="form-label">Imagen Comentarios</label>
                <input type="file" class="form-control" id="comments_image" name="comments_image">

                <?php
                if ($comments_image) {
                  echo '<br><img src="data:image/jpeg;base64,' . base64_encode($comments_image) . '" alt="" style="max-width: 200px; max-height: 100px;">';
                  echo '<div class="form-check mt-2">';
                  echo '<input type="checkbox" class="form-check-input" id="delete_comments_image" name="delete_comments_image">';
                  echo '<label class="form-check-label" for="delete_comments_image">Eliminar imagen de comentarios</label>';
                  echo '</div>';
                }
                ?>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar cambios</button>
              </div>
            </form>
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
    /*document.addEventListener('DOMContentLoaded', function () {
      var table = document.getElementById('inspectionTable');
      var exportButton = document.getElementById('exportButton');

      var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
      var hasRecords = rows.length > 0 && rows[0].getElementsByTagName('td').length > 1;

      exportButton.disabled = !hasRecords;
    });*/
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

</body>

</html>