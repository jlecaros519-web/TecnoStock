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

$idCategoria = filter_input(INPUT_POST, 'id_categoria', FILTER_VALIDATE_INT);
$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

if (
    $idCategoria === false
    || $idCategoria === null
    || $idCategoria < 1
    || mb_strlen($nombre) < 3
    || mb_strlen($nombre) > 100
) {
    header('Location: listar.php?error=1');
    exit;
}

$sql = 'UPDATE categorias
        SET nombre = :nombre,
            descripcion = :descripcion
        WHERE id_categoria = :id_categoria
        AND estado = 1';

try {
    $sentencia = $pdo->prepare($sql);
    $sentencia->execute([
        ':nombre' => $nombre,
        ':descripcion' => $descripcion !== '' ? $descripcion : null,
        ':id_categoria' => $idCategoria,
    ]);

    header('Location: listar.php?editado=1');
    exit;
} catch (PDOException $error) {
    header('Location: editar.php?id=' . $idCategoria . '&error=1');
    exit;
}
