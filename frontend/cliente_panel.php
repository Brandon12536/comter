<?php
session_start();
require '../config/connection.php';

$id_usuario = $_SESSION['id_usuarios'];
$db = new Database();
$con = $db->conectar();

$sql = "SELECT firstname, lastname, role, photo, active FROM usuarios WHERE id_usuarios = :id_usuario";
$stmt = $con->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $firstname = $row['firstname'];
    $lastname = $row['lastname'];
    $role = $row['role'];
    $photo = $row['photo'];
    $active = $row['active'];

    if ((int) $active === 0) {
        echo 'Tu cuenta ha sido desactivada. Por favor, contacta con el administrador.';
        header('Location: ../index.php');
        exit();
    }

    if ($role !== 'Cliente') {
        echo 'No tienes permiso para acceder a esta página.';
        header('Location: ../index.php');
        exit();
    }


    if ($photo !== null) {
        $base64Image = base64_encode($photo);
        $imageSrc = 'data:image/jpeg;base64,' . $base64Image;
    } else {
        $imageSrc = '../assets/img/avatars/1.png';
    }
} else {
    echo 'Usuario no encontrado o cuenta no válida.';
    header('Location: ../index.php');
    exit();
}



$sql = "SELECT * FROM wire_failures";
$stmt = $con->prepare($sql);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

$wireFailuresData = [];
$totalGeneral = 0;

foreach ($records as $record) {
    $a = isset($record['a']) && $record['a'] !== '' ? (int) $record['a'] : 0;
    $b = isset($record['b']) && $record['b'] !== '' ? (int) $record['b'] : 0;
    $c = isset($record['c']) && $record['c'] !== '' ? (int) $record['c'] : 0;
    $a_and_b = isset($record['a_and_b']) && $record['a_and_b'] !== '' ? (int) $record['a_and_b'] : 0;
    $goods = isset($record['goods']) && $record['goods'] !== '' ? (int) $record['goods'] : 0;
    $total = $a + $b + $c + $a_and_b + $goods;

    $totalGeneral += $total;
    $wireFailuresData[] = [
        'box' => $record['box'],
        'a' => $a,
        'b' => $b,
        'c' => $c,
        'a_and_b' => $a_and_b,
        'goods' => $goods,
        'total' => $total,
    ];
}

$jsonData = json_encode($wireFailuresData);




$sql = "SELECT * FROM reporte";
$stmt = $con->prepare($sql);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

$reporteData = [];

foreach ($records as $record) {
    $reporteData[] = [
        'folio_captura' => $record['folio_captura'],
        'folio_requisicion' => $record['folio_requisicion'],
        'cliente_fabricante' => $record['cliente_fabricante'],
        'fecha_reporte' => $record['fecha_reporte'],
        'caja' => $record['caja'],
        'po_skid' => $record['po_skid'],
        'num_parte' => $record['num_parte'],
        'date_code' => $record['date_code'],
        'nombre_operador' => $record['nombre_operador'],
        'horario' => $record['horario'],
        'productividad_a' => $record['productividad_a'],
        'productividad_b' => $record['productividad_b'],
        'total_inspeccionadas' => $record['total_inspeccionadas'],
        'total_defectos' => $record['total_defectos'],
        'buenas' => $record['buenas'],
    ];
}

$jsonData = json_encode($reporteData);




$sql = "SELECT * FROM report_fails";
$stmt = $con->prepare($sql);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

$reportFailsData = [];

foreach ($records as $record) {
    $reportFailsData[] = [
        'inspection_date' => isset($record['inspection_date']) ? $record['inspection_date'] : '',
        'operators' => isset($record['operators']) ? $record['operators'] : '',
        'descripcion' => isset($record['descripcion']) ? $record['descripcion'] : '',
        'primer_t' => isset($record['primer_t']) ? $record['primer_t'] : 0,
        'segundo_t' => isset($record['segundo_t']) ? $record['segundo_t'] : 0,
        'tercer_t' => isset($record['tercer_t']) ? $record['tercer_t'] : 0,
        'comments' => isset($record['comments']) ? $record['comments'] : '',
        'burr' => isset($record['burr']) ? $record['burr'] : 0,
        'blockend_hole' => isset($record['blockend_hole']) ? $record['blockend_hole'] : 0,
        'non_flat_edge' => isset($record['non_flat_edge']) ? $record['non_flat_edge'] : 0
    ];
}

$jsonData = json_encode($reportFailsData);








$sql = "SELECT * FROM inspection_data";
$stmt = $con->prepare($sql);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

$inspectionData = [];

foreach ($records as $record) {
    $inspectionData[] = [
        'goods' => isset($record['goods']) ? $record['goods'] : 0,
        'primer_t' => isset($record['primer_t']) ? $record['primer_t'] : 0,
        'segundo_t' => isset($record['segundo_t']) ? $record['segundo_t'] : 0,
        'tercer_t' => isset($record['tercer_t']) ? $record['tercer_t'] : 0,
        'coupler' => isset($record['coupler']) ? $record['coupler'] : 0,
        'dano_end_face' => isset($record['dano_end_face']) ? $record['dano_end_face'] : 0,
        'golpe_top' => isset($record['golpe_top']) ? $record['golpe_top'] : 0,
        'rebaba' => isset($record['rebaba']) ? $record['rebaba'] : 0,
        'dano_en_lente' => isset($record['dano_en_lente']) ? $record['dano_en_lente'] : 0,
        'fuera_de_spc' => isset($record['fuera_de_spc']) ? $record['fuera_de_spc'] : 0,
        'dano_fisico' => isset($record['dano_fisico']) ? $record['dano_fisico'] : 0,
        'coupler_dano' => isset($record['coupler_dano']) ? $record['coupler_dano'] : 0,
        'hundimiento' => isset($record['hundimiento']) ? $record['hundimiento'] : 0,
        'fisura' => isset($record['fisura']) ? $record['fisura'] : 0,
        'silicon' => isset($record['silicon']) ? $record['silicon'] : 0,
        'contaminacion' => isset($record['contaminacion']) ? $record['contaminacion'] : 0,
        'total' => isset($record['total']) ? $record['total'] : 0
    ];
}

$jsonData = json_encode($inspectionData);
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

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
        google.charts.load('current', { 'packages': ['corechart'] });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {

            var rawData = <?php echo json_encode($wireFailuresData); ?>;

            if (!rawData.length) {
                document.getElementById('chart_div').innerHTML = '<p>No hay datos disponibles para graficar.</p>';
                return;
            }

            var data = google.visualization.arrayToDataTable([
                ['Box', 'A', 'B', 'C', 'A & B', 'Goods', 'Total'],
                <?php
                foreach ($wireFailuresData as $row) {
                    echo "['" . $row['box'] . "', " . $row['a'] . ", " . $row['b'] . ", " . $row['c'] . ", " . $row['a_and_b'] . ", " . $row['goods'] . ", " . $row['total'] . "],";
                }
                ?>
            ]);

            data.addRow(['Total General', null, null, null, null, null, <?= $totalGeneral ?>]);

            var options = {
                title: 'INFINEX',
                hAxis: { title: 'Box' },
                vAxis: { title: 'Count' },
                legend: { position: 'top' },
                backgroundColor: 'transparent',
                chartArea: {
                    backgroundColor: 'transparent'
                }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));

            chart.draw(data, options);
        }
    </script>



    <script type="text/javascript">
        google.charts.load('current', { 'packages': ['corechart'] });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {

            var rawData = <?php echo $jsonData; ?>;

            if (!rawData.length) {
                document.getElementById('chart_div_molex').innerHTML = '<p>No hay datos disponibles para graficar.</p>';
                return;
            }

            var data = google.visualization.arrayToDataTable([
                ['Campo', 'Total'],
                ['Folio Captura', <?= count($reporteData) ?>],
                ['Folio Requisicion', <?= count($reporteData) ?>],
                ['Cliente/Fabricante', <?= count(array_unique(array_column($reporteData, 'cliente_fabricante'))) ?>],
                ['Fecha Reporte', <?= count(array_unique(array_column($reporteData, 'fecha_reporte'))) ?>],
                ['Caja', <?= count(array_unique(array_column($reporteData, 'caja'))) ?>],
                ['PO/SKID', <?= count(array_unique(array_column($reporteData, 'po_skid'))) ?>],
                ['Número de Parte', <?= count(array_unique(array_column($reporteData, 'num_parte'))) ?>],
                ['Date Code', <?= count(array_unique(array_column($reporteData, 'date_code'))) ?>],
                ['Nombre del Operador', <?= count(array_unique(array_column($reporteData, 'nombre_operador'))) ?>],
                ['Horario', <?= count(array_unique(array_column($reporteData, 'horario'))) ?>],
                ['Productividad A', <?= array_sum(array_column($reporteData, 'productividad_a')) ?>],
                ['Productividad B', <?= array_sum(array_column($reporteData, 'productividad_b')) ?>],
                ['Total Inspeccionadas', <?= array_sum(array_column($reporteData, 'total_inspeccionadas')) ?>],
                ['Total Defectos', <?= array_sum(array_column($reporteData, 'total_defectos')) ?>],
                ['Buenas', <?= array_sum(array_column($reporteData, 'buenas')) ?>]
            ]);

            var options = {
                title: 'MOLEX SEM46',
                pieHole: 0.4,
                legend: { position: 'top' },
                backgroundColor: 'transparent',
                chartArea: {
                    backgroundColor: 'transparent'
                }
            };

            var chart = new google.visualization.PieChart(document.getElementById('chart_div_molex'));

            chart.draw(data, options);
        }
    </script>



    <script type="text/javascript">
        google.charts.load('current', { 'packages': ['corechart'] });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {

            var rawData = [
                ['Campo', 'Total'],
                ['Primer T', <?= array_sum(array_column($reportFailsData, 'primer_t')) ?>],
                ['Segundo T', <?= array_sum(array_column($reportFailsData, 'segundo_t')) ?>],
                ['Tercer T', <?= array_sum(array_column($reportFailsData, 'tercer_t')) ?>],
                ['Burr', <?= array_sum(array_column($reportFailsData, 'burr')) ?>],
                ['Blockend Hole', <?= array_sum(array_column($reportFailsData, 'blockend_hole')) ?>],
                ['Non Flat Edge', <?= array_sum(array_column($reportFailsData, 'non_flat_edge')) ?>]
            ];

            if (rawData.length <= 1) {
                document.getElementById('chart_div_report_fails').innerHTML = '<p>No hay datos disponibles para graficar.</p>';
                return;
            }

            var data = google.visualization.arrayToDataTable(rawData);

            var options = {
                title: 'Reporte de Fallas',
                pieHole: 0.4,
                legend: { position: 'top' },
                backgroundColor: 'transparent',
                chartArea: {
                    backgroundColor: 'transparent'
                }
            };

            var chart = new google.visualization.PieChart(document.getElementById('chart_div_report_fails'));

            chart.draw(data, options);
        }
    </script>


    <script type="text/javascript">
        google.charts.load('current', { 'packages': ['corechart'] });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var rawData = <?php echo json_encode($inspectionData); ?>;

            if (!rawData.length) {
                document.getElementById('chart_div_inspection_data').innerHTML = '<p>No hay datos disponibles para graficar.</p>';
                return;
            }

            var data = google.visualization.arrayToDataTable([
                ['Element', 'Goods', 'Primer T', 'Segundo T', 'Tercer T', 'Coupler', 'Daño End Face', 'Golpe Top', 'Rebaba', 'Daño En Lente', 'Fuera De SPC', 'Daño Físico', 'Coupler Daño', 'Hundimiento', 'Fisura', 'Silicón', 'Contaminación', 'Total'],
                <?php
                foreach ($inspectionData as $row) {
                    echo "['', " . implode(", ", $row) . "],";
                }
                ?>
            ]);

            var options = {
                title: 'Inspección de Datos',
                hAxis: {
                    title: 'Elementos',
                    format: 'string',
                },
                vAxis: {
                    title: 'Cantidad'
                },
                legend: { position: 'top', maxLines: 3 },
                bar: { groupWidth: '100%' },
                isStacked: false,
                chartArea: { width: '90%', height: '70%' },
                colors: ['#1b9e77', '#d95f02', '#7570b3', '#e7298a', '#66a61e', '#e6ab02', '#a6761d', '#666666']
            };
            var options = {
                title: 'Inspección de Datos',
                hAxis: {
                    title: 'Elementos',
                    format: 'string',
                },
                vAxis: {
                    title: 'Cantidad'
                },
                legend: { position: 'top', maxLines: 3 },
                bar: { groupWidth: '100%' },
                isStacked: false,
                chartArea: { width: '90%', height: '70%' },
                colors: ['#1b9e77', '#d95f02', '#7570b3', '#e7298a', '#66a61e', '#e6ab02', '#a6761d', '#666666'],
                backgroundColor: 'transparent',
                chartArea: {
                    backgroundColor: 'transparent'
                }
            };
            var chart = new google.visualization.ColumnChart(document.getElementById('chart_div_inspection_data'));

            chart.draw(data, options);
        }
    </script>
    <title>Comter</title>

</head>

<body>



    <header class="header fixed-top" style="background-color:#1B419B;">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="header__logo">
                        <a href="cliente_panel.php"><img src="../ico/comter.png"  alt="" style="width:50px" ></a>
                    </div>
                </div>
                <div class="col-lg-10">
                    <div class="header__nav__option">
                        <nav class="header__nav__menu mobile-menu">
                            <ul>
                                <li class="active"><a href="cliente_panel.php">Home</a></li>
                                <!--<li><a href="./about.html">About</a></li>
                            <li><a href="./portfolio.html">Portfolio</a></li>
                            <li><a href="./services.html">Services</a></li>-->
                                <li>
                                    <a href="#">
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $imageSrc; ?>" alt="" class="user-image" />
                                            <span
                                                class="fw-semibold d-block ms-2"><?php echo $firstname . ' ' . $lastname; ?></span>
                                        </div>
                                    </a>
                                    <ul class="dropdown">
                                        <!-- <li><a href="./about.html">About</a></li>
                                   <li><a href="./portfolio.html">Portfolio</a></li>
                                   <li><a href="./blog.html">Blog</a></li>-->
                                        <li> <a href="#"> <small class="text-muted">Rol: <?php echo $role; ?></small></a>
                                            <hr>

                                        <li><a href="#" onclick="confirmLogout()">Cerrar sesión</a></li>
                                    </ul>
                                </li>
                                <!--<li><a href="./contact.html">Contact</a></li>-->
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
    background-color:rgb(27, 114, 155); 
    border-radius: 10px;
    transition: background-color 0.3s ease, transform 0.3s ease;
    margin: 4px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2); 
}


::-webkit-scrollbar-thumb:hover {
    background-color:rgb(27, 114, 155); 
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

    </style>


    <div class="container">
        <div id="chart_div" style="width: 100%; height: 500px;"></div>
    </div>


    <div class="container">

        <hr>
    </div>


    <div class="container">
        <div id="chart_div_molex" style="width: 100%; height: 500px;"></div>

    </div>

    <div class="container">

        <hr>
    </div>

    <div class="container">
        <div id="chart_div_report_fails" style="width: 100%; height: 500px;"></div>
    </div>


    <div class="container">

        <hr>
    </div>



    <div class="container">
        <div id="chart_div_inspection_data" style="width: 100%; height: 500px;"></div>
    </div>


    <br><br>
    <footer class="content-footer footer" style="background-color:#edebea">
    <div class="container-xxl d-flex flex-wrap justify-content-center py-2 flex-md-row flex-column text-center" style="color:#838383;">
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
</body>

</html>