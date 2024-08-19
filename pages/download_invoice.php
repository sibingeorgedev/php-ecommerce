<?php
require_once '../classes/Order.php';
require_once '../classes/Product.php';
require_once '../classes/Cart.php';
require_once '../includes/fpdf186/fpdf.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$orderId = $_GET['order_id'] ?? null;

if (!$orderId) {
    die("Invalid order ID.");
}

$order = new Order();
$product = new Product();
$cart = new Cart();

$orderDetails = $order->getOrderDetails($orderId);
$orderItems = $order->getOrderItems($orderId);

if (!$orderDetails) {
    die("Order not found.");
}

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

$pdf->Cell(0, 10, 'Invoice', 0, 1, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Order ID: ' . htmlspecialchars($orderDetails['id']), 0, 1);
$pdf->Cell(0, 10, 'Name: ' . htmlspecialchars($orderDetails['name']), 0, 1);
$pdf->Cell(0, 10, 'Email: ' . htmlspecialchars($orderDetails['email']), 0, 1);
$pdf->Cell(0, 10, 'Phone: ' . htmlspecialchars($orderDetails['phone']), 0, 1);
$pdf->Cell(0, 10, 'Address: ' . htmlspecialchars($orderDetails['address']), 0, 1);
$pdf->Cell(0, 10, 'Payment Method: ' . htmlspecialchars($orderDetails['payment']), 0, 1);
$pdf->Cell(0, 10, 'Date: ' . htmlspecialchars($orderDetails['created_at']), 0, 1);

$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Items', 0, 1);
$pdf->SetFont('Arial', '', 12);

foreach ($orderItems as $item) {
    $productDetails = $product->getProductById($item['product_id']);
    $productName = htmlspecialchars($productDetails['name']);
    $pdf->Cell(0, 10, $productName . ' - $' . number_format($item['price'], 2) . ' x ' . htmlspecialchars($item['quantity']) . ' = $' . number_format($item['price'] * $item['quantity'], 2), 0, 1);
}

$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Total: $' . number_format($orderDetails['total'], 2), 0, 1);

$pdf->Output('D', 'invoice_' . htmlspecialchars($orderId) . '.pdf');

$cart->clearCart();
?>
