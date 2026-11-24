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

if (
    $idCategoria === false
    || $idCategoria === null
    || $idCategoria < 1
) {
    header('Location: listar.php?error=1');
    exit;
}

$sql = 'UPDATE categorias
        SET estado = 1
        WHERE id_categoria = :id_categoria';

$sentencia = $pdo->prepare($sql);
$sentencia->execute([
    ':id_categoria' => $idCategoria,
]);

header('Location: listar.php?activado=1');
exit;
