<?php
session_start();
require_once 'conexion.php';
if (!isset($_SESSION['nombre'])) {
    echo json_encode(['success' => false]);
    exit;
}
$usuario = $_SESSION['nombre'];
$conn->query("DELETE FROM carrito WHERE usuario = '$usuario'");
echo json_encode(['success' => true]);