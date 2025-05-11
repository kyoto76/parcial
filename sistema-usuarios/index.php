<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    header('Location: perfil.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplicación Web con Registro, Login y Perfil de Usuario</title>
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
        .feature-icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <header class="bg-white shadow-sm">
        <div class="container py-3">
            <h1 class="fs-2 fw-bold text-dark">Aplicación Web con Registro, Login y Perfil de Usuario</h1>
        </div>
    </header>
    
    <main class="main-content container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h2 class="fs-4 mb-0">Bienvenido al Sistema</h2>
                        <p class="text-muted small mb-0">Sistema de gestión de usuarios con registro, login y perfil</p>
                    </div>
                    <div class="card-body">
                        <p class="mb-4">
                            Esta aplicación permite a los usuarios registrarse, iniciar sesión y gestionar su perfil.
                            Utiliza PHP orientado a objetos y almacena los datos en archivos planos.
                        </p>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="p-4 bg-light rounded">
                                    <h3 class="fs-5 fw-medium mb-3">Características principales:</h3>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="bi bi-check-circle feature-icon"></i> Registro de usuarios</li>
                                        <li class="mb-2"><i class="bi bi-check-circle feature-icon"></i> Inicio de sesión seguro</li>
                                        <li class="mb-2"><i class="bi bi-check-circle feature-icon"></i> Función "Recordarme"</li>
                                        <li class="mb-2"><i class="bi bi-check-circle feature-icon"></i> Recuperación de contraseña</li>
                                        <li class="mb-2"><i class="bi bi-check-circle feature-icon"></i> Gestión de perfil</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-4 bg-light rounded">
                                    <h3 class="fs-5 fw-medium mb-3">Tecnologías utilizadas:</h3>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="bi bi-code-square feature-icon"></i> PHP Orientado a Objetos</li>
                                        <li class="mb-2"><i class="bi bi-bootstrap feature-icon"></i> Bootstrap 5</li>
                                        <li class="mb-2"><i class="bi bi-key feature-icon"></i> Sesiones y Cookies</li>
                                        <li class="mb-2"><i class="bi bi-file-text feature-icon"></i> Manejo de archivos planos</li>
                                        <li class="mb-2"><i class="bi bi-envelope feature-icon"></i> PHPMailer para emails</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-center gap-3">
                        <a href="login.php" class="btn btn-primary">Iniciar Sesión</a>
                        <a href="registro.php" class="btn btn-outline-secondary">Registrarse</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="bg-white border-top py-3">
        <div class="container text-center text-muted small">
            &copy; <?php echo date('Y'); ?> kyoto
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</body>
</html>