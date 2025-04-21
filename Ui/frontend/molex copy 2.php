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
                            <i class="fas fa-file-alt"></i>
                            Reportes
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

            <div class="col-lg-10 main-content">
                <div class="container-fluid px-4">
                <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Selección de Semana</h5>
                    <?php
                    // Verificar si la semana ya existe
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

                    if(isset($_SESSION['error_molex_admin'])) {
                        echo '<div class="alert alert-danger mb-3">' . $_SESSION['error_molex_admin'] . '</div>';
                        unset($_SESSION['error_molex_admin']);
                    }
                    if(isset($_SESSION['success_molex_admin'])) {
                        echo '<div class="alert alert-success mb-3">' . $_SESSION['success_molex_admin'] . '</div>';
                        unset($_SESSION['success_molex_admin']);
                    }
                    ?>
                    <form action="molex.php" method="POST" class="row g-3" id="weekForm" onsubmit="return validateForm()">
                        <div class="col-md-6 mb-4">
                            <label for="weekSelect" class="form-label">Seleccione la Semana</label>
                            <select class="form-select" id="weekSelect" name="nombre_version" onchange="checkWeekExists(this.value)">
                                <option value="">Seleccione una semana</option>
                                <?php
                                for($i = 1; $i <= 1000; $i++) {
                                    $semana = "sem" . $i;
                                    $selected = ($semana == "sem34") ? "selected" : "";
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
                    </form>

                    <!-- Tabla Editable -->
                    <div id="tablaEditable" class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Tabla de Datos</h5>
                            <div>
                                <button type="button" class="btn btn-primary btn-sm me-2" id="agregarFila">
                                    <i class="bx bx-plus"></i> Agregar Fila
                                </button>
                                <button type="button" class="btn btn-success btn-sm" id="guardarDatos">
                                    <i class="bx bx-save"></i> Guardar Datos
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm custom-table" id="editableTable">
                                <thead>
                                    <tr id="editableHeaderRow">
                                        <!-- Los encabezados se generarán dinámicamente -->
                                    </tr>
                                </thead>
                                <tbody id="editableTableBody">
                                    <!-- Las filas se generarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            if(isset($_POST['guardar_semana'])) {
                $nombre_version = $_POST['nombre_version'];
                
                if(empty($nombre_version)) {
                    $_SESSION['error_molex_admin'] = "Por favor seleccione una semana";
                    header("Location: molex.php");
                    exit();
                }
                
                try {
                    // Verificar si la semana ya existe
                    $stmt = $con->prepare("SELECT id_version FROM versiones_inspeccion WHERE nombre_version = ?");
                    $stmt->execute([$nombre_version]);
                    
                    if($stmt->rowCount() > 0) {
                        // La semana ya existe, mostrar mensaje informativo
                        echo '<div class="alert alert-info mt-3">
                            <i class="bx bx-info-circle me-1"></i>
                            La semana ya existe en la base de datos. Los registros que agregue se enlazarán a esta semana.
                        </div>';
                    } else {
                        // La semana no existe, crearla
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
         </div>

         <script>
         // Objeto con las traducciones de los campos
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
             
             // Limpiar encabezados existentes
             headerRow.innerHTML = '';

             // Agregar columnas base
             const baseColumns = ['INSPECTION DATE', 'OPERATORS', 'DESCRIPCIÓN'];
             baseColumns.forEach(col => {
                 headerRow.innerHTML += `<th>${col}</th>`;
             });

             // Agregar columnas de campos (ahora fijas en lugar de basadas en checkboxes)
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

             // Agregar columnas finales
             const finalColumns = ['GOODS', 'TOTAL', 'TOTAL FINAL', 'COMMENTS', 'ACCIONES'];
             finalColumns.forEach(col => {
                 headerRow.innerHTML += `<th>${col}</th>`;
             });
         }

         function updatePreviewRow() {
             const tbody = document.querySelector('#previewTable tbody');
             const tr = tbody.querySelector('tr') || tbody.insertRow();
             tr.innerHTML = ''; // Limpiar la fila

             // Agregar celdas base
             tr.insertCell().textContent = '2025-03-26';
             tr.insertCell().textContent = 'Operador 1';
             tr.insertCell().textContent = 'Descripción de ejemplo';

             // Agregar celdas para campos seleccionados
             document.querySelectorAll('.campo-selector:checked').forEach(() => {
                 tr.insertCell().textContent = '0';
             });

             // Agregar celdas finales
             tr.insertCell().textContent = '0'; // GOODS
             tr.insertCell().textContent = '0'; // TOTAL
             tr.insertCell().textContent = '0'; // TOTAL FINAL
             tr.insertCell().textContent = '-'; // COMMENTS
             tr.insertCell().textContent = '-'; // ACCIONES
         }

         // Event Listeners
         document.addEventListener('DOMContentLoaded', function() {
             // Inicializar la vista previa de la tabla
             updateTablePreview();

             // Recuperar la semana de la URL si existe
             const urlParams = new URLSearchParams(window.location.search);
             const semanaFromUrl = urlParams.get('semana');
             if (semanaFromUrl) {
                 const weekSelect = document.getElementById('weekSelect');
                 weekSelect.value = semanaFromUrl;
                 selectedWeek = semanaFromUrl;
             } else {
                 // Establecer sem34 como predeterminado si no hay semana en la URL
                 const weekSelect = document.getElementById('weekSelect');
                 weekSelect.value = 'sem34';
             }

             // Agregar listeners a los checkboxes
             document.querySelectorAll('.campo-selector').forEach(checkbox => {
                 checkbox.addEventListener('change', updateTablePreview);
             });

             // Listener para el select de semana
             document.getElementById('weekSelect').addEventListener('change', function() {
                 selectedWeek = this.value;
                 if (this.value) {
                     this.classList.remove('is-invalid');
                     document.getElementById('weekError').style.display = 'none';
                     checkWeekExists(this.value);
                 }
             });

             // Cargar la tabla automáticamente
             const tbody = document.getElementById('editableTableBody');
             tbody.innerHTML = ''; // Limpiar tabla existente
             tbody.appendChild(createEditableRow());

             // Listener para el botón de agregar fila
             document.getElementById('agregarFila').addEventListener('click', function() {
                 document.getElementById('editableTableBody').appendChild(createEditableRow());
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
                 } else if (col.textContent === 'TOTAL' || col.textContent === 'TOTAL FINAL' || col.textContent === 'GOODS') {
                     const input = document.createElement('input');
                     input.type = 'number';
                     input.className = 'form-control form-control-sm';
                     input.readOnly = true;
                     input.value = '0';
                     td.appendChild(input);
                 } else {
                     // Create select for numeric fields
                     const select = document.createElement('select');
                     select.className = 'form-control form-control-sm';
                     
                     // Add options from 0 to 7000
                     for (let i = 0; i <= 7000; i++) {
                         const option = document.createElement('option');
                         option.value = i;
                         option.textContent = i;
                         select.appendChild(option);
                     }
                     
                     // Add change event listener for calculations
                     select.addEventListener('change', function() {
                         updateTotals(tr);
                     });
                     
                     td.appendChild(select);
                 }
                 
                 tr.appendChild(td);
             });

             // Agregar botón de eliminar
             const tdActions = document.createElement('td');
             tdActions.className = 'text-center';
             tdActions.innerHTML = `
                 <button type="button" class="btn btn-danger btn-sm delete-row" style="margin: 0; padding: 4px;">
                     <i class="fas fa-trash" style="font-size: 18px; display: inline-block;"></i>
                 </button>
             `;
             tr.appendChild(tdActions);
             
             return tr;
         }

         function updateTotals(row) {
             // Define the fields to sum for total
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

             // Calculate total (sum of all defect fields)
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

             // Get turn inputs and calculate GOODS
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

             // Update goods, total and total final
             const goodsInput = row.querySelector('td:nth-last-child(4) input'); // GOODS
             const totalInput = row.querySelector('td:nth-last-child(3) input'); // TOTAL
             const totalFinalInput = row.querySelector('td:nth-last-child(2) input'); // TOTAL FINAL
             
             if (goodsInput && totalInput && totalFinalInput) {
                 goodsInput.value = goodsTotal;  // GOODS shows sum of turns
                 totalInput.value = defectsTotal;  // TOTAL shows defects
                 totalFinalInput.value = defectsTotal;  // TOTAL FINAL shows defects
             }
         }

         // Variable global para almacenar la semana seleccionada
         let selectedWeek = 'sem34';

         document.addEventListener('DOMContentLoaded', function() {
             // Inicializar la tabla
             updateTablePreview();

             // Recuperar la semana de la URL si existe
             const urlParams = new URLSearchParams(window.location.search);
             const semanaFromUrl = urlParams.get('semana');
             if (semanaFromUrl) {
                 const weekSelect = document.getElementById('weekSelect');
                 weekSelect.value = semanaFromUrl;
                 selectedWeek = semanaFromUrl;
             } else {
                 // Establecer sem34 como predeterminado si no hay semana en la URL
                 const weekSelect = document.getElementById('weekSelect');
                 weekSelect.value = 'sem34';
             }

             // Listener para el select de semana
             document.getElementById('weekSelect').addEventListener('change', function() {
                 selectedWeek = this.value;
                 if (this.value) {
                     this.classList.remove('is-invalid');
                     document.getElementById('weekError').style.display = 'none';
                     checkWeekExists(this.value);
                 }
             });

             // Cargar la tabla automáticamente
             const tbody = document.getElementById('editableTableBody');
             tbody.innerHTML = ''; // Limpiar tabla existente
             tbody.appendChild(createEditableRow());

             // Listener para el botón de agregar fila
             document.getElementById('agregarFila').addEventListener('click', function() {
                 document.getElementById('editableTableBody').appendChild(createEditableRow());
             });

             // Listener para el botón de guardar datos
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

                 // Recopilar datos de todas las filas
                 const datos = [];
                 rows.forEach(row => {
                     const inputs = row.querySelectorAll('input, select, textarea');
                     const rowData = {};
                     let isRowEmpty = true;

                     // Mapear los valores de los inputs a los nombres de columnas
                     inputs.forEach((input, index) => {
                         const headerText = document.querySelectorAll('#editableHeaderRow th')[index].textContent;
                         const value = input.value.trim();
                         
                         if (value !== '' && value !== '0') {
                             isRowEmpty = false;
                         }

                         // Asignar valores según el tipo de campo
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
                                 // Convertir el encabezado a nombre de campo de base de datos
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

                 console.log('Datos a enviar:', datos); // Para depuración

                 // Mostrar indicador de carga
                 Swal.fire({
                     title: 'Guardando datos...',
                     text: 'Por favor espere',
                     allowOutsideClick: false,
                     didOpen: () => {
                         Swal.showLoading();
                     }
                 });

                 // Enviar datos al servidor
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
                             // Limpiar la tabla después de guardar exitosamente
                             document.getElementById('editableTableBody').innerHTML = '';
                             // Agregar una nueva fila vacía
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

         // Limpiar error cuando el usuario selecciona una opción
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
<script>
function cerrarModalCliente() {
    location.reload();  

   
}
</script>
</div>
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



    <footer class="content-footer footer" style="background-color:#edebea">
        <div class="container-xxl d-flex flex-wrap justify-content-center py-2 flex-md-row flex-column text-center"
            style="color:#838383;">
            <div class="mb-2 mb-md-0 fw-bolder">
                &copy;
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
        function confirmarEliminacion(id_respaldo) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Este respaldo se eliminará permanentemente y no podrá ser recuperado!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {

                    window.location.href = '../backend/eliminar_respaldo.php?id_respaldo=' + id_respaldo;
                }
            });
        }
    </script>

    <script>
        document.getElementById('respaldo_automatico').addEventListener('change', function () {
            var checkboxes = document.querySelectorAll('input[name="tablas[]"]');
            var tipoRespaldo = document.getElementById('tipo_respaldo');

            if (this.value == "1") {
                checkboxes.forEach(function (checkbox) {
                    checkbox.checked = true;
                });

                tipoRespaldo.value = "Completo";
            } else {
                checkboxes.forEach(function (checkbox) {
                    checkbox.checked = false;
                });

                tipoRespaldo.value = "Individual";
            }
        });
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

<script>
   /* const form = document.getElementById('formNuevoRegistro');
    const guardarBtn = document.getElementById('guardarBtn');

    function checkFormCompletion() {
        const inputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], input[type="password"], input[type="time"], select');
        let allFilled = true;

        inputs.forEach(input => {
            if (input.type !== 'checkbox' && input.value.trim() === '') {
                allFilled = false;
            }
        });

        guardarBtn.disabled = !allFilled;
    }

    form.addEventListener('input', checkFormCompletion);
    form.addEventListener('change', checkFormCompletion);*/
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