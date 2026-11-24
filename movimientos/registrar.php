<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/conexion.php';

$sql = 'SELECT
            id_producto,
            codigo,
            nombre,
            stock_actual
        FROM productos
        WHERE estado = 1
        ORDER BY nombre';

$productos = $pdo
    ->query($sql)
    ->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar movimiento - TecnoStock</title>
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
                <div class="eyebrow">Movimientos</div>
                <h1>Registrar movimiento</h1>
                <p class="subtitulo">Registra una entrada o salida de existencias.</p>
            </div>
            <a href="listar.php" class="btn btn-outline-secondary">
                <i class="bi bi-clock-history me-1"></i>
                Historial
            </a>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">Revise los datos del movimiento.</div>
        <?php endif; ?>

        <div class="card form-panel">
            <div class="card-body">
                <form action="guardar.php" method="post" class="validar-formulario" novalidate>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="producto_id" class="form-label">Producto</label>
                            <select class="form-select" id="producto_id" name="producto_id" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($productos as $producto): ?>
                                    <option value="<?= (int) $producto['id_producto'] ?>">
                                        <?= htmlspecialchars(
                                            $producto['codigo'] . ' - ' . $producto['nombre'] . ' (Stock: ' . $producto['stock_actual'] . ')',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="tipo" class="form-label">Tipo</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="">Seleccione...</option>
                                <option value="ENTRADA">Entrada</option>
                                <option value="SALIDA">Salida</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="cantidad" class="form-label">Cantidad</label>
                            <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" step="1" required>
                        </div>

                        <div class="col-12">
                            <label for="observacion" class="form-label">Observación</label>
                            <textarea class="form-control" id="observacion" name="observacion" rows="3" maxlength="255"></textarea>
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button class="btn btn-success" type="submit">
                                <i class="bi bi-check-circle me-1"></i>
                                Registrar movimiento
                            </button>
                            <button class="btn btn-outline-secondary" type="reset">Limpiar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="../assets/js/validaciones.js"></script>
</body>
</html>
