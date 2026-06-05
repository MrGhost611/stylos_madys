<?php
/**
 * contact.php - Formulario de contacto para Stylos Madys
 * Guarda en base de datos y envía email
 */

// ============================================
// 1. CONFIGURACIÓN DE BASE DE DATOS
// ============================================
$servername = "127.0.0.1";  // o "localhost"
$database = "stylos_madys";  // NOMBRE DE TU BASE DE DATOS
$username = "root";          // Usuario de MySQL
$password = "123ss";              // Tu contraseña (si es 123ss, cámbiala)

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("❌ Error de conexión a la BD: " . $conn->connect_error);
}

// ============================================
// 2. RECIBIR DATOS DEL FORMULARIO
// ============================================
$nombre = $_POST['name'];
$email = $_POST['email'];
$telefono = isset($_POST['phone']) ? $_POST['phone'] : '';
$mensaje = $_POST['message'];
$asunto = $_POST['subject'];
$fecha = date('Y-m-d H:i:s');

// ============================================
// 3. GUARDAR EN BASE DE DATOS
// ============================================
$sql = "INSERT INTO contactos (nombre, email, telefono, asunto, mensaje, fecha) 
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $nombre, $email, $telefono, $asunto, $mensaje, $fecha);

if ($stmt->execute()) {
    $resultado_bd = "✅ Mensaje guardado correctamente en la base de datos";
    $bd_exito = true;
} else {
    $resultado_bd = "❌ Error al guardar: " . $stmt->error;
    $bd_exito = false;
}
$stmt->close();

// ============================================
// 4. CONFIGURACIÓN Y ENVÍO DE EMAIL
// ============================================

// INCLUIR LA LIBRERÍA DE EMAIL (el archivo enorme que ya tienes)
if (file_exists('./assets/vendor/php-email-form/php-email-form.php')) {
    include('./assets/vendor/php-email-form/php-email-form.php');
    
    // CONFIGURACIÓN DEL EMAIL
    $receiving_email_address = 'tucorreo@gmail.com'; // ¡CAMBIA POR TU EMAIL REAL!
    
    $contact = new PHP_Email_Form;
    $contact->ajax = true;
    $contact->to = $receiving_email_address;
    $contact->from_name = $nombre;
    $contact->from_email = $email;
    $contact->subject = $asunto;
    
    // Agregar mensajes al email
    $contact->add_message($nombre, 'Nombre');
    $contact->add_message($email, 'Email');
    if ($telefono) {
        $contact->add_message($telefono, 'Teléfono');
    }
    $contact->add_message($mensaje, 'Mensaje', 10);
    
    // CONFIGURACIÓN SMTP (para Gmail - OPCIONAL)
    // Si quieres usar Gmail, DESCOMENTA estas líneas y completa tus datos:
    /*
    $contact->smtp = array(
        'host' => 'smtp.gmail.com',
        'username' => 'tucorreo@gmail.com',
        'password' => 'tucontraseñadeaplicacion',
        'port' => '587',
        'encryption' => 'tls'
    );
    */
    
    // Enviar email
    $email_resultado = $contact->send();
    
    if ($email_resultado == 'OK') {
        $resultado_email = "✅ Email enviado correctamente";
        $email_exito = true;
    } else {
        $resultado_email = "❌ Error al enviar email: " . $email_resultado;
        $email_exito = false;
    }
} else {
    $resultado_email = "⚠️ Librería de email no encontrada (el email no se envió)";
    $email_exito = false;
}

// ============================================
// 5. CERRAR CONEXIÓN
// ============================================
$conn->close();

// ============================================
// 6. MOSTRAR RESULTADO
// ============================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stylos Madys - Contacto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .resultado {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .exito {
            color: green;
            border-left: 4px solid green;
            background: #e8f5e9;
            padding: 15px;
            margin: 10px 0;
        }
        .error {
            color: red;
            border-left: 4px solid red;
            background: #ffebee;
            padding: 15px;
            margin: 10px 0;
        }
        .boton {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #8B4513;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .boton:hover {
            background: #6B3410;
        }
    </style>
</head>
<body>
    <div class="resultado">
        <h2>Stylos Madys</h2>
        
        <?php if ($bd_exito): ?>
            <div class="exito">
                <?php echo $resultado_bd; ?>
            </div>
        <?php else: ?>
            <div class="error">
                <?php echo $resultado_bd; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($email_exito): ?>
            <div class="exito">
                <?php echo $resultado_email; ?>
            </div>
        <?php else: ?>
            <div class="error">
                <?php echo $resultado_email; ?>
            </div>
        <?php endif; ?>
        
        <a href="index.html" class="boton">← Volver al inicio</a>
    </div>
</body>
</html>