<?php
require_once 'config/conexion.php';
session_start();

// Verificar si el usuario ya está logueado
if (isset($_SESSION['user_id'])) {
    header("Location: " . getDashboardByRole($_SESSION['user_role']));
    exit();
}

// Mensajes del sistema
$error = '';
$success = '';

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userRole = $_POST['userRole'] ?? 'student';
    $documentType = $_POST['documentType'] ?? 'V';
    $documentNumber = $_POST['usernameField'] ?? '';
    $username = $_POST['userField'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validar campos según el tipo de usuario
    if ($userRole === 'student') {
        if (empty($documentNumber) || !preg_match('/^\d{6,12}$/', $documentNumber)) {
            $error = "Número de documento inválido (6-12 dígitos)";
        }
    } else {
        if (empty($username) || !preg_match('/^[a-zA-Z0-9_]{4,20}$/', $username)) {
            $error = "Usuario inválido (4-20 caracteres alfanuméricos)";
        }
    }
    
    if (empty($password) || strlen($password) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres";
    }
    
    // Si no hay errores, intentar login
    if (empty($error)) {
        try {
            $userToFind = ($userRole === 'student') ? $documentType . $documentNumber : $username;
            
            $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE usuario = ? LIMIT 1");
            $stmt->bind_param("s", $userToFind);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                if (password_verify($password, $user['password'])) {
                    // Verificar si la cuenta está activa
                    if ($user['status'] !== 'activo') {
                        $error = "Tu cuenta está inactiva. Contacta al administrador.";
                    } else {
                        // Mapear roles del formulario a roles de la base de datos
                        $expectedDbRole = '';
                        switch($userRole) {
                            case 'student': $expectedDbRole = 'estudiante'; break;
                            case 'teacher': $expectedDbRole = 'docente'; break;
                            case 'admin': $expectedDbRole = 'admin'; break;
                        }
                        
                        // Verificar que el rol coincida
                        if ($user['nivel'] !== $expectedDbRole) {
                            $error = "No tienes permisos para acceder como este tipo de usuario";
                        } else {
                            // Iniciar sesión
                            $_SESSION['user_id'] = $user['id_usuario'];
                            $_SESSION['user_role'] = $user['nivel'];
                            $_SESSION['username'] = $user['usuario'];
                            $_SESSION['last_login'] = time();
                            
                            // Registrar el login (opcional)
                            registerLoginAttempt($conexion, $user['id_usuario'], true);
                            
                            // Redirigir al dashboard correspondiente
                            header("Location: " . getDashboardByRole($user['nivel']));
                            exit();
                        }
                    }
                } else {
                    $error = "Credenciales incorrectas";
                    registerLoginAttempt($conexion, $user['id_usuario'], false);
                }
            } else {
                $error = "Usuario no encontrado";
            }
        } catch (Exception $e) {
            error_log("Error en login: " . $e->getMessage());
            $error = "Error en el sistema. Por favor intenta más tarde.";
        }
    }
}

// Mensaje de registro exitoso
if (isset($_GET['registro']) && $_GET['registro'] === 'exito') {
    $success = "¡Registro exitoso! Ahora puedes iniciar sesión.";
}

// Función para obtener dashboard por rol
function getDashboardByRole($role) {
    switch ($role) {
        case 'estudiante': return 'estudiante/inicio.php';
        case 'docente': return 'docente/inicio.php';
        case 'admin': return 'admin/inicio.php';
        default: return 'index.php';
    }
}

// Función para registrar intentos de login (opcional)
function registerLoginAttempt($conexion, $userId, $success) {
    $stmt = $conexion->prepare("INSERT INTO login_attempts (user_id, success, attempt_time, ip_address)
    VALUES (?, ?, NOW(), ?)");
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt->bind_param("iis", $userId, $success, $ip);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="login-container" id="loginSection">
        <div class="logo">
            <img src="img/logo-2.png" alt="Logo del Sistema">
        </div>
        
        <div class="login-form">
            <div class="title">Iniciar Sesión</div>
            
            <?php if (!empty($success)): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form id="loginForm" method="POST" action="login.php">
            
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                
                <select class="usuarios" id="userRole" name="userRole">
                    <option value="student" <?php echo ($_POST['userRole'] ?? 'student') === 'student' ? 'selected' : ''; ?>>Estudiante</option>
                    <option value="teacher" <?php echo ($_POST['userRole'] ?? '') === 'teacher' ? 'selected' : ''; ?>>Docente</option>
                    <option value="admin" <?php echo ($_POST['userRole'] ?? '') === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                </select>
                
                <div class="documento-group" id="grupoDocumento">
                    <select class="tipo-documento" id="documentType" name="documentType">
                        <option value="V" <?php echo ($_POST['documentType'] ?? 'V') === 'V' ? 'selected' : ''; ?>>V</option>
                        <option value="J" <?php echo ($_POST['documentType'] ?? '') === 'J' ? 'selected' : ''; ?>>J</option>
                        <option value="E" <?php echo ($_POST['documentType'] ?? '') === 'E' ? 'selected' : ''; ?>>E</option>
                    </select>
                    <div class="input-cedula">
                        <i class="fas fa-id-card" id="idIcon"></i>
                        <input type="text" id="usernameField" name="usernameField" 
                                placeholder="Número Documento" 
                                value="<?php echo htmlspecialchars($_POST['usernameField'] ?? ''); ?>"
                                pattern="[0-9]{6,12}" 
                                title="Entre 6 y 12 dígitos" required>
                    </div>
                </div>
                
                <div class="input-usuario" id="grupoUsuario">
                    <i class="fas fa-user" id="userIcon"></i>
                    <input type="text" id="userField" name="userField" 
                            placeholder="Usuario"
                            value="<?php echo htmlspecialchars($_POST['userField'] ?? ''); ?>"
                            pattern="[a-zA-Z0-9_]{4,20}" 
                            title="4-20 caracteres alfanuméricos">
                </div>
                
                <div class="input-password">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="passwordField"
                            placeholder="Contraseña" 
                            minlength="8" 
                            title="Mínimo 8 caracteres" required>
                </div>
                
                <div class="recordar">
                    <a href="recuperarcontra.php">¿Olvidaste tu contraseña?</a>
                </div>
                
                <button type="submit">Ingresar</button>
                
                <div class="register-link">
                    ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
                </div>
            </form>
        </div>
    </div>

    <script src="javascript/script.js"></script>
</body>
</html>

<?php
// Función para generar token CSRF (protección contra ataques)
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
?>