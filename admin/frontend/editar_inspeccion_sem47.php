<?php
session_start();
require '../../config/connection.php';

if (!isset($_SESSION['id_proveedor'])) {
    $_SESSION['error_message_sem47'] = "La sesión no tiene el id_proveedor.";
    header("Location: molexsem47.php");
    exit();
}

$id_proveedor = $_SESSION['id_proveedor'];
$id_inspeccion = isset($_GET['id_inspeccion']) ? $_GET['id_inspeccion'] : null;

if ($id_inspeccion === null) {
    $_SESSION['error_message_sem47'] = "ID de inspección no proporcionado.";
    header("Location: molexsem47.php");
    exit();
}

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

$sql = "SELECT * FROM inspecciones WHERE id_inspeccion = :id_inspeccion AND id_proveedor = :id_proveedor";
$stmt = $con->prepare($sql);
$stmt->bindParam(':id_inspeccion', $id_inspeccion, PDO::PARAM_INT);
$stmt->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $inspeccion = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $_SESSION['error_message_sem47'] = "Inspección no encontrada.";
    header("Location: molexsem47.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inspection_date = $_POST['inspection_date'];
    $operators = $_POST['operators'];
    $descripcion = $_POST['descripcion'];
    $primer_t = $_POST['primer_t'];
    $segundo_t = $_POST['segundo_t'];
    $tercer_t = $_POST['tercer_t'];
    $goods = $_POST['goods'];
    $coupler = $_POST['coupler'];
    $dano_end_face = $_POST['dano_end_face'];
    $golpe_top = $_POST['golpe_top'];
    $rebaba = $_POST['rebaba'];
    $dano_en_lente = $_POST['dano_en_lente'];
    $fuera_de_spc = $_POST['fuera_de_spc'];
    $dano_fisico = $_POST['dano_fisico'];
    $coupler_danado = $_POST['coupler_danado'];
    $hundimiento = $_POST['hundimiento'];
    $fisura = $_POST['fisura'];
    $silicon_contaminacion = $_POST['silicon_contaminacion'];
    $contaminacion_end_face = $_POST['contaminacion_end_face'];
    $total = $_POST['total'];
    $total_final = $_POST['total_final'];
    $comments = $_POST['comments'];

    $sql = "UPDATE inspecciones SET 
            inspection_date = :inspection_date, 
            operators = :operators, 
            descripcion = :descripcion,
            primer_t = :primer_t,
            segundo_t = :segundo_t,
            tercer_t = :tercer_t,
            goods = :goods,
            coupler = :coupler,
            dano_end_face = :dano_end_face,
            golpe_top = :golpe_top,
            rebaba = :rebaba,
            dano_en_lente = :dano_en_lente,
            fuera_de_spc = :fuera_de_spc,
            dano_fisico = :dano_fisico,
            coupler_danado = :coupler_danado,
            hundimiento = :hundimiento,
            fisura = :fisura,
            silicon_contaminacion = :silicon_contaminacion,
            contaminacion_end_face = :contaminacion_end_face,
            total = :total,
            total_final = :total_final,
            comments = :comments
            WHERE id_inspeccion = :id_inspeccion AND id_proveedor = :id_proveedor";

    $stmt = $con->prepare($sql);

    $stmt->bindParam(':inspection_date', $inspection_date);
    $stmt->bindParam(':operators', $operators);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':primer_t', $primer_t, PDO::PARAM_INT);
    $stmt->bindParam(':segundo_t', $segundo_t, PDO::PARAM_INT);
    $stmt->bindParam(':tercer_t', $tercer_t, PDO::PARAM_INT);
    $stmt->bindParam(':goods', $goods, PDO::PARAM_INT);
    $stmt->bindParam(':coupler', $coupler, PDO::PARAM_INT);
    $stmt->bindParam(':dano_end_face', $dano_end_face, PDO::PARAM_INT);
    $stmt->bindParam(':golpe_top', $golpe_top, PDO::PARAM_INT);
    $stmt->bindParam(':rebaba', $rebaba, PDO::PARAM_INT);
    $stmt->bindParam(':dano_en_lente', $dano_en_lente, PDO::PARAM_INT);
    $stmt->bindParam(':fuera_de_spc', $fuera_de_spc, PDO::PARAM_INT);
    $stmt->bindParam(':dano_fisico', $dano_fisico, PDO::PARAM_INT);
    $stmt->bindParam(':coupler_danado', $coupler_danado, PDO::PARAM_INT);
    $stmt->bindParam(':hundimiento', $hundimiento, PDO::PARAM_INT);
    $stmt->bindParam(':fisura', $fisura, PDO::PARAM_INT);
    $stmt->bindParam(':silicon_contaminacion', $silicon_contaminacion, PDO::PARAM_INT);
    $stmt->bindParam(':contaminacion_end_face', $contaminacion_end_face, PDO::PARAM_INT);
    $stmt->bindParam(':total', $total, PDO::PARAM_INT);
    $stmt->bindParam(':total_final', $total_final, PDO::PARAM_INT);
    $stmt->bindParam(':comments', $comments);
    $stmt->bindParam(':id_inspeccion', $id_inspeccion, PDO::PARAM_INT);
    $stmt->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['success_message_sem47'] = "Inspección actualizada correctamente.";
        header("Location: molexsem47.php");
        exit();
    } else {
        $_SESSION['error_message_sem47'] = "Error al actualizar la inspección.";
        header("Location: molexsem47.php");
        exit();
    }
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

    <title>Editar Molex SEM47</title>
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

                </ul>
                </li>


            </aside>

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
                    <h3 class="text-center mt-3">Editar MOLEX SEM47</h3>
                        <form action="editar_inspeccion_sem47.php?id_inspeccion=<?php echo $id_inspeccion; ?>" method="post"
                            enctype="multipart/form-data" id="formInspeccion">
                            <div class="mb-3">
                                <label for="inspection_date" class="form-label">INSPECTION DATE</label>
                                <input type="date" class="form-control" id="inspection_date" name="inspection_date"
                                    value="<?= $inspeccion['inspection_date'] ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="operators" class="form-label">OPERATORS</label>
                                <textarea class="form-control" id="operators"
                                    name="operators"><?= htmlspecialchars($nombre . ' ' . $apellido) ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="descripcion" class="form-label">DESCRIPTION</label>
                                <textarea class="form-control" id="descripcion"
                                    name="descripcion"><?= htmlspecialchars($inspeccion['descripcion']) ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="primer_t" class="form-label">Primer T</label>
                                <select class="form-control" id="primer_t" name="primer_t">
                                    <?php for ($i = 0; $i <= 6000; $i++): ?>
                                            <option value="<?= $i ?>" <?= $i == $inspeccion['primer_t'] ? 'selected' : '' ?>>
                                                <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="segundo_t" class="form-label">Segundo T</label>
                                <select class="form-control" id="segundo_t" name="segundo_t">
                                    <?php for ($i = 0; $i <= 6000; $i++): ?>
                                            <option value="<?= $i ?>" <?= $i == $inspeccion['segundo_t'] ? 'selected' : '' ?>>
                                                <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="tercer_t" class="form-label">Tercer T</label>
                                <select class="form-control" id="tercer_t" name="tercer_t">
                                    <?php for ($i = 0; $i <= 6000; $i++): ?>
                                            <option value="<?= $i ?>" <?= $i == $inspeccion['tercer_t'] ? 'selected' : '' ?>>
                                                <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="goods" class="form-label">GOODS</label>
                                <input type="number" class="form-control" id="goods" name="goods" required readonly
                                    value="<?= $inspeccion['goods'] ?>">
                            </div>

                            <div class="mb-3">
                                <label for="coupler" class="form-label">Coupler</label>
                                <select class="form-control" id="coupler" name="coupler">
                                    <?php for ($i = 0; $i <= 5000; $i++): ?>
                                            <option value="<?= $i ?>" <?= $i == $inspeccion['coupler'] ? 'selected' : '' ?>>
                                                <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="dano_end_face" class="form-label">Daño en el extremo (end face)</label>
                                <select class="form-control" id="dano_end_face" name="dano_end_face">
                                    <?php for ($i = 0; $i <= 5000; $i++): ?>
                                            <option value="<?= $i ?>" <?= $i == $inspeccion['dano_end_face'] ? 'selected' : '' ?>>
                                                <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="golpe_top" class="form-label">Golpe en la parte superior</label>
                                <select class="form-control" id="golpe_top" name="golpe_top">
                                    <?php for ($i = 0; $i <= 5000; $i++): ?>
                                            <option value="<?= $i ?>" <?= $i == $inspeccion['golpe_top'] ? 'selected' : '' ?>>
                                                <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="rebaba" class="form-label">Rebaba</label>
                                <select class="form-control" id="rebaba" name="rebaba">
                                    <?php for ($i = 0; $i <= 5000; $i++): ?>
                                            <option value="<?= $i ?>" <?= $i == $inspeccion['rebaba'] ? 'selected' : '' ?>>
                                                <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="dano_en_lente" class="form-label">Daño en lente</label>
                                <select class="form-control" id="dano_en_lente" name="dano_en_lente">
                                    <?php for ($i = 0; $i <= 5000; $i++): ?>
                                            <option value="<?= $i ?>" <?= $i == $inspeccion['dano_en_lente'] ? 'selected' : '' ?>>
                                                <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="fuera_de_spc" class="form-label">Fuera de SPC</label>
                                <select class="form-control" id="fuera_de_spc" name="fuera_de_spc">
                                    <?php for ($i = 0; $i <= 5000; $i++): ?>
                                            <option value="<?= $i ?>" <?= $i == $inspeccion['fuera_de_spc'] ? 'selected' : '' ?>>
                                                <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="dano_fisico" class="form-label">Daño físico</label>
                                <select class="form-control" id="dano_fisico" name="dano_fisico">
                                    <?php for ($i = 0; $i <= 5000; $i++): ?>
                                            <option value="<?= $i ?>" <?= $i == $inspeccion['dano_fisico'] ? 'selected' : '' ?>>
                                                <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="coupler_danado" class="form-label">Coupler dañado</label>
                                <select class="form-control" id="coupler_danado" name="coupler_danado">
                                    <?php for ($i = 0; $i <= 5000; $i++): ?>
                                            <option value="<?= $i ?>" <?= $i == $inspeccion['coupler_danado'] ? 'selected' : '' ?>>
                                                <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="hundimiento" class="form-label">Hundimiento</label>
                                <select class="form-control" id="hundimiento" name="hundimiento">
                                    <?php for ($i = 0; $i <= 5000; $i++): ?>
                                            <option value="<?= $i ?>" <?= $i == $inspeccion['hundimiento'] ? 'selected' : '' ?>>
                                                <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="fisura" class="form-label">Fisura</label>
                                <select class="form-control" id="fisura" name="fisura">
                                    <?php for ($i = 0; $i <= 5000; $i++): ?>
                                            <option value="<?= $i ?>" <?= $i == $inspeccion['fisura'] ? 'selected' : '' ?>>
                                                <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="silicon_contaminacion" class="form-label">Contaminación de silicio</label>
                                <select class="form-control" id="silicon_contaminacion" name="silicon_contaminacion">
                                    <?php for ($i = 0; $i <= 5000; $i++): ?>
                                            <option value="<?= $i ?>" <?= $i == $inspeccion['silicon_contaminacion'] ? 'selected' : '' ?>><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="contaminacion_end_face" class="form-label">Contaminación del extremo (end
                                    face)</label>
                                <select class="form-control" id="contaminacion_end_face" name="contaminacion_end_face">
                                    <?php for ($i = 0; $i <= 5000; $i++): ?>
                                            <option value="<?= $i ?>" <?= $i == $inspeccion['contaminacion_end_face'] ? 'selected' : '' ?>><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="total" class="form-label">Total</label>
                                <input type="number" class="form-control" id="total" name="total" required readonly
                                    value="<?= $inspeccion['total'] ?>">
                            </div>

                            <div class="mb-3">
                                <label for="total_final" class="form-label">Total final</label>
                                <input type="number" class="form-control" id="total_final" name="total_final" required
                                    readonly value="<?= $inspeccion['total_final'] ?>">
                            </div>

                            <div class="mb-3">
                                <label for="comments" class="form-label">Comentarios</label>
                                <textarea class="form-control" id="comments"
                                    name="comments"><?= htmlspecialchars($inspeccion['comments']) ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </form>
                    </div>
                </div>





                <br><br><br>
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
        document.addEventListener('DOMContentLoaded', function () {
            const fields = [
                'coupler',
                'dano_end_face',
                'golpe_top',
                'rebaba',
                'dano_en_lente',
                'fuera_de_spc',
                'dano_fisico',
                'coupler_danado',
                'hundimiento',
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
        document.getElementById('formInspeccion').addEventListener('submit', function (event) {

            event.preventDefault();


            Swal.fire({
                title: 'Aplicando cambios...',
                text: 'Estamos aplicando los cambios realizados, por favor espera.',
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });


            this.submit();
        });
    </script>



</body>

</html>