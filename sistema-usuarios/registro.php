<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    header('Location: perfil.php');
    exit;
}


require_once 'clases/Usuario.php';

$errores = [];
$exito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    if (empty($nombre)) {
        $errores['nombre'] = 'El nombre es obligatorio';
    }
    
    if (empty($email)) {
        $errores['email'] = 'El email es obligatorio';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'El formato del email no es válido';
    }
    
    if (empty($password)) {
        $errores['password'] = 'La contraseña es obligatoria';
    } elseif (strlen($password) < 6) {
        $errores['password'] = 'La contraseña debe tener al menos 6 caracteres';
    }
    
    if ($password !== $confirmPassword) {
        $errores['confirmPassword'] = 'Las contraseñas no coinciden';
    }
    
    if (empty($errores)) {
        try {
            $usuario = new Usuario();
            
            if ($usuario->existeEmail($email)) {
                $errores['email'] = 'Este email ya está registrado';
            } else {

                $resultado = $usuario->registrar($nombre, $email, $password);
                
                if ($resultado) {
                    $exito = true;
                    
                    header('Refresh: 2; URL=login.php');
                } else {
                    $errores['general'] = 'Error al registrar el usuario';
                }
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
    <title>Registro de Usuario</title>
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
    <div class="main-content container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <a href="index.php" class="d-inline-flex align-items-center text-decoration-none text-muted mb-4">
                    <i class="bi bi-arrow-left me-2"></i> Volver al inicio
                </a>
                
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h1 class="fs-4 mb-0">Registro de Usuario</h1>
                        <p class="text-muted small mb-0">Crea una nueva cuenta para acceder al sistema</p>
                    </div>
                    <div class="card-body">
                        <?php if ($exito): ?>
                            <div class="alert alert-success" role="alert">
                                ¡Registro exitoso! Redirigiendo al login...
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($errores['general'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $errores['general']; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre completo</label>
                                <input type="text" class="form-control <?php echo isset($errores['nombre']) ? 'is-invalid' : ''; ?>" 
                                       id="nombre" name="nombre" placeholder="Ingresa tu nombre" 
                                       value="<?php echo htmlspecialchars($nombre ?? ''); ?>">
                                <?php if (isset($errores['nombre'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errores['nombre']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control <?php echo isset($errores['email']) ? 'is-invalid' : ''; ?>" 
                                       id="email" name="email" placeholder="tu@ejemplo.com" 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>">
                                <?php if (isset($errores['email'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errores['email']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control <?php echo isset($errores['password']) ? 'is-invalid' : ''; ?>" 
                                       id="password" name="password" placeholder="Contraseña">
                                <?php if (isset($errores['password'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errores['password']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-4">
                                <label for="confirmPassword" class="form-label">Confirmar contraseña</label>
                                <input type="password" class="form-control <?php echo isset($errores['confirmPassword']) ? 'is-invalid' : ''; ?>" 
                                       id="confirmPassword" name="confirmPassword" placeholder="Confirma tu contraseña">
                                <?php if (isset($errores['confirmPassword'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errores['confirmPassword']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Registrarse</button>
                        </form>
                    </div>
                    <div class="card-footer bg-white text-center">
                        <p class="mb-0 text-muted">
                            ¿Ya tienes una cuenta? <a href="login.php" class="text-decoration-none">Iniciar sesión</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</body>
</html>