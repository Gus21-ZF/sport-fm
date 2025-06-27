<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'conexion.php';

$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$correo = $_POST['correo'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nombre, apellidos, correo, password) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nombre, $apellidos, $correo, $password);

if ($stmt->execute()) {
    echo "
    <script>
        localStorage.setItem('usuario', '".addslashes($nombre)."');
        window.location.href = 'index.php';
    </script>
    ";
    exit();
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>