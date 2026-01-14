<?php
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
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : '';

    // Validar email real
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Please enter a valid email address.'); window.history.back();</script>";
        exit;
    }

    try {
        // Insertar en base de datos (Tabla greenline_contact)
        $stmt = $pdo->prepare("INSERT INTO greenline_contact (email) VALUES (:email)");
        
        $stmt->execute([
            ":email" => $email
        ]);

        // ====================================
        // CONFIRMACIÓN VISUAL AL USUARIO
        // ====================================
        echo "<script>
            alert('✅ Thank you! Your submission has been received.');
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
