<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['nombre']) || !isset($_POST['password'])) {
    echo json_encode(['success' => false]);
    exit;
}

$usuario = $_SESSION['nombre'];
$password = $_POST['password'];

// Busca el usuario y compara la contraseña (ajusta el nombre de tu tabla y campo)
$stmt = $conn->prepare("SELECT password FROM usuarios WHERE nombre = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$stmt->bind_result($hash);
if ($stmt->fetch()) {
    // Si usas password_hash en tu base de datos:
    if (password_verify($password, $hash)) {
        echo json_encode(['success' => true]);
        exit;
    }
}
echo json_encode(['success' => false]);
?>