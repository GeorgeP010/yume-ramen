<?php
/**
 * Customer Model
 * Beheer van klanten/gebruikers
 */

require_once CONFIG_PATH . '/Database.php';

class Customer {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Registreer nieuwe klant
     */
    public function register($data) {
        $sql = "INSERT INTO customers (name, email, password_hash, member_since, created_at) 
                VALUES (:name, :email, :password_hash, CURDATE(), NOW())";
        
        $params = [
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT)
        ];
        
        return $this->db->insert($sql, $params);
    }
    
    /**
     * Login verificatie
     */
    public function login($email, $password) {
        $sql = "SELECT * FROM customers WHERE email = :email LIMIT 1";
        $customer = $this->db->fetchOne($sql, [':email' => $email]);
        
        if ($customer && password_verify($password, $customer['password_hash'])) {
            // Update last login
            $this->updateLastLogin($customer['customer_id']);
            return $customer;
        }
        
        return false;
    }
    
    /**
     * Get customer by ID
     */
    public function getById($id) {
        $sql = "SELECT customer_id, name, email, avatar_url, profile_icon, member_since, created_at,
                       street, postal_code, city, country, loyalty_points
                FROM customers WHERE customer_id = :id LIMIT 1";
        return $this->db->fetchOne($sql, [':id' => $id]);
    }
    
    /**
     * Get customer by email
     */
    public function getByEmail($email) {
        $sql = "SELECT * FROM customers WHERE email = :email LIMIT 1";
        return $this->db->fetchOne($sql, [':email' => $email]);
    }
    
    /**
     * Update last login timestamp
     */
    private function updateLastLogin($customerId) {
        $sql = "UPDATE customers SET last_login = NOW() WHERE customer_id = :id";
        return $this->db->query($sql, [':id' => $customerId]);
    }

    /**
     * Add loyalty points to a customer
     */
    public function addLoyaltyPoints($customerId, $points) {
        $sql = "UPDATE customers 
                SET loyalty_points = loyalty_points + :points 
                WHERE customer_id = :id";

        return $this->db->query($sql, [
            ':points' => (int) $points,
            ':id' => $customerId
        ]);
    }
    
    /**
     * Get customer orders
     */
    public function getOrders($customerId) {
        $sql = "SELECT o.*, 
                       COUNT(oi.order_item_id) as item_count
                FROM orders o
                LEFT JOIN order_items oi ON o.order_id = oi.order_id
                WHERE o.customer_id = :customer_id
                GROUP BY o.order_id
                ORDER BY o.created_at DESC";
        
        return $this->db->fetchAll($sql, [':customer_id' => $customerId]);
    }
    
    /**
     * Update profile
     */
    public function updateProfile($customerId, $data) {
        $sql = "UPDATE customers 
                SET name = :name
                WHERE customer_id = :id";
        
        $params = [
            ':name' => $data['name'],
            ':id' => $customerId
        ];
        
        return $this->db->query($sql, $params);
    }
    
    /**
     * Add or update customer address
     * Note: This uses a simple approach - storing address in customers table
     * For production, use the customer_addresses table
     */
    public function updateAddress($customerId, $address, $zipcode, $city) {
        // Since the actual customers table doesn't have address fields,
        // we'll need to add them or use the customer_addresses table
        // For now, return true as placeholder
        return true;
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email) {
        $sql = "SELECT COUNT(*) as count FROM customers WHERE email = :email";
        $result = $this->db->fetchOne($sql, [':email' => $email]);
        return $result['count'] > 0;
    }
}
