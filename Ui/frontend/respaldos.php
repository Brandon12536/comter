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
    echo 'Proveedor no encontrado o cuenta no válida.';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipo_respaldo = $_POST['tipo_respaldo'];
    $respaldo_automatico = $_POST['respaldo_automatico'];
    $descripcion = $_POST['descripcion'];
    $tablas = isset($_POST['tablas']) ? $_POST['tablas'] : [];

    if (empty($tipo_respaldo) || empty($respaldo_automatico)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header('Location: respaldos.php');
        exit();
    }

    if (empty($descripcion)) {
        $descripcion = "Sin descripción";
    }


    $contenido_sql = "-- Respaldo de la base de datos - " . date('Y-m-d H:i:s') . "\n\n";

    if (!empty($tablas)) {
        foreach ($tablas as $tabla) {

            $sql_tabla = "SELECT * FROM $tabla";
            $stmt_tabla = $con->prepare($sql_tabla);
            $stmt_tabla->execute();
            $columnas = $stmt_tabla->columnCount();

            while ($row_tabla = $stmt_tabla->fetch(PDO::FETCH_ASSOC)) {
                $columnas_insert = array_keys($row_tabla);
                $valores_insert = array_map(function ($value) {
                    return "'" . addslashes($value) . "'";
                }, array_values($row_tabla));

                $contenido_sql .= "INSERT INTO $tabla (" . implode(", ", $columnas_insert) . ") VALUES (" . implode(", ", $valores_insert) . ");\n";
            }

            $contenido_sql .= "\n";
        }
    }


    $archivo = $contenido_sql;
    $tamano_archivo = strlen($archivo);

    try {
        $con->beginTransaction();

        $nombre_archivo = 'respaldo_' . date('Ymd_His') . '.sql';


        $sql_insert_respaldo = "INSERT INTO respaldos (tipo_respaldo, respaldo_automatico, descripcion, id_administrador, nombre_archivo, estado, ruta_archivo, tamano_archivo)
                               VALUES (:tipo_respaldo, :respaldo_automatico, :descripcion, :id_administrador, :nombre_archivo, :estado, :ruta_archivo, :tamano_archivo)";
        $stmt_respaldo = $con->prepare($sql_insert_respaldo);
        $stmt_respaldo->bindParam(':tipo_respaldo', $tipo_respaldo);
        $stmt_respaldo->bindParam(':respaldo_automatico', $respaldo_automatico);
        $stmt_respaldo->bindParam(':descripcion', $descripcion);
        $stmt_respaldo->bindParam(':id_administrador', $id_administrador);
        $stmt_respaldo->bindParam(':nombre_archivo', $nombre_archivo);

        $estado = 'Completado';
        $stmt_respaldo->bindParam(':estado', $estado);


        $stmt_respaldo->bindParam(':ruta_archivo', $archivo, PDO::PARAM_LOB);
        $stmt_respaldo->bindParam(':tamano_archivo', $tamano_archivo, PDO::PARAM_INT);

        $stmt_respaldo->execute();

        $id_respaldo = $con->lastInsertId();


        if (!empty($tablas)) {
            $sql_insert_tablas = "INSERT INTO detalles_respaldo (id_respaldo, tabla) VALUES (:id_respaldo, :tabla)";
            $stmt_tablas = $con->prepare($sql_insert_tablas);

            foreach ($tablas as $tabla) {
                $stmt_tablas->bindParam(':id_respaldo', $id_respaldo);
                $stmt_tablas->bindParam(':tabla', $tabla);
                $stmt_tablas->execute();
            }
        }

        $con->commit();

        $_SESSION['success'] = "El respaldo ha sido creado con éxito.";
        header('Location: respaldos.php');
        exit();

    } catch (Exception $e) {
        $con->rollBack();
        $estado = 'Fallido';


        $sql_insert_respaldo_error = "INSERT INTO respaldos (tipo_respaldo, respaldo_automatico, descripcion, id_administrador, nombre_archivo, estado)
                                      VALUES (:tipo_respaldo, :respaldo_automatico, :descripcion, :id_administrador, :nombre_archivo, :estado)";
        $stmt_respaldo_error = $con->prepare($sql_insert_respaldo_error);
        $stmt_respaldo_error->bindParam(':tipo_respaldo', $tipo_respaldo);
        $stmt_respaldo_error->bindParam(':respaldo_automatico', $respaldo_automatico);
        $stmt_respaldo_error->bindParam(':descripcion', $descripcion);
        $stmt_respaldo_error->bindParam(':id_administrador', $id_administrador);
        $stmt_respaldo_error->bindParam(':nombre_archivo', $nombre_archivo);
        $stmt_respaldo_error->bindParam(':estado', $estado);
        $stmt_respaldo_error->execute();

        $_SESSION['error'] = "Ocurrió un error al crear el respaldo: " . $e->getMessage();
        header('Location: respaldos.php');
        exit();
    }
}




function formatSize($bytes)
{
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
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
                    <h2 class="mt-4">Listado de Respaldos</h2>

                    <?php
                    if (isset($_SESSION['success'])) {
                        echo '<div class="alert alert-success" role="alert">' . $_SESSION['success'] . '</div>';
                        unset($_SESSION['success']);
                    }

                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']);
                    }
                    ?>

                    <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#respaldoModal">
                        <i class="fas fa-plus"></i> Crear Respaldo
                    </button>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Respaldos Realizados
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table id="dataTable" class="table table-striped table-bordered d-none d-sm-table">
                                    <thead style="position: sticky; top: 0; background-color: white; z-index: 10;">
                                        <tr>
                                            <th>ID Respaldo</th>
                                            <th>Nombre del Archivo</th>
                                            <th>Fecha de Respaldo</th>
                                            <th>Estado</th>
                                            <th>Descripción</th>
                                            <th>Tamaño del Archivo</th>
                                            <th>Tipo de Respaldo</th>
                                            <th>Respaldo Completo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT r.id_respaldo, r.nombre_archivo, r.fecha_respaldo, r.estado, r.descripcion, r.tamano_archivo, r.tipo_respaldo, r.respaldo_automatico
                        FROM respaldos r";
                                        $stmt = $con->prepare($sql);
                                        $stmt->execute();

                                        if ($stmt->rowCount() > 0) {
                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                $fecha_respaldo = DateTime::createFromFormat('Y-m-d H:i:s', $row['fecha_respaldo'])->format('d/m/Y H:i');
                                                $estado = ucfirst(strtolower($row['estado']));
                                                ?>
                                                <tr>
                                                    <td><?php echo $row['id_respaldo']; ?></td>
                                                    <td><?php echo $row['nombre_archivo']; ?></td>
                                                    <td><?php echo $fecha_respaldo; ?></td>
                                                    <td><?php echo ucfirst(strtolower($estado)); ?></td>
                                                    <td><?php echo $row['descripcion']; ?></td>
                                                    <td><?php echo formatSize($row['tamano_archivo']); ?></td>
                                                    <td><?php echo $row['tipo_respaldo']; ?></td>
                                                    <td><?php echo ($row['respaldo_automatico'] ? 'Sí' : 'No'); ?></td>
                                                    <td>
                                                        <a href="../backend/descargar_respaldo.php?id_respaldo=<?php echo $row['id_respaldo']; ?>"
                                                            class="btn btn-success">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <button class="btn btn-danger"
                                                            onclick="confirmarEliminacion(<?php echo $row['id_respaldo']; ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            echo '<tr><td colspan="9" class="text-center">No hay respaldos disponibles</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>

                                <div class="d-block d-sm-none">
                                    <?php
                                    $stmt->execute();
                                    if ($stmt->rowCount() > 0) {
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $fecha_respaldo = DateTime::createFromFormat('Y-m-d H:i:s', $row['fecha_respaldo'])->format('d/m/Y H:i');
                                            $estado = ucfirst(strtolower($row['estado']));
                                            ?>
                                            <div class="card mb-3">
                                                <div class="card-header">
                                                    <strong>Respaldo ID: <?php echo $row['id_respaldo']; ?></strong>
                                                </div>
                                                <div class="card-body">
                                                    <p style="color:#000;"><strong>Nombre del Archivo:</strong>
                                                        <?php echo $row['nombre_archivo']; ?></p>
                                                    <p style="color:#000;"><strong>Fecha de Respaldo:</strong>
                                                        <?php echo $fecha_respaldo; ?></p>
                                                    <p style="color:#000;"><strong>Estado:</strong>
                                                        <?php echo ucfirst(strtolower($estado)); ?></p>
                                                    <p style="color:#000;"><strong>Descripción:</strong>
                                                        <?php echo $row['descripcion']; ?></p>
                                                    <p style="color:#000;"><strong>Tamaño del Archivo:</strong>
                                                        <?php echo formatSize($row['tamano_archivo']); ?></p>
                                                    <p style="color:#000;"><strong>Tipo de Respaldo:</strong>
                                                        <?php echo $row['tipo_respaldo']; ?></p>
                                                    <p style="color:#000;"><strong>Respaldo Completo:</strong>
                                                        <?php echo ($row['respaldo_automatico'] ? 'Sí' : 'No'); ?></p>
                                                    <a href="../backend/descargar_respaldo.php?id_respaldo=<?php echo $row['id_respaldo']; ?>"
                                                        class="btn btn-success">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button class="btn btn-danger"
                                                        onclick="confirmarEliminacion(<?php echo $row['id_respaldo']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    } else {
                                        echo '<div class="card mb-3"><div class="card-body text-center">No hay respaldos disponibles</div></div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
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