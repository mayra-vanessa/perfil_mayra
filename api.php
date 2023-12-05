<?php

$nombreServidor = "localhost";
$nombreUsuario = "u899137107_perfil";
$passwordBaseDeDatos = "o?ZAzqxC8";
$nombreBaseDeDatos = "u899137107_perfil";

header('Content-Type: application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$conn = new mysqli($nombreServidor, $nombreUsuario, $passwordBaseDeDatos, $nombreBaseDeDatos);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset('utf8');

$json = file_get_contents('php://input');
$obj = json_decode($json, TRUE) ?? array();

$respuesta = array();

// Registro de Usuario
if (isset($obj['t_o']) && $obj['t_o'] == 1) {
    $nombre = isset($obj['nombre']) ? $conn->real_escape_string($obj['nombre']) : null;
    $correo = isset($obj['correo']) ? $conn->real_escape_string($obj['correo']) : null;
    $password = isset($obj['password']) ? $conn->real_escape_string($obj['password']) : null;
    $password = md5($password);

    $sql = sprintf("INSERT INTO usuarios (nombre, correo, password) VALUES ('%s', '%s', '%s')", $nombre, $correo, $password);
    $resultado = $conn->query($sql);

    if ($resultado) {
        $idUsuario = $conn->insert_id;

        $sqlSelect = "SELECT * FROM usuarios WHERE id= $idUsuario";
        $resultadoSelect = $conn->query($sqlSelect);

        if ($resultadoSelect->num_rows > 0) {
            $filaUsuario = $resultadoSelect->fetch_assoc();
            $respuesta = array('respuesta' => 'ok', 'usuario' => $filaUsuario);
        } else {
            $respuesta = array('respuesta' => 'error', 'message' => 'Error al obtener la información del usuario');
        }
    } else {
        $error = $conn->error;
        $respuesta = array('respuesta' => 'error', 'message' => $error);
        http_response_code(500);
    }
}


// Inicio de sesión de Usuario
if (isset($obj['t_o']) && $obj['t_o'] == 2) {
    $correo = isset($obj['correo']) ? $conn->real_escape_string($obj['correo']) : null;
    $password = isset($obj['password']) ? $conn->real_escape_string($obj['password']) : null;
    $password = md5($password);

    $sqlUsuario = sprintf("SELECT * FROM usuarios WHERE correo = '%s' AND password = '%s'", $correo, $password);
    $resultadoUsuario = $conn->query($sqlUsuario);

    if ($resultadoUsuario->num_rows > 0) {
        $usuario = $resultadoUsuario->fetch_assoc();
        $respuesta = array('respuesta' => 'ok', 'usuario' => $usuario);
    } else {
        $respuesta = array('respuesta' => 'error', 'message' => 'Credenciales incorrectas');
    }
}

// Editar perfil de Usuario
if (isset($obj['t_o']) && $obj['t_o'] == 3) {
    $idUsuario = isset($obj['id']) ? $conn->real_escape_string($obj['id']) : null;
    $nombre = isset($obj['nombre']) ? $conn->real_escape_string($obj['nombre']) : null;
    $apellido_paterno = isset($obj['apellido_paterno']) ? $conn->real_escape_string($obj['apellido_paterno']) : null;
    $apellido_materno = isset($obj['apellido_materno']) ? $conn->real_escape_string($obj['apellido_materno']) : null;
    $sexo = isset($obj['sexo']) ? $conn->real_escape_string($obj['sexo']) : null;
    $password = isset($obj['password']) ? $conn->real_escape_string($obj['password']) : null;

    $sqlUpdate = "UPDATE usuarios SET 
        nombre = '$nombre',
        apellido_paterno = '$apellido_paterno',
        apellido_materno = '$apellido_materno',
        sexo = '$sexo'
    ";

    if ($password !== null && $password !== '') {
        $password = md5($password);
        $sqlUpdate .= ", password = '$password'";
    }

    $sqlUpdate .= " WHERE id = $idUsuario";

    if ($idUsuario) {
        $resultadoUpdate = $conn->query($sqlUpdate);

        if ($resultadoUpdate) {
            $sqlSelect = "SELECT * FROM usuarios WHERE id = $idUsuario";
            $resultadoSelect = $conn->query($sqlSelect);

            if ($resultadoSelect->num_rows > 0) {
                $filaUsuario = $resultadoSelect->fetch_assoc();
                $respuesta = array('respuesta' => 'ok', 'usuario' => $filaUsuario);
            } else {
                $respuesta = array('respuesta' => 'error', 'message' => 'Error al obtener la información del usuario después de la actualización');
            }
        } else {
            $error = $conn->error;
            $respuesta = array('respuesta' => 'error', 'message' => $error);
            http_response_code(500);
        }
    } else {
        $respuesta = array('respuesta' => 'error', 'message' => 'ID de usuario no proporcionado');
        http_response_code(400);  // Bad Request
    }
}


echo json_encode($respuesta);
exit;
?>
