<?php
include '../includes/header.php';
require_once '../classes/Cart.php';
require_once '../classes/Order.php';

$cart = new Cart();
$cartItems = $cart->getCartItems();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $payment = $_POST['payment'];

    $order = new Order();
    $order->createOrder($cartItems, $name, $email, $phone, $address, $payment);
    $orderId = $order->getLastOrderId();

    echo "Last Order ID: " . htmlspecialchars($orderId) . "<br>";

    if ($orderId) {
        header("Location: invoice.php?order_id=" . urlencode($orderId));
        exit();
    } else {
        echo "Failed to retrieve order ID.";
    }
}
?>

<div class="container">
    <h1>Checkout</h1>
    <form action="checkout.php" method="POST" class="checkout-form">
        <p>Enter your details and confirm the order:</p>
        <input type="text" name="name" placeholder="Your Name" required class="form-control">
        <input type="text" name="email" placeholder="Your Email" required class="form-control">
        <input type="text" name="phone" placeholder="Your Phone" required class="form-control">
        <input type="text" name="address" placeholder="Your Address" required class="form-control">
        
        <label for="payment">Payment Method:</label>
        <select name="payment" id="payment" required class="form-control">
            <option value="">Select Payment Method</option>
            <option value="Credit">Credit</option>
            <option value="Debit">Debit</option>
            <option value="Apple Pay">Apple Pay</option>
        </select>
        
        <button type="submit" class="btn">Place Order</button>
    </form>
</div>


<?php include '../includes/footer.php'; ?>
