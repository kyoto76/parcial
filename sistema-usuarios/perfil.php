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


if (isset($_POST['cerrar_sesion'])) {
if (isset($_COOKIE['recordar_usuario'])) {
        setcookie('recordar_usuario', '', time() - 3600, '/');
    }
    
    session_destroy();

    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
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
        .profile-avatar {
            width: 120px;
            height: 120px;
            background-color: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: #6c757d;
        }
        .profile-info {
            margin-bottom: 1.5rem;
        }
        .profile-label {
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }
        .profile-value {
            font-size: 1.125rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <header class="bg-white shadow-sm">
        <div class="container py-3 d-flex justify-content-between align-items-center">
            <h1 class="fs-4 fw-bold mb-0">Mi Perfil</h1>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <button type="submit" name="cerrar_sesion" class="btn btn-sm btn-outline-secondary d-flex align-items-center">
                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </header>
    
    <div class="main-content container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <a href="index.php" class="d-inline-flex align-items-center text-decoration-none text-muted mb-4">
                    <i class="bi bi-arrow-left me-2"></i> Volver al inicio
                </a>
                
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fs-4 mb-0">Información del Usuario</h2>
                            <p class="text-muted small mb-0">Datos de tu cuenta en el sistema</p>
                        </div>
                        <a href="editar_perfil.php" class="btn btn-sm btn-outline-primary d-flex align-items-center">
                            <i class="bi bi-pencil me-2"></i> Editar Perfil
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4 text-center">
                                <div class="profile-avatar mx-auto mb-3">
                                    <?php echo substr($datosUsuario['nombre'], 0, 1); ?>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="profile-info">
                                    <div class="profile-label d-flex align-items-center">
                                        <i class="bi bi-person me-2"></i> Nombre completo
                                    </div>
                                    <div class="profile-value">
                                        <?php echo htmlspecialchars($datosUsuario['nombre']); ?>
                                    </div>
                                </div>
                                
                                <div class="profile-info">
                                    <div class="profile-label d-flex align-items-center">
                                        <i class="bi bi-envelope me-2"></i> Correo electrónico
                                    </div>
                                    <div class="profile-value">
                                        <?php echo htmlspecialchars($datosUsuario['email']); ?>
                                    </div>
                                </div>
                                
                                <div class="profile-info">
                                    <div class="profile-label d-flex align-items-center">
                                        <i class="bi bi-calendar me-2"></i> Fecha de registro
                                    </div>
                                    <div class="profile-value">
                                        <?php echo date('d/m/Y', strtotime($datosUsuario['fecha_registro'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</body>
</html>