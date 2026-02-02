<?php
// ====================================
// CONFIGURACIÓN INICIAL (PHPMailer)
// ====================================
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer-master/src/SMTP.php';
require __DIR__ . '/../PHPMailer-master/src/Exception.php';

// CREDENCIALES SMTP
$smtpHost = 'mail.zerotoplan.com';
$smtpUser = 'no-reply@zerotoplan.com';
$smtpPass = '5)}dQ&%jli4j!8bc';
$smtpPort = 465;

// ====================================
// CONEXIÓN A LA BASE DE DATOS
// ====================================
$host = 'localhost';
$dbname = 'zero9111_landing';
$username = 'zero9111_jesusrey';
$password = 'o+[ZdH33O£RhD2/';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Database connection error: " . $e->getMessage());
}

// ====================================
// PROCESAMIENTO DEL FORMULARIO
// ====================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Recoger datos
    $firstName       = isset($_POST["firstName"]) ? trim($_POST["firstName"]) : '';
    $lastName        = isset($_POST["lastName"]) ? trim($_POST["lastName"]) : '';
    $phone           = isset($_POST["phone"]) ? trim($_POST["phone"]) : '';
    $email           = isset($_POST["email"]) ? trim($_POST["email"]) : '';
    $preferredOption = isset($_POST["preferredOption"]) ? trim($_POST["preferredOption"]) : '';
    $moveInDate      = isset($_POST["moveInDate"]) ? trim($_POST["moveInDate"]) : '';
    $occupancy       = isset($_POST["occupancy"]) ? trim($_POST["occupancy"]) : '';

    $fullName = $firstName . ' ' . $lastName;

    // Validar campos obligatorios
    if (empty($firstName) || empty($lastName) || empty($phone) || empty($email) || empty($preferredOption) || empty($moveInDate) || empty($occupancy)) {
        echo "<script>alert('All fields are required. / Todos los campos son obligatorios.'); window.history.back();</script>";
        exit;
    }

    // Validar email real
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Please enter a valid email address.'); window.history.back();</script>";
        exit;
    }

    try {
        // Insertar en base de datos (Tabla greenline_form)
        $sql = "INSERT INTO greenline_form (first_name, last_name, phone, email, preferred_option, move_in_date, occupancy) 
                VALUES (:first_name, :last_name, :phone, :email, :preferred_option, :move_in_date, :occupancy)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":first_name"       => $firstName,
            ":last_name"        => $lastName,
            ":phone"            => $phone,
            ":email"            => $email,
            ":preferred_option" => $preferredOption,
            ":move_in_date"     => $moveInDate,
            ":occupancy"        => $occupancy
        ]);

        // ====================================
        // CONFIGURAR CORREO (PHPMailer)
        // ====================================
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = $smtpHost;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUser;
        $mail->Password   = $smtpPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = $smtpPort;
        $mail->CharSet    = 'UTF-8';

        // ====================================
        // ENVÍO AL CLIENTE
        // ====================================
        $mail->setFrom($smtpUser, 'Greenline Team');
        $mail->addAddress($email, $fullName);
        $mail->Subject = "Application Received - Greenline Property Management";
        $mail->isHTML(true);

        $mail->Body = "
        <html>
        <body style='font-family: Arial, sans-serif; color:#333;'>
            <h2>Dear {$fullName},</h2>
            <p>Thank you for submitting your application to <strong>Greenline Property Management</strong>.</p>
            <p>We have received your details and our team will review your application shortly. We will contact you at <strong>{$phone}</strong> or via email if we need further information.</p>
            
            <p><strong>Your Submission Details:</strong></p>
            <ul>
                <li><strong>Preferred Option:</strong> {$preferredOption}</li>
                <li><strong>Move-in Date:</strong> {$moveInDate}</li>
                <li><strong>Occupancy:</strong> {$occupancy}</li>
            </ul>

            <p>We look forward to having you with us.</p>

            <br><br>
            <p>Sincerely,</p>
            <strong>Greenline Team</strong><br>
            <a href='https://greenline.com' style='color:#2d5a27;text-decoration:none;'>www.greenline.com</a>
        </body>
        </html>
        ";

        $mail->send();

        // ====================================
        // ENVÍO INTERNO AL EQUIPO
        // ====================================
        $mail->clearAddresses();
        $mail->addAddress("rooms@atexgrp.com");
        $mail->Subject = "📩 New Application Received - {$fullName}";
        $mail->isHTML(true);
        $mail->Body = "
        <html>
        <body style='font-family:Arial,sans-serif;color:#333;'>
          <h3>New Application Received</h3>
          <p><strong>Name:</strong> {$fullName}</p>
          <p><strong>Email:</strong> {$email}</p>
          <p><strong>Phone:</strong> {$phone}</p>
          <p><strong>Preferred Option:</strong> {$preferredOption}</p>
          <p><strong>Move-in Date:</strong> {$moveInDate}</p>
          <p><strong>Occupancy:</strong> {$occupancy}</p>
          <p><em>Submitted on " . date('Y-m-d H:i:s') . "</em><br></p>
          <h5>GLINE - #INTAKE</h5>
        </body>
        </html>";

        $mail->send();

        // ====================================
        // CONFIRMACIÓN VISUAL AL USUARIO
        // ====================================
        echo "<script>
            alert('✅ Thank you! Your application has been received and a confirmation email has been sent.');
            window.location.href='index.html';
        </script>";

    } catch (Exception $e) {
        // Error de Mail - Loguear o mostrar (en desarrollo mejor mostrar)
        echo "<script>
            alert('⚠️ Mail error: " . addslashes($e->getMessage()) . "');
            window.location.href='index.html';
        </script>";
    } catch (PDOException $e) {
        echo "<script>
            alert('⚠️ Database error: " . addslashes($e->getMessage()) . "');
            window.history.back();
        </script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.history.back();</script>";
}
?>

