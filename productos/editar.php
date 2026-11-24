<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/conexion.php';

$idProducto = filter_input(
    INPUT_GET,
    'id',
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

$sql = 'SELECT *
        FROM productos
        WHERE id_producto = :id_producto
        AND estado = 1';

$sentencia = $pdo->prepare($sql);
$sentencia->execute([
    ':id_producto' => $idProducto,
]);

$producto = $sentencia->fetch();

if (!$producto) {
    header('Location: ../index.php?error=1');
    exit;
}

$categorias = $pdo
    ->query('SELECT id_categoria, nombre FROM categorias WHERE estado = 1 ORDER BY nombre')
    ->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar producto - TecnoStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
    <nav class="barra-principal">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="../index.php" class="marca">
                <img src="../assets/img/logo-tecnostock.svg" class="marca-logo" alt="Logo TecnoStock">
                <span class="marca-texto">
                    TecnoStock
                    <small>Sistema de inventario</small>
                </span>
            </a>
            <a href="../index.php" class="btn btn-salir btn-sm">Volver</a>
        </div>
    </nav>

    <main class="container contenido-principal">
        <div class="page-heading">
            <div>
                <div class="eyebrow">Productos</div>
                <h1>Editar producto</h1>
                <p class="subtitulo">Actualiza la información necesaria.</p>
            </div>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">Revise los datos ingresados.</div>
        <?php endif; ?>

        <div class="card form-panel">
            <div class="card-body">
                <form action="actualizar.php" method="post" class="validar-formulario" novalidate>
                    <input type="hidden" name="id_producto" value="<?= (int) $producto['id_producto'] ?>">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="codigo" class="form-label">Código</label>
                            <input type="text" class="form-control" id="codigo" value="<?= htmlspecialchars($producto['codigo'], ENT_QUOTES, 'UTF-8') ?>" readonly>
                        </div>

                        <div class="col-md-8">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" minlength="3" maxlength="100" value="<?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="categoria_id" class="form-label">Categoría</label>
                            <select class="form-select" id="categoria_id" name="categoria_id" required>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= (int) $categoria['id_categoria'] ?>" <?= $categoria['id_categoria'] == $producto['id_categoria'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($categoria['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="precio" class="form-label">Precio</label>
                            <input type="number" class="form-control" id="precio" name="precio" min="0" step="0.01" value="<?= $producto['precio'] ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="stock_actual" class="form-label">Stock actual</label>
                            <input type="number" class="form-control" id="stock_actual" name="stock_actual" min="0" step="1" value="<?= (int) $producto['stock_actual'] ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="stock_minimo" class="form-label">Stock mínimo</label>
                            <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" min="0" step="1" value="<?= (int) $producto['stock_minimo'] ?>" required>
                        </div>

                        <div class="col-12">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="255"><?= htmlspecialchars($producto['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button class="btn btn-primary" type="submit">Guardar cambios</button>
                            <a href="../index.php" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="../assets/js/validaciones.js"></script>
</body>
</html>
