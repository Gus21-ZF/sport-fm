<?php
session_start();
require_once 'conexion.php';

$usuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : '';
if (!$usuario || empty($_SESSION['carrito'])) {
    echo json_encode(['success' => false, 'msg' => 'Carrito vacío o usuario no autenticado']);
    exit;
}

$carrito = $_SESSION['carrito'];
foreach ($carrito as $id => $cantidad) {
    // Descontar stock
    $conn->query("UPDATE productos SET cantidad = cantidad - $cantidad WHERE id = $id AND cantidad >= $cantidad");
    // Registrar compra
    $stmt = $conn->prepare("INSERT INTO compras (usuario, producto_id, cantidad) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $usuario, $id, $cantidad);
    $stmt->execute();
}
unset($_SESSION['carrito']);
echo json_encode(['success' => true]);