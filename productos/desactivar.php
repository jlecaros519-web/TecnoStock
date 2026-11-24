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

$idProducto = filter_input(
    INPUT_POST,
    'id_producto',
    FILTER_VALIDATE_INT
);

if (
    $idProducto === false
    || $idProducto === null
    || $idProducto < 1
) {
    header('Location: ../index.php?error=1');
    exit;
}

$sql = 'UPDATE productos
        SET estado = 0
        WHERE id_producto = :id_producto';

$sentencia = $pdo->prepare($sql);
$sentencia->execute([
    ':id_producto' => $idProducto,
]);

header('Location: ../index.php?desactivado=1');
exit;
