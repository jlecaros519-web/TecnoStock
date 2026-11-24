<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/conexion.php';

$sql = 'SELECT
            c.id_categoria,
            c.nombre,
            c.descripcion,
            c.estado,
            COUNT(p.id_producto) AS productos_activos
        FROM categorias c
        LEFT JOIN productos p
            ON p.id_categoria = c.id_categoria
            AND p.estado = 1
        GROUP BY
            c.id_categoria,
            c.nombre,
            c.descripcion,
            c.estado
        ORDER BY c.estado DESC, c.nombre';

$categorias = $pdo
    ->query($sql)
    ->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Categorías - TecnoStock</title>
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
            <a href="../index.php" class="btn btn-salir btn-sm">Inicio</a>
        </div>
    </nav>

    <main class="container contenido-principal">
        <div class="page-heading">
            <div>
                <div class="eyebrow">Administración</div>
                <h1>Categorías</h1>
                <p class="subtitulo">Organiza los productos del inventario.</p>
            </div>
            <a href="crear.php" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>
                Nueva categoría
            </a>
        </div>

        <?php if (isset($_GET['creado'])): ?>
            <div class="alert alert-success">Categoría registrada correctamente.</div>
        <?php endif; ?>

        <?php if (isset($_GET['editado'])): ?>
            <div class="alert alert-success">Categoría actualizada correctamente.</div>
        <?php endif; ?>

        <?php if (isset($_GET['desactivado'])): ?>
            <div class="alert alert-success">Categoría desactivada correctamente.</div>
        <?php endif; ?>

        <?php if (isset($_GET['activado'])): ?>
            <div class="alert alert-success">Categoría activada correctamente.</div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">No fue posible realizar esa operación.</div>
        <?php endif; ?>

        <section class="card">
            <div class="card-header">
                <h2 class="section-title">
                    <i class="bi bi-tags me-2"></i>
                    Listado de categorías
                </h2>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Productos activos</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categorias as $categoria): ?>
                                <tr>
                                    <td><?= htmlspecialchars($categoria['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($categoria['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= (int) $categoria['productos_activos'] ?></td>
                                    <td>
                                        <?php if ($categoria['estado']): ?>
                                            <span class="badge text-bg-success">Activa</span>
                                        <?php else: ?>
                                            <span class="badge text-bg-secondary">Inactiva</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <?php if ($categoria['estado']): ?>
                                                <a href="editar.php?id=<?= (int) $categoria['id_categoria'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                                <form action="desactivar.php" method="post">
                                                    <input type="hidden" name="id_categoria" value="<?= (int) $categoria['id_categoria'] ?>">
                                                    <button class="btn btn-danger btn-sm" type="submit">Desactivar</button>
                                                </form>
                                            <?php else: ?>
                                                <form action="activar.php" method="post">
                                                    <input type="hidden" name="id_categoria" value="<?= (int) $categoria['id_categoria'] ?>">
                                                    <button class="btn btn-success btn-sm" type="submit">Activar</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
