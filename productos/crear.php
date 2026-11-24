<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/conexion.php';

$sql = 'SELECT id_categoria, nombre
        FROM categorias
        WHERE estado = 1
        ORDER BY nombre';

$categorias = $pdo
    ->query($sql)
    ->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar producto - TecnoStock</title>
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
                <h1>Registrar producto</h1>
                <p class="subtitulo">Completa los datos del nuevo producto.</p>
            </div>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">Revise los datos ingresados.</div>
        <?php endif; ?>

        <div class="card form-panel">
            <div class="card-body">
                <form action="guardar.php" method="post" class="validar-formulario" novalidate>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="codigo" class="form-label">Código</label>
                            <input type="text" class="form-control" id="codigo" name="codigo" maxlength="20" pattern="[A-Za-z0-9-]+" placeholder="TEC-001" required>
                        </div>

                        <div class="col-md-8">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" minlength="3" maxlength="100" required>
                        </div>

                        <div class="col-md-6">
                            <label for="categoria_id" class="form-label">Categoría</label>
                            <select class="form-select" id="categoria_id" name="categoria_id" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= (int) $categoria['id_categoria'] ?>">
                                        <?= htmlspecialchars($categoria['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="precio" class="form-label">Precio</label>
                            <input type="number" class="form-control" id="precio" name="precio" min="0" step="0.01" required>
                        </div>

                        <div class="col-md-6">
                            <label for="stock_actual" class="form-label">Stock actual</label>
                            <input type="number" class="form-control" id="stock_actual" name="stock_actual" min="0" step="1" required>
                        </div>

                        <div class="col-md-6">
                            <label for="stock_minimo" class="form-label">Stock mínimo</label>
                            <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" min="0" step="1" required>
                        </div>

                        <div class="col-12">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="255"></textarea>
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>
                                Guardar producto
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">Limpiar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="../assets/js/validaciones.js"></script>
</body>
</html>
