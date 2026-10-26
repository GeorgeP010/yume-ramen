<?php
/**
 * Order Model
 * Beheer van bestellingen
 */

require_once CONFIG_PATH . '/Database.php';

class Order {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create nieuwe bestelling
     */
    public function create($orderData) {
        $sql = "INSERT INTO orders (
                    customer_id, order_number, order_type, order_status,
                    subtotal, tax_amount, delivery_fee, discount_amount, total_amount,
                    payment_method, payment_status, special_instructions, created_at
                ) VALUES (
                    :customer_id, :order_number, :order_type, :order_status,
                    :subtotal, :tax_amount, :delivery_fee, :discount_amount, :total_amount,
                    :payment_method, :payment_status, :special_instructions, NOW()
                )";
        
        return $this->db->insert($sql, $orderData);
    }
    
    /**
     * Add item to order
     */
    public function addItem($orderId, $itemData) {
        $sql = "INSERT INTO order_items (order_id, item_id, quantity, unit_price, subtotal, special_requests)
                VALUES (:order_id, :item_id, :quantity, :unit_price, :subtotal, :special_requests)";
        
        return $this->db->insert($sql, $itemData);
    }
    
    /**
     * Get order by ID
     */
    public function getById($orderId) {
        $sql = "SELECT * FROM orders WHERE order_id = :id LIMIT 1";
        return $this->db->fetchOne($sql, [':id' => $orderId]);
    }
    
    /**
     * Get orders by customer
     */
    public function getByCustomer($customerId, $limit = null) {
        $sql = "SELECT o.*, 
                       COUNT(oi.order_item_id) as item_count
                FROM orders o
                LEFT JOIN order_items oi ON o.order_id = oi.order_id
                WHERE o.customer_id = :customer_id
                GROUP BY o.order_id
                ORDER BY o.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        
        return $this->db->fetchAll($sql, [':customer_id' => $customerId]);
    }
    
    /**
     * Get order items
     */
    public function getOrderItems($orderId) {
        // Gebruik de eenvoudige order_items structuur die door process_order.php wordt gevuld:
        // order_item_id, order_id, item_name, quantity, price
        $sql = "SELECT 
                    order_item_id,
                    order_id,
                    item_name AS name,
                    quantity,
                    price AS unit_price,
                    (quantity * price) AS subtotal
                FROM order_items
                WHERE order_id = :order_id";

        return $this->db->fetchAll($sql, [':order_id' => $orderId]);
    }
    
    /**
     * Update order status
     */
    public function updateStatus($orderId, $status) {
        $sql = "UPDATE orders SET order_status = :status, updated_at = NOW() 
                WHERE order_id = :id";
        
        return $this->db->query($sql, [
            ':status' => $status,
            ':id' => $orderId
        ]);
    }
    
    /**
     * Generate unique order number
     */
    public function generateOrderNumber() {
        return 'YR-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
    
    /**
     * Get most ordered dish for a customer
     */
    public function getMostOrderedDish($customerId) {
        $sql = "SELECT 
                    oi.item_name,
                    COUNT(*) as order_count,
                    SUM(oi.quantity) as total_quantity
                FROM order_items oi
                INNER JOIN orders o ON oi.order_id = o.order_id
                WHERE o.customer_id = :customer_id
                GROUP BY oi.item_name
                ORDER BY total_quantity DESC, order_count DESC
                LIMIT 1";
        
        return $this->db->fetchOne($sql, [':customer_id' => $customerId]);
    }
}
