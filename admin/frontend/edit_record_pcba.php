<?php
session_start();
require '../../config/connection.php';

if (!isset($_SESSION['id_proveedor'])) {
  $_SESSION['error_message'] = 'Proveedor no encontrado o cuenta no válida.';
  header('Location: molexpcba.php');
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
  /*$_SESSION['error_message'] = 'Proveedor no encontrado o cuenta no válida.';
  header('Location: molexpcba.php');
  exit();*/
}


if (isset($_GET['id_pcba'])) {
  $id_pcba = $_GET['id_pcba'];

  $sql_pcba = "SELECT * FROM PCBA WHERE id_pcba = :id_pcba AND user_id = :id_proveedor";
  $stmt_pcba = $con->prepare($sql_pcba);
  $stmt_pcba->bindParam(':id_pcba', $id_pcba, PDO::PARAM_INT);
  $stmt_pcba->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
  $stmt_pcba->execute();

  if ($stmt_pcba->rowCount() > 0) {
    $pcba_row = $stmt_pcba->fetch(PDO::FETCH_ASSOC);

    $inspection_date = $pcba_row['inspection_date'];
    $description = $pcba_row['description'];
    $shift = $pcba_row['shift'];
    $operators = $pcba_row['operators'];
    $goods = $pcba_row['goods'];
    $fails_dedos_oro = $pcba_row['fails_dedos_oro'];
    $fails_mal_corte = $pcba_row['fails_mal_corte'];
    $fails_contaminacion = $pcba_row['fails_contaminacion'];
    $pd = $pcba_row['pd'];
    $fails_desplazados = $pcba_row['fails_desplazados'];
    $fails_insuficiencias = $pcba_row['fails_insuficiencias'];
    $fails_despanelizados = $pcba_row['fails_despanelizados'];
    $fails_desprendidos = $pcba_row['fails_desprendidos'];
    $total_fails = $pcba_row['total_fails'];
    $total = $pcba_row['total'];
    $yield = $pcba_row['yield'];
    $comments = $pcba_row['comments'];
  } else {
    $_SESSION['error_message'] = 'PCBA no encontrado o no autorizado.';
    header('Location: molexpcba.php');
    exit();
  }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Recuperar los valores del formulario
  $inspection_date = $_POST['inspection_date'];
  $description = $_POST['description'];
  $shift = $_POST['shift'];
  $operators = $_POST['operators'];
  $goods = $_POST['goods'];
  $fails_dedos_oro = $_POST['fails_dedos_oro'];
  $fails_mal_corte = $_POST['fails_mal_corte'];
  $fails_contaminacion = $_POST['fails_contaminacion'];
  $pd = $_POST['pd'];
  $fails_desplazados = $_POST['fails_desplazados'];
  $fails_insuficiencias = $_POST['fails_insuficiencias'];
  $fails_despanelizados = $_POST['fails_despanelizados'];
  $fails_desprendidos = $_POST['fails_desprendidos'];
  $total_fails = $_POST['total_fails'];
  $total = $_POST['total'];
  $yield = $_POST['yield'];
  $comments = $_POST['comments'];


  $sql_update = "UPDATE PCBA SET 
                    inspection_date = :inspection_date, 
                    description = :description, 
                    shift = :shift, 
                    operators = :operators, 
                    goods = :goods, 
                    fails_dedos_oro = :fails_dedos_oro, 
                    fails_mal_corte = :fails_mal_corte, 
                    fails_contaminacion = :fails_contaminacion, 
                    pd = :pd, 
                    fails_desplazados = :fails_desplazados, 
                    fails_insuficiencias = :fails_insuficiencias, 
                    fails_despanelizados = :fails_despanelizados, 
                    fails_desprendidos = :fails_desprendidos, 
                    total_fails = :total_fails, 
                    total = :total, 
                    yield = :yield, 
                    comments = :comments
                   WHERE id_pcba = :id_pcba AND user_id = :id_proveedor";

  $stmt_update = $con->prepare($sql_update);
  $stmt_update->bindParam(':inspection_date', $inspection_date);
  $stmt_update->bindParam(':description', $description);
  $stmt_update->bindParam(':shift', $shift);
  $stmt_update->bindParam(':operators', $operators);
  $stmt_update->bindParam(':goods', $goods);
  $stmt_update->bindParam(':fails_dedos_oro', $fails_dedos_oro);
  $stmt_update->bindParam(':fails_mal_corte', $fails_mal_corte);
  $stmt_update->bindParam(':fails_contaminacion', $fails_contaminacion);
  $stmt_update->bindParam(':pd', $pd);
  $stmt_update->bindParam(':fails_desplazados', $fails_desplazados);
  $stmt_update->bindParam(':fails_insuficiencias', $fails_insuficiencias);
  $stmt_update->bindParam(':fails_despanelizados', $fails_despanelizados);
  $stmt_update->bindParam(':fails_desprendidos', $fails_desprendidos);
  $stmt_update->bindParam(':total_fails', $total_fails);
  $stmt_update->bindParam(':total', $total);
  $stmt_update->bindParam(':yield', $yield);
  $stmt_update->bindParam(':comments', $comments);
  $stmt_update->bindParam(':id_pcba', $id_pcba, PDO::PARAM_INT);
  $stmt_update->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);

  if ($stmt_update->execute()) {
    $_SESSION['success_message'] = 'PCBA actualizado correctamente.';
  } else {
    $_SESSION['error_message'] = 'Error al actualizar el PCBA.';
  }


  header('Location: molexpcba.php');
  exit();
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
  <script src="../../assets/vendor/js/helpers.js"></script>
  <script src="../../assets/js/config.js"></script>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/style_pcba_block_button.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link rel="shortcut icon" href="../ico/comter.png" type="image/x-icon">

  <title>Molex PCBA</title>
</head>

<body>



  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">


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
                <!-- <form method="GET" action="search_molex_pcba.php" class="d-flex align-items-center">
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
            <h3 class="text-center mt-3">Editar PCBA</h3>
            <form action="edit_record_pcba.php?id_pcba=<?php echo $id_pcba; ?>" method="post">
              <?php
              $currentDate = date("Y-m-d");
              ?>

              <div class="mb-3">
                <label for="inspection_date" class="form-label">INSPECTION DATE</label>
                <input type="date" class="form-control" id="inspection_date" name="inspection_date"
                  value="<?php echo $inspection_date ?? $currentDate; ?>">
              </div>

              <div class="mb-3">
                <label for="description" class="form-label">DESCRIPTION</label>
                <textarea class="form-control" id="description"
                  name="description"><?php echo htmlspecialchars($description ?? ''); ?></textarea>
              </div>

              <div class="mb-3">
                <label for="shift" class="form-label">SHIFT</label>
                <input type="text" class="form-control" id="shift" name="shift"
                  value="<?php echo $shift ?? 'No asignado'; ?>" readonly>
              </div>

              <div class="mb-3">
                <label for="operators" class="form-label">OPERATORS</label>
                <textarea class="form-control" id="operators"
                  name="operators"><?php echo htmlspecialchars($operators ?? ''); ?></textarea>
              </div>

              <div class="mb-3">
                <label for="goods" class="form-label">GOODS</label>
                <select class="form-control" id="goods" name="goods" required>
                  <?php for ($i = 0; $i <= 7000; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($goods == $i) ? 'selected' : ''; ?>><?php echo $i; ?>
                    </option>
                  <?php endfor; ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="fails_dedos_oro" class="form-label">DEDOS DE ORO CONTAMINADOS</label>
                <select class="form-control" id="fails_dedos_oro" name="fails_dedos_oro" required>
                  <?php for ($i = 0; $i <= 7000; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($fails_dedos_oro == $i) ? 'selected' : ''; ?>>
                      <?php echo $i; ?></option>
                  <?php endfor; ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="fails_mal_corte" class="form-label">MAL CORTE</label>
                <select class="form-control" id="fails_mal_corte" name="fails_mal_corte" required>
                  <?php for ($i = 0; $i <= 7000; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($fails_mal_corte == $i) ? 'selected' : ''; ?>>
                      <?php echo $i; ?></option>
                  <?php endfor; ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="fails_contaminacion" class="form-label">CONTAMINACION</label>
                <select class="form-control" id="fails_contaminacion" name="fails_contaminacion" required>
                  <?php for ($i = 0; $i <= 7000; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($fails_contaminacion == $i) ? 'selected' : ''; ?>>
                      <?php echo $i; ?></option>
                  <?php endfor; ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="pd" class="form-label">PD</label>
                <select class="form-control" id="pd" name="pd" required>
                  <?php for ($i = 0; $i <= 7000; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($pd == $i) ? 'selected' : ''; ?>><?php echo $i; ?>
                    </option>
                  <?php endfor; ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="fails_desplazados" class="form-label">DESPLAZADOS</label>
                <select class="form-control" id="fails_desplazados" name="fails_desplazados" required>
                  <?php for ($i = 0; $i <= 7000; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($fails_desplazados == $i) ? 'selected' : ''; ?>>
                      <?php echo $i; ?></option>
                  <?php endfor; ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="fails_insuficiencias" class="form-label">INSUFICIENCIAS</label>
                <select class="form-control" id="fails_insuficiencias" name="fails_insuficiencias" required>
                  <?php for ($i = 0; $i <= 7000; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($fails_insuficiencias == $i) ? 'selected' : ''; ?>>
                      <?php echo $i; ?></option>
                  <?php endfor; ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="fails_despanelizados" class="form-label">DESPANELIZADOS</label>
                <select class="form-control" id="fails_despanelizados" name="fails_despanelizados" required>
                  <?php for ($i = 0; $i <= 7000; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($fails_despanelizados == $i) ? 'selected' : ''; ?>>
                      <?php echo $i; ?></option>
                  <?php endfor; ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="fails_desprendidos" class="form-label">DESPRENDIDOS</label>
                <select class="form-control" id="fails_desprendidos" name="fails_desprendidos" required>
                  <?php for ($i = 0; $i <= 7000; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($fails_desprendidos == $i) ? 'selected' : ''; ?>>
                      <?php echo $i; ?></option>
                  <?php endfor; ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="total_fails" class="form-label">Total</label>
                <input type="number" class="form-control" id="total_fails" name="total_fails"
                  value="<?php echo $total_fails ?? ''; ?>" required readonly>
              </div>

              <div class="mb-3">
                <label for="total" class="form-label">Total</label>
                <input type="number" class="form-control" id="total" name="total" value="<?php echo $total ?? ''; ?>"
                  required readonly>
              </div>

              <div class="mb-3">
                <label for="yield" class="form-label">Yield (%)</label>
                <input type="text" class="form-control" id="yield" name="yield" value="<?php echo $yield ?? ''; ?>"
                  required readonly>
              </div>

              <div class="mb-3">
                <label for="comments" class="form-label">Comments</label>
                <textarea class="form-control" id="comments"
                  name="comments"><?php echo htmlspecialchars($comments ?? ''); ?></textarea>
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


      const failsDedosOro = parseFloat(document.getElementById('fails_dedos_oro').value) || 0;
      const failsMalCorte = parseFloat(document.getElementById('fails_mal_corte').value) || 0;
      const failsContaminacion = parseFloat(document.getElementById('fails_contaminacion').value) || 0;
      const pd = parseFloat(document.getElementById('pd').value) || 0;
      const failsDesplazados = parseFloat(document.getElementById('fails_desplazados').value) || 0;
      const failsInsuficiencias = parseFloat(document.getElementById('fails_insuficiencias').value) || 0;
      const failsDespanelizados = parseFloat(document.getElementById('fails_despanelizados').value) || 0;
      const failsDesprendidos = parseFloat(document.getElementById('fails_desprendidos').value) || 0;


      const goods = parseFloat(document.getElementById('goods').value) || 0;


      const totalFails = failsDedosOro + failsMalCorte + failsContaminacion + pd + failsDesplazados + failsInsuficiencias + failsDespanelizados + failsDesprendidos;


      document.getElementById('total_fails').value = totalFails;


      const totalInspected = goods + totalFails;


      let yieldPercentage = 0;
      if (totalInspected > 0) {
        yieldPercentage = Math.round((goods / totalInspected) * 100);
      }


      document.getElementById('total').value = totalInspected;
      document.getElementById('yield').value = yieldPercentage + '%';
    }


    document.getElementById('fails_dedos_oro').addEventListener('input', updateTotalInspectedAndYield);
    document.getElementById('fails_mal_corte').addEventListener('input', updateTotalInspectedAndYield);
    document.getElementById('fails_contaminacion').addEventListener('input', updateTotalInspectedAndYield);
    document.getElementById('pd').addEventListener('input', updateTotalInspectedAndYield);
    document.getElementById('fails_desplazados').addEventListener('input', updateTotalInspectedAndYield);
    document.getElementById('fails_insuficiencias').addEventListener('input', updateTotalInspectedAndYield);
    document.getElementById('fails_despanelizados').addEventListener('input', updateTotalInspectedAndYield);
    document.getElementById('fails_desprendidos').addEventListener('input', updateTotalInspectedAndYield);
    document.getElementById('goods').addEventListener('input', updateTotalInspectedAndYield);


    updateTotalInspectedAndYield();
  </script>





  <script>
    document.addEventListener('DOMContentLoaded', function () {

      const puedeEditar = <?php echo json_encode($puedeEditar); ?>;
      const puedeCapturar = <?php echo json_encode($puedeCapturar); ?>;


      const addRecordBtn = document.querySelector('.add');
      if (!puedeCapturar && addRecordBtn) {
        addRecordBtn.classList.add('disabled-btn');
      }


      const editBtns = document.querySelectorAll('.edit');
      editBtns.forEach(function (editBtn) {
        if (!puedeEditar) {
          editBtn.classList.add('disabled-btn');
        }
      });
    });

  </script>






</body>

</html>