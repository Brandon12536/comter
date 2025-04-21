<?php
session_start();
require '../config/connection.php';

$id_usuario = $_SESSION['id_usuarios'];
$db = new Database();
$con = $db->conectar();

$sql = "SELECT nombre, apellido, role, verificado FROM usuarios WHERE id_usuarios = :id_usuario";
$stmt = $con->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $nombre = $row['nombre'];
    $apellido = $row['apellido'];
    $role = $row['role'];
    $verificado = $row['verificado'];

    if ((int) $verificado === 0) {
        echo 'Tu cuenta no está verificada. Por favor, contacta con el administrador.';
        header('Location: ../index.php');
        exit();
    }


    if ($role !== 'Cliente') {
        echo 'No tienes permiso para acceder a esta página.';
        header('Location: ../index.php');
        exit();
    }


    $imageSrc = '../assets/img/avatars/1.png';

} else {
    echo 'Usuario no encontrado o cuenta no válida.';
    header('Location: ../index.php');
    exit();
}


/*
$sql = "SELECT MIN(inspection_date) AS start_date, MAX(inspection_date) AS end_date FROM PCBA";
$stmt = $con->prepare($sql);
$stmt->execute();

$dateRange = $stmt->fetch(PDO::FETCH_ASSOC);
$start_date = $dateRange['start_date'] ?? '2025-01-01';
$end_date = $dateRange['end_date'] ?? date('Y-m-d');


$sql = "
  SELECT
    inspection_date,
    operators,
    SUM(goods) AS total_goods,
    SUM(fails_dedos_oro) AS total_fails_dedos_oro,
    SUM(fails_mal_corte) AS total_fails_mal_corte,
    SUM(fails_contaminacion) AS total_fails_contaminacion,
    SUM(pd) AS total_pd,
    SUM(fails_desplazados) AS total_fails_desplazados,
    SUM(fails_insuficiencias) AS total_fails_insuficiencias,
    SUM(fails_despanelizados) AS total_fails_despanelizados,
    SUM(fails_desprendidos) AS total_fails_desprendidos,
    SUM(total_fails) AS total_total_fails,
    SUM(total) AS total_total
  FROM
    PCBA
  WHERE
    inspection_date BETWEEN :start_date AND :end_date
  GROUP BY
    inspection_date, operators
  ORDER BY
    inspection_date DESC";


$stmt = $con->prepare($sql);
$stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
$stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);



$chartData = [];
foreach ($data as $row) {
    $chartData[] = [
        'inspection_date' => $row['inspection_date'],
        'total_goods' => $row['total_goods'],
        'total_fails_dedos_oro' => $row['total_fails_dedos_oro'],
        'total_fails_mal_corte' => $row['total_fails_mal_corte'],
        'total_fails_contaminacion' => $row['total_fails_contaminacion'],
        'total_pd' => $row['total_pd'],
        'total_fails_desplazados' => $row['total_fails_desplazados'],
        'total_fails_insuficiencias' => $row['total_fails_insuficiencias'],
        'total_fails_despanelizados' => $row['total_fails_despanelizados'],
        'total_fails_desprendidos' => $row['total_fails_desprendidos'],
        'total_total_fails' => $row['total_total_fails'],
        'total_total' => $row['total_total']
    ];
}

*/

$records_per_page = 20;
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

$total_records_sql = "SELECT COUNT(*) FROM materiales";
$total_records_stmt = $con->prepare($total_records_sql);
$total_records_stmt->execute();
$total_records = $total_records_stmt->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);

$sql_sum_goods = "SELECT SUM(goods) AS total_goods FROM materiales";
$stmt_sum_goods = $con->prepare($sql_sum_goods);
$stmt_sum_goods->execute();
$row_sum_goods = $stmt_sum_goods->fetch(PDO::FETCH_ASSOC);
$total_goods = $row_sum_goods['total_goods'] ?? 0;

$sql_sum_fails_dedos_oro = "SELECT SUM(dedos_de_oro_contaminados) AS total_fails_dedos_oro FROM materiales";
$stmt_sum_fails_dedos_oro = $con->prepare($sql_sum_fails_dedos_oro);
$stmt_sum_fails_dedos_oro->execute();
$row_sum_fails_dedos_oro = $stmt_sum_fails_dedos_oro->fetch(PDO::FETCH_ASSOC);
$total_fails_dedos_oro = $row_sum_fails_dedos_oro['total_fails_dedos_oro'] ?? 0;

$sql_sum_fails_faltante = "SELECT SUM(faltante) AS total_fails_faltante FROM materiales";
$stmt_sum_fails_faltante = $con->prepare($sql_sum_fails_faltante);
$stmt_sum_fails_faltante->execute();
$row_sum_fails_faltante = $stmt_sum_fails_faltante->fetch(PDO::FETCH_ASSOC);
$faltante = $row_sum_fails_faltante['total_fails_faltante'] ?? 0;

$sql_sum_fails_desplazados = "SELECT SUM(desplazados) AS total_fails_desplazados FROM materiales";
$stmt_sum_fails_desplazados = $con->prepare($sql_sum_fails_desplazados);
$stmt_sum_fails_desplazados->execute();
$row_sum_fails_desplazados = $stmt_sum_fails_desplazados->fetch(PDO::FETCH_ASSOC);
$total_fails_desplazados = $row_sum_fails_desplazados['total_fails_desplazados'] ?? 0;

$sql_sum_fails_insuficiencias = "SELECT SUM(insuficiencias) AS total_fails_insuficiencias FROM materiales";
$stmt_sum_fails_insuficiencias = $con->prepare($sql_sum_fails_insuficiencias);
$stmt_sum_fails_insuficiencias->execute();
$row_sum_fails_insuficiencias = $stmt_sum_fails_insuficiencias->fetch(PDO::FETCH_ASSOC);
$total_fails_insuficiencias = $row_sum_fails_insuficiencias['total_fails_insuficiencias'] ?? 0;

$sql_sum_fails_despanelizados = "SELECT SUM(despanelizados) AS total_fails_despanelizados FROM materiales";
$stmt_sum_fails_despanelizados = $con->prepare($sql_sum_fails_despanelizados);
$stmt_sum_fails_despanelizados->execute();
$row_sum_fails_despanelizados = $stmt_sum_fails_despanelizados->fetch(PDO::FETCH_ASSOC);
$total_fails_despanelizados = $row_sum_fails_despanelizados['total_fails_despanelizados'] ?? 0;

$sql_sum_fails_desprendidos = "SELECT SUM(desprendidos) AS total_fails_desprendidos FROM materiales";
$stmt_sum_fails_desprendidos = $con->prepare($sql_sum_fails_desprendidos);
$stmt_sum_fails_desprendidos->execute();
$row_sum_fails_desprendidos = $stmt_sum_fails_desprendidos->fetch(PDO::FETCH_ASSOC);
$total_fails_desprendidos = $row_sum_fails_desprendidos['total_fails_desprendidos'] ?? 0;

$sql_sum_total_fails = "SELECT SUM(total) AS total_total_fails FROM materiales";
$stmt_sum_total_fails = $con->prepare($sql_sum_total_fails);
$stmt_sum_total_fails->execute();
$row_sum_total_fails = $stmt_sum_total_fails->fetch(PDO::FETCH_ASSOC);
$total_total_fails = $row_sum_total_fails['total_total_fails'] ?? 0;

$sql_sum_total_final = "SELECT SUM(total_final) AS total_total_final FROM materiales";
$stmt_sum_total_final = $con->prepare($sql_sum_total_final);
$stmt_sum_total_final->execute();
$row_sum_total_final = $stmt_sum_total_final->fetch(PDO::FETCH_ASSOC);
$total_total_final = $row_sum_total_final['total_total_final'] ?? 0;

$sql_sum_total = "SELECT SUM(total) AS total_total FROM materiales";
$stmt_sum_total = $con->prepare($sql_sum_total);
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

$sql = "SELECT * FROM materiales ORDER BY inspection_date ASC, id_material ASC LIMIT :offset, :records_per_page";
$stmt = $con->prepare($sql);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->execute();

$records = $stmt->fetchAll(PDO::FETCH_ASSOC);




function getUniqueValues($column, $con)
{
    $sql = "SELECT DISTINCT $column FROM materiales WHERE $column IS NOT NULL AND $column != '' ORDER BY $column";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

$descripcion_values = getUniqueValues('descripcion', $con);
$shift_values = getUniqueValues('shift', $con);
$operators_values = getUniqueValues('operators', $con);
$goods_values = getUniqueValues('goods', $con);
$dedos_oro_values = getUniqueValues('dedos_de_oro_contaminados', $con);
$faltante_values = getUniqueValues('faltante', $con);
$desplazados_values = getUniqueValues('desplazados', $con);
$insuficiencias_values = getUniqueValues('insuficiencias', $con);
$despanelizados_values = getUniqueValues('despanelizados', $con);
$desprendidos_values = getUniqueValues('desprendidos', $con);
$total_values = getUniqueValues('total', $con);
$total_final_values = getUniqueValues('total_final', $con);
$yield_values = getUniqueValues('yield', $con);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../ico/comter.png" type="image/x-icon">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="../css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="../css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="../css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="../css/style.css" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>


    <title>Comter</title>

</head>

<body>



    <header class="header fixed-top" style="background-color:#1B419B;">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="header__logo">
                        <a href="cliente_panel.php"><img src="../ico/comter.png" alt="" style="width:50px"></a>
                    </div>
                </div>
                <div class="col-lg-10">
                    <div class="header__nav__option">
                        <nav class="header__nav__menu mobile-menu">
                            <ul>

                                <li>
                                    <select class="form-select transparent-select"
                                        onchange="window.location.href=this.value">
                                        <option value="#" selected disabled>Seleccione una opción</option>
                                        <option value="cliente_panel.php">PCBA</option>
                                        <option value="materiales.php">Materiales Acumulados o de Almacén</option>
                                        <option value="sem42.php">MOLEX SEM42</option>
                                        <option value="sem43.php">MOLEX SEM43</option>
                                        <option value="sem44.php">MOLEX SEM44</option>
                                        <option value="sem45.php">MOLEX SEM45</option>
                                        <option value="sem46.php">MOLEX SEM46</option>
                                        <option value="sem47.php">MOLEX SEM47</option>
                                        <option value="sem48.php">MOLEX SEM48</option>
                                        <option value="sem49.php">MOLEX SEM49</option>
                                        <option value="sem50.php">MOLEX SEM50</option>
                                        <option value="sem51.php">MOLEX SEM51</option>
                                        <option value="sem52.php">MOLEX SEM52</option>
                                    </select>
                                </li>
                                <li>
                                    <a href="graficas.php" class="d-flex align-items-center">
                                        <i class="fas fa-chart-pie"></i> &nbsp;&nbsp;Gráficas
                                    </a>
                                </li>
                                <li>
                                    <div class="d-flex align-items-center">
                                        <!--<button id="btnGraficar" class="btn btn-primary" onclick="drawChart()">Graficar</button>-->
                                        <span class="fw-semibold d-block ms-2"
                                            style="color:#fff; text-transform: uppercase;"><span
                                                style="text-transform: uppercase;">Bienvenido </span>
                                            <?php echo $role; ?></span>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </div>
                                </li>
                                <li>
                                    <a href="#">
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $imageSrc; ?>" alt="" class="user-image" />
                                            <span
                                                class="fw-semibold d-block ms-2"><?php echo $nombre . ' ' . $apellido; ?></span>
                                        </div>
                                    </a>
                                    <ul class="dropdown">

                                        <li> <a href="#"> <small class="text-muted">Rol:
                                                    <?php echo $role; ?></small></a>
                                            <hr>

                                        <li><a href="#" onclick="confirmLogout()">Cerrar sesión</a></li>
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




    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;600;700;800&display=swap');

        html {
            height: 100%;
            background-color: #E5E5E6;
        }



        a {
            text-decoration: none;
            color: inherit;
        }

        * {
            box-sizing: border-box;
        }

        .user-image {
            width: 25px;
            height: 25px;
            border-radius: 50%;
        }


        .d-flex {
            display: flex;
            align-items: center;
        }


        .ms-2 {
            margin-left: 0.5rem;
        }

        body {
            background-color: #E5E5E6;
        }


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

        .transparent-select {
            background-color: transparent;
            border: none;
            color: #fff;
            box-shadow: none;
        }

        .transparent-select option {
            background-color: transparent;
            color: #000;
        }


        .transparent-select option:disabled {
            color: #fff;
        }
    </style>


    <!--<div class="container">
        <div id="chart_div" style="width: 100%; height: 500px;"></div>
    </div>-->



    <div class="content-wrapper">
        <div class="container my-5">

            <div class="container">
                <h3 class="text-center mt-3">Records Materiales Acumulados o de Almacén</h3>

                <button id="resetFilters" class="btn btn-danger mb-3"> <i class="fa fa-refresh"></i> Reset
                    Filters</button>
                <div class="table-responsive" style="max-height: 700px; overflow-y: auto;">
                    <table class="table table-bordered text-center" id="inspectionTable" style="border-color: black;">
                        <thead style="background-color: #D9DAD9; position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th colspan="5" style="color:#000000; border-color: black;"> </th>
                                <th colspan="7"
                                    style="background-color:#000000; color:#ffffff; text-align:center; border-color: black;">
                                    Fails Report
                                </th>
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
                                    <select id="filter_shift" class="form-select form-select-sm mt-2">
                                        <option value="">All</option>
                                        <?php
                                        foreach ($shift_values as $value) {
                                            echo "<option value=\"$value\">$value</option>";
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
                            </tr>
                            <tr>
                                <th colspan="4"
                                    style="background-color:#626262; color:#ffffff; text-align:center; border-color: black;">
                                    GRAN TOTAL / SEMANA 29
                                </th>
                                <th style="color:#000000; border-color: black;">
                                    <?php echo number_format($total_goods); ?>
                                </th>
                                <th style="color:#000000; border-color: black;"><?php echo $total_fails_dedos_oro; ?>
                                </th>
                                <th style="color:#000000; border-color: black;"><?php echo $faltante; ?></th>
                                <th style="color:#000000; border-color: black;"><?php echo $total_fails_desplazados; ?>
                                </th>
                                <th style="color:#000000; border-color: black;">
                                    <?php echo $total_fails_insuficiencias; ?></th>
                                <th style="color:#000000; border-color: black;">
                                    <?php echo $total_fails_despanelizados; ?></th>
                                <th style="color:#000000; border-color: black;"><?php echo $total_fails_desprendidos; ?>
                                </th>
                                <th style="color:#000000; border-color: black;"><?php echo $total_total_fails; ?></th>
                                <th style="color:#000000; border-color: black;">
                                    <?php echo number_format($total_total_final); ?></th>
                                <th style="color:#000000; border-color: black;"><?php echo $result_division . '%'; ?>
                                </th>
                                <th style="color:#000000; border-color: black;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($records)): ?>
                                <tr>
                                    <td colspan="16" class="fw-bold text-danger" style="border-color: black;">No records
                                        available.</td>
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
                                            if (!empty($record['descripcion_image'])) {
                                                echo '<img src="data:image/jpeg;base64,' . base64_encode($record['descripcion_image']) . '" alt="Descripción" style="max-width: 200px; max-height: 100px;" />';
                                            } elseif (!empty($record['descripcion'])) {
                                                echo htmlspecialchars($record['descripcion']);
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </td>
                                        <td style="border-color: black; color:#000000;">
                                            <?= htmlspecialchars($record['shift']) ?>
                                        </td>
                                        <td style="border-color: black; color:#000000;">
                                            <?= htmlspecialchars($record['operators']) ?>
                                        </td>
                                        <td style="border-color: black; color:#000000;">
                                            <?= htmlspecialchars($record['goods']) ?>
                                        </td>
                                        <td style="border-color: black; color:#000000;">
                                            <?= htmlspecialchars($record['dedos_de_oro_contaminados']) ?>
                                        </td>
                                        <td style="border-color: black; color:#000000;">
                                            <?= htmlspecialchars($record['faltante']) ?>
                                        </td>
                                        <td style="border-color: black; color:#000000;">
                                            <?= htmlspecialchars($record['desplazados']) ?>
                                        </td>
                                        <td style="border-color: black; color:#000000;">
                                            <?= htmlspecialchars($record['insuficiencias']) ?>
                                        </td>
                                        <td style="border-color: black; color:#000000;">
                                            <?= htmlspecialchars($record['despanelizados']) ?>
                                        </td>
                                        <td style="border-color: black; color:#000000;">
                                            <?= htmlspecialchars($record['desprendidos']) ?>
                                        </td>
                                        <td style="border-color: black; color:#000000;">
                                            <?= htmlspecialchars($record['total']) ?>
                                        </td>
                                        <td style="border-color: black; color:#000000;">
                                            <?= htmlspecialchars($record['total_final']) ?>
                                        </td>
                                        <td style="border-color: black; color:#000000;">
                                            <?= htmlspecialchars($record['yield']) ?>%
                                        </td>
                                        <td style="border-color: black; color:#000000;">
                                            <?= htmlspecialchars($record['comments']) ?>
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
            </div>
            <br><br>
            <footer class="content-footer footer" style="background-color:#edebea">
                <div class="container-xxl d-flex flex-wrap justify-content-center py-2 flex-md-row flex-column text-center"
                    style="color:#838383;">
                    <div class="mb-2 mb-md-0 fw-bolder">
                        ©
                        <script>
                            document.write(new Date().getFullYear());
                        </script>
                        , Comter |
                        <a href="#" class="footer-link fw-bolder" style="color: #6c757d;">Osdems Digital Group</a>
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
                            window.location.href = '../admin/backend/home/logout.php';
                        }
                    });
                }
            </script>
            <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

            <script>
                document.addEventListener('DOMContentLoaded', function () {

                    flatpickr("#filter_date", {
                        dateFormat: "Y-m-d",
                        onChange: function (selectedDates, dateStr, instance) {
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
                        filterElement.addEventListener("change", filterTable);
                    });


                    function filterTable() {
                        const rows = document.querySelectorAll("#inspectionTable tbody tr");

                        rows.forEach(function (row) {
                            let showRow = true;


                            const filterDate = document.getElementById("filter_date").value;
                            const filterDescription = document.getElementById("filter_description").value.toLowerCase();
                            const filterShift = document.getElementById("filter_shift").value.toLowerCase();
                            const filterOperators = document.getElementById("filter_operators").value.toLowerCase();
                            const filterGoods = document.getElementById("filter_goods").value.toLowerCase();
                            const filterDedosOro = document.getElementById("filter_dedos_oro").value.toLowerCase();
                            const filterFaltante = document.getElementById("filter_faltante").value.toLowerCase();
                            const filterDesplazados = document.getElementById("filter_desplazados").value.toLowerCase();
                            const filterInsuficiencias = document.getElementById("filter_insuficiencias").value.toLowerCase();
                            const filterDespanelizados = document.getElementById("filter_despanelizados").value.toLowerCase();
                            const filterDesprendidos = document.getElementById("filter_desprendidos").value.toLowerCase();
                            const filterTotal = document.getElementById("filter_total").value.toLowerCase();
                            const filterTotalFinal = document.getElementById("filter_total_final").value.toLowerCase();
                            const filterYield = document.getElementById("filter_yield").value.toLowerCase();
                            const filterComments = document.getElementById("filter_comments").value.toLowerCase();


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
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    }
                });
            </script>
            <script>
                document.getElementById('resetFilters').addEventListener('click', function () {

                    document.getElementById('filter_date').value = '';
                    document.getElementById('filter_description').value = '';
                    document.getElementById('filter_shift').value = '';
                    document.getElementById('filter_operators').value = '';
                    document.getElementById('filter_goods').value = '';
                    document.getElementById('filter_dedos_oro').value = '';
                    document.getElementById('filter_faltante').value = '';
                    document.getElementById('filter_desplazados').value = '';
                    document.getElementById('filter_insuficiencias').value = '';
                    document.getElementById('filter_despanelizados').value = '';
                    document.getElementById('filter_desprendidos').value = '';
                    document.getElementById('filter_total').value = '';
                    document.getElementById('filter_total_final').value = '';
                    document.getElementById('filter_yield').value = '';
                    document.getElementById('filter_comments').value = '';


                    reloadTableData();
                });

                function reloadTableData() {

                    location.reload();
                }

            </script>

</body>

</html>