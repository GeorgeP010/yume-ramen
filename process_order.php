<?php
/**
 * Process Order Handler
 * Handles AJAX order submission
 */

header('Content-Type: application/json');

// Start session
session_start();

// Load configuration
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/Customer.php';

// Determine logged-in customer (if any)
$customerId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

// Validate required fields
if (empty($data['cart']) || empty($data['customer']) || empty($data['delivery_address'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Prepare delivery address string
    $deliveryAddress = sprintf(
        "%s\n%s %s\nTel: %s",
        $data['delivery_address']['street'],
        $data['delivery_address']['postal_code'],
        $data['delivery_address']['city'],
        $data['customer']['phone']
    );
    
    // Generate order number
    $orderNumber = 'YR-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
    
    // Insert order
    // Note: status enum is 'active', 'completed', 'cancelled'
    $sql = "INSERT INTO orders (customer_id, order_number, order_date, total_amount, status, delivery_address, payment_method, notes)
            VALUES (:customer_id, :order_number, NOW(), :total, 'active', :address, :payment, :notes)";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':customer_id' => $customerId ? $customerId : 0, // Use 0 for guest if null not allowed, or check schema
        ':order_number' => $orderNumber,
        ':total' => $data['total'],
        ':address' => $deliveryAddress,
        ':payment' => $data['payment_method'],
        ':notes' => $data['notes'] ?? ''
    ]);
    
    $orderId = $conn->lastInsertId();
    
    // Insert order items
    // Table order_items has: order_item_id, order_id, item_name, quantity, price, special_requests
    $itemSql = "INSERT INTO order_items (order_id, item_name, quantity, price, special_requests)
                VALUES (:order_id, :item_name, :quantity, :price, :special_requests)";
    $itemStmt = $conn->prepare($itemSql);
    
    foreach ($data['cart'] as $item) {
        // Maak customization string voor special_requests
        $customizationText = '';
        if (isset($item['customizations'])) {
            $customizations = $item['customizations'];
            $parts = [];
            
            if (isset($customizations['broth']) && $customizations['broth'] !== 'medium') {
                $parts[] = 'Broth: ' . ucfirst($customizations['broth']);
            }
            if (isset($customizations['noodles']) && $customizations['noodles'] !== 'soft') {
                $parts[] = 'Noodles: ' . ucfirst($customizations['noodles']);
            }
            if (isset($customizations['spice']) && $customizations['spice'] !== 'mild') {
                $parts[] = 'Spice: ' . ucfirst($customizations['spice']);
            }
            if (isset($customizations['flavor']) && $customizations['flavor'] !== 'cola') {
                $parts[] = 'Smaak: ' . ucfirst($customizations['flavor']);
            }
            if (isset($customizations['toppings']) && !empty($customizations['toppings'])) {
                $toppingNames = array_map(function($t) { return $t['name']; }, $customizations['toppings']);
                $parts[] = 'Extra: ' . implode(', ', $toppingNames);
            }
            if (isset($customizations['specialInstructions']) && !empty($customizations['specialInstructions'])) {
                $parts[] = 'Opmerking: ' . $customizations['specialInstructions'];
            }
            
            $customizationText = implode(' | ', $parts);
        }
        
        $itemStmt->execute([
            ':order_id' => $orderId,
            ':item_name' => $item['name'],
            ':quantity' => $item['quantity'],
            ':price' => $item['price'],
            ':special_requests' => $customizationText
        ]);
    }

    // Loyalty points: 1 punt per euro totaalbedrag
    if ($customerId !== null) {
        $pointsPerEuro = 1;
        $pointsEarned = (int) floor((float) $data['total'] * $pointsPerEuro);

        if ($pointsEarned > 0) {
            $customerModel = new Customer();
            $customerModel->addLoyaltyPoints($customerId, $pointsEarned);
        }
    }
    
    // Success response
    echo json_encode([
        'success' => true,
        'order_id' => $orderId,
        'message' => 'Order placed successfully'
    ]);
    
} catch (Exception $e) {
    error_log("Order processing error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
