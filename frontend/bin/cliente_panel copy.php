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



$total_records_sql = "SELECT COUNT(*) FROM PCBA";
$total_records_stmt = $con->prepare($total_records_sql);
$total_records_stmt->execute();
$total_records = $total_records_stmt->fetchColumn();


$total_pages = ceil($total_records / $records_per_page);


$offset = ($current_page - 1) * $records_per_page;


$sql_select = "SELECT * FROM PCBA ORDER BY inspection_date ASC LIMIT :offset, :records_per_page";
$stmt_select = $con->prepare($sql_select);


$stmt_select->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt_select->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
$stmt_select->execute();


$records = $stmt_select->fetchAll(PDO::FETCH_ASSOC);



$sql_sum_goods = "SELECT SUM(goods) AS total_goods FROM PCBA";
$stmt_sum_goods = $con->prepare($sql_sum_goods);
$stmt_sum_goods->execute();
$row_sum_goods = $stmt_sum_goods->fetch(PDO::FETCH_ASSOC);


$total_goods = $row_sum_goods['total_goods'] ?? 0;

$sql_sum_fails_dedos_oro = "SELECT SUM(fails_dedos_oro) AS total_fails_dedos_oro FROM PCBA";
$stmt_sum_fails_dedos_oro = $con->prepare($sql_sum_fails_dedos_oro);
$stmt_sum_fails_dedos_oro->execute();
$row_sum_fails_dedos_oro = $stmt_sum_fails_dedos_oro->fetch(PDO::FETCH_ASSOC);
$total_fails_dedos_oro = $row_sum_fails_dedos_oro['total_fails_dedos_oro'] ?? 0;


$sql_sum_fails_mal_corte = "SELECT SUM(fails_mal_corte) AS total_fails_mal_corte FROM PCBA";
$stmt_sum_fails_mal_corte = $con->prepare($sql_sum_fails_mal_corte);
$stmt_sum_fails_mal_corte->execute();
$row_sum_fails_mal_corte = $stmt_sum_fails_mal_corte->fetch(PDO::FETCH_ASSOC);
$total_fails_mal_corte = $row_sum_fails_mal_corte['total_fails_mal_corte'] ?? 0;


$sql_sum_fails_contaminacion = "SELECT SUM(fails_contaminacion) AS total_fails_contaminacion FROM PCBA";
$stmt_sum_fails_contaminacion = $con->prepare($sql_sum_fails_contaminacion);
$stmt_sum_fails_contaminacion->execute();
$row_sum_fails_contaminacion = $stmt_sum_fails_contaminacion->fetch(PDO::FETCH_ASSOC);
$total_fails_contaminacion = $row_sum_fails_contaminacion['total_fails_contaminacion'] ?? 0;


$sql_sum_pd = "SELECT SUM(pd) AS total_pd FROM PCBA";
$stmt_sum_pd = $con->prepare($sql_sum_pd);
$stmt_sum_pd->execute();
$row_sum_pd = $stmt_sum_pd->fetch(PDO::FETCH_ASSOC);
$total_pd = $row_sum_pd['total_pd'] ?? 0;


$sql_sum_fails_desplazados = "SELECT SUM(fails_desplazados) AS total_fails_desplazados FROM PCBA";
$stmt_sum_fails_desplazados = $con->prepare($sql_sum_fails_desplazados);
$stmt_sum_fails_desplazados->execute();
$row_sum_fails_desplazados = $stmt_sum_fails_desplazados->fetch(PDO::FETCH_ASSOC);
$total_fails_desplazados = $row_sum_fails_desplazados['total_fails_desplazados'] ?? 0;


$sql_sum_fails_insuficiencias = "SELECT SUM(fails_insuficiencias) AS total_fails_insuficiencias FROM PCBA";
$stmt_sum_fails_insuficiencias = $con->prepare($sql_sum_fails_insuficiencias);
$stmt_sum_fails_insuficiencias->execute();
$row_sum_fails_insuficiencias = $stmt_sum_fails_insuficiencias->fetch(PDO::FETCH_ASSOC);
$total_fails_insuficiencias = $row_sum_fails_insuficiencias['total_fails_insuficiencias'] ?? 0;


$sql_sum_fails_despanelizados = "SELECT SUM(fails_despanelizados) AS total_fails_despanelizados FROM PCBA";
$stmt_sum_fails_despanelizados = $con->prepare($sql_sum_fails_despanelizados);
$stmt_sum_fails_despanelizados->execute();
$row_sum_fails_despanelizados = $stmt_sum_fails_despanelizados->fetch(PDO::FETCH_ASSOC);
$total_fails_despanelizados = $row_sum_fails_despanelizados['total_fails_despanelizados'] ?? 0;


$sql_sum_fails_desprendidos = "SELECT SUM(fails_desprendidos) AS total_fails_desprendidos FROM PCBA";
$stmt_sum_fails_desprendidos = $con->prepare($sql_sum_fails_desprendidos);
$stmt_sum_fails_desprendidos->execute();
$row_sum_fails_desprendidos = $stmt_sum_fails_desprendidos->fetch(PDO::FETCH_ASSOC);
$total_fails_desprendidos = $row_sum_fails_desprendidos['total_fails_desprendidos'] ?? 0;


$sql_sum_total_fails = "SELECT SUM(total_fails) AS total_total_fails FROM PCBA";
$stmt_sum_total_fails = $con->prepare($sql_sum_total_fails);
$stmt_sum_total_fails->execute();
$row_sum_total_fails = $stmt_sum_total_fails->fetch(PDO::FETCH_ASSOC);
$total_total_fails = $row_sum_total_fails['total_total_fails'] ?? 0;


$sql_sum_total = "SELECT SUM(total) AS total_total FROM PCBA";
$stmt_sum_total = $con->prepare($sql_sum_total);
$stmt_sum_total->execute();
$row_sum_total = $stmt_sum_total->fetch(PDO::FETCH_ASSOC);
$total_total = $row_sum_total['total_total'] ?? 0;

if ($total_total != 0) {
    $result_division = ($total_goods / $total_total) * 100;
} else {
    $result_division = 0;
}


$result_division = round($result_division);




function getUniqueValues($column, $con)
{
    $sql = "SELECT DISTINCT $column FROM PCBA WHERE $column IS NOT NULL AND $column != '' ORDER BY $column";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

$descripcion_values = getUniqueValues('description', $con);
$shift_values = getUniqueValues('shift', $con);
$operators_values = getUniqueValues('operators', $con);
$goods_values = getUniqueValues('goods', $con);
$dedos_oro_values = getUniqueValues('fails_dedos_oro', $con);
$faltante_values = getUniqueValues('fails_mal_corte', $con);
$contaminacion_values = getUniqueValues('fails_contaminacion', $con);
$pd_values = getUniqueValues('pd', $con);
$desplazados_values = getUniqueValues('fails_desplazados', $con);
$insuficiencias_values = getUniqueValues('fails_insuficiencias', $con);
$despanelizados_values = getUniqueValues('fails_despanelizados', $con);
$desprendidos_values = getUniqueValues('fails_desprendidos', $con);
$total_values = getUniqueValues('total_fails', $con);
$total_final_values = getUniqueValues('total', $con);
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

    <?php
    $locale = 'es_ES.UTF-8';
    setlocale(LC_TIME, $locale);
    ?>

    <script type="text/javascript">
      /*  google.charts.load('current', { 'packages': ['corechart', 'bar'] });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var rawData = <?php echo json_encode($chartData); ?>;

        if (!rawData.length) {
            document.getElementById('chart_div').innerHTML = '<p>No hay datos disponibles para graficar.</p>';
            return;
        }

        function formatDate(dateString) {
            var date = new Date(dateString);
            var day = date.getDate().toString().padStart(2, '0');
            var month = (date.getMonth() + 1).toString().padStart(2, '0');
            var year = date.getFullYear();

            return day + '/' + month + '/' + year;
        }

        var data = google.visualization.arrayToDataTable([
            ['Fecha', 'Goods', 'Fails Dedos Oro', 'Fails Mal Corte', 'Fails Contaminacion', 'PD', 'Fails Desplazados', 'Fails Insuficiencias', 'Fails Despanelizados', 'Fails Desprendidos', 'Total Fails', 'Total'],
            <?php
            foreach ($chartData as $row) {
                $formattedDate = strftime("%A, %d/%m/%Y", strtotime($row['inspection_date'])); // Día de la semana, día, mes y año
                echo "['" . $formattedDate . "', " . $row['total_goods'] . ", " . $row['total_fails_dedos_oro'] . ", " . $row['total_fails_mal_corte'] . ", " . $row['total_fails_contaminacion'] . ", " . $row['total_pd'] . ", " . $row['total_fails_desplazados'] . ", " . $row['total_fails_insuficiencias'] . ", " . $row['total_fails_despanelizados'] . ", " . $row['total_fails_desprendidos'] . ", " . $row['total_total_fails'] . ", " . $row['total_total'] . "],";
            }
            ?>
        ]);

        var options = {
            title: 'MOLEX PCBA - Material Acumulado o de Almacén',
            chartArea: { width: '50%' },
            hAxis: { title: 'Cantidad', minValue: 0 },
            vAxis: { title: 'Fecha' },
            isStacked: true,
        };

        var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
        chart.draw(data, options);
        }*/
    </script>


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
                                <!--<li class="active"><a href="cliente_panel.php">Home</a></li>-->
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
                <h3 class="text-center mt-3">Records PCBA</h3>


                <button id="resetFilters" class="btn btn-danger mb-3"><i class="bx bx-reset"></i> Reset Filters</button>
                <div class="table-responsive">

                    <table class="table table-bordered text-center" id="inspectionTable" style="border-color: black;">
                        <thead style="background-color: #D9DAD9;">
                            <tr>
                                <th colspan="5" style="color:#000000; border-color: black;"> </th>
                                <th colspan="7"
                                    style="background-color:#000000; color:#ffffff; text-align:center; border-color: black;">
                                    Fails
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
                                <th class="text-center" style="color:#0fcb59; border-color: black;">
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
                                <th class="text-center" style="color:#000000; border-color: black;">
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
                                <th class="text-center" style="color:#000000; border-color: black;">
                                    MAL CORTE
                                    <select id="filter_mal_corte" class="form-select form-select-sm">
                                        <option value="">All</option>
                                        <?php
                                        foreach ($faltante_values as $value) {
                                            echo "<option value=\"$value\">$value</option>";
                                        }
                                        ?>
                                    </select>
                                </th>
                                <th class="text-center" style="color:#000000; border-color: black;">
                                    CONTAMINACION
                                    <select id="filter_contaminacion" class="form-select form-select-sm">
                                        <option value="">All</option>
                                        <?php
                                        foreach ($contaminacion_values as $value) {
                                            echo "<option value=\"$value\">$value</option>";
                                        }
                                        ?>
                                    </select>
                                </th>
                                <th class="text-center" style="color:#000000; border-color: black;">
                                    PD
                                    <select id="filter_pd" class="form-select form-select-sm">
                                        <option value="">All</option>
                                        <?php
                                        foreach ($pd_values as $value) {
                                            echo "<option value=\"$value\">$value</option>";
                                        }
                                        ?>
                                    </select>
                                </th>
                                <th class="text-center" style="color:#000000; border-color: black;">
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
                                <th class="text-center" style="color:#000000; border-color: black;">
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
                                <th class="text-center" style="color:#000000; border-color: black;">
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
                                <th class="text-center" style="color:#000000; border-color: black;">
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
                                <th class="text-center" style="color:#000000; border-color: black;">
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
                                <th class="text-center" style="color:#0fcb59; border-color: black;">
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
                                <th class="text-center" style="color:#0fcb59; border-color: black;">
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
                                <th class="text-center" style="color:#000000; border-color: black;">
                                    COMMENTS
                                    <select id="filter_comments" class="form-select form-select-sm">
                                        <option value="">All</option>
                                    </select>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="4"
                                    style="background-color:#626262; color:#ffffff; text-align:center; border-color: black;">
                                    GRAN
                                    TOTAL / SEMANA 29</th>
                                <th style="color:#000000; border-color: black;">
                                    <?php echo number_format($total_goods); ?></th>
                                <th style="color:#000000; border-color: black;"><?php echo $total_fails_dedos_oro; ?>
                                </th>
                                <th style="color:#000000; border-color: black;"><?php echo $total_fails_mal_corte; ?>
                                </th>
                                <th style="color:#000000; border-color: black;">
                                    <?php echo $total_fails_contaminacion; ?></th>
                                <th style="color:#000000; border-color: black;"><?php echo $total_pd; ?></th>
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
                                    <?php echo number_format($total_total); ?></th>
                                <th style="color:#000000; border-color: black;"><?php echo $result_division . '%'; ?>
                                </th>
                                <th style="color:#000000; border-color: black;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($records)): ?>
                                <tr>
                                    <td colspan="16" class="fw-bold text-danger" style="border-color: black;">No records
                                        available.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($records as $record): ?>
                                    <tr>
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
                                            <?= htmlspecialchars($record['description']) ?>
                                        </td>
                                        <td style="border-color: black; color:#000000;">
                                            <?= htmlspecialchars($record['shift']) ?></td>
                                        <td style="border-color: black; color:#000000;">
                                            <?= htmlspecialchars($record['operators']) ?></td>
                                        <td style="border-color: black; color:#000000;">
                                            <?= htmlspecialchars($record['goods']) ?></td>
                                        <td
                                            style="border-color: black; <?= $record['fails_dedos_oro'] != 0 ? 'color:#FF0000; background-color: #FFCCCC;' : ''; ?>">
                                            <?= $record['fails_dedos_oro'] != 0 ? '<b>' . htmlspecialchars($record['fails_dedos_oro']) . '</b>' : htmlspecialchars($record['fails_dedos_oro']) ?>
                                        </td>
                                        <td
                                            style="border-color: black; <?= $record['fails_mal_corte'] != 0 ? 'color:#FF0000; background-color: #FFCCCC;' : ''; ?>">
                                            <?= $record['fails_mal_corte'] != 0 ? '<b>' . htmlspecialchars($record['fails_mal_corte']) . '</b>' : htmlspecialchars($record['fails_mal_corte']) ?>
                                        </td>
                                        <td
                                            style="border-color: black; <?= $record['fails_contaminacion'] != 0 ? 'color:#FF0000; background-color: #FFCCCC;' : ''; ?>">
                                            <?= $record['fails_contaminacion'] != 0 ? '<b>' . htmlspecialchars($record['fails_contaminacion']) . '</b>' : htmlspecialchars($record['fails_contaminacion']) ?>
                                        </td>
                                        <td
                                            style="border-color: black; <?= $record['pd'] != 0 ? 'color:#FF0000; background-color: #FFCCCC;' : ''; ?>">
                                            <?= $record['pd'] != 0 ? '<b>' . htmlspecialchars($record['pd']) . '</b>' : htmlspecialchars($record['pd']) ?>
                                        </td>
                                        <td
                                            style="border-color: black; <?= $record['fails_desplazados'] != 0 ? 'color:#FF0000; background-color: #FFCCCC;' : ''; ?>">
                                            <?= $record['fails_desplazados'] != 0 ? '<b>' . htmlspecialchars($record['fails_desplazados']) . '</b>' : htmlspecialchars($record['fails_desplazados']) ?>
                                        </td>
                                        <td
                                            style="border-color: black; <?= $record['fails_insuficiencias'] != 0 ? 'color:#FF0000; background-color: #FFCCCC;' : ''; ?>">
                                            <?= $record['fails_insuficiencias'] != 0 ? '<b>' . htmlspecialchars($record['fails_insuficiencias']) . '</b>' : htmlspecialchars($record['fails_insuficiencias']) ?>
                                        </td>
                                        <td
                                            style="border-color: black; <?= $record['fails_despanelizados'] != 0 ? 'color:#FF0000; background-color: #FFCCCC;' : ''; ?>">
                                            <?= $record['fails_despanelizados'] != 0 ? '<b>' . htmlspecialchars($record['fails_despanelizados']) . '</b>' : htmlspecialchars($record['fails_despanelizados']) ?>
                                        </td>
                                        <td
                                            style="border-color: black; <?= $record['fails_desprendidos'] != 0 ? 'color:#FF0000; background-color: #FFCCCC;' : ''; ?>">
                                            <?= $record['fails_desprendidos'] != 0 ? '<b>' . htmlspecialchars($record['fails_desprendidos']) . '</b>' : htmlspecialchars($record['fails_desprendidos']) ?>
                                        </td>
                                        <td
                                            style="border-color: black; <?= $record['total_fails'] != 0 ? 'color:#FF0000; background-color: #FFCCCC;' : ''; ?>">
                                            <?= $record['total_fails'] != 0 ? '<b>' . htmlspecialchars($record['total_fails']) . '</b>' : htmlspecialchars($record['total_fails']) ?>
                                        </td>
                                        <td style="border-color: black; color:#000000;">
                                            <?= htmlspecialchars($record['total']) ?></td>
                                        <td style="border-color: black; color:#000000;">
                                            <?= htmlspecialchars($record['yield']) ?></td>
                                        <td style="border-color: black; color:#000000;">
                                            <?= htmlspecialchars($record['comments']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>



            </div>
            <div class="pagination"
                style="display: flex; justify-content: center; align-items: center; text-align: center; margin-top: 20px;">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?= $current_page - 1 ?>" class="btn btn-primary" style="margin-right: 5px;">Previous</a>
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
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var table = document.getElementById('inspectionTable');
                var exportButton = document.getElementById('exportButton');

                var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
                var hasRecords = rows.length > 0 && rows[0].getElementsByTagName('td').length > 1;

                exportButton.disabled = !hasRecords;
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const resetButton = document.getElementById("resetFilters");
                const filterDate = document.getElementById("filter_date");
                const filterDescription = document.getElementById("filter_description");
                const filterShift = document.getElementById("filter_shift");
                const filterOperators = document.getElementById("filter_operators");
                const filterGoods = document.getElementById("filter_goods");
                const filterDedosOro = document.getElementById("filter_dedos_oro");
                const filterMalCorte = document.getElementById("filter_mal_corte");
                const filterContaminacion = document.getElementById("filter_contaminacion");
                const filterPd = document.getElementById("filter_pd");
                const filterDesplazados = document.getElementById("filter_desplazados");
                const filterInsuficiencias = document.getElementById("filter_insuficiencias");
                const filterDespanelizados = document.getElementById("filter_despanelizados");
                const filterDesprendidos = document.getElementById("filter_desprendidos");
                const filterTotal = document.getElementById("filter_total");
                const filterTotalFinal = document.getElementById("filter_total_final");
                const filterYield = document.getElementById("filter_yield");
                const filterComments = document.getElementById("filter_comments");

                const tableRows = document.querySelectorAll("#inspectionTable tbody tr");

                function filterTable() {
                    tableRows.forEach(row => {
                        let showRow = true;


                        if (filterDate.value) {
                            const rowDate = row.cells[0].textContent.trim();
                            if (rowDate !== filterDate.value) {
                                showRow = false;
                            }
                        }

                        if (filterDescription.value && !row.cells[1].textContent.includes(filterDescription.value)) {
                            showRow = false;
                        }
                        if (filterShift.value && !row.cells[2].textContent.includes(filterShift.value)) {
                            showRow = false;
                        }
                        if (filterOperators.value && !row.cells[3].textContent.includes(filterOperators.value)) {
                            showRow = false;
                        }
                        if (filterGoods.value && !row.cells[4].textContent.includes(filterGoods.value)) {
                            showRow = false;
                        }
                        if (filterDedosOro.value && !row.cells[5].textContent.includes(filterDedosOro.value)) {
                            showRow = false;
                        }
                        if (filterMalCorte.value && !row.cells[6].textContent.includes(filterMalCorte.value)) {
                            showRow = false;
                        }
                        if (filterContaminacion.value && !row.cells[7].textContent.includes(filterContaminacion.value)) {
                            showRow = false;
                        }
                        if (filterPd.value && !row.cells[8].textContent.includes(filterPd.value)) {
                            showRow = false;
                        }
                        if (filterDesplazados.value && !row.cells[9].textContent.includes(filterDesplazados.value)) {
                            showRow = false;
                        }
                        if (filterInsuficiencias.value && !row.cells[10].textContent.includes(filterInsuficiencias.value)) {
                            showRow = false;
                        }
                        if (filterDespanelizados.value && !row.cells[11].textContent.includes(filterDespanelizados.value)) {
                            showRow = false;
                        }
                        if (filterDesprendidos.value && !row.cells[12].textContent.includes(filterDesprendidos.value)) {
                            showRow = false;
                        }
                        if (filterTotal.value && !row.cells[13].textContent.includes(filterTotal.value)) {
                            showRow = false;
                        }
                        if (filterTotalFinal.value && !row.cells[14].textContent.includes(filterTotalFinal.value)) {
                            showRow = false;
                        }
                        if (filterYield.value && !row.cells[15].textContent.includes(filterYield.value)) {
                            showRow = false;
                        }
                        if (filterComments.value && !row.cells[16].textContent.includes(filterComments.value)) {
                            showRow = false;
                        }

                        if (showRow) {
                            row.style.display = "";
                        } else {
                            row.style.display = "none";
                        }
                    });
                }

                resetButton.addEventListener("click", function () {
                    filterDate.value = '';
                    filterDescription.value = '';
                    filterShift.value = '';
                    filterOperators.value = '';
                    filterGoods.value = '';
                    filterDedosOro.value = '';
                    filterMalCorte.value = '';
                    filterContaminacion.value = '';
                    filterPd.value = '';
                    filterDesplazados.value = '';
                    filterInsuficiencias.value = '';
                    filterDespanelizados.value = '';
                    filterDesprendidos.value = '';
                    filterTotal.value = '';
                    filterTotalFinal.value = '';
                    filterYield.value = '';
                    filterComments.value = '';

                    filterTable();
                });

                filterDate.addEventListener("input", filterTable);
                filterDescription.addEventListener("change", filterTable);
                filterShift.addEventListener("change", filterTable);
                filterOperators.addEventListener("change", filterTable);
                filterGoods.addEventListener("change", filterTable);
                filterDedosOro.addEventListener("change", filterTable);
                filterMalCorte.addEventListener("change", filterTable);
                filterContaminacion.addEventListener("change", filterTable);
                filterPd.addEventListener("change", filterTable);
                filterDesplazados.addEventListener("change", filterTable);
                filterInsuficiencias.addEventListener("change", filterTable);
                filterDespanelizados.addEventListener("change", filterTable);
                filterDesprendidos.addEventListener("change", filterTable);
                filterTotal.addEventListener("change", filterTable);
                filterTotalFinal.addEventListener("change", filterTable);
                filterYield.addEventListener("change", filterTable);
                filterComments.addEventListener("change", filterTable);

                flatpickr("#filter_date", {
                    dateFormat: "Y-m-d",
                    onChange: function (selectedDates, dateStr, instance) {
                        filterTable();
                    }
                });

                filterTable();
            });
        </script>

</body>

</html>