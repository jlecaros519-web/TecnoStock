<?php
session_start();
require_once 'config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

$correo = trim($_POST['correo'] ?? '');
$clave = $_POST['clave'] ?? '';

if ($correo === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL) || $clave === '') {
    header('Location: login.php?error=1');
    exit;
}

$sql = 'SELECT
            id_usuario,
            nombre,
            correo,
            clave,
            estado
        FROM usuarios
        WHERE correo = :correo
        LIMIT 1';

$sentencia = $pdo->prepare($sql);
$sentencia->execute([
    ':correo' => $correo,
]);

$usuario = $sentencia->fetch();

if (
    !$usuario
    || (int) $usuario['estado'] !== 1
    || !password_verify($clave, $usuario['clave'])
) {
    header('Location: login.php?error=1');
    exit;
}

$_SESSION['id_usuario'] = (int) $usuario['id_usuario'];
$_SESSION['nombre'] = $usuario['nombre'];

header('Location: index.php');
exit;
