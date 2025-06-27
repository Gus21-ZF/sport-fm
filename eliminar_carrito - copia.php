<?php
session_start();
require_once 'conexion.php';
if (!isset($_SESSION['nombre'])) {
    echo json_encode(['success' => false]);
    exit;
}
$usuario = $_SESSION['nombre'];
if (isset($_POST['producto_id'])) {
    $id = intval($_POST['producto_id']);
    $conn->query("DELETE FROM carrito WHERE usuario = '$usuario' AND producto_id = $id");
    echo json_encode(['success' => true]);
    exit;
}
echo json_encode(['success' => false]);