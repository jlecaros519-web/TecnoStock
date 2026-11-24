<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100) {
    header('Location: crear.php?error=1');
    exit;
}

$sql = 'INSERT INTO categorias
        (nombre, descripcion, estado)
        VALUES
        (:nombre, :descripcion, 1)';

try {
    $sentencia = $pdo->prepare($sql);
    $sentencia->execute([
        ':nombre' => $nombre,
        ':descripcion' => $descripcion !== '' ? $descripcion : null,
    ]);

    header('Location: listar.php?creado=1');
    exit;
} catch (PDOException $error) {
    header('Location: crear.php?error=1');
    exit;
}
