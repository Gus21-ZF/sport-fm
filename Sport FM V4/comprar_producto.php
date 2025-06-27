<?php
session_start();
require_once 'conexion.php';

$usuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : '';
if (!$usuario || !isset($_POST['id']) || !isset($_POST['cantidad'])) {
    echo json_encode(['success' => false, 'msg' => 'Datos incompletos']);
    exit;
}

$id = intval($_POST['id']);
$cantidad = intval($_POST['cantidad']);

// Verifica stock
$res = $conn->query("SELECT cantidad FROM productos WHERE id = $id");
$row = $res->fetch_assoc();
$disponible = $row ? intval($row['cantidad']) : 0;

if ($cantidad > 0 && $disponible >= $cantidad) {
    // Descontar stock
    $conn->query("UPDATE productos SET cantidad = cantidad - $cantidad WHERE id = $id");
    // Registrar compra
    $stmt = $conn->prepare("INSERT INTO compras (usuario, producto_id, cantidad) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $usuario, $id, $cantidad);
    $stmt->execute();

    $restante = $disponible - $cantidad;
    echo json_encode(['success' => true, 'restante' => $restante]);
    exit;
}
echo json_encode(['success' => false, 'msg' => 'Stock insuficiente']);