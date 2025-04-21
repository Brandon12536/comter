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
        $_SESSION['error_message_sem49'] = 'El proveedor no está disponible. Asegúrate de estar logueado.';
        header('Location: molexsem49.php');
        exit();
    }

    $id_proveedor = $_SESSION['id_proveedor'];

    $inspection_date = $_POST['inspection_date'];
    $description = $_POST['description'];
    $operators = $_POST['operators'];

    $goods = isset($_POST['goods']) && is_numeric($_POST['goods']) ? (int) $_POST['goods'] : 0;

    $primer_t = isset($_POST['primer_t']) ? (int) $_POST['primer_t'] : 0;
    $segundo_t = isset($_POST['segundo_t']) ? (int) $_POST['segundo_t'] : 0;
    $tercer_t = isset($_POST['tercer_t']) ? (int) $_POST['tercer_t'] : 0;

    $fails_dedos_oro_contaminados = isset($_POST['coupler']) ? (int) $_POST['coupler'] : 0;
    $fails_faltante = isset($_POST['dano_end_face']) ? (int) $_POST['dano_end_face'] : 0;
    $fails_desplazados = isset($_POST['golpe_top']) ? (int) $_POST['golpe_top'] : 0;
    $fails_insuficiencias = isset($_POST['rebaba']) ? (int) $_POST['rebaba'] : 0;
    $fails_desprendidos = isset($_POST['dano_en_lente']) ? (int) $_POST['dano_en_lente'] : 0;
    $fails_fuera_de_spc = isset($_POST['fuera_de_spc']) ? (int) $_POST['fuera_de_spc'] : 0;
    $fails_dano_fisico = isset($_POST['dano_fisico']) ? (int) $_POST['dano_fisico'] : 0;
    $fails_wirebond_corto = isset($_POST['wirebond_corto']) ? (int) $_POST['wirebond_corto'] : 0;
    $fails_chueco = isset($_POST['wirebond_chueco']) ? (int) $_POST['wirebond_chueco'] : 0;
    $fails_fisura = isset($_POST['fisura']) ? (int) $_POST['fisura'] : 0;
    $fails_silicon_contaminacion = isset($_POST['silicon_contaminacion']) ? (int) $_POST['silicon_contaminacion'] : 0;
    $fails_contaminacion_end_face = isset($_POST['contaminacion_end_face']) ? (int) $_POST['contaminacion_end_face'] : 0;

    $total = isset($_POST['total']) ? (int) $_POST['total'] : 0;
    $total_final = isset($_POST['total_final']) ? (int) $_POST['total_final'] : 0;

    $comments = $_POST['comments'];

    $missing_fields = [];
    if (empty($inspection_date)) {
        $missing_fields[] = 'Inspection Date';
    }

    if (!empty($missing_fields)) {
        $fields = implode(', ', $missing_fields);
        $_SESSION['error_message_sem49'] = 'Por favor, rellene los siguientes campos obligatorios: ' . $fields . '.';
        header('Location: molexsem49.php');
        exit();
    }

    $sqlGetVersion = "SELECT id_version FROM versiones_inspeccion WHERE nombre_version = :nombre_version";
    $stmtGetVersion = $con->prepare($sqlGetVersion);
    $stmtGetVersion->bindValue(':nombre_version', 'sem49', PDO::PARAM_STR);
    $stmtGetVersion->execute();
    $version = $stmtGetVersion->fetch(PDO::FETCH_ASSOC);

    if (!$version) {
        $sqlInsertVersion = "INSERT INTO versiones_inspeccion (nombre_version) VALUES ('sem49')";
        $stmtInsertVersion = $con->prepare($sqlInsertVersion);
        if ($stmtInsertVersion->execute()) {
            $id_version = $con->lastInsertId();
        } else {
            $_SESSION['error_message_sem49'] = 'Error al insertar la versión sem49 en la base de datos.';
            header('Location: molexsem49.php');
            exit();
        }
    } else {
        $id_version = $version['id_version'];
    }

    $sqlInsert = "INSERT INTO molex 
    (id_version, id_proveedor, inspection_date, descripcion, operators, goods, 
    coupler, dano_end_face, golpe_top, rebaba, dano_en_lente, fuera_de_spc, 
    dano_fisico, wirebond_corto, wirebond_chueco, fisura, silicon_contaminacion, contaminacion_end_face, 
    total, total_final, comments, primer_t, segundo_t, tercer_t)
    VALUES 
    (:id_version, :id_proveedor, :inspection_date, :description, :operators, :goods, 
    :coupler, :dano_end_face, :golpe_top, :rebaba, :dano_en_lente, :fuera_de_spc, 
    :dano_fisico, :wirebond_corto, :wirebond_chueco, :fisura, :silicon_contaminacion, :contaminacion_end_face, 
    :total, :total_final, :comments, :primer_t, :segundo_t, :tercer_t)";

    $stmtInsert = $con->prepare($sqlInsert);

    $stmtInsert->bindValue(':id_version', $id_version, PDO::PARAM_INT);
    $stmtInsert->bindValue(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
    $stmtInsert->bindValue(':inspection_date', $inspection_date, PDO::PARAM_STR);
    $stmtInsert->bindValue(':description', $description, PDO::PARAM_STR);
    $stmtInsert->bindValue(':operators', $operators, PDO::PARAM_STR);
    $stmtInsert->bindValue(':goods', $goods, PDO::PARAM_INT);
    $stmtInsert->bindValue(':coupler', $fails_dedos_oro_contaminados, PDO::PARAM_INT);
    $stmtInsert->bindValue(':dano_end_face', $fails_faltante, PDO::PARAM_INT);
    $stmtInsert->bindValue(':golpe_top', $fails_desplazados, PDO::PARAM_INT);
    $stmtInsert->bindValue(':rebaba', $fails_insuficiencias, PDO::PARAM_INT);
    $stmtInsert->bindValue(':dano_en_lente', $fails_desprendidos, PDO::PARAM_INT);
    $stmtInsert->bindValue(':fuera_de_spc', $fails_fuera_de_spc, PDO::PARAM_INT);
    $stmtInsert->bindValue(':dano_fisico', $fails_dano_fisico, PDO::PARAM_INT);
    $stmtInsert->bindValue(':wirebond_corto', $fails_wirebond_corto, PDO::PARAM_INT);
    $stmtInsert->bindValue(':wirebond_chueco', $fails_chueco, PDO::PARAM_INT);
    $stmtInsert->bindValue(':fisura', $fails_fisura, PDO::PARAM_INT);
    $stmtInsert->bindValue(':silicon_contaminacion', $fails_silicon_contaminacion, PDO::PARAM_INT);
    $stmtInsert->bindValue(':contaminacion_end_face', $fails_contaminacion_end_face, PDO::PARAM_INT);
    $stmtInsert->bindValue(':total', $total, PDO::PARAM_INT);
    $stmtInsert->bindValue(':total_final', $total_final, PDO::PARAM_INT);
    $stmtInsert->bindValue(':comments', $comments, PDO::PARAM_STR);
    $stmtInsert->bindValue(':primer_t', $primer_t, PDO::PARAM_INT);
    $stmtInsert->bindValue(':segundo_t', $segundo_t, PDO::PARAM_INT);
    $stmtInsert->bindValue(':tercer_t', $tercer_t, PDO::PARAM_INT);

    if ($stmtInsert->execute()) {
        $_SESSION['success_message_sem49'] = 'Record added successfully!';
        header('Location: molexsem49.php');
        exit();
    } else {
        $_SESSION['error_message_sem49'] = 'Error while inserting the record. Please try again.';
        header('Location: molexsem49.php');
        exit();
    }
}

$records_per_page = 100;
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

$id_version = isset($_GET['id_version']) ? (int) $_GET['id_version'] : null;
$nombre_version = isset($_GET['nombre_version']) ? $_GET['nombre_version'] : 'sem49';

$total_records_sql = "SELECT COUNT(*)
                      FROM molex i 
                      JOIN versiones_inspeccion vi ON i.id_version = vi.id_version
                      WHERE i.id_proveedor = :id_proveedor";

$where_conditions = [];

if ($id_version) {
    $where_conditions[] = "i.id_version = :id_version";
}
if ($nombre_version) {
    $where_conditions[] = "vi.nombre_version = :nombre_version";
}

if (count($where_conditions) > 0) {
    $total_records_sql .= " AND " . implode(" AND ", $where_conditions);
}

$total_records_stmt = $con->prepare($total_records_sql);
$total_records_stmt->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);

if ($id_version) {
    $total_records_stmt->bindParam(':id_version', $id_version, PDO::PARAM_INT);
}
if ($nombre_version) {
    $total_records_stmt->bindParam(':nombre_version', $nombre_version, PDO::PARAM_STR);
}

$total_records_stmt->execute();
$total_records = $total_records_stmt->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);

$records_sql = "SELECT i.*, vi.nombre_version 
                FROM molex i 
                JOIN versiones_inspeccion vi ON i.id_version = vi.id_version
                WHERE i.id_proveedor = :id_proveedor";

if (count($where_conditions) > 0) {
    $records_sql .= " AND " . implode(" AND ", $where_conditions);
}

$records_sql .= " ORDER BY i.inspection_date ASC LIMIT :offset, :records_per_page";

$records_stmt = $con->prepare($records_sql);
$records_stmt->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);

if ($id_version) {
    $records_stmt->bindParam(':id_version', $id_version, PDO::PARAM_INT);
}
if ($nombre_version) {
    $records_stmt->bindParam(':nombre_version', $nombre_version, PDO::PARAM_STR);
}
$records_stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$records_stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);

$records_stmt->execute();
$records = $records_stmt->fetchAll(PDO::FETCH_ASSOC);



function getUniqueValues($column, $con, $id_proveedor)
{
    if ($column === 'nombre_version') {
        $sql = "SELECT DISTINCT vi.nombre_version FROM molex i
                JOIN versiones_inspeccion vi ON i.id_version = vi.id_version
                WHERE vi.nombre_version = 'sem49' AND vi.nombre_version IS NOT NULL AND vi.nombre_version != ''
                AND i.id_proveedor = :id_proveedor
                ORDER BY vi.nombre_version";
    } else {
        $sql = "SELECT DISTINCT i.$column FROM molex i
                JOIN versiones_inspeccion vi ON i.id_version = vi.id_version
                WHERE vi.nombre_version = 'sem49' AND i.$column IS NOT NULL AND i.$column != ''
                AND i.id_proveedor = :id_proveedor
                ORDER BY i.$column";
    }

    $stmt = $con->prepare($sql);
    $stmt->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}


$descripcion_values = getUniqueValues('descripcion', $con, $id_proveedor);
$operators_values = getUniqueValues('operators', $con, $id_proveedor);
$primer_t_values = getUniqueValues('primer_t', $con, $id_proveedor);
$segundo_t_values = getUniqueValues('segundo_t', $con, $id_proveedor);
$tercer_t_values = getUniqueValues('tercer_t', $con, $id_proveedor);
$goods_values = getUniqueValues('goods', $con, $id_proveedor);
$coupler_values = getUniqueValues('coupler', $con, $id_proveedor);
$dano_end_face_values = getUniqueValues('dano_end_face', $con, $id_proveedor);
$golpe_top_values = getUniqueValues('golpe_top', $con, $id_proveedor);
$rebaba_values = getUniqueValues('rebaba', $con, $id_proveedor);
$dano_en_lente_values = getUniqueValues('dano_en_lente', $con, $id_proveedor);
$fuera_de_spc_values = getUniqueValues('fuera_de_spc', $con, $id_proveedor);
$dano_fisico_values = getUniqueValues('dano_fisico', $con, $id_proveedor);
$wirebond_corto_values = getUniqueValues('wirebond_corto', $con, $id_proveedor);
$wirebond_chueco_values = getUniqueValues('wirebond_chueco', $con, $id_proveedor);
$fisura_values = getUniqueValues('fisura', $con, $id_proveedor);
$silicon_contaminacion_values = getUniqueValues('silicon_contaminacion', $con, $id_proveedor);
$contaminacion_end_face_values = getUniqueValues('contaminacion_end_face', $con, $id_proveedor);
$total_values = getUniqueValues('total', $con, $id_proveedor);
$total_final_values = getUniqueValues('total_final', $con, $id_proveedor);
$comments_values = getUniqueValues('comments', $con, $id_proveedor);
$nombre_version_values = getUniqueValues('nombre_version', $con, $id_proveedor);
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
    <link rel="shortcut icon" href="../ico/comter.png" type="image/x-icon">

    <title>Molex SEM49</title>
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

                    <a href="javascript:void(0);"
                        class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
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
                                <form action="../backend/exportar_excel_sem49.php" method="post">
                                    <button type="submit" class="btn btn-success mb-4" id="exportButton" 
                                        style="width: 100%;">
                                        <i class="bx bxs-file"></i> Exportar a Excel
                                    </button>
                                </form>
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary mb-4 add_sem49" data-bs-toggle="modal"
                                    data-bs-target="#addRecordModalsem49" style="width: 100%;">
                                    <i class="bx bx-plus"></i> Agregar registro
                                </button>
                            </div>
                            <button id="resetFilters" class="btn btn-danger mb-3" style="width: 100%;"><i
                                    class="bx bx-reset"></i> Reiniciar Filtros</button>
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

                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

                        <div class="navbar-nav align-items-center">
                            <!--<div class="nav-item d-flex align-items-center">
                                <form method="GET" action="search_molex_pcba.php" class="d-flex align-items-center">
                                    <input type="text" name="search" class="form-control border-0 shadow-none"
                                        placeholder="Buscar por Operador..." aria-label="Search..."
                                        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
                                    <button type="submit" class="btn border-0 bg-transparent p-0 ms-2">
                                        <i class="bx bx-search fs-4 lh-0"></i>
                                    </button>

                                    <a href="molexpcba.php" class="btn border-0 bg-transparent p-0 ms-2">
                                        <i class="bx bx-x fs-4 lh-0"></i>
                                    </a>
                                </form>
                            </div>-->
                        </div>


                        <ul class="navbar-nav flex-row align-items-center ms-auto">


                            <li class="fw-semibold d-block">BIENVENIDO COMTER </li> &nbsp;&nbsp;&nbsp;<span
                                class="fw-semibold d-block"><?php echo $nombre . ' ' . $apellido; ?></span>


                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
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
                                                        <img src="<?php echo $photo; ?>" alt=""
                                                            class="w-px-40 h-auto rounded-circle" />
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
                        <?php if (isset($_SESSION['success_message_sem49'])): ?>
                            <div class="alert alert-success">
                                <?= $_SESSION['success_message_sem49'];
                                unset($_SESSION['success_message_sem49']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['error_message_sem49'])): ?>
                            <div class="alert alert-danger">
                                <?= $_SESSION['error_message_sem49'];
                                unset($_SESSION['error_message_sem49']); ?>
                            </div>
                        <?php endif; ?>

                        <div class="container">
                            <h3 class="text-center mt-3">Records MOLEX SEM49</h3>
                            <div class="mt-5 d-flex justify-content-between">
                                <!--<div>
                                    <form action="../backend/exportar_excel_sem49.php" method="post">
                                        <button type="submit" class="btn btn-success mb-4" id="exportButton" disabled>
                                            <i class="bx bxs-file"></i> Export to Excel
                                        </button>
                                    </form>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal"
                                        data-bs-target="#addRecordModalsem49">
                                        <i class="bx bx-plus"></i> Add Record
                                    </button>
                                </div>
                            </div>-->

                                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                                    <!--<button id="resetFilters" class="btn btn-danger mb-3"><i class="bx bx-reset"></i> Reset Filters</button>-->
                                    <table class="table table-bordered text-center" id="inspectionTable"
                                        style="border-color: black;">
                                        <thead
                                            style="background-color: #D9DAD9; position: sticky; top: 0; z-index: 10;">
                                            <tr>
                                                <th style="color:#000000; border-color: black;">INSPECTION DATE</th>
                                                <th style="color:#000000; border-color: black;">DESCRIPTION
                                                    <select id="filter_description"
                                                        class="form-select form-select-sm mt-2">
                                                        <option value="">All</option>
                                                        <?php foreach ($descripcion_values as $descripcion) {
                                                            echo "<option value=\"" . htmlspecialchars($descripcion) . "\">" . htmlspecialchars($descripcion) . "</option>";
                                                        } ?>
                                                    </select>
                                                </th>
                                                <th style="color:#000000; border-color: black;">OPERATORS
                                                    <select id="filter_operators"
                                                        class="form-select form-select-sm mt-2">
                                                        <option value="">All</option>
                                                        <?php foreach ($operators_values as $operators) {
                                                            echo "<option value=\"" . htmlspecialchars($operators) . "\">" . htmlspecialchars($operators) . "</option>";
                                                        } ?>
                                                    </select>
                                                </th>
                                                <th style="color:#000000; border-color: black;">PRIMER T
                                                    <select id="filter_primer_t"
                                                        class="form-select form-select-sm mt-2">
                                                        <option value="">All</option>
                                                        <?php foreach ($primer_t_values as $primer_t) {
                                                            echo "<option value=\"" . htmlspecialchars($primer_t) . "\">" . htmlspecialchars($primer_t) . "</option>";
                                                        } ?>
                                                    </select>
                                                </th>
                                                <th style="color:#000000; border-color: black;">SEGUNDO T
                                                    <select id="filter_segundo_t"
                                                        class="form-select form-select-sm mt-2">
                                                        <option value="">All</option>
                                                        <?php foreach ($segundo_t_values as $segundo_t) {
                                                            echo "<option value=\"" . htmlspecialchars($segundo_t) . "\">" . htmlspecialchars($segundo_t) . "</option>";
                                                        } ?>
                                                    </select>
                                                </th>
                                                <th style="color:#000000; border-color: black;">TERCER T
                                                    <select id="filter_tercer_t"
                                                        class="form-select form-select-sm mt-2">
                                                        <option value="">All</option>
                                                        <?php foreach ($tercer_t_values as $tercer_t) {
                                                            echo "<option value=\"" . htmlspecialchars($tercer_t) . "\">" . htmlspecialchars($tercer_t) . "</option>";
                                                        } ?>
                                                    </select>
                                                </th>
                                                <th style="color:#0fcb59; border-color: black;">GOODS
                                                    <select id="filter_goods" class="form-select form-select-sm mt-2">
                                                        <option value="">All</option>
                                                        <?php foreach ($goods_values as $goods) {
                                                            echo "<option value=\"" . htmlspecialchars($goods) . "\">" . htmlspecialchars($goods) . "</option>";
                                                        } ?>
                                                    </select>
                                                </th>
                                                <th style="color:#000000; border-color: black;">COUPLER
                                                    <select id="filter_coupler" class="form-select form-select-sm mt-2">
                                                        <option value="">All</option>
                                                        <?php foreach ($coupler_values as $coupler) {
                                                            echo "<option value=\"" . htmlspecialchars($coupler) . "\">" . htmlspecialchars($coupler) . "</option>";
                                                        } ?>
                                                    </select>
                                                </th>
                                                <th style="color:#000000; border-color: black;">DANO END FACE
                                                    <select id="filter_dano_end_face"
                                                        class="form-select form-select-sm mt-2">
                                                        <option value="">All</option>
                                                        <?php foreach ($dano_end_face_values as $dano_end_face) {
                                                            echo "<option value=\"" . htmlspecialchars($dano_end_face) . "\">" . htmlspecialchars($dano_end_face) . "</option>";
                                                        } ?>
                                                    </select>
                                                </th>
                                                <th style="color:#000000; border-color: black;">GOLPE TOP
                                                    <select id="filter_golpe_top"
                                                        class="form-select form-select-sm mt-2">
                                                        <option value="">All</option>
                                                        <?php foreach ($golpe_top_values as $golpe_top) {
                                                            echo "<option value=\"" . htmlspecialchars($golpe_top) . "\">" . htmlspecialchars($golpe_top) . "</option>";
                                                        } ?>
                                                    </select>
                                                </th>
                                                <th style="color:#000000; border-color: black;">REBABA
                                                    <select id="filter_rebaba" class="form-select form-select-sm mt-2">
                                                        <option value="">All</option>
                                                        <?php foreach ($rebaba_values as $rebaba) {
                                                            echo "<option value=\"" . htmlspecialchars($rebaba) . "\">" . htmlspecialchars($rebaba) . "</option>";
                                                        } ?>
                                                    </select>
                                                </th>
                                                <th style="color:#000000; border-color: black;">DANO EN LENTE
                                                    <select id="filter_dano_en_lente"
                                                        class="form-select form-select-sm mt-2">
                                                        <option value="">All</option>
                                                        <?php foreach ($dano_en_lente_values as $dano_en_lente) {
                                                            echo "<option value=\"" . htmlspecialchars($dano_en_lente) . "\">" . htmlspecialchars($dano_en_lente) . "</option>";
                                                        } ?>
                                                    </select>
                                                </th>
                                                <th style="color:#000000; border-color: black;">FUERA DE SPC
                                                    <select id="filter_fuera_de_spc"
                                                        class="form-select form-select-sm mt-2">
                                                        <option value="">All</option>
                                                        <?php foreach ($fuera_de_spc_values as $fuera_de_spc) {
                                                            echo "<option value=\"" . htmlspecialchars($fuera_de_spc) . "\">" . htmlspecialchars($fuera_de_spc) . "</option>";
                                                        } ?>
                                                    </select>
                                                </th>
                                                <th style="color:#000000; border-color: black;">DANO FISICO
                                                    <select id="filter_dano_fisico"
                                                        class="form-select form-select-sm mt-2">
                                                        <option value="">All</option>
                                                        <?php foreach ($dano_fisico_values as $dano_fisico) {
                                                            echo "<option value=\"" . htmlspecialchars($dano_fisico) . "\">" . htmlspecialchars($dano_fisico) . "</option>";
                                                        } ?>
                                                    </select>
                                                </th>
                                                <th style="color:#000000; border-color: black;">WIREBOND CORTO
                                                    <select id="filter_wirebond_corto"
                                                        class="form-select form-select-sm mt-2">
                                                        <option value="">All</option>
                                                        <?php foreach ($wirebond_corto_values as $wirebond_corto) {
                                                            echo "<option value=\"" . htmlspecialchars($wirebond_corto) . "\">" . htmlspecialchars($wirebond_corto) . "</option>";
                                                        } ?>
                                                    </select>
                                                </th>
                                                <th style="color:#000000; border-color: black;">WIREBOND CHUECO
                                                    <select id="filter_wirebond_chueco"
                                                        class="form-select form-select-sm mt-2">
                                                        <option value="">All</option>
                                                        <?php foreach ($wirebond_chueco_values as $wirebond_chueco) {
                                                            echo "<option value=\"" . htmlspecialchars($wirebond_chueco) . "\">" . htmlspecialchars($wirebond_chueco) . "</option>";
                                                        } ?>
                                                    </select>
                                                </th>
                                                <th style="color:#000000; border-color: black;">FISURA
                                                    <select id="filter_fisura" class="form-select form-select-sm mt-2">
                                                        <option value="">All</option>
                                                        <?php foreach ($fisura_values as $fisura) {
                                                            echo "<option value=\"" . htmlspecialchars($fisura) . "\">" . htmlspecialchars($fisura) . "</option>";
                                                        } ?>
                                                    </select>
                                                </th>
                                                <th style="color:#000000; border-color: black;">SILICON CONTAMINACION
                                                    <select id="filter_silicon_contaminacion"
                                                        class="form-select form-select-sm mt-2">
                                                        <option value="">All</option>
                                                        <?php foreach ($silicon_contaminacion_values as $silicon_contaminacion) {
                                                            echo "<option value=\"" . htmlspecialchars($silicon_contaminacion) . "\">" . htmlspecialchars($silicon_contaminacion) . "</option>";
                                                        } ?>
                                                    </select>
                                                </th>
                                                <th style="color:#000000; border-color: black;">CONTAMINACION END FACE
                                                    <select id="filter_contaminacion_end_face"
                                                        class="form-select form-select-sm mt-2">
                                                        <option value="">All</option>
                                                        <?php foreach ($contaminacion_end_face_values as $contaminacion_end_face) {
                                                            echo "<option value=\"" . htmlspecialchars($contaminacion_end_face) . "\">" . htmlspecialchars($contaminacion_end_face) . "</option>";
                                                        } ?>
                                                    </select>
                                                </th>
                                                <th style="color:#000000; border-color: black;">TOTAL
                                                    <select id="filter_total" class="form-select form-select-sm mt-2">
                                                        <option value="">All</option>
                                                        <?php foreach ($total_values as $total) {
                                                            echo "<option value=\"" . htmlspecialchars($total) . "\">" . htmlspecialchars($total) . "</option>";
                                                        } ?>
                                                    </select>
                                                </th>
                                                <th style="color:#000000; border-color: black;">TOTAL FINAL
                                                    <select id="filter_total_final"
                                                        class="form-select form-select-sm mt-2">
                                                        <option value="">All</option>
                                                        <?php foreach ($total_final_values as $total_total_final) {
                                                            echo "<option value=\"" . htmlspecialchars($total_total_final) . "\">" . htmlspecialchars($total_total_final) . "</option>";
                                                        } ?>
                                                    </select>
                                                </th>
                                                <th style="color:#000000; border-color: black;">COMMENTS
                                                    <select id="filter_comments"
                                                        class="form-select form-select-sm mt-2">
                                                        <option value="">All</option>
                                                        <?php foreach ($comments_values as $comments) {
                                                            echo "<option value=\"" . htmlspecialchars($comments) . "\">" . htmlspecialchars($comments) . "</option>";
                                                        } ?>
                                                    </select>
                                                </th>
                                                <th style="color:#000000; border-color: black;">
                                                    Acciones
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $total_goods = 0;
                                            $total_coupler = 0;
                                            $total_dano_end_face = 0;
                                            $total_golpe_top = 0;
                                            $total_rebaba = 0;
                                            $total_dano_en_lente = 0;
                                            $total_fuera_de_spc = 0;
                                            $total_dano_fisico = 0;
                                            $total_wirebond_corto = 0;
                                            $total_wirebond_chueco = 0;
                                            $total_fisura = 0;
                                            $total_silicon_contaminacion = 0;
                                            $total_contaminacion_end_face = 0;
                                            $total_total = 0;
                                            $total_total_final = 0;

                                            $previous_date = "";

                                            foreach ($records as $record):

                                                $total_goods += (float) $record['goods'];
                                                $total_coupler += (float) $record['coupler'];
                                                $total_dano_end_face += (float) $record['dano_end_face'];
                                                $total_golpe_top += (float) $record['golpe_top'];
                                                $total_rebaba += (float) $record['rebaba'];
                                                $total_dano_en_lente += (float) $record['dano_en_lente'];
                                                $total_fuera_de_spc += (float) $record['fuera_de_spc'];
                                                $total_dano_fisico += (float) $record['dano_fisico'];
                                                $total_wirebond_corto += (float) $record['wirebond_corto'];
                                                $total_wirebond_chueco += (float) $record['wirebond_chueco'];
                                                $total_fisura += (float) $record['fisura'];
                                                $total_silicon_contaminacion += (float) $record['silicon_contaminacion'];
                                                $total_contaminacion_end_face += (float) $record['contaminacion_end_face'];
                                                $total_total += (float) $record['total'];
                                                $total_total_final += (float) $record['total_final'];

                                                $date = new DateTime($record['inspection_date']);
                                                $current_date = $date->format('d/m/y');

                                                if ($current_date != $previous_date && $previous_date != "") {
                                                    echo '<tr style="background-color: yellow; border:none; height: 30px;"><td colspan="22"></td></tr>';
                                                }
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?php
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

                                                    <td>
                                                        <?php
                                                        if (!empty($record['descripcion_image'])) {
                                                            echo '<img src="data:image/jpeg;base64,' . base64_encode($record['descripcion_image']) . '" alt="Descripción" style="max-width: 200px; max-height: 100px;" />';
                                                        } elseif (!empty($record['descripcion'])) {
                                                            echo htmlspecialchars($record['descripcion']);
                                                        } else {
                                                            echo 'N/A';
                                                        }
                                                        ?>
                                                    </td>

                                                    <td style="border-color: black;">
                                                        <?= htmlspecialchars($record['operators']) ?>
                                                    </td>

                                                    <td
                                                        style="border-color: black; <?= ($record['primer_t'] > 0) ? 'font-weight: bold; background-color: #d4f1c2; color: #000000;' : '' ?>">
                                                        <?= htmlspecialchars($record['primer_t']) ?>
                                                    </td>

                                                    <td
                                                        style="border-color: black; <?= ($record['segundo_t'] > 0) ? 'font-weight: bold; background-color: #d4f1c2; color: #000000;' : '' ?>">
                                                        <?= htmlspecialchars($record['segundo_t']) ?>
                                                    </td>

                                                    <td
                                                        style="border-color: black; <?= ($record['tercer_t'] > 0) ? 'font-weight: bold; background-color: #d4f1c2; color: #000000;' : '' ?>">
                                                        <?= htmlspecialchars($record['tercer_t']) ?>
                                                    </td>

                                                    <td
                                                        style="border-color: black; <?= ($record['goods'] > 0) ? 'font-weight: bold; background-color: #d4f1c2; color: #000000;' : '' ?>">
                                                        <?= htmlspecialchars($record['goods']) ?>
                                                    </td>

                                                    <td
                                                        style="border-color: black; <?= ($record['coupler'] != 0) ? 'font-weight: bold; color: red; background-color: #FFCCCC;' : '' ?>">
                                                        <?= htmlspecialchars($record['coupler']) ?>
                                                    </td>

                                                    <td
                                                        style="border-color: black; <?= ($record['dano_end_face'] != 0) ? 'font-weight: bold; color: red; background-color: #FFCCCC;' : '' ?>">
                                                        <?= htmlspecialchars($record['dano_end_face']) ?>
                                                    </td>

                                                    <td
                                                        style="border-color: black; <?= ($record['golpe_top'] != 0) ? 'font-weight: bold; color: red; background-color: #FFCCCC;' : '' ?>">
                                                        <?= htmlspecialchars($record['golpe_top']) ?>
                                                    </td>

                                                    <td
                                                        style="border-color: black; <?= ($record['rebaba'] != 0) ? 'font-weight: bold; color: red; background-color: #FFCCCC;' : '' ?>">
                                                        <?= htmlspecialchars($record['rebaba']) ?>
                                                    </td>

                                                    <td
                                                        style="border-color: black; <?= ($record['dano_en_lente'] != 0) ? 'font-weight: bold; color: red; background-color: #FFCCCC;' : '' ?>">
                                                        <?= htmlspecialchars($record['dano_en_lente']) ?>
                                                    </td>

                                                    <td
                                                        style="border-color: black; <?= ($record['fuera_de_spc'] != 0) ? 'font-weight: bold; color: red; background-color: #FFCCCC;' : '' ?>">
                                                        <?= htmlspecialchars($record['fuera_de_spc']) ?>
                                                    </td>

                                                    <td
                                                        style="border-color: black; <?= ($record['dano_fisico'] != 0) ? 'font-weight: bold; color: red; background-color: #FFCCCC;' : '' ?>">
                                                        <?= htmlspecialchars($record['dano_fisico']) ?>
                                                    </td>

                                                    <td
                                                        style="border-color: black; <?= ($record['wirebond_corto'] != 0) ? 'font-weight: bold; color: red; background-color: #FFCCCC;' : '' ?>">
                                                        <?= htmlspecialchars($record['wirebond_corto']) ?>
                                                    </td>

                                                    <td
                                                        style="border-color: black; <?= ($record['wirebond_chueco'] != 0) ? 'font-weight: bold; color: red; background-color: #FFCCCC;' : '' ?>">
                                                        <?= htmlspecialchars($record['wirebond_chueco']) ?>
                                                    </td>

                                                    <td
                                                        style="border-color: black; <?= ($record['fisura'] != 0) ? 'font-weight: bold; color: red; background-color: #FFCCCC;' : '' ?>">
                                                        <?= htmlspecialchars($record['fisura']) ?>
                                                    </td>

                                                    <td
                                                        style="border-color: black; <?= ($record['silicon_contaminacion'] != 0) ? 'font-weight: bold; color: red; background-color: #FFCCCC;' : '' ?>">
                                                        <?= htmlspecialchars($record['silicon_contaminacion']) ?>
                                                    </td>

                                                    <td
                                                        style="border-color: black; <?= ($record['contaminacion_end_face'] != 0) ? 'font-weight: bold; color: red; background-color: #FFCCCC;' : '' ?>">
                                                        <?= htmlspecialchars($record['contaminacion_end_face']) ?>
                                                    </td>

                                                    <td
                                                        style="border-color: black; <?= ($record['total'] != 0) ? 'font-weight: bold; color: red; background-color: #FFCCCC;' : '' ?>">
                                                        <?= htmlspecialchars($record['total']) ?>
                                                    </td>

                                                    <td style="border-color: black;">
                                                        <?= htmlspecialchars($record['total_final']) ?>
                                                    </td>

                                                    <td style="border-color: black;">
                                                        <?= htmlspecialchars($record['comments']) ?>
                                                    </td>
                                                    <td style="border-color: black;">
                                                        <?php if ($puedeEditar): ?>
                                                            <a href="editar_molex_sem49.php?id_molex=<?= $record['id_molex']; ?>"
                                                                class="btn btn-warning edit_sem49">
                                                                <i class="bx bx-edit"></i> Edit
                                                            </a>
                                                        <?php else: ?>
                                                            <a href="#" class="btn btn-warning edit_sem49 disabled-btn">
                                                                <i class="bx bx-edit"></i> Edit
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php

                                                $previous_date = $current_date;
                                            endforeach;
                                            ?>
                                        </tbody>

                                        <tfoot
                                            style="background-color: #f2f2f2; position: sticky; bottom: 0; z-index: 10;">
                                            <tr>
                                                <td colspan="6" style="border-color: black; font-weight: bold;">Total
                                                </td>
                                                <td style="border-color: black;"><?= number_format($total_goods, 0) ?>
                                                </td>
                                                <td style="border-color: black;"><?= number_format($total_coupler, 0) ?>
                                                </td>
                                                <td style="border-color: black;">
                                                    <?= number_format($total_dano_end_face, 0) ?>
                                                </td>
                                                <td style="border-color: black;">
                                                    <?= number_format($total_golpe_top, 0) ?>
                                                </td>
                                                <td style="border-color: black;"><?= number_format($total_rebaba, 0) ?>
                                                </td>
                                                <td style="border-color: black;">
                                                    <?= number_format($total_dano_en_lente, 0) ?>
                                                </td>
                                                <td style="border-color: black;">
                                                    <?= number_format($total_fuera_de_spc, 0) ?>
                                                </td>
                                                <td style="border-color: black;">
                                                    <?= number_format($total_dano_fisico, 0) ?>
                                                </td>
                                                <td style="border-color: black;">
                                                    <?= number_format($total_wirebond_corto, 0) ?>
                                                </td>
                                                <td style="border-color: black;">
                                                    <?= number_format($total_wirebond_chueco, 0) ?>
                                                </td>
                                                <td style="border-color: black;"><?= number_format($total_fisura, 0) ?>
                                                </td>
                                                <td style="border-color: black;">
                                                    <?= number_format($total_silicon_contaminacion, 0) ?>
                                                </td>
                                                <td style="border-color: black;">
                                                    <?= number_format($total_contaminacion_end_face, 0) ?>
                                                </td>
                                                <td style="border-color: black;"><?= number_format($total_total, 0) ?>
                                                </td>
                                                <td style="border-color: black;">
                                                    <?= number_format($total_total_final, 0) ?>
                                                </td>
                                                <td style="border-color: black;"></td>
                                                <td style="border-color: black;"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="pagination"
                                style="display: flex; justify-content: center; align-items: center; text-align: center; margin-top: 20px;">
                                <?php if ($current_page > 1): ?>
                                    <a href="?page=<?= $current_page - 1 ?>" class="btn btn-primary"
                                        style="margin-right: 5px;">Previous</a>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <a href="?page=<?= $i ?>"
                                        class="btn <?= $i === $current_page ? 'btn-info' : 'btn-secondary' ?>"
                                        style="margin-right: 5px;">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($current_page < $total_pages): ?>
                                    <a href="?page=<?= $current_page + 1 ?>" class="btn btn-primary"
                                        style="margin-right: 5px;">Next</a>
                                <?php endif; ?>
                            </div>


                            <div class="modal fade" id="addRecordModalsem49" tabindex="-1"
                                aria-labelledby="addRecordModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addRecordModalLabel">Add Record</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="molexsem49.php" method="post" enctype="multipart/form-data">
                                                <?php

                                                date_default_timezone_set('America/Mexico_City');


                                                $fecha_actual = date('Y-m-d');
                                                ?>
                                                <div class="mb-3">
                                                    <label for="inspection_date" class="form-label">INSPECTION
                                                        DATE</label>
                                                    <input type="date" class="form-control" id="inspection_date"
                                                        name="inspection_date" value="<?= $fecha_actual ?>">
                                                </div>



                                                <div class="mb-3">
                                                    <label for="operators" class="form-label">OPERATORS</label>
                                                    <textarea class="form-control" id="operators"
                                                        name="operators"><?php echo htmlspecialchars($nombre . ' ' . $apellido); ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="descripcion" class="form-label">DESCRIPTION</label>
                                                    <textarea class="form-control" id="descripcion"
                                                        name="description"></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="primer_t" class="form-label">Primer T</label>
                                                    <select class="form-control" id="primer_t" name="primer_t">
                                                        <?php for ($i = 0; $i <= 6000; $i++): ?>
                                                            <option value="<?= $i ?>" <?= $i == 0 ? 'selected' : '' ?>>
                                                                <?= $i ?>
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="segundo_t" class="form-label">Segundo T</label>
                                                    <select class="form-control" id="segundo_t" name="segundo_t">
                                                        <?php for ($i = 0; $i <= 6000; $i++): ?>
                                                            <option value="<?= $i ?>" <?= $i == 0 ? 'selected' : '' ?>>
                                                                <?= $i ?>
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="tercer_t" class="form-label">Tercer T</label>
                                                    <select class="form-control" id="tercer_t" name="tercer_t">
                                                        <?php for ($i = 0; $i <= 6000; $i++): ?>
                                                            <option value="<?= $i ?>" <?= $i == 0 ? 'selected' : '' ?>>
                                                                <?= $i ?>
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="goods" class="form-label">GOODS</label>
                                                    <input type="number" class="form-control" id="goods" name="goods"
                                                        required readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="coupler" class="form-label">Coupler</label>
                                                    <select class="form-control" id="coupler" name="coupler">
                                                        <?php for ($i = 0; $i <= 5000; $i++): ?>
                                                            <option value="<?= $i ?>" <?= $i == 0 ? 'selected' : '' ?>>
                                                                <?= $i ?>
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="dano_end_face" class="form-label">Daño en el extremo
                                                        (end
                                                        face)</label>
                                                    <select class="form-control" id="dano_end_face"
                                                        name="dano_end_face">
                                                        <?php for ($i = 0; $i <= 5000; $i++): ?>
                                                            <option value="<?= $i ?>" <?= $i == 0 ? 'selected' : '' ?>>
                                                                <?= $i ?>
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="golpe_top" class="form-label">Golpe Top</label>
                                                    <select class="form-control" id="golpe_top" name="golpe_top">
                                                        <?php for ($i = 0; $i <= 5000; $i++): ?>
                                                            <option value="<?= $i ?>" <?= $i == 0 ? 'selected' : '' ?>>
                                                                <?= $i ?>
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="rebaba" class="form-label">Rebaba</label>
                                                    <select class="form-control" id="rebaba" name="rebaba">
                                                        <?php for ($i = 0; $i <= 5000; $i++): ?>
                                                            <option value="<?= $i ?>" <?= $i == 0 ? 'selected' : '' ?>>
                                                                <?= $i ?>
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="dano_en_lente" class="form-label">Daño en lente</label>
                                                    <select class="form-control" id="dano_en_lente"
                                                        name="dano_en_lente">
                                                        <?php for ($i = 0; $i <= 5000; $i++): ?>
                                                            <option value="<?= $i ?>" <?= $i == 0 ? 'selected' : '' ?>>
                                                                <?= $i ?>
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="fuera_de_spc" class="form-label">Fuera de SPC</label>
                                                    <select class="form-control" id="fuera_de_spc" name="fuera_de_spc">
                                                        <?php for ($i = 0; $i <= 5000; $i++): ?>
                                                            <option value="<?= $i ?>" <?= $i == 0 ? 'selected' : '' ?>>
                                                                <?= $i ?>
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="dano_fisico" class="form-label">Daño físico</label>
                                                    <select class="form-control" id="dano_fisico" name="dano_fisico">
                                                        <?php for ($i = 0; $i <= 5000; $i++): ?>
                                                            <option value="<?= $i ?>" <?= $i == 0 ? 'selected' : '' ?>>
                                                                <?= $i ?>
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="wirebond_corto" class="form-label">Wirebond
                                                        corto</label>
                                                    <select class="form-control" id="wirebond_corto"
                                                        name="wirebond_corto">
                                                        <?php for ($i = 0; $i <= 5000; $i++): ?>
                                                            <option value="<?= $i ?>" <?= $i == 0 ? 'selected' : '' ?>>
                                                                <?= $i ?>
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="wirebond_chueco" class="form-label">Wirebond
                                                        chueco</label>
                                                    <select class="form-control" id="wirebond_chueco"
                                                        name="wirebond_chueco">
                                                        <?php for ($i = 0; $i <= 5000; $i++): ?>
                                                            <option value="<?= $i ?>" <?= $i == 0 ? 'selected' : '' ?>>
                                                                <?= $i ?>
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="fisura" class="form-label">Fisura</label>
                                                    <select class="form-control" id="fisura" name="fisura">
                                                        <?php for ($i = 0; $i <= 5000; $i++): ?>
                                                            <option value="<?= $i ?>" <?= $i == 0 ? 'selected' : '' ?>>
                                                                <?= $i ?>
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="silicon_contaminacion"
                                                        class="form-label">Silicon/Contaminación</label>
                                                    <select class="form-control" id="silicon_contaminacion"
                                                        name="silicon_contaminacion">
                                                        <?php for ($i = 0; $i <= 5000; $i++): ?>
                                                            <option value="<?= $i ?>" <?= $i == 0 ? 'selected' : '' ?>>
                                                                <?= $i ?>
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="contaminacion_end_face" class="form-label">Contaminación
                                                        en
                                                        el end face</label>
                                                    <select class="form-control" id="contaminacion_end_face"
                                                        name="contaminacion_end_face">
                                                        <?php for ($i = 0; $i <= 5000; $i++): ?>
                                                            <option value="<?= $i ?>" <?= $i == 0 ? 'selected' : '' ?>>
                                                                <?= $i ?>
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>



                                                <div class="mb-3">
                                                    <label for="total" class="form-label">Total</label>
                                                    <input type="number" class="form-control" id="total" name="total"
                                                        required readonly>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="total_final" class="form-label">Total Final</label>
                                                    <input type="number" class="form-control" id="total_final"
                                                        name="total_final" required readonly>
                                                </div>


                                                <div class="mb-3">
                                                    <label for="comments" class="form-label">COMMENTS</label>
                                                    <textarea class="form-control" id="comments"
                                                        name="comments"></textarea>
                                                </div>


                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
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
                /*document.addEventListener('DOMContentLoaded', function () {
                    var table = document.getElementById('inspectionTable');
                    var exportButton = document.getElementById('exportButton');

                    var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
                    var hasRecords = rows.length > 0 && rows[0].getElementsByTagName('td').length > 1;

                    exportButton.disabled = !hasRecords;
                });*/
            </script>


            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const fields = [
                        'coupler',
                        'dano_end_face',
                        'golpe_top',
                        'rebaba',
                        'dano_en_lente',
                        'fuera_de_spc',
                        'dano_fisico',
                        'wirebond_corto',
                        'wirebond_chueco',
                        'fisura',
                        'silicon_contaminacion',
                        'contaminacion_end_face'
                    ];

                    const totalField = document.getElementById('total');
                    const goodsField = document.getElementById('goods');
                    const totalFinalField = document.getElementById('total_final');


                    function calculateTotal() {
                        let total = 0;


                        fields.forEach(function (field) {
                            const value = parseFloat(document.getElementById(field).value) || 0;
                            total += value;
                        });


                        totalField.value = total;


                        updateTotalFinal();


                        updateGoods();
                    }


                    function updateTotalFinal() {
                        const goodsValue = parseFloat(goodsField.value) || 0;
                        const totalValue = parseFloat(totalField.value) || 0;
                        const totalFinal = goodsValue + totalValue;


                        totalFinalField.value = totalFinal;
                    }


                    function updateGoods() {
                        const primerT = parseFloat(document.getElementById('primer_t').value) || 0;
                        const segundoT = parseFloat(document.getElementById('segundo_t').value) || 0;
                        const tercerT = parseFloat(document.getElementById('tercer_t').value) || 0;

                        const goodsValue = primerT + segundoT + tercerT;


                        goodsField.value = goodsValue;


                        updateTotalFinal();
                    }


                    fields.forEach(function (field) {
                        document.getElementById(field).addEventListener('input', function () {
                            calculateTotal();
                        });
                    });


                    document.getElementById('primer_t').addEventListener('input', updateGoods);
                    document.getElementById('segundo_t').addEventListener('input', updateGoods);
                    document.getElementById('tercer_t').addEventListener('input', updateGoods);


                    calculateTotal();
                });

            </script>

            <script>

                const filterDescription = document.getElementById("filter_description");
                const filterOperators = document.getElementById("filter_operators");
                const filterPrimerT = document.getElementById("filter_primer_t");
                const filterSegundoT = document.getElementById("filter_segundo_t");
                const filterTercerT = document.getElementById("filter_tercer_t");
                const filterGoods = document.getElementById("filter_goods");
                const filterCoupler = document.getElementById("filter_coupler");
                const filterDanoEndFace = document.getElementById("filter_dano_end_face");
                const filterGolpeTop = document.getElementById("filter_golpe_top");
                const filterRebaba = document.getElementById("filter_rebaba");
                const filterDanoEnLente = document.getElementById("filter_dano_en_lente");
                const filterFueraDeSpc = document.getElementById("filter_fuera_de_spc");
                const filterDanoFisico = document.getElementById("filter_dano_fisico");
                const filterWirebondCorto = document.getElementById("filter_wirebond_corto");
                const filterWirebondChueco = document.getElementById("filter_wirebond_chueco");
                const filterFisura = document.getElementById("filter_fisura");
                const filterSiliconContaminacion = document.getElementById("filter_silicon_contaminacion");
                const filterContaminacionEndFace = document.getElementById("filter_contaminacion_end_face");
                const filterTotal = document.getElementById("filter_total");
                const filterTotalFinal = document.getElementById("filter_total_final");
                const filterComments = document.getElementById("filter_comments");


                function filterTable() {
                    const rows = document.querySelectorAll("#inspectionTable tbody tr");

                    rows.forEach(row => {
                        let showRow = true;


                        if (filterDescription.value && !row.cells[1].textContent.includes(filterDescription.value)) {
                            showRow = false;
                        }
                        if (filterOperators.value && !row.cells[2].textContent.includes(filterOperators.value)) {
                            showRow = false;
                        }
                        if (filterPrimerT.value && !row.cells[3].textContent.includes(filterPrimerT.value)) {
                            showRow = false;
                        }
                        if (filterSegundoT.value && !row.cells[4].textContent.includes(filterSegundoT.value)) {
                            showRow = false;
                        }
                        if (filterTercerT.value && !row.cells[5].textContent.includes(filterTercerT.value)) {
                            showRow = false;
                        }
                        if (filterGoods.value && !row.cells[6].textContent.includes(filterGoods.value)) {
                            showRow = false;
                        }
                        if (filterCoupler.value && !row.cells[7].textContent.includes(filterCoupler.value)) {
                            showRow = false;
                        }
                        if (filterDanoEndFace.value && !row.cells[8].textContent.includes(filterDanoEndFace.value)) {
                            showRow = false;
                        }
                        if (filterGolpeTop.value && !row.cells[9].textContent.includes(filterGolpeTop.value)) {
                            showRow = false;
                        }
                        if (filterRebaba.value && !row.cells[10].textContent.includes(filterRebaba.value)) {
                            showRow = false;
                        }
                        if (filterDanoEnLente.value && !row.cells[11].textContent.includes(filterDanoEnLente.value)) {
                            showRow = false;
                        }
                        if (filterFueraDeSpc.value && !row.cells[12].textContent.includes(filterFueraDeSpc.value)) {
                            showRow = false;
                        }
                        if (filterDanoFisico.value && !row.cells[13].textContent.includes(filterDanoFisico.value)) {
                            showRow = false;
                        }
                        if (filterWirebondCorto.value && !row.cells[14].textContent.includes(filterWirebondCorto.value)) {
                            showRow = false;
                        }
                        if (filterWirebondChueco.value && !row.cells[15].textContent.includes(filterWirebondChueco.value)) {
                            showRow = false;
                        }
                        if (filterFisura.value && !row.cells[16].textContent.includes(filterFisura.value)) {
                            showRow = false;
                        }
                        if (filterSiliconContaminacion.value && !row.cells[17].textContent.includes(filterSiliconContaminacion.value)) {
                            showRow = false;
                        }
                        if (filterContaminacionEndFace.value && !row.cells[18].textContent.includes(filterContaminacionEndFace.value)) {
                            showRow = false;
                        }
                        if (filterTotal.value && !row.cells[19].textContent.includes(filterTotal.value)) {
                            showRow = false;
                        }
                        if (filterTotalFinal.value && !row.cells[20].textContent.includes(filterTotalFinal.value)) {
                            showRow = false;
                        }
                        if (filterComments.value && !row.cells[21].textContent.includes(filterComments.value)) {
                            showRow = false;
                        }


                        row.style.display = showRow ? "" : "none";
                    });
                }


                const filters = document.querySelectorAll("select, input");
                filters.forEach(filter => {
                    filter.addEventListener("input", filterTable);
                });


                document.getElementById("resetFilters").addEventListener("click", function () {
                    filters.forEach(filter => {
                        filter.value = "";
                    });
                    filterTable();
                });


                window.onload = filterTable;
            </script>

            <script>
                document.addEventListener('DOMContentLoaded', function () {

                    const puedeEditar = <?php echo json_encode($puedeEditar); ?>;
                    const puedeCapturar = <?php echo json_encode($puedeCapturar); ?>;

                    const addRecordBtn = document.querySelector('.add_sem49');
                    if (!puedeCapturar && addRecordBtn) {
                        addRecordBtn.classList.add('disabled-btn');
                        addRecordBtn.setAttribute('disabled', 'disabled');
                    }

                    const editBtns = document.querySelectorAll('.edit_sem49');
                    editBtns.forEach(function (editBtn) {
                        if (!puedeEditar) {
                            editBtn.classList.add('disabled-btn');
                            editBtn.setAttribute('disabled', 'disabled');
                        }
                    });
                });
            </script>
            <style>
                .disabled-btn {
                    opacity: 0.5;
                    pointer-events: none;
                }
            </style>
</body>

</html>