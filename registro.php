<?php
session_start();
require_once "config/conexion.php";

if (isset($_SESSION["id_usuario"])) {
    header("Location: index.php");
    exit;
}

$error = "";
$nombre = "";
$correo = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $clave = $_POST["clave"] ?? "";
    $confirmar = $_POST["confirmar_clave"] ?? "";

    if ($nombre === "" || $correo === "" || $clave === "" || $confirmar === "") {
        $error = "Debes completar todos los campos.";
    } elseif (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100) {
        $error = "El nombre debe tener entre 3 y 100 caracteres.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "El correo electrónico no es válido.";
    } elseif (strlen($clave) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres.";
    } elseif ($clave !== $confirmar) {
        $error = "Las contraseñas no coinciden.";
    } else {
        $sql = "SELECT id_usuario FROM usuarios WHERE correo = :correo LIMIT 1";
        $consulta = $conexion->prepare($sql);
        $consulta->execute([":correo" => $correo]);

        if ($consulta->fetch()) {
            $error = "Ya existe una cuenta con ese correo.";
        } else {
            $hash = password_hash($clave, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (nombre, correo, clave, estado) VALUES (:nombre, :correo, :clave, 1)";
            $consulta = $conexion->prepare($sql);
            $consulta->execute([
                ":nombre" => $nombre,
                ":correo" => $correo,
                ":clave" => $hash
            ]);
            header("Location: login.php?registrado=1");
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Crear cuenta - TecnoStock</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="assets/css/estilos.css">
    </head>
    <body>
        <main class="auth-shell">
            <div class="card auth-card">
                <div class="auth-top">
                    <img src="assets/img/logo-tecnostock.svg" class="auth-logo" alt="Logo TecnoStock">
                    <h1>TecnoStock</h1>
                    <p>Crear cuenta de usuario</p>
                </div>
                <div class="card-body">
                    <?php if ($error !== ""): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" minlength="3" maxlength="100" value="<?= htmlspecialchars($nombre) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" maxlength="150" value="<?= htmlspecialchars($correo) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="clave" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="clave" name="clave" minlength="8" required>
                        </div>
                        <div class="mb-4">
                            <label for="confirmar_clave" class="form-label">Confirmar contraseña</label>
                            <input type="password" class="form-control" id="confirmar_clave" name="confirmar_clave" minlength="8" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i> Crear cuenta</button>
                        </div>
                    </form>
                    <hr>
                    <p class="text-center mb-0">
                        ¿Ya tienes una cuenta?
                        <a href="login.php">
                            Iniciar sesión
                        </a>
                    </p>
                </div>
            </div>
        </main>
    </body>
</html>
