<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/conexion.php';

$busqueda = trim($_GET['busqueda'] ?? '');

$sql = 'SELECT
            p.id_producto,
            p.codigo,
            p.nombre,
            c.nombre AS categoria,
            p.precio,
            p.stock_actual,
            p.stock_minimo
        FROM productos p
        INNER JOIN categorias c
            ON c.id_categoria = p.id_categoria
        WHERE p.estado = 1';

$parametros = [];

if ($busqueda !== '') {
    $sql .= ' AND (
                p.codigo LIKE :codigo
                OR p.nombre LIKE :nombre
                OR c.nombre LIKE :categoria
              )';

    $parametros = [
        ':codigo' => '%' . $busqueda . '%',
        ':nombre' => '%' . $busqueda . '%',
        ':categoria' => '%' . $busqueda . '%',
    ];
}

$sql .= ' ORDER BY p.nombre';

$sentencia = $pdo->prepare($sql);
$sentencia->execute($parametros);
$productos = $sentencia->fetchAll();

$totalProductos = (int) $pdo
    ->query('SELECT COUNT(*) FROM productos WHERE estado = 1')
    ->fetchColumn();

$totalStockBajo = (int) $pdo
    ->query('SELECT COUNT(*) FROM productos WHERE estado = 1 AND stock_actual < stock_minimo')
    ->fetchColumn();

$totalCategorias = (int) $pdo
    ->query('SELECT COUNT(*) FROM categorias WHERE estado = 1')
    ->fetchColumn();

$totalMovimientos = (int) $pdo
    ->query('SELECT COUNT(*) FROM movimientos')
    ->fetchColumn();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TecnoStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
    <nav class="barra-principal">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
            <a href="index.php" class="marca">
                <img src="assets/img/logo-tecnostock.svg" class="marca-logo" alt="Logo TecnoStock">
                <span class="marca-texto">
                    TecnoStock
                    <small>Sistema de inventario</small>
                </span>
            </a>

            <div class="d-flex align-items-center gap-3">
                <span class="nombre-usuario">
                    <i class="bi bi-person-circle me-1"></i>
                    <?= htmlspecialchars($_SESSION['nombre'], ENT_QUOTES, 'UTF-8') ?>
                </span>
                <a href="logout.php" class="btn btn-salir btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>
                    Salir
                </a>
            </div>
        </div>
    </nav>

    <main class="container contenido-principal">
        <div class="page-heading">
            <div>
                <div class="eyebrow">Panel principal</div>
                <h1>Inventario TecnoStock</h1>
                <p class="subtitulo">Administra productos, categorías y movimientos desde un solo lugar.</p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="productos/crear.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    Nuevo producto
                </a>
                <a href="movimientos/registrar.php" class="btn btn-success">
                    <i class="bi bi-arrow-left-right me-1"></i>
                    Movimiento
                </a>
            </div>
        </div>

        <?php if (isset($_GET['creado'])): ?>
            <div class="alert alert-success">Producto registrado correctamente.</div>
        <?php endif; ?>

        <?php if (isset($_GET['editado'])): ?>
            <div class="alert alert-success">Producto actualizado correctamente.</div>
        <?php endif; ?>

        <?php if (isset($_GET['desactivado'])): ?>
            <div class="alert alert-success">Producto desactivado correctamente.</div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">No fue posible realizar la operación.</div>
        <?php endif; ?>

        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <span class="stat-label">Productos activos</span>
                        <span class="stat-value"><?= $totalProductos ?></span>
                    </div>
                    <span class="stat-icon"><i class="bi bi-box-seam"></i></span>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <span class="stat-label">Stock bajo</span>
                        <span class="stat-value"><?= $totalStockBajo ?></span>
                    </div>
                    <span class="stat-icon rojo"><i class="bi bi-exclamation-triangle"></i></span>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <span class="stat-label">Categorías activas</span>
                        <span class="stat-value"><?= $totalCategorias ?></span>
                    </div>
                    <span class="stat-icon violeta"><i class="bi bi-tags"></i></span>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <span class="stat-label">Movimientos</span>
                        <span class="stat-value"><?= $totalMovimientos ?></span>
                    </div>
                    <span class="stat-icon verde"><i class="bi bi-arrow-left-right"></i></span>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mb-4">
            <a href="categorias/listar.php" class="btn btn-secondary">
                <i class="bi bi-tags me-1"></i>
                Categorías
            </a>
            <a href="movimientos/listar.php" class="btn btn-outline-secondary">
                <i class="bi bi-clock-history me-1"></i>
                Historial
            </a>
        </div>

        <section class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h2 class="section-title">
                    <i class="bi bi-boxes me-2"></i>
                    Productos
                </h2>
            </div>

            <div class="card-body">
                <form action="index.php" method="get" class="row g-2 mb-4">
                    <div class="col-md-8">
                        <input
                            type="search"
                            name="busqueda"
                            class="form-control"
                            placeholder="Buscar por código, nombre o categoría"
                            value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search me-1"></i>
                            Buscar
                        </button>
                    </div>
                    <div class="col-auto">
                        <a href="index.php" class="btn btn-outline-secondary">Limpiar</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table tabla-productos">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Stock actual</th>
                                <th>Stock mínimo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($productos): ?>
                                <?php foreach ($productos as $producto): ?>
                                    <?php $stockBajo = $producto['stock_actual'] < $producto['stock_minimo']; ?>
                                    <tr class="<?= $stockBajo ? 'fila-stock-bajo' : '' ?>">
                                        <td><?= htmlspecialchars($producto['codigo'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($producto['categoria'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>
                                            $<?= number_format((float) $producto['precio'], 0, ',', '.') ?>
                                        </td>
                                        <td><?= (int) $producto['stock_actual'] ?></td>
                                        <td><?= (int) $producto['stock_minimo'] ?></td>
                                        <td>
                                            <?php if ($stockBajo): ?>
                                                <span class="badge text-bg-danger">Stock bajo</span>
                                            <?php else: ?>
                                                <span class="badge text-bg-success">Disponible</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <a href="productos/editar.php?id=<?= (int) $producto['id_producto'] ?>" class="btn btn-warning btn-sm">
                                                    <i class="bi bi-pencil"></i>
                                                    Editar
                                                </a>
                                                <form action="productos/desactivar.php" method="post" onsubmit="return confirm('¿Desactivar este producto?')">
                                                    <input type="hidden" name="id_producto" value="<?= (int) $producto['id_producto'] ?>">
                                                    <button class="btn btn-danger btn-sm" type="submit">
                                                        <i class="bi bi-x-circle"></i>
                                                        Desactivar
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">No se encontraron productos.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <div class="pie-pagina">TecnoStock · Sistema de gestión de inventario</div>
    </main>
</body>
</html>
