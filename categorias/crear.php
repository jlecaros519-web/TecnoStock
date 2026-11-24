<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php');
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva categoría - TecnoStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <a href="listar.php" class="btn btn-salir btn-sm">Volver</a>
        </div>
    </nav>

    <main class="container contenido-principal">
        <div class="page-heading">
            <div>
                <div class="eyebrow">Categorías</div>
                <h1>Registrar categoría</h1>
            </div>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">Revise los datos ingresados.</div>
        <?php endif; ?>

        <div class="card form-panel">
            <div class="card-body">
                <form action="guardar.php" method="post" class="validar-formulario" novalidate>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" minlength="3" maxlength="100" required>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4" maxlength="255"></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" type="submit">Guardar categoría</button>
                        <button class="btn btn-outline-secondary" type="reset">Limpiar</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="../assets/js/validaciones.js"></script>
</body>
</html>
