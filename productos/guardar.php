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

$codigo = strtoupper(
    trim($_POST['codigo'] ?? '')
);

$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

$categoriaId = filter_input(
    INPUT_POST,
    'categoria_id',
    FILTER_VALIDATE_INT
);

$precio = filter_input(
    INPUT_POST,
    'precio',
    FILTER_VALIDATE_FLOAT
);

$stockActual = filter_input(
    INPUT_POST,
    'stock_actual',
    FILTER_VALIDATE_INT
);

$stockMinimo = filter_input(
    INPUT_POST,
    'stock_minimo',
    FILTER_VALIDATE_INT
);

$errores = [];

if (
    $codigo === ''
    || !preg_match('/^[A-Z0-9-]+$/', $codigo)
) {
    $errores[] = 'Código obligatorio o inválido.';
}

if (
    mb_strlen($nombre) < 3
    || mb_strlen($nombre) > 100
) {
    $errores[] = 'Nombre entre 3 y 100 caracteres.';
}

if (
    $categoriaId === false
    || $categoriaId === null
    || $categoriaId < 1
) {
    $errores[] = 'Categoría inválida.';
}

if (
    $precio === false
    || $precio === null
    || $precio < 0
) {
    $errores[] = 'Precio inválido.';
}

if (
    $stockActual === false
    || $stockActual === null
    || $stockActual < 0
) {
    $errores[] = 'Stock actual inválido.';
}

if (
    $stockMinimo === false
    || $stockMinimo === null
    || $stockMinimo < 0
) {
    $errores[] = 'Stock mínimo inválido.';
}

if ($errores) {
    header('Location: crear.php?error=1');
    exit;
}

$sql = 'SELECT id_categoria
        FROM categorias
        WHERE id_categoria = :id_categoria
        AND estado = 1';

$sentencia = $pdo->prepare($sql);
$sentencia->execute([
    ':id_categoria' => $categoriaId,
]);

if (!$sentencia->fetch()) {
    header('Location: crear.php?error=1');
    exit;
}

$sql = 'INSERT INTO productos
        (codigo, nombre, descripcion,
         precio, stock_actual,
         stock_minimo, estado, id_categoria)
        VALUES
        (:codigo, :nombre, :descripcion,
         :precio, :stock_actual,
         :stock_minimo, 1, :id_categoria)';

try {
    $sentencia = $pdo->prepare($sql);
    $sentencia->execute([
        ':codigo' => $codigo,
        ':nombre' => $nombre,
        ':descripcion' => $descripcion !== '' ? $descripcion : null,
        ':precio' => $precio,
        ':stock_actual' => $stockActual,
        ':stock_minimo' => $stockMinimo,
        ':id_categoria' => $categoriaId,
    ]);

    header('Location: ../index.php?creado=1');
    exit;
} catch (PDOException $error) {
    header('Location: crear.php?error=1');
    exit;
}
