<?php
session_start();

if (isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión - TecnoStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
    <main class="auth-shell">
        <div class="card auth-card">
            <div class="auth-top">
                <img src="assets/img/logo-tecnostock.svg" class="auth-logo" alt="Logo TecnoStock">
                <h1>TecnoStock</h1>
                <p>Sistema de gestión de inventario</p>
            </div>
            <div class="card-body">
                <h2 class="h4 text-center mb-4">Iniciar sesión</h2>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">Correo o contraseña incorrectos.</div>
                <?php endif; ?>

                <?php if (isset($_GET['cerrado'])): ?>
                    <div class="alert alert-success">Sesión cerrada correctamente.</div>
                <?php endif; ?>

                <form action="procesar_login.php" method="post" class="validar-formulario" novalidate>
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="correo" name="correo" maxlength="150" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="clave" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="clave" name="clave" required>
                            <button class="btn btn-outline-secondary" type="button" id="verClave">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            Iniciar sesión
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="assets/js/validaciones.js"></script>
    <script>
        const boton = document.getElementById('verClave');
        const clave = document.getElementById('clave');

        boton.addEventListener('click', function () {
            if (clave.type === 'password') {
                clave.type = 'text';
                this.innerHTML = '<i class="bi bi-eye-slash"></i>';
            } else {
                clave.type = 'password';
                this.innerHTML = '<i class="bi bi-eye"></i>';
            }
        });
    </script>
</body>
</html>
