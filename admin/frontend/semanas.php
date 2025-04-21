<?php
session_start();
require '../../config/connection.php';

// Verificar si la sesión tiene el id_proveedor
if (!isset($_SESSION['id_proveedor'])) {
  echo 'La sesión no tiene el id_proveedor.';
  exit();
}

// Obtener el id_proveedor de la sesión
$id_proveedor = $_SESSION['id_proveedor'];

// Conectar a la base de datos
$db = new Database();
$con = $db->conectar();

// Preparar la consulta para obtener los datos del proveedor
$sql = "SELECT nombre, apellido, compania, business_unit, telefono, correo, role, puesto, created_at, updated_at
        FROM proveedores 
        WHERE id_proveedor = :id_proveedor";
$stmt = $con->prepare($sql);
$stmt->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
$stmt->execute();

// Verificar si se obtuvieron resultados
if ($stmt->rowCount() > 0) {
  // Obtener los datos del proveedor
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  // Asignar los datos del proveedor a variables
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

  // Asignar la ruta de la foto del proveedor
  $photo = '../../assets/img/avatars/1.png';
} else {
  echo 'Proveedor no encontrado o cuenta no válida.';
  exit();
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
  <link rel="stylesheet" href="../../assets/vendor/fonts/boxicons.css" />
  <link rel="stylesheet" href="../../assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../../assets/css/demo.css" />
  <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../../assets/vendor/libs/apex-charts/apex-charts.css" />
  <link rel="stylesheet" href="css/styles.css">

  <script src="../../assets/vendor/js/helpers.js"></script>
  <script src="../../assets/js/config.js"></script>
  <link rel="shortcut icon" href="../../ico/comter.png" type="image/x-icon">
 
  <title>Proveedor</title>
</head>

<body>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">


    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="../admin_panel.php" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="../../ico/comter.png" alt="" width="50">
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

        <a href="javascript:void(0);" class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <i class="bx bx-menu bx-sm"></i>
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
<li class="nav-item mt-1 w-100">
    <form action="semanas.php" method="POST" id="weekForm" onsubmit="return validateForm()" style="margin-bottom: 0;">
        <div style="margin-bottom: 8px;">
            <label for="weekSelect" class="form-label" style="color: #fff; margin-bottom: 4px; display: block;">Seleccione la Semana</label>
            <select class="form-select" id="weekSelect" name="nombre_version" onchange="checkWeekExists(this.value)" style="color: #333; width: 100%; margin-bottom: 8px;">
                <option value="" style="color: #333;">Seleccione una semana</option>
                <?php
               
                $selectedWeek = 'sem34'; 
                
                if (isset($_POST['nombre_version']) && !empty($_POST['nombre_version'])) {
                    $selectedWeek = $_POST['nombre_version'];
                } elseif (isset($_GET['semana']) && !empty($_GET['semana'])) {
                    $selectedWeek = $_GET['semana'];
                }
                
                for($i = 1; $i <= 1000; $i++) {
                    $semana = "sem" . $i;
                    $selected = ($semana == $selectedWeek) ? "selected" : "";
                    echo "<option value='$semana' $selected style='color: #333;'>Semana $i</option>";
                }
                ?>
            </select>
            <div id="weekError" class="invalid-feedback">Campo requerido</div>
        </div>
    
        <input type="hidden" id="targetTable" name="targetTable" value="">
    
        <button type="submit" name="guardar_semana" class="btn btn-primary w-100" style="color: #fff;"><i class="bx bx-save"></i> Guardar Semana</button>
    </form>
    </li>
    <li class="nav-item mt-1 w-100" >
<button type="button" class="btn btn-primary w-100" id="agregarFila" style="color: #fff;">
    <i class="bx bx-plus"></i> Agregar Fila
</button>
</li>
<li class="nav-item mt-1 w-100">
<button type="button" class="btn btn-success w-100" id="guardarDatos" style="color: #fff;">
    <i class="bx bxs-save"></i> Guardar Datos
</button>
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


         <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
            <div class="card-body">
                    <!--<h5 class="card-title">Selección de Semana</h5>-->
                    <?php
                    
                    if(isset($_GET['semana'])) {
                        $semana = $_GET['semana'];
                        $stmt = $con->prepare("SELECT id_version FROM versiones_inspeccion WHERE nombre_version = ?");
                        $stmt->execute([$semana]);
                        if($stmt->rowCount() > 0) {
                            echo '<div class="alert alert-info mb-4">
                                <i class="bx bx-info-circle me-1"></i>
                                La semana ya existe en la base de datos. Los registros que agregue se enlazarán a esta semana.
                            </div>';
                        }
                    }

                    if(isset($_SESSION['error_molex_sem'])) {
                        echo '<div class="alert alert-danger mb-3">' . $_SESSION['error_molex_sem'] . '</div>';
                        unset($_SESSION['error_molex_sem']);
                    }
                    if(isset($_SESSION['success_molex_sem'])) {
                        echo '<div class="alert alert-success mb-3">' . $_SESSION['success_molex_sem'] . '</div>';
                        unset($_SESSION['success_molex_sem']);
                    }
                    ?>
                    
            <?php
            if(isset($_POST['guardar_semana'])) {
                $nombre_version = $_POST['nombre_version'];
                
                if(empty($nombre_version)) {
                    $_SESSION['error_molex_sem'] = "Por favor seleccione una semana";
                    header("Location: semanas.php");
                    exit();
                }
                
                try {
                   
                    $stmt = $con->prepare("SELECT id_version FROM versiones_inspeccion WHERE nombre_version = ?");
                    $stmt->execute([$nombre_version]);
                    
                    if($stmt->rowCount() > 0) {
                       
                        echo '<div class="alert alert-info mt-3">
                            <i class="bx bx-info-circle me-1"></i>
                            La semana ya existe en la base de datos. Los registros que agregue se enlazarán a esta semana.
                        </div>';
                    } else {
                      
                        $stmt = $con->prepare("INSERT INTO versiones_inspeccion (nombre_version) VALUES (?)");
                        $stmt->execute([$nombre_version]);
                        echo '<div class="alert alert-success mt-3">
                            <i class="bx bx-check-circle me-1"></i>
                            Semana guardada correctamente
                        </div>';
                    }
                } catch(PDOException $e) {
                    echo '<div class="alert alert-danger mt-3">
                        <i class="bx bx-error-circle me-1"></i>
                        Error al procesar la semana: ' . $e->getMessage() . '
                    </div>';
                }
            }
            ?>
                    <!--<form action="molex.php" method="POST" class="row g-3" id="weekForm" onsubmit="return validateForm()">
                        <div class="col-md-6 mb-4">
                            <label for="weekSelect" class="form-label">Seleccione la Semana</label>
                            <select class="form-select" id="weekSelect" name="nombre_version" onchange="checkWeekExists(this.value)">
                                <option value="">Seleccione una semana</option>
                                <?php
                               
                                $selectedWeek = 'sem34'; 
                                
                                if (isset($_POST['nombre_version']) && !empty($_POST['nombre_version'])) {
                                    $selectedWeek = $_POST['nombre_version'];
                                } elseif (isset($_GET['semana']) && !empty($_GET['semana'])) {
                                    $selectedWeek = $_GET['semana'];
                                }
                                
                                for($i = 1; $i <= 1000; $i++) {
                                    $semana = "sem" . $i;
                                    $selected = ($semana == $selectedWeek) ? "selected" : "";
                                    echo "<option value='$semana' $selected>Semana $i</option>";
                                }
                                ?>
                            </select>
                            <div id="weekError" class="invalid-feedback">Campo requerido</div>
                        </div>

                        <input type="hidden" id="targetTable" name="targetTable" value="">

                        <div class="col-12 mt-4">
                            <button type="submit" name="guardar_semana" class="btn btn-primary">Guardar Semana</button>
                        </div>
                    </form>-->

                   
                    <div id="tablaEditable" class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Tabla de Datos</h5>
                            <div>
                                <!--<button type="button" class="btn btn-primary btn-sm me-2" id="agregarFila">
                                    <i class="bx bx-plus"></i> Agregar Fila
                                </button>
                                <button type="button" class="btn btn-success btn-sm" id="guardarDatos">
                                    <i class="bx bx-save"></i> Guardar Datos
                                </button>-->
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm custom-table" id="editableTable">
                                <thead>
                                    <tr id="editableHeaderRow">
                                      
                                    </tr>
                                </thead>
                                <tbody id="editableTableBody">
                                   
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

         </div>

         <script>
    
         const fieldTranslations = {
             'primer_t': '1ER T',
             'segundo_t': '2DO T',
             'tercer_t': '3ER T',
             'goods': 'GOODS',
             'coupler': 'COUPLER',
             'dano_end_face': 'DAÑO END FACE',
             'golpe_top': 'GOLPE TOP',
             'rebaba': 'REBABA',
             'dano_en_lente': 'DAÑO EN LENTE',
             'fuera_de_spc': 'FUERA DE SPC',
             'dano_fisico': 'DAÑO FÍSICO',
             'wirebond_corto': 'WIREBOND CORTO',
             'wirebond_chueco': 'WIREBOND CHUECO',
             'fisura': 'FISURA',
             'silicon_contaminacion': 'SILICON CONTAMINACIÓN',
             'contaminacion_end_face': 'CONTAMINACIÓN END FACE'
         };

         function updateTablePreview() {
             const headerRow = document.getElementById('editableHeaderRow');
             
          
             headerRow.innerHTML = '';

           
             const baseColumns = ['INSPECTION DATE', 'OPERATORS', 'DESCRIPCIÓN'];
             baseColumns.forEach(col => {
                 headerRow.innerHTML += `<th>${col}</th>`;
             });

           
             const fixedFields = [
                 '1ER T', '2DO T', '3ER T', 'GOODS', 'COUPLER',
                 'DAÑO END FACE', 'GOLPE TOP', 'REBABA', 'DAÑO EN LENTE', 
                 'FUERA DE SPC', 'DAÑO FÍSICO', 'WIREBOND CORTO', 
                 'WIREBOND CHUECO', 'FISURA', 'SILICON CONTAMINACIÓN', 
                 'CONTAMINACIÓN END FACE'
             ];
             
             fixedFields.forEach(field => {
                 headerRow.innerHTML += `<th>${field}</th>`;
             });

             
             const finalColumns = ['TOTAL', 'TOTAL FINAL', 'COMMENTS', 'ACCIONES'];
             finalColumns.forEach(col => {
                 headerRow.innerHTML += `<th>${col}</th>`;
             });
         }

         function updatePreviewRow() {
             const tbody = document.querySelector('#previewTable tbody');
             const tr = tbody.querySelector('tr') || tbody.insertRow();
             tr.innerHTML = '';

           
             tr.insertCell().textContent = '2025-03-26';
             tr.insertCell().textContent = 'Operador 1';
             tr.insertCell().textContent = 'Descripción de ejemplo';

            
             document.querySelectorAll('.campo-selector:checked').forEach(() => {
                 tr.insertCell().textContent = '0';
             });

           
             tr.insertCell().textContent = '0'; 
             tr.insertCell().textContent = '0'; 
             tr.insertCell().textContent = '0'; 
             tr.insertCell().textContent = '-'; 
             tr.insertCell().textContent = '-'; 
         }

        
         document.addEventListener('DOMContentLoaded', function() {
            
             updateTablePreview();

          
             const urlParams = new URLSearchParams(window.location.search);
             const semanaFromUrl = urlParams.get('semana');
             const weekSelect = document.getElementById('weekSelect');
             
           
             if (semanaFromUrl) {
                 weekSelect.value = semanaFromUrl;
                 selectedWeek = semanaFromUrl;
             } else {
               
                 selectedWeek = weekSelect.value;
             }
             
          
             if (!selectedWeek) {
                 selectedWeek = 'sem34';
                 weekSelect.value = 'sem34';
             }

           
             weekSelect.addEventListener('change', function() {
                 selectedWeek = this.value;
                 if (this.value) {
                     this.classList.remove('is-invalid');
                     document.getElementById('weekError').style.display = 'none';
                     checkWeekExists(this.value);
                 }
             });

           
             const tbody = document.getElementById('editableTableBody');
             tbody.innerHTML = ''; 
             tbody.appendChild(createEditableRow());

            
             document.getElementById('agregarFila').addEventListener('click', function() {
                 document.getElementById('editableTableBody').appendChild(createEditableRow());
             });
             
           
             document.getElementById('editableTable').addEventListener('click', function(e) {
                 const target = e.target;
                
                 if (target.classList.contains('delete-row') || target.closest('.delete-row')) {
                     const row = target.closest('tr');
                     if (row) {
                         Swal.fire({
                             title: '¿Está seguro?',
                             text: "Esta acción eliminará la fila de la tabla",
                             icon: 'warning',
                             showCancelButton: true,
                             confirmButtonColor: '#3085d6',
                             cancelButtonColor: '#d33',
                             confirmButtonText: 'Sí, eliminar',
                             cancelButtonText: 'Cancelar'
                         }).then((result) => {
                             if (result.isConfirmed) {
                                 row.remove();
                                
                                 updateAllTotals();
                             }
                         });
                     }
                 }
             });
         });

         function createEditableRow() {
             const tr = document.createElement('tr');
             const columns = document.querySelectorAll('#editableHeaderRow th');
             
             columns.forEach((col, index) => {
                 const td = document.createElement('td');
                 
                 if (col.textContent === 'INSPECTION DATE') {
                     const input = document.createElement('input');
                     input.type = 'date';
                     input.className = 'form-control form-control-sm';
                     input.value = new Date().toISOString().split('T')[0];
                     td.appendChild(input);
                 } else if (col.textContent === 'OPERATORS') {
                     const input = document.createElement('input');
                     input.type = 'text';
                     input.className = 'form-control form-control-sm';
                     input.value = '<?php echo $nombre . " " . $apellido; ?>';
                     td.appendChild(input);
                 } else if (col.textContent === 'DESCRIPCIÓN') {
                     const input = document.createElement('input');
                     input.type = 'text';
                     input.className = 'form-control form-control-sm';
                     td.appendChild(input);
                 } else if (col.textContent === 'COMMENTS') {
                     const textarea = document.createElement('textarea');
                     textarea.className = 'form-control form-control-sm';
                     textarea.rows = 1;
                     textarea.placeholder = 'Comentarios...';
                     td.appendChild(textarea);
                 } else if (col.textContent === 'GOODS') {
                    
                     const input = document.createElement('input');
                     input.type = 'number';
                     input.className = 'form-control form-control-sm goods-field';
                     input.readOnly = true;
                     input.value = '0';
                     input.setAttribute('data-calculated', 'true');
                     td.appendChild(input);
                 } else if (col.textContent === 'TOTAL' || col.textContent === 'TOTAL FINAL') {
                     const input = document.createElement('input');
                     input.type = 'number';
                     input.className = 'form-control form-control-sm';
                     input.readOnly = true;
                     input.value = '0';
                     td.appendChild(input);
                 } else if (col.textContent === '1ER T' || col.textContent === '2DO T' || col.textContent === '3ER T') {
                    
                     const select = document.createElement('select');
                     select.className = 'form-control form-control-sm turno-field';
                     select.setAttribute('data-turno', col.textContent);
                     
                     
                     for (let i = 0; i <= 7000; i++) {
                         const option = document.createElement('option');
                         option.value = i;
                         option.textContent = i;
                         select.appendChild(option);
                     }
                     
                    
                     select.addEventListener('change', function() {
                         updateTurnTotals(tr);
                         updateTotals(tr);
                     });
                     
                     td.appendChild(select);
                 } else if (col.textContent === 'ACCIONES') {
                    
                     td.className = 'text-center';
                     td.innerHTML = `
                         <button type="button" class="btn btn-danger btn-sm delete-row" style="margin: 0; padding: 4px;">
                             <i class="bx bx-trash" style="font-size: 18px; display: inline-block;"></i>
                         </button>
                     `;
                 } else {
                     
                     const select = document.createElement('select');
                     select.className = 'form-control form-control-sm';
                     
                    
                     for (let i = 0; i <= 7000; i++) {
                         const option = document.createElement('option');
                         option.value = i;
                         option.textContent = i;
                         select.appendChild(option);
                     }
                     
                    
                     select.addEventListener('change', function() {
                         updateTotals(tr);
                     });
                     
                     td.appendChild(select);
                 }
                 
                 tr.appendChild(td);
             });

             return tr;
         }

         function updateTurnTotals(row) {
            
             const turns = ['1ER T', '2DO T', '3ER T'];
             let goodsTotal = 0;
             
             turns.forEach(turn => {
                 const index = Array.from(row.parentElement.closest('table').querySelectorAll('th')).findIndex(th => th.textContent === turn);
                 if (index !== -1) {
                     const select = row.querySelector(`td:nth-child(${index + 1}) select`);
                     if (select) {
                         goodsTotal += parseInt(select.value) || 0;
                     }
                 }
             });
             
            
             const goodsIndex = Array.from(row.parentElement.closest('table').querySelectorAll('th')).findIndex(th => th.textContent === 'GOODS');
             if (goodsIndex !== -1) {
                 const goodsInput = row.querySelector(`td:nth-child(${goodsIndex + 1}) input`);
                 if (goodsInput) {
                     goodsInput.value = goodsTotal;
                 }
             }
             
             return goodsTotal; 
         }
         
         function updateTotals(row) {
        
             const fields = [
                 'COUPLER',
                 'DAÑO END FACE',
                 'GOLPE TOP',
                 'REBABA',
                 'DAÑO EN LENTE',
                 'FUERA DE SPC',
                 'DAÑO FÍSICO',
                 'WIREBOND CORTO',
                 'WIREBOND CHUECO',
                 'FISURA',
                 'SILICON CONTAMINACIÓN',
                 'CONTAMINACIÓN END FACE'
             ];

             
             let defectsTotal = 0;
             fields.forEach(fieldName => {
                 const index = Array.from(row.parentElement.closest('table').querySelectorAll('th')).findIndex(th => th.textContent === fieldName);
                 if (index !== -1) {
                     const select = row.querySelector(`td:nth-child(${index + 1}) select`);
                     if (select) {
                         defectsTotal += parseInt(select.value) || 0;
                     }
                 }
             });

           
             let goodsTotal = updateTurnTotals(row);

          
             const totalIndex = Array.from(row.parentElement.closest('table').querySelectorAll('th')).findIndex(th => th.textContent === 'TOTAL');
             const totalFinalIndex = Array.from(row.parentElement.closest('table').querySelectorAll('th')).findIndex(th => th.textContent === 'TOTAL FINAL');
             
             if (totalIndex !== -1 && totalFinalIndex !== -1) {
                 const totalInput = row.querySelector(`td:nth-child(${totalIndex + 1}) input`);
                 const totalFinalInput = row.querySelector(`td:nth-child(${totalFinalIndex + 1}) input`);
                 
                 if (totalInput && totalFinalInput) {
                   
                     totalInput.value = defectsTotal;
                     
                    
                     totalFinalInput.value = defectsTotal + goodsTotal;
                     
                   
                     console.log('Defectos total:', defectsTotal);
                     console.log('Goods total:', goodsTotal);
                     console.log('Total final:', defectsTotal + goodsTotal);
                 }
             }
         }

        
         function updateAllTotals() {
             const rows = document.querySelectorAll('#editableTableBody tr');
             rows.forEach(row => {
                 updateTurnTotals(row);
                 updateTotals(row);
             });
         }

        
         let selectedWeek = '';

         document.addEventListener('DOMContentLoaded', function() {
            
             updateTablePreview();

           
             const urlParams = new URLSearchParams(window.location.search);
             const semanaFromUrl = urlParams.get('semana');
             const weekSelect = document.getElementById('weekSelect');
             
          
             if (semanaFromUrl) {
                 weekSelect.value = semanaFromUrl;
                 selectedWeek = semanaFromUrl;
             } else {
                
                 selectedWeek = weekSelect.value;
             }
             
           
             if (!selectedWeek) {
                 selectedWeek = 'sem34';
                 weekSelect.value = 'sem34';
             }

           
             weekSelect.addEventListener('change', function() {
                 selectedWeek = this.value;
                 if (this.value) {
                     this.classList.remove('is-invalid');
                     document.getElementById('weekError').style.display = 'none';
                     checkWeekExists(this.value);
                 }
             });

            
             const tbody = document.getElementById('editableTableBody');
             tbody.innerHTML = ''; 
             tbody.appendChild(createEditableRow());

          
             document.getElementById('agregarFila').addEventListener('click', function() {
                 document.getElementById('editableTableBody').appendChild(createEditableRow());
             });

            
             document.getElementById('guardarDatos').addEventListener('click', function() {
                 const rows = document.querySelectorAll('#editableTableBody tr');
                 
                 if (!selectedWeek) {
                     Swal.fire({
                         icon: 'error',
                         title: 'Error',
                         text: 'Por favor seleccione una semana antes de guardar los datos'
                     });
                     return;
                 }

               
                 const datos = [];
                 rows.forEach(row => {
                     const inputs = row.querySelectorAll('input, select, textarea');
                     const rowData = {};
                     let isRowEmpty = true;

                   
                     inputs.forEach((input, index) => {
                         const headerText = document.querySelectorAll('#editableHeaderRow th')[index].textContent;
                         const value = input.value.trim();
                         
                         if (value !== '' && value !== '0') {
                             isRowEmpty = false;
                         }

                        
                         switch(headerText) {
                             case 'INSPECTION DATE':
                                 rowData.fecha = value || new Date().toISOString().split('T')[0];
                                 break;
                             case 'OPERATORS':
                                 rowData.operador = value || 'Operador';
                                 break;
                             case 'DESCRIPCIÓN':
                                 rowData.descripcion = value || '-';
                                 break;
                             case 'COMMENTS':
                                 rowData.comentarios = value || '-';
                                 break;
                             case 'TOTAL':
                                 rowData.total = parseInt(value) || 0;
                                 break;
                             case 'TOTAL FINAL':
                                 rowData.total_final = parseInt(value) || 0;
                                 break;
                             case 'GOODS':
                                 rowData.goods = parseInt(value) || 0;
                                 break;
                             default:
                                
                                 const fieldName = Object.entries(fieldTranslations).find(([key, val]) => 
                                     val === headerText
                                 )?.[0];
                                 if (fieldName) {
                                     rowData[fieldName] = parseInt(value) || 0;
                                 }
                         }
                     });

                     if (!isRowEmpty) {
                         rowData.semana = selectedWeek;
                         datos.push(rowData);
                     }
                 });

                 if (datos.length === 0) {
                     Swal.fire({
                         icon: 'warning',
                         title: 'Sin datos',
                         text: 'No hay datos para guardar'
                     });
                     return;
                 }

                 //console.log('Datos a enviar:', datos); 

                
                 Swal.fire({
                     title: 'Guardando datos...',
                     text: 'Por favor espere',
                     allowOutsideClick: false,
                     didOpen: () => {
                         Swal.showLoading();
                     }
                 });

                
                 fetch('guardar_datos.php', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json'
                     },
                     body: JSON.stringify(datos)
                 })
                 .then(response => response.json())
                 .then(data => {
                     if (data.success) {
                         Swal.fire({
                             icon: 'success',
                             title: 'Éxito',
                             text: data.message
                         }).then(() => {
                             
                             document.getElementById('editableTableBody').innerHTML = '';
                          
                             document.getElementById('editableTableBody').appendChild(createEditableRow());
                         });
                     } else {
                         throw new Error(data.message);
                     }
                 })
                 .catch(error => {
                     console.error('Error:', error);
                     Swal.fire({
                         icon: 'error',
                         title: 'Error',
                         text: 'Hubo un error al guardar los datos: ' + error.message
                     });
                 });
             });
         });

         function validateForm() {
             const weekSelect = document.getElementById('weekSelect');
             const weekError = document.getElementById('weekError');
             
             if (!weekSelect.value) {
                 weekSelect.classList.add('is-invalid');
                 weekError.style.display = 'block';
                 return false;
             }
             
             weekSelect.classList.remove('is-invalid');
             weekError.style.display = 'none';
             return true;
         }

        
         document.getElementById('weekSelect').addEventListener('change', function() {
             if (this.value) {
                 this.classList.remove('is-invalid');
                 document.getElementById('weekError').style.display = 'none';
                 checkWeekExists(this.value);
             }
         });
         </script>


         <style>
         .table-preview {
             background-color: white;
             padding: 1px;
         }
         .custom-table {
             border-collapse: collapse;
             width: 100%;
             margin-bottom: 0;
         }
         .custom-table th {
             background-color: #4a4a4a !important;
             color: white !important;
             font-weight: 500;
             font-size: 11px;
             padding: 8px 4px;
             text-align: center;
             vertical-align: middle;
             border: 1px solid #666;
             white-space: nowrap;
             min-width: 60px;
         }
         .custom-table td {
             font-size: 11px;
             padding: 4px;
             text-align: center;
             vertical-align: middle;
             border: 1px solid #dee2e6;
         }
         .custom-table tbody tr:nth-of-type(odd) {
             background-color: rgba(0,0,0,.02);
         }
         .table-responsive {
             overflow-x: auto;
             -webkit-overflow-scrolling: touch;
         }
         </style>
    <div class="layout-overlay layout-menu-toggle"></div>
  </div>


  <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../../assets/vendor/libs/popper/popper.js"></script>
  <script src="../../assets/vendor/js/bootstrap.js"></script>
  <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../../assets/vendor/js/menu.js"></script>
  <script src="../../assets/vendor/libs/apex-charts/apexcharts.js"></script>
  <script src="../../assets/js/main.js"></script>
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
          window.location.href = '../backend/home/logout.php';
        }
      });
    }
  </script>


</body>

</html>