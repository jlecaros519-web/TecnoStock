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

$idProducto = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);
$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$categoriaId = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT);
$precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
$stockActual = filter_input(INPUT_POST, 'stock_actual', FILTER_VALIDATE_INT);
$stockMinimo = filter_input(INPUT_POST, 'stock_minimo', FILTER_VALIDATE_INT);

$errores = [];

if ($idProducto === false || $idProducto === null || $idProducto < 1) {
    $errores[] = 'Producto inválido.';
}

if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100) {
    $errores[] = 'Nombre entre 3 y 100 caracteres.';
}

if ($categoriaId === false || $categoriaId === null || $categoriaId < 1) {
    $errores[] = 'Categoría inválida.';
}

if ($precio === false || $precio === null || $precio < 0) {
    $errores[] = 'Precio inválido.';
}

if ($stockActual === false || $stockActual === null || $stockActual < 0) {
    $errores[] = 'Stock actual inválido.';
}

if ($stockMinimo === false || $stockMinimo === null || $stockMinimo < 0) {
    $errores[] = 'Stock mínimo inválido.';
}

if ($errores) {
    header('Location: editar.php?id=' . (int) $idProducto . '&error=1');
    exit;
}

$sql = 'UPDATE productos
        SET nombre = :nombre,
            descripcion = :descripcion,
            precio = :precio,
            stock_actual = :stock_actual,
            stock_minimo = :stock_minimo,
            id_categoria = :id_categoria
        WHERE id_producto = :id_producto
        AND estado = 1';

$sentencia = $pdo->prepare($sql);
$sentencia->execute([
    ':nombre' => $nombre,
    ':descripcion' => $descripcion !== '' ? $descripcion : null,
    ':precio' => $precio,
    ':stock_actual' => $stockActual,
    ':stock_minimo' => $stockMinimo,
    ':id_categoria' => $categoriaId,
    ':id_producto' => $idProducto,
]);

header('Location: ../index.php?editado=1');
exit;
