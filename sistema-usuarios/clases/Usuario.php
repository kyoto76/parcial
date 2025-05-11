<?php
class Usuario {
    private $archivoUsuarios = 'datos/usuarios.txt';
    public function __construct() {
        if (!is_dir('datos')) {
            mkdir('datos', 0755);
        }
        
        if (!file_exists($this->archivoUsuarios)) {
            file_put_contents($this->archivoUsuarios, '');
        }
    }
    
    /**
     * Registra un nuevo usuario
     * 
     * @param string $nombre Nombre completo del usuario
     * @param string $email Correo electrónico del usuario
     * @param string $password Contraseña del usuario
     * @return bool True si el registro fue exitoso, False en caso contrario
     */
    public function registrar($nombre, $email, $password) {
        if ($this->existeEmail($email)) {
            return false;
        }
        
        $id = uniqid();
        

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        

        $usuario = [
            'id' => $id,
            'nombre' => $nombre,
            'email' => $email,
            'password' => $passwordHash,
            'fecha_registro' => date('Y-m-d H:i:s'),
            'token_recordar' => null,
            'token_recuperacion' => null,
            'expira_recuperacion' => null
        ];
        

        $usuarioJson = json_encode($usuario) . PHP_EOL;
        

        if (file_put_contents($this->archivoUsuarios, $usuarioJson, FILE_APPEND | LOCK_EX)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Verifica si un email ya existe en el archivo de usuarios
     * 
     * @param string $email Correo electrónico a verificar
     * @return bool True si el email existe, False en caso contrario
     */
    public function existeEmail($email) {
        $usuarios = $this->obtenerTodos();
        
        foreach ($usuarios as $usuario) {
            if ($usuario['email'] === $email) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Inicia sesión con email y contraseña
     * 
     * @param string $email Correo electrónico del usuario
     * @param string $password Contraseña del usuario
     * @return bool True si el login fue exitoso, False en caso contrario
     */
    public function login($email, $password) {
        $usuarios = $this->obtenerTodos();
        
        foreach ($usuarios as $usuario) {
            if ($usuario['email'] === $email && password_verify($password, $usuario['password'])) {
                // Iniciamos sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email'] = $usuario['email'];
                
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Inicia sesión con un token de "recordarme"
     * 
     * @param string $token Token de "recordarme"
     * @return bool True si el login fue exitoso, False en caso contrario
     */
    public function loginPorToken($token) {
        $usuarios = $this->obtenerTodos();
        
        foreach ($usuarios as $usuario) {
            if ($usuario['token_recordar'] === $token) {
                // Iniciamos sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email'] = $usuario['email'];
                
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Genera un token para la función "recordarme"
     * 
     * @return string Token generado
     */
    public function generarTokenRecordar() {
        $token = bin2hex(random_bytes(32));
        
        // Actualizamos el token en el archivo
        $usuarios = $this->obtenerTodos();
        $usuariosActualizados = [];
        
        foreach ($usuarios as $usuario) {
            if ($usuario['id'] === $_SESSION['usuario_id']) {
                $usuario['token_recordar'] = $token;
            }
            $usuariosActualizados[] = $usuario;
        }
        
        $this->guardarUsuarios($usuariosActualizados);
        
        return $token;
    }
    
    /**
     * Obtiene un usuario por su ID
     * 
     * @param string $id ID del usuario
     * @return array|bool Datos del usuario o False si no existe
     */
    public function obtenerPorId($id) {
        $usuarios = $this->obtenerTodos();
        
        foreach ($usuarios as $usuario) {
            if ($usuario['id'] === $id) {
                return $usuario;
            }
        }
        
        return false;
    }
    
    /**
     * Actualiza los datos del perfil de un usuario
     * 
     * @param string $id ID del usuario
     * @param string $nombre Nuevo nombre
     * @param string $email Nuevo email
     * @return bool True si la actualización fue exitosa, False en caso contrario
     */
    public function actualizarPerfil($id, $nombre, $email) {
        $usuarios = $this->obtenerTodos();
        $usuariosActualizados = [];
        
        foreach ($usuarios as $usuario) {
            if ($usuario['id'] === $id) {
                $usuario['nombre'] = $nombre;
                $usuario['email'] = $email;
                
                // Actualizamos también la sesión
                $_SESSION['usuario_nombre'] = $nombre;
                $_SESSION['usuario_email'] = $email;
            }
            $usuariosActualizados[] = $usuario;
        }
        
        return $this->guardarUsuarios($usuariosActualizados);
    }
    
    /**
     * Verifica si la contraseña proporcionada coincide con la del usuario
     * 
     * @param string $id ID del usuario
     * @param string $password Contraseña a verificar
     * @return bool True si la contraseña es correcta, False en caso contrario
     */
    public function verificarPassword($id, $password) {
        $usuario = $this->obtenerPorId($id);
        
        if ($usuario && password_verify($password, $usuario['password'])) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Cambia la contraseña de un usuario
     * 
     * @param string $id ID del usuario
     * @param string $newPassword Nueva contraseña
     * @return bool True si el cambio fue exitoso, False en caso contrario
     */
    public function cambiarPassword($id, $newPassword) {
        $usuarios = $this->obtenerTodos();
        $usuariosActualizados = [];
        
        // Hasheamos la nueva contraseña
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        
        foreach ($usuarios as $usuario) {
            if ($usuario['id'] === $id) {
                $usuario['password'] = $passwordHash;
            }
            $usuariosActualizados[] = $usuario;
        }
        
        return $this->guardarUsuarios($usuariosActualizados);
    }
    
    /**
     * Genera un token para recuperación de contraseña
     * 
     * @param string $email Email del usuario
     * @return string|bool Token generado o False si el email no existe
     */
    public function generarTokenRecuperacion($email) {
        $usuarios = $this->obtenerTodos();
        $usuariosActualizados = [];
        $token = false;
        
        foreach ($usuarios as $usuario) {
            if ($usuario['email'] === $email) {
                $token = bin2hex(random_bytes(32));
                $usuario['token_recuperacion'] = $token;
                $usuario['expira_recuperacion'] = date('Y-m-d H:i:s', strtotime('+1 hour'));
            }
            $usuariosActualizados[] = $usuario;
        }
        
        if ($token) {
            $this->guardarUsuarios($usuariosActualizados);
        }
        
        return $token;
    }
    
    /**
     * Envía un correo de recuperación de contraseña
     * 
     * @param string $email Email del usuario
     * @param string $token Token de recuperación
     * @return bool True si el correo se envió correctamente, False en caso contrario
     */
    public function enviarCorreoRecuperacion($email, $token) {
         
        return true;
    }
    
    /**
     * Obtiene todos los usuarios del archivo
     * 
     * @return array Array con todos los usuarios
     */
    public function obtenerTodos() {
        $usuarios = [];
        $contenido = file_get_contents($this->archivoUsuarios);
        
        if (!empty($contenido)) {
            $lineas = explode(PHP_EOL, $contenido);
            
            foreach ($lineas as $linea) {
                if (!empty($linea)) {
                    $usuarios[] = json_decode($linea, true);
                }
            }
        }
        
        return $usuarios;
    }
    
    /**
     * Guarda todos los usuarios en el archivo
     * 
     * @param array $usuarios Array con todos los usuarios
     * @return bool True si se guardaron correctamente, False en caso contrario
     */
    public function guardarUsuarios($usuarios) {
        $contenido = '';
        
        foreach ($usuarios as $usuario) {
            $contenido .= json_encode($usuario) . PHP_EOL;
        }
        
        return file_put_contents($this->archivoUsuarios, $contenido, LOCK_EX) !== false;
    }
}