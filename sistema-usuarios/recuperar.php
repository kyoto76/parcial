<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    header('Location: perfil.php');
    exit;
}

require_once 'clases/Usuario.php';

$error = '';
$exito = false;
$enviando = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Por favor ingresa tu correo electrónico';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El formato del email no es válido';
    } else {
        $enviando = true;
        
        try {
            $usuario = new Usuario();
            
            if ($usuario->existeEmail($email)) {
                $token = $usuario->generarTokenRecuperacion($email);

                if ($usuario->enviarCorreoRecuperacion($email, $token)) {
                    $exito = true;
                } else {
                    $error = 'Error al enviar el correo de recuperación';
                }
            } else {
                $exito = true;
            }
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
        
        $enviando = false;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
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
                <a href="login.php" class="d-inline-flex align-items-center text-decoration-none text-muted mb-4">
                    <i class="bi bi-arrow-left me-2"></i> Volver al login
                </a>
                
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h1 class="fs-4 mb-0">Recuperar Contraseña</h1>
                        <p class="text-muted small mb-0">Ingresa tu correo electrónico para recibir un enlace de recuperación</p>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($exito): ?>
                            <div class="text-center py-4">
                                <div class="mx-auto d-flex h-12 w-12 align-items-center justify-content-center rounded-circle bg-success bg-opacity-25 mb-4" style="width: 64px; height: 64px;">
                                    <i class="bi bi-envelope text-success" style="font-size: 1.5rem;"></i>
                                </div>
                                <h3 class="fs-5 fw-medium mb-2">Correo enviado</h3>
                                <p class="text-muted mb-4">
                                    Hemos enviado un enlace de recuperación a <strong><?php echo htmlspecialchars($email); ?></strong>. 
                                    Por favor revisa tu bandeja de entrada.
                                </p>
                                <a href="login.php" class="btn btn-outline-secondary mt-2">
                                    Volver al login
                                </a>
                            </div>
                        <?php else: ?>
                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                <div class="mb-4">
                                    <label for="email" class="form-label">Correo electrónico</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           placeholder="tu@ejemplo.com" value="<?php echo htmlspecialchars($email ?? ''); ?>">
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100" <?php echo $enviando ? 'disabled' : ''; ?>>
                                    <?php if ($enviando): ?>
                                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                        Enviando...
                                    <?php else: ?>
                                        Enviar enlace de recuperación
                                    <?php endif; ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-white text-center">
                        <p class="mb-0 text-muted">
                            ¿Recordaste tu contraseña? <a href="login.php" class="text-decoration-none">Iniciar sesión</a>
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