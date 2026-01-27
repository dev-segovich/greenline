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
    $firstName       = isset($_POST["firstName"]) ? trim($_POST["firstName"]) : '';
    $lastName        = isset($_POST["lastName"]) ? trim($_POST["lastName"]) : '';
    $phone           = isset($_POST["phone"]) ? trim($_POST["phone"]) : '';
    $email           = isset($_POST["email"]) ? trim($_POST["email"]) : '';
    $preferredOption = isset($_POST["preferredOption"]) ? trim($_POST["preferredOption"]) : '';
    $moveInDate      = isset($_POST["moveInDate"]) ? trim($_POST["moveInDate"]) : '';
    $occupancy       = isset($_POST["occupancy"]) ? trim($_POST["occupancy"]) : '';

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
        // CONFIRMACIÓN VISUAL AL USUARIO
        // ====================================
        echo "<script>
            alert('✅ Thank you! Your application has been received. / ¡Gracias! Tu solicitud ha sido recibida.');
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
