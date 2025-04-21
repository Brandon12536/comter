<?php
session_start();
require '../../config/connection.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['id_proveedor'])) {
        throw new Exception('No se encontró el ID del proveedor en la sesión');
    }
    $id_proveedor = $_SESSION['id_proveedor'];

    $jsonData = file_get_contents('php://input');
    $datos = json_decode($jsonData, true);

    if (!$datos || !is_array($datos)) {
        throw new Exception('Datos inválidos');
    }

    $db = new Database();
    $con = $db->conectar();
    $con->beginTransaction();

    $stmt = $con->prepare("SELECT id_version FROM versiones_inspeccion WHERE nombre_version = ?");
    
    foreach ($datos as $fila) {
        if (empty($fila['semana'])) {
            throw new Exception('La semana es requerida');
        }

        $stmt->execute([$fila['semana']]);
        $version = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$version) {
            $stmtInsertVersion = $con->prepare("INSERT INTO versiones_inspeccion (nombre_version) VALUES (?)");
            $stmtInsertVersion->execute([$fila['semana']]);
            $id_version = $con->lastInsertId();
        } else {
            $id_version = $version['id_version'];
        }

        // Preparar los datos con valores por defecto
        $data = [
            'id_version' => $id_version,
            'id_proveedor' => $id_proveedor,
            'inspection_date' => $fila['fecha'] ?? date('Y-m-d'),
            'operators' => $fila['operador'] ?? ($_SESSION['nombre'] ?? '') . ' ' . ($_SESSION['apellido'] ?? ''),
            'descripcion' => $fila['descripcion'] ?? '-',
            'primer_t' => intval($fila['primer_t'] ?? 0),
            'segundo_t' => intval($fila['segundo_t'] ?? 0),
            'tercer_t' => intval($fila['tercer_t'] ?? 0),
            'goods' => intval($fila['goods'] ?? 0),
            'coupler' => intval($fila['coupler'] ?? 0),
            'dano_end_face' => intval($fila['dano_end_face'] ?? 0),
            'golpe_top' => intval($fila['golpe_top'] ?? 0),
            'rebaba' => intval($fila['rebaba'] ?? 0),
            'dano_en_lente' => intval($fila['dano_en_lente'] ?? 0),
            'fuera_de_spc' => intval($fila['fuera_de_spc'] ?? 0),
            'dano_fisico' => intval($fila['dano_fisico'] ?? 0),
            'wirebond_corto' => intval($fila['wirebond_corto'] ?? 0),
            'wirebond_chueco' => intval($fila['wirebond_chueco'] ?? 0),
            'fisura' => intval($fila['fisura'] ?? 0),
            'silicon_contaminacion' => intval($fila['silicon_contaminacion'] ?? 0),
            'contaminacion_end_face' => intval($fila['contaminacion_end_face'] ?? 0),
            'comments' => $fila['comentarios'] ?? '',
            'total' => 0,  // Will be calculated below
            'total_final' => 0  // Will be calculated below
        ];

        // Calcular el total (suma de todos los campos excepto goods)
        $data['total'] = array_sum([
            $data['primer_t'], $data['segundo_t'], $data['tercer_t'],
            $data['coupler'], $data['dano_end_face'],
            $data['golpe_top'], $data['rebaba'], $data['dano_en_lente'],
            $data['fuera_de_spc'], $data['dano_fisico'], $data['wirebond_corto'],
            $data['wirebond_chueco'], $data['fisura'], $data['silicon_contaminacion'],
            $data['contaminacion_end_face']
        ]);

        // Calcular el total final (total + goods)
        $data['total_final'] = $data['total'] + $data['goods'];

        $sql = "INSERT INTO molex (
            id_version, id_proveedor, inspection_date, operators, descripcion,
            primer_t, segundo_t, tercer_t, goods, coupler,
            dano_end_face, golpe_top, rebaba, dano_en_lente,
            fuera_de_spc, dano_fisico, wirebond_corto,
            wirebond_chueco, fisura, silicon_contaminacion,
            contaminacion_end_face, total, total_final, comments
        ) VALUES (
            :id_version, :id_proveedor, :inspection_date, :operators, :descripcion,
            :primer_t, :segundo_t, :tercer_t, :goods, :coupler,
            :dano_end_face, :golpe_top, :rebaba, :dano_en_lente,
            :fuera_de_spc, :dano_fisico, :wirebond_corto,
            :wirebond_chueco, :fisura, :silicon_contaminacion,
            :contaminacion_end_face, :total, :total_final, :comments
        )";

        try {
            // Insert into molex table
            $stmt = $con->prepare($sql);
            $stmt->execute($data);

            // Prepare data for inspecciones table
            $dataInspecciones = [
                'id_version' => $data['id_version'],
                'id_proveedor' => $data['id_proveedor'],
                'inspection_date' => $data['inspection_date'],
                'operators' => $data['operators'],
                'descripcion' => $data['descripcion'],
                'primer_t' => $data['primer_t'],
                'segundo_t' => $data['segundo_t'],
                'tercer_t' => $data['tercer_t'],
                'goods' => $data['goods'],
                'coupler' => $data['coupler'],
                'dano_end_face' => $data['dano_end_face'],
                'golpe_top' => $data['golpe_top'],
                'rebaba' => $data['rebaba'],
                'dano_en_lente' => $data['dano_en_lente'],
                'fuera_de_spc' => $data['fuera_de_spc'],
                'dano_fisico' => $data['dano_fisico'],
                'coupler_danado' => 0, // Default value as it's not in molex table
                'hundimiento' => 0, // Default value as it's not in molex table
                'fisura' => $data['fisura'],
                'silicon_contaminacion' => $data['silicon_contaminacion'],
                'contaminacion_end_face' => $data['contaminacion_end_face'],
                'total' => $data['total'],
                'total_final' => $data['total_final'],
                'comments' => $data['comments']
            ];

            // Insert into inspecciones table
            $sqlInspecciones = "INSERT INTO inspecciones (
                id_version, id_proveedor, inspection_date, operators, descripcion,
                primer_t, segundo_t, tercer_t, goods, coupler,
                dano_end_face, golpe_top, rebaba, dano_en_lente,
                fuera_de_spc, dano_fisico, coupler_danado, hundimiento,
                fisura, silicon_contaminacion, contaminacion_end_face,
                total, total_final, comments
            ) VALUES (
                :id_version, :id_proveedor, :inspection_date, :operators, :descripcion,
                :primer_t, :segundo_t, :tercer_t, :goods, :coupler,
                :dano_end_face, :golpe_top, :rebaba, :dano_en_lente,
                :fuera_de_spc, :dano_fisico, :coupler_danado, :hundimiento,
                :fisura, :silicon_contaminacion, :contaminacion_end_face,
                :total, :total_final, :comments
            )";

            $stmtInspecciones = $con->prepare($sqlInspecciones);
            $stmtInspecciones->execute($dataInspecciones);

        } catch (PDOException $e) {
            throw new Exception('Error al insertar datos: ' . $e->getMessage() . 
                              "\nSQL: " . $sql . 
                              "\nData: " . json_encode($data));
        }
    }

    $con->commit();
    echo json_encode([
        'success' => true,
        'message' => 'Datos guardados correctamente'
    ]);

} catch (Exception $e) {
    if (isset($con) && $con->inTransaction()) {
        $con->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar los datos: ' . $e->getMessage()
    ]);
}
