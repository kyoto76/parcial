<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    header('Location: perfil.php');
    exit;
}

require_once 'clases/Usuario.php';

$errores = [];
$exito = false;

if (!isset($_SESSION['usuario_id']) && isset($_COOKIE['recordar_usuario'])) {
    $usuario = new Usuario();
    $resultado = $usuario->loginPorToken($_COOKIE['recordar_usuario']);
    
    if ($resultado) {

        header('Location: perfil.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $recordar = isset($_POST['recordar']);
    
    if (empty($email)) {
        $errores['email'] = 'El email es obligatorio';
    }
    
    if (empty($password)) {
        $errores['password'] = 'La contraseña es obligatoria';
    }
    
    if (empty($errores)) {
        try {
            $usuario = new Usuario();
            $resultado = $usuario->login($email, $password);
            
            if ($resultado) {
                $exito = true;
                

                if ($recordar) {
                    $token = $usuario->generarTokenRecordar();
                    setcookie('recordar_usuario', $token, time() + (30 * 24 * 60 * 60), '/'); // 30 días
                }

                header('Refresh: 1; URL=perfil.php');
            } else {
                $errores['general'] = 'Email o contraseña incorrectos';
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
    <title>Iniciar Sesión</title>
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
                        <h1 class="fs-4 mb-0">Iniciar Sesión</h1>
                        <p class="text-muted small mb-0">Ingresa tus credenciales para acceder al sistema</p>
                    </div>
                    <div class="card-body">
                        <?php if ($exito): ?>
                            <div class="alert alert-success" role="alert">
                                ¡Login exitoso! Redirigiendo...
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($errores['general'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $errores['general']; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
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
                                <div class="d-flex justify-content-between">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <a href="recuperar.php" class="text-decoration-none small">¿Olvidaste tu contraseña?</a>
                                </div>
                                <input type="password" class="form-control <?php echo isset($errores['password']) ? 'is-invalid' : ''; ?>" 
                                       id="password" name="password" placeholder="Contraseña">
                                <?php if (isset($errores['password'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errores['password']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="recordar" name="recordar">
                                <label class="form-check-label" for="recordar">Recordarme en este dispositivo</label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
                        </form>
                    </div>
                    <div class="card-footer bg-white text-center">
                        <p class="mb-0 text-muted">
                            ¿No tienes una cuenta? <a href="registro.php" class="text-decoration-none">Regístrate</a>
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