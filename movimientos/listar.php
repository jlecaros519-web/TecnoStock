<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/conexion.php';

$sql = 'SELECT
            m.fecha_hora,
            m.tipo,
            m.cantidad,
            m.observacion,
            p.codigo,
            p.nombre AS producto,
            u.nombre AS usuario
        FROM movimientos m
        INNER JOIN productos p
            ON p.id_producto = m.id_producto
        INNER JOIN usuarios u
            ON u.id_usuario = m.id_usuario
        ORDER BY m.fecha_hora DESC,
                 m.id_movimiento DESC';

$movimientos = $pdo
    ->query($sql)
    ->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Historial - TecnoStock</title>
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
                <div class="eyebrow">Inventario</div>
                <h1>Historial de movimientos</h1>
                <p class="subtitulo">Revisa las entradas y salidas registradas.</p>
            </div>
            <a href="registrar.php" class="btn btn-success">
                <i class="bi bi-plus-circle me-1"></i>
                Nuevo movimiento
            </a>
        </div>

        <?php if (isset($_GET['creado'])): ?>
            <div class="alert alert-success">Movimiento registrado correctamente.</div>
        <?php endif; ?>

        <section class="card">
            <div class="card-header">
                <h2 class="section-title">
                    <i class="bi bi-clock-history me-2"></i>
                    Movimientos registrados
                </h2>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Tipo</th>
                                <th>Cantidad</th>
                                <th>Observación</th>
                                <th>Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($movimientos): ?>
                                <?php foreach ($movimientos as $movimiento): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($movimiento['fecha_hora'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($movimiento['codigo'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($movimiento['producto'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>
                                            <?php if ($movimiento['tipo'] === 'ENTRADA'): ?>
                                                <span class="badge text-bg-success">Entrada</span>
                                            <?php else: ?>
                                                <span class="badge text-bg-danger">Salida</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= (int) $movimiento['cantidad'] ?></td>
                                        <td><?= htmlspecialchars($movimiento['observacion'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($movimiento['usuario'], ENT_QUOTES, 'UTF-8') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Todavía no existen movimientos.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
