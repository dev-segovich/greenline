<?php
// ====================================
// CONFIGURACIÓN INICIAL (PHPMailer)
// ====================================
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-master/src/SMTP.php';
require __DIR__ . '/PHPMailer-master/src/Exception.php';

// CREDENCIALES SMTP
$smtpHost = 'mail.zerotoplan.com';
$smtpUser = 'no-reply@zerotoplan.com';
$smtpPass = '5)}dQ&%jli4j!8bc';
$smtpPort = 465;

// ====================================
// CONEXIÓN A LA BASE DE DATOS (Variables)
// ====================================
$host = 'localhost';
$dbname = 'zero9111_landing';
$username = 'zero9111_jesusrey';
$password = 'o+[ZdH33O£RhD2/';

// ====================================
// PROCESAMIENTO DEL FORMULARIO
// ====================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Recoger datos
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : '';

    // Validar email real
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Please enter a valid email address.'); window.history.back();</script>";
        exit;
    }

    // Variables de control
    $dbSuccess = false;
    $mailSuccess = false;
    $errorLog = [];

    // ====================================
    // PROCESO 1: BASE DE DATOS
    // ====================================
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("INSERT INTO greenline_contact (email) VALUES (:email)");
        $stmt->execute([
            ":email" => $email
        ]);
        $dbSuccess = true;
    } catch (Exception $e) {
        $errorLog[] = "Database error: " . $e->getMessage();
    }

    // ====================================
    // PROCESO 2: ENVÍO DE CORREOS
    // ====================================
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = $smtpHost;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUser;
        $mail->Password   = $smtpPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = $smtpPort;
        $mail->CharSet    = 'UTF-8';

        // ENVÍO INTERNO AL EQUIPO
        $mail->setFrom($smtpUser, 'Greenline Team');
        $mail->addAddress("info@zerotoplan.com");
        $mail->addAddress("rooms@atexgrp.com");
        $mail->Subject = "📩 New Contact Lead - {$email}";
        $mail->isHTML(true);
        $mail->Body = "
        <html>
        <body style='font-family:Arial,sans-serif;color:#333;'>
          <h3>New Lead Received</h3>
          <p>A new user has submitted their email through the landing page form.</p>
          <p><strong>Email:</strong> {$email}</p>
          <p><em>Submitted on " . date('Y-m-d H:i:s') . "</em><br></p>
          <h5>GLINE - #CONTACT</h5>
        </body>
        </html>";

        $mail->send();
        $mailSuccess = true;
    } catch (Exception $e) {
        $errorLog[] = "Mail error: " . $e->getMessage();
    }

    // ====================================
    // RESPUESTA AL USUARIO
    // ====================================
    if ($dbSuccess && $mailSuccess) {
        echo "<script>
            alert('✅ Thank you! Your submission has been received.');
            window.location.href='index.html';
        </script>";
    } elseif (!$dbSuccess && $mailSuccess) {
        echo "<script>
            alert('✅ Thank you! Your email was sent successfully, but there was a recording issue. We will contact you anyway.');
            window.location.href='index.html';
        </script>";
    } elseif ($dbSuccess && !$mailSuccess) {
        echo "<script>
            alert('✅ Thank you! Your submission was saved, but we couldn\'t send the confirmation email at this moment.');
            window.location.href='index.html';
        </script>";
    } else {
        $errors = addslashes(implode("\\n", $errorLog));
        echo "<script>
            alert('⚠️ Something went wrong:\\n{$errors}');
            window.history.back();
        </script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.history.back();</script>";
}
?>

