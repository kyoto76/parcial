<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'clases/Usuario.php';

$usuario = new Usuario();
$datosUsuario = $usuario->obtenerPorId($_SESSION['usuario_id']);

if (!$datosUsuario) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$errores = [];
$exito = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_perfil'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    if (empty($nombre)) {
        $errores['nombre'] = 'El nombre es obligatorio';
    }
    
    if (empty($email)) {
        $errores['email'] = 'El email es obligatorio';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'El formato del email no es válido';
    } elseif ($email !== $datosUsuario['email'] && $usuario->existeEmail($email)) {
        $errores['email'] = 'Este email ya está registrado por otro usuario';
    }

    if (empty($errores)) {
        try {
            $resultado = $usuario->actualizarPerfil($_SESSION['usuario_id'], $nombre, $email);
            
            if ($resultado) {
                $exito = 'Perfil actualizado correctamente';
                $datosUsuario['nombre'] = $nombre;
                $datosUsuario['email'] = $email;
            } else {
                $errores['general'] = 'Error al actualizar el perfil';
            }
        } catch (Exception $e) {
            $errores['general'] = 'Error: ' . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_password'])) {
    $currentPassword = $_POST['currentPassword'] ?? '';
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    if (empty($currentPassword)) {
        $errores['currentPassword'] = 'La contraseña actual es obligatoria';
    } elseif (!$usuario->verificarPassword($_SESSION['usuario_id'], $currentPassword)) {
        $errores['currentPassword'] = 'La contraseña actual es incorrecta';
    }
    
    if (empty($newPassword)) {
        $errores['newPassword'] = 'La nueva contraseña es obligatoria';
    } elseif (strlen($newPassword) < 6) {
        $errores['newPassword'] = 'La contraseña debe tener al menos 6 caracteres';
    }
    
    if ($newPassword !== $confirmPassword) {
        $errores['confirmPassword'] = 'Las contraseñas no coinciden';
    }
    
    if (empty($errores)) {
        try {
            $resultado = $usuario->cambiarPassword($_SESSION['usuario_id'], $newPassword);
            
            if ($resultado) {
                $exito = 'Contraseña actualizada correctamente';
            } else {
                $errores['general'] = 'Error al actualizar la contraseña';
            }
        } catch (Exception $e) {
            $errores['general'] = 'Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
        }
    </style>
</head>
<body>
    <header class="bg-white shadow-sm">
        <div class="container py-3">
            <h1 class="fs-4 fw-bold mb-0">Editar Perfil</h1>
        </div>
    </header>
    
    <div class="main-content container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <a href="perfil.php" class="d-inline-flex align-items-center text-decoration-none text-muted mb-4">
                    <i class="bi bi-arrow-left me-2"></i> Volver al perfil
                </a>
                
                <?php if ($exito): ?>
                    <div class="alert alert-success mb-4" role="alert">
                        <?php echo $exito; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($errores['general'])): ?>
                    <div class="alert alert-danger mb-4" role="alert">
                        <?php echo $errores['general']; ?>
                    </div>
                <?php endif; ?>
                
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h2 class="fs-5 mb-0">Editar Información</h2>
                        <p class="text-muted small mb-0">Actualiza tus datos personales</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre completo</label>
                                <input type="text" class="form-control <?php echo isset($errores['nombre']) ? 'is-invalid' : ''; ?>" 
                                       id="nombre" name="nombre" 
                                       value="<?php echo htmlspecialchars($datosUsuario['nombre']); ?>">
                                <?php if (isset($errores['nombre'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errores['nombre']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-4">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control <?php echo isset($errores['email']) ? 'is-invalid' : ''; ?>" 
                                       id="email" name="email" 
                                       value="<?php echo htmlspecialchars($datosUsuario['email']); ?>">
                                <?php if (isset($errores['email'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errores['email']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <button type="submit" name="actualizar_perfil" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                <i class="bi bi-save me-2"></i> Guardar Cambios
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h2 class="fs-5 mb-0">Cambiar Contraseña</h2>
                        <p class="text-muted small mb-0">Actualiza tu contraseña de acceso</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                            <div class="mb-3">
                                <label for="currentPassword" class="form-label">Contraseña actual</label>
                                <input type="password" class="form-control <?php echo isset($errores['currentPassword']) ? 'is-invalid' : ''; ?>" 
                                       id="currentPassword" name="currentPassword">
                                <?php if (isset($errores['currentPassword'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errores['currentPassword']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="newPassword" class="form-label">Nueva contraseña</label>
                                <input type="password" class="form-control <?php echo isset($errores['newPassword']) ? 'is-invalid' : ''; ?>" 
                                       id="newPassword" name="newPassword">
                                <?php if (isset($errores['newPassword'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errores['newPassword']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-4">
                                <label for="confirmPassword" class="form-label">Confirmar nueva contraseña</label>
                                <input type="password" class="form-control <?php echo isset($errores['confirmPassword']) ? 'is-invalid' : ''; ?>" 
                                       id="confirmPassword" name="confirmPassword">
                                <?php if (isset($errores['confirmPassword'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errores['confirmPassword']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <button type="submit" name="cambiar_password" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                <i class="bi bi-key me-2"></i> Actualizar Contraseña
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</body>
</html>