<?php
require_once __DIR__ . '/../config/config.php';

class Order {
    private $db;
    private $lastOrderId;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function createOrder($cartItems, $name, $email, $phone, $address, $payment) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO orders (name, email, phone, address, payment, total) 
                VALUES (:name, :email, :phone, :address, :payment, :total)
            ");
            $total = $this->calculateTotal($cartItems);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':payment', $payment);
            $stmt->bindParam(':total', $total);
            $stmt->execute();

            $this->lastOrderId = $this->db->lastInsertId();

            foreach ($cartItems as $productId => $quantity) {
                $productDetails = $this->getProductById($productId);
                if ($productDetails) {
                    $price = $productDetails['price'];

                    $stmt = $this->db->prepare("
                        INSERT INTO order_items (order_id, product_id, quantity, price) 
                        VALUES (:order_id, :product_id, :quantity, :price)
                    ");
                    $stmt->bindParam(':order_id', $this->lastOrderId);
                    $stmt->bindParam(':product_id', $productId);
                    $stmt->bindParam(':quantity', $quantity);
                    $stmt->bindParam(':price', $price);
                    $stmt->execute();
                } else {
                    throw new Exception("Product with ID $productId not found.");
                }
            }

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            echo "Failed to create order: " . $e->getMessage();
        }
    }

    public function calculateTotal($cartItems) {
        $total = 0;
        foreach ($cartItems as $productId => $quantity) {
            $productDetails = $this->getProductById($productId);
            if ($productDetails) {
                $total += $productDetails['price'] * $quantity;
            } else {
                throw new Exception("Product with ID $productId not found.");
            }
        }
        return $total;
    }

    public function getProductById($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLastOrderId() {
        return $this->lastOrderId;
    }

    public function getOrderDetails($orderId) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->bindParam(':id', $orderId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOrderItems($orderId) {
        $stmt = $this->db->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
