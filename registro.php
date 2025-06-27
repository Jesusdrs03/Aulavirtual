<?php
require_once 'config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y sanitizar datos
    $tipo_doc = $_POST['documentType'] ?? '';
    $num_doc = $_POST['documentNumber'] ?? '';
    $p1 = $_POST['p1'] ?? '';
    $rp1 = $_POST['respuesta1'] ?? '';
    $p2 = $_POST['p2'] ?? '';
    $rp2 = $_POST['respuesta2'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    // Validaciones básicas
    $errors = [];
    
    if (!in_array($tipo_doc, ['V', 'J', 'E'])) {
        $errors[] = "Tipo de documento no válido";
    }
    
    if (!preg_match('/^\d{6,12}$/', $num_doc)) {
        $errors[] = "Número de documento debe tener entre 6 y 12 dígitos";
    }
    
    if (empty($p1) || empty($rp1)) {
        $errors[] = "Debe completar la primera pregunta de seguridad";
    }
    
    if (empty($p2) || empty($rp2)) {
        $errors[] = "Debe completar la segunda pregunta de seguridad";
    }
    
    if (strlen($password) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres";
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = "Las contraseñas no coinciden";
    }
    
    // Si no hay errores procede con el registro
    if (empty($errors)) {
        try {
            // Verificar si el usuario ya existe
            $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE usuario = ?");
            $usuario = strtolower($tipo_doc . $num_doc); 
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $errors[] = "El usuario ya está registrado";
            } else {
                // Hash de la contraseña
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insertar en tabla usuarios
                $stmt = $conexion->prepare("INSERT INTO usuarios 
                    (usuario, password, correo, nivel, p1, rp1, p2, rp2, status) 
                    VALUES (?, ?, ?, 'estudiante', ?, ?, ?, ?, 'activo')");
                
                $stmt->bind_param("sssssss", 
                    $usuario,
                    $hashed_password,
                    $correo_temp,
                    $p1,
                    $rp1,
                    $p2,
                    $rp2
                );
                
                if ($stmt->execute()) {
                    $id_usuario = $conexion->insert_id;
                    
                    // Redirigir a login con mensaje de éxito
                    header("Location: login.php?registro=exito");
                    exit();
                } else {
                    $errors[] = "Error al registrar el usuario: " . $conexion->error;
                }
            }
        } catch (Exception $e) {
            $errors[] = "Error en el sistema: " . $e->getMessage();
        }
    }
} 
?> 

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Estudiante</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/registro.css">
</head>
<body>
    <div class="main-container">
        <form class="form-container" id="registroForm" method="POST" action="registro.php">
            <div class="title-container">
                <h1 class="title">Formulario del Estudiante</h1>
                <p class="subtitle">Complete el formulario para crear su cuenta</p>
            </div>
            
<!-- Grupo para documento -->
<div class="form-group document-container">
    <!-- Etiqueta del grupo -->
    <label for="documentType" class="document-label">Tipo de Documento</label>
    <!-- Contenedor de los campos relacionados con documento -->
    <div class="document-fields-group">
        <!-- Select para tipo de documento -->
        <select class="document-type-select" id="documentType">
            <option value="venezolano">V</option>
            <option value="juridico">J</option>
            <option value="extranjero">E</option>
        </select>
        
        <!-- Campo de número de documento con ícono -->
        <div class="document-input-container">
            <i class="fas fa-id-card document-icon"></i>
            <input type="text" class="document-number-input" 
                    id="documentNumber" 
                    placeholder="Número de Documento" 
                    required>
        </div>
    </div>
</div>
            
            <!-- Preguntas de seguridad -->
            <div class="security-questions-container">
                <div class="security-question-group">
                    <div class="form-group">
                        <label for="p1">Primera pregunta de seguridad</label>
                        <select class="preguntas-seguridad" id="p1">
                            <option value="" disabled selected>Seleccione una pregunta</option>
                            <option value="preg1">¿Cómo se llama tu mamá?</option>
                            <option value="preg2">¿Cómo se llama tu papá?</option>
                            <option value="preg3">¿En qué ciudad se conocieron tus padres?</option>
                            <option value="preg4">¿Cuál es el nombre de tu mascota?</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="respuesta1">Respuesta</label>
                        <input type="text" id="respuesta1" placeholder="Ingrese su respuesta" required>
                    </div>
                </div>

                <div class="security-question-group">
                    <div class="form-group">
                        <label for="p2">Segunda pregunta de seguridad</label>
                        <select class="preguntas-seguridad" id="p2">
                            <option value="" disabled selected>Seleccione una pregunta</option>
                            <option value="preg1">¿Cuál es tu pasatiempo favorito?</option>
                            <option value="preg2">¿Qué país te gustaría conocer?</option>
                            <option value="preg3">¿Cuál es tu postre favorito?</option>
                            <option value="preg4">¿Cuál fue tu primer vehículo?</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="respuesta2">Respuesta</label>
                        <input type="text" id="respuesta2" placeholder="Ingrese su respuesta" required>
                    </div>
                </div>
            </div>

            <!-- Contraseña -->
            <div class="password-fields-container">
                <div class="form-group password-group">
                    <label for="password">Contraseña</label>
                    <div class="password-container">
                        <input type="password" id="password" placeholder="Ingrese su contraseña" required>
                        
                    </div>
                </div>
                <div class="form-group password-group">
                    <label for="confirmPassword">Confirmar contraseña</label>
                    <div class="password-container">
                        <input type="password" id="confirmPassword" placeholder="Confirme su contraseña" required>
                        
                    </div>
                    <span class="error-message" id="passwordMatchError">Las contraseñas no coinciden</span>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <button type="submit">Registrarse</button>
            <div class="recordar">
                Ya estás registrado? <a href="login.php">Inicia Sesión</a>
            </div>
        </form>
    </div>
</body>
</html>