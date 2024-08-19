<?php
include '../includes/header.php';
require_once '../classes/Order.php';
require_once '../classes/Product.php';

$orderId = $_GET['order_id'] ?? null;

if (!$orderId) {
    echo "Invalid order ID.";
    exit();
}

$order = new Order();
$product = new Product();
$orderDetails = $order->getOrderDetails($orderId);
$orderItems = $order->getOrderItems($orderId);

if (!$orderDetails) {
    echo "Order not found.";
    exit();
}
?>

<div class="container">
    <h1>Invoice</h1>
    <p><strong>Order ID:</strong> <?= htmlspecialchars($orderDetails['id']) ?></p>
    <p><strong>Name:</strong> <?= htmlspecialchars($orderDetails['name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($orderDetails['email']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($orderDetails['phone']) ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($orderDetails['address']) ?></p>
    <p><strong>Payment Method:</strong> <?= htmlspecialchars($orderDetails['payment']) ?></p>
    <p><strong>Date:</strong> <?= htmlspecialchars($orderDetails['created_at']) ?></p>
    
    <h2>Items</h2>
    <ul>
        <?php foreach ($orderItems as $item) : 
            $productDetails = $product->getProductById($item['product_id']);
            $productName = htmlspecialchars($productDetails['name']);
        ?>
            <li>
                <?= $productName ?> - $<?= number_format($item['price'], 2) ?> x <?= htmlspecialchars($item['quantity']) ?> = $<?= number_format($item['price'] * $item['quantity'], 2) ?>
            </li>
        <?php endforeach; ?>
    </ul>
    
    <h3>Total: $<?= number_format($orderDetails['total'], 2) ?></h3>
    
    <a href="download_invoice.php?order_id=<?= htmlspecialchars($orderId) ?>" class="btn">Download PDF</a>
</div>

<?php include '../includes/footer.php'; ?>
