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
$records_per_page = 20;
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

$id_version = isset($_GET['id_version']) ? (int) $_GET['id_version'] : null;
$nombre_version = isset($_GET['nombre_version']) ? $_GET['nombre_version'] : 'sem46';


$total_records_sql = "SELECT COUNT(*)
                      FROM inspecciones i 
                      JOIN versiones_inspeccion vi ON i.id_version = vi.id_version";

$where_conditions = [];

if ($id_version) {
    $where_conditions[] = "i.id_version = :id_version";
}
if ($nombre_version) {
    $where_conditions[] = "vi.nombre_version = :nombre_version";
}

if (count($where_conditions) > 0) {
    $total_records_sql .= " WHERE " . implode(" AND ", $where_conditions);
}

$total_records_stmt = $con->prepare($total_records_sql);

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
                FROM inspecciones i 
                JOIN versiones_inspeccion vi ON i.id_version = vi.id_version";

if (count($where_conditions) > 0) {
    $records_sql .= " WHERE " . implode(" AND ", $where_conditions);
}


$records_sql .= " ORDER BY i.inspection_date ASC LIMIT :offset, :records_per_page";

$records_stmt = $con->prepare($records_sql);


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




function getUniqueValues($column, $con)
{

    if ($column === 'nombre_version') {
        $sql = "SELECT DISTINCT vi.nombre_version FROM inspecciones i
                JOIN versiones_inspeccion vi ON i.id_version = vi.id_version
                WHERE vi.nombre_version = 'sem46' AND vi.nombre_version IS NOT NULL AND vi.nombre_version != ''
                ORDER BY vi.nombre_version";
    } else {

        $sql = "SELECT DISTINCT i.$column FROM inspecciones i
                JOIN versiones_inspeccion vi ON i.id_version = vi.id_version
                WHERE vi.nombre_version = 'sem46' AND i.$column IS NOT NULL AND i.$column != ''
                ORDER BY i.$column";
    }

    $stmt = $con->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}


$descripcion_values = getUniqueValues('descripcion', $con);
$operators_values = getUniqueValues('operators', $con);
$primer_t_values = getUniqueValues('primer_t', $con);
$segundo_t_values = getUniqueValues('segundo_t', $con);
$tercer_t_values = getUniqueValues('tercer_t', $con);
$goods_values = getUniqueValues('goods', $con);
$coupler_values = getUniqueValues('coupler', $con);
$dano_end_face_values = getUniqueValues('dano_end_face', $con);
$golpe_top_values = getUniqueValues('golpe_top', $con);
$rebaba_values = getUniqueValues('rebaba', $con);
$dano_en_lente_values = getUniqueValues('dano_en_lente', $con);
$fuera_de_spc_values = getUniqueValues('fuera_de_spc', $con);
$dano_fisico_values = getUniqueValues('dano_fisico', $con);
$coupler_danado_values = getUniqueValues('coupler_danado', $con);
$hundimiento_values = getUniqueValues('hundimiento', $con);
$fisura_values = getUniqueValues('fisura', $con);
$silicon_contaminacion_values = getUniqueValues('silicon_contaminacion', $con);
$contaminacion_end_face_values = getUniqueValues('contaminacion_end_face', $con);
$total_values = getUniqueValues('total', $con);
$total_final_values = getUniqueValues('total_final', $con);
$comments_values = getUniqueValues('comments', $con);
$nombre_version_values = getUniqueValues('nombre_version', $con);


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
                <h3 class="text-center mt-3">Records MOLEX SEM46</h3>


                <button id="resetFilters" class="btn btn-danger mb-3"> <i class="fa fa-refresh"></i> Reset
                    Filters</button>
                <div class="table-responsive" style="max-height: 700px; overflow-y: auto;">
                    <table class="table table-bordered text-center" id="inspectionTable" style="border-color: black;">
                        <thead style="background-color: #D9DAD9; position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th style="color:#000000; border-color: black;">INSPECTION DATE</th>
                                <th style="color:#000000; border-color: black;">DESCRIPTION
                                    <select id="filter_description" class="form-select form-select-sm mt-2">
                                        <option value="">All</option>
                                        <?php foreach ($descripcion_values as $descripcion) {
                                            echo "<option value=\"" . htmlspecialchars($descripcion) . "\">" . htmlspecialchars($descripcion) . "</option>";
                                        } ?>
                                    </select>
                                </th>
                                <th style="color:#000000; border-color: black;">OPERATORS
                                    <select id="filter_operators" class="form-select form-select-sm mt-2">
                                        <option value="">All</option>
                                        <?php foreach ($operators_values as $operators) {
                                            echo "<option value=\"" . htmlspecialchars($operators) . "\">" . htmlspecialchars($operators) . "</option>";
                                        } ?>
                                    </select>
                                </th>
                                <th style="color:#000000; border-color: black;">PRIMER T
                                    <select id="filter_primer_t" class="form-select form-select-sm mt-2">
                                        <option value="">All</option>
                                        <?php foreach ($primer_t_values as $primer_t) {
                                            echo "<option value=\"" . htmlspecialchars($primer_t) . "\">" . htmlspecialchars($primer_t) . "</option>";
                                        } ?>
                                    </select>
                                </th>
                                <th style="color:#000000; border-color: black;">SEGUNDO T
                                    <select id="filter_segundo_t" class="form-select form-select-sm mt-2">
                                        <option value="">All</option>
                                        <?php foreach ($segundo_t_values as $segundo_t) {
                                            echo "<option value=\"" . htmlspecialchars($segundo_t) . "\">" . htmlspecialchars($segundo_t) . "</option>";
                                        } ?>
                                    </select>
                                </th>
                                <th style="color:#000000; border-color: black;">TERCER T
                                    <select id="filter_tercer_t" class="form-select form-select-sm mt-2">
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
                                    <select id="filter_dano_end_face" class="form-select form-select-sm mt-2">
                                        <option value="">All</option>
                                        <?php foreach ($dano_end_face_values as $dano_end_face) {
                                            echo "<option value=\"" . htmlspecialchars($dano_end_face) . "\">" . htmlspecialchars($dano_end_face) . "</option>";
                                        } ?>
                                    </select>
                                </th>
                                <th style="color:#000000; border-color: black;">GOLPE TOP
                                    <select id="filter_golpe_top" class="form-select form-select-sm mt-2">
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
                                    <select id="filter_dano_en_lente" class="form-select form-select-sm mt-2">
                                        <option value="">All</option>
                                        <?php foreach ($dano_en_lente_values as $dano_en_lente) {
                                            echo "<option value=\"" . htmlspecialchars($dano_en_lente) . "\">" . htmlspecialchars($dano_en_lente) . "</option>";
                                        } ?>
                                    </select>
                                </th>
                                <th style="color:#000000; border-color: black;">FUERA DE SPC
                                    <select id="filter_fuera_de_spc" class="form-select form-select-sm mt-2">
                                        <option value="">All</option>
                                        <?php foreach ($fuera_de_spc_values as $fuera_de_spc) {
                                            echo "<option value=\"" . htmlspecialchars($fuera_de_spc) . "\">" . htmlspecialchars($fuera_de_spc) . "</option>";
                                        } ?>
                                    </select>
                                </th>
                                <th style="color:#000000; border-color: black;">DANO FISICO
                                    <select id="filter_dano_fisico" class="form-select form-select-sm mt-2">
                                        <option value="">All</option>
                                        <?php foreach ($dano_fisico_values as $dano_fisico) {
                                            echo "<option value=\"" . htmlspecialchars($dano_fisico) . "\">" . htmlspecialchars($dano_fisico) . "</option>";
                                        } ?>
                                    </select>
                                </th>
                                <th style="color:#000000; border-color: black;">COUPLER DANADO
                                    <select id="filter_coupler_danado" class="form-select form-select-sm mt-2">
                                        <option value="">All</option>
                                        <?php foreach ($coupler_danado_values as $coupler_danado) {
                                            echo "<option value=\"" . htmlspecialchars($coupler_danado) . "\">" . htmlspecialchars($coupler_danado) . "</option>";
                                        } ?>
                                    </select>
                                </th>
                                <th style="color:#000000; border-color: black;">HUNDIMIENTO
                                    <select id="filter_hundimiento" class="form-select form-select-sm mt-2">
                                        <option value="">All</option>
                                        <?php foreach ($hundimiento_values as $hundimiento) {
                                            echo "<option value=\"" . htmlspecialchars($hundimiento) . "\">" . htmlspecialchars($hundimiento) . "</option>";
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
                                    <select id="filter_silicon_contaminacion" class="form-select form-select-sm mt-2">
                                        <option value="">All</option>
                                        <?php foreach ($silicon_contaminacion_values as $silicon_contaminacion) {
                                            echo "<option value=\"" . htmlspecialchars($silicon_contaminacion) . "\">" . htmlspecialchars($silicon_contaminacion) . "</option>";
                                        } ?>
                                    </select>
                                </th>
                                <th style="color:#000000; border-color: black;">CONTAMINACION END FACE
                                    <select id="filter_contaminacion_end_face" class="form-select form-select-sm mt-2">
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
                                    <select id="filter_total_final" class="form-select form-select-sm mt-2">
                                        <option value="">All</option>
                                        <?php foreach ($total_final_values as $total_total_final) {
                                            echo "<option value=\"" . htmlspecialchars($total_total_final) . "\">" . htmlspecialchars($total_total_final) . "</option>";
                                        } ?>
                                    </select>
                                </th>
                                <th style="color:#000000; border-color: black;">COMMENTS
                                    <select id="filter_comments" class="form-select form-select-sm mt-2">
                                        <option value="">All</option>
                                        <?php foreach ($comments_values as $comments) {
                                            echo "<option value=\"" . htmlspecialchars($comments) . "\">" . htmlspecialchars($comments) . "</option>";
                                        } ?>
                                    </select>
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
                            $total_coupler_danado = 0;
                            $total_hundimiento = 0;
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
                                $total_coupler_danado += (float) $record['coupler_danado'];
                                $total_hundimiento += (float) $record['hundimiento'];
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

                                    <td style="border-color: black;"><?= htmlspecialchars($record['operators']) ?></td>

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
                                        style="border-color: black; <?= ($record['coupler_danado'] != 0) ? 'font-weight: bold; color: red; background-color: #FFCCCC;' : '' ?>">
                                        <?= htmlspecialchars($record['coupler_danado']) ?>
                                    </td>

                                    <td
                                        style="border-color: black; <?= ($record['hundimiento'] != 0) ? 'font-weight: bold; color: red; background-color: #FFCCCC;' : '' ?>">
                                        <?= htmlspecialchars($record['hundimiento']) ?>
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

                                    <td style="border-color: black;"><?= htmlspecialchars($record['total_final']) ?></td>

                                    <td style="border-color: black;"><?= htmlspecialchars($record['comments']) ?></td>
                                </tr>
                                <?php
                                $previous_date = $current_date;
                            endforeach;
                            ?>
                        </tbody>

                        <tfoot style="background-color: #f2f2f2; position: sticky; bottom: 0; z-index: 10;">
                            <tr>
                                <td colspan="6" style="border-color: black; font-weight: bold;">Total</td>
                                <td style="border-color: black;"><?= number_format($total_goods, 0) ?></td>
                                <td style="border-color: black;"><?= number_format($total_coupler, 0) ?></td>
                                <td style="border-color: black;"><?= number_format($total_dano_end_face, 0) ?></td>
                                <td style="border-color: black;"><?= number_format($total_golpe_top, 0) ?></td>
                                <td style="border-color: black;"><?= number_format($total_rebaba, 0) ?></td>
                                <td style="border-color: black;"><?= number_format($total_dano_en_lente, 0) ?></td>
                                <td style="border-color: black;"><?= number_format($total_fuera_de_spc, 0) ?></td>
                                <td style="border-color: black;"><?= number_format($total_dano_fisico, 0) ?></td>
                                <td style="border-color: black;"><?= number_format($total_coupler_danado, 0) ?></td>
                                <td style="border-color: black;"><?= number_format($total_hundimiento, 0) ?></td>
                                <td style="border-color: black;"><?= number_format($total_fisura, 0) ?></td>
                                <td style="border-color: black;"><?= number_format($total_silicon_contaminacion, 0) ?>
                                </td>
                                <td style="border-color: black;"><?= number_format($total_contaminacion_end_face, 0) ?>
                                </td>
                                <td style="border-color: black;"><?= number_format($total_total, 0) ?></td>
                                <td style="border-color: black;"><?= number_format($total_total_final, 0) ?></td>
                                <td style="border-color: black;"></td>
                            </tr>
                        </tfoot>
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
                const filterCouplerDanado = document.getElementById("filter_coupler_danado");
                const filterHundimiento = document.getElementById("filter_hundimiento");
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
                        if (filterCouplerDanado.value && !row.cells[14].textContent.includes(filterCouplerDanado.value)) {
                            showRow = false;
                        }
                        if (filterHundimiento.value && !row.cells[15].textContent.includes(filterHundimiento.value)) {
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

                const filters = document.querySelectorAll("select");
                filters.forEach(filter => {
                    filter.addEventListener("input", filterTable);
                });

                document.getElementById("resetFilters").addEventListener("click", function () {
                    filters.forEach(filter => {
                        filter.value = "";
                    });
                    filterTable();
                });
            </script>

</body>

</html>