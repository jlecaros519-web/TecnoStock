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

$productoId = filter_input(
    INPUT_POST,
    'producto_id',
    FILTER_VALIDATE_INT
);

$tipo = $_POST['tipo'] ?? '';

$cantidad = filter_input(
    INPUT_POST,
    'cantidad',
    FILTER_VALIDATE_INT
);

$observacion = trim($_POST['observacion'] ?? '');

$errores = [];

if (
    $productoId === false
    || $productoId === null
    || $productoId < 1
) {
    $errores[] = 'Producto inválido.';
}

if (!in_array($tipo, ['ENTRADA', 'SALIDA'], true)) {
    $errores[] = 'Tipo de movimiento inválido.';
}

if (
    $cantidad === false
    || $cantidad === null
    || $cantidad < 1
) {
    $errores[] = 'Cantidad inválida.';
}

if ($errores) {
    header('Location: registrar.php?error=1');
    exit;
}

$sql = 'SELECT stock_actual
        FROM productos
        WHERE id_producto = :id_producto
        AND estado = 1';

$sentencia = $pdo->prepare($sql);
$sentencia->execute([
    ':id_producto' => $productoId,
]);

$producto = $sentencia->fetch();

if (!$producto) {
    header('Location: registrar.php?error=1');
    exit;
}

$stockActual = (int) $producto['stock_actual'];

if ($tipo === 'SALIDA' && $cantidad > $stockActual) {
    header('Location: registrar.php?error=1');
    exit;
}

if ($tipo === 'ENTRADA') {
    $nuevoStock = $stockActual + $cantidad;
} else {
    $nuevoStock = $stockActual - $cantidad;
}

$sql = 'UPDATE productos
        SET stock_actual = :stock_actual
        WHERE id_producto = :id_producto';

$sentencia = $pdo->prepare($sql);
$sentencia->execute([
    ':stock_actual' => $nuevoStock,
    ':id_producto' => $productoId,
]);

$sql = 'INSERT INTO movimientos
        (tipo, cantidad, observacion,
         id_producto, id_usuario)
        VALUES
        (:tipo, :cantidad, :observacion,
         :id_producto, :id_usuario)';

$sentencia = $pdo->prepare($sql);
$sentencia->execute([
    ':tipo' => $tipo,
    ':cantidad' => $cantidad,
    ':observacion' => $observacion !== '' ? $observacion : null,
    ':id_producto' => $productoId,
    ':id_usuario' => $_SESSION['id_usuario'],
]);

header('Location: listar.php?creado=1');
exit;
