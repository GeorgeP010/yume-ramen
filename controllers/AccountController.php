<?php
/**
 * Account Controller
 * Handles account/profile page
 */

require_once MODEL_PATH . '/Customer.php';
require_once MODEL_PATH . '/Order.php';
require_once MODEL_PATH . '/Favorite.php';

class AccountController {
    private $customerModel;
    private $orderModel;
    private $favoriteModel;
    
    public function __construct() {
        $this->customerModel = new Customer();
        $this->orderModel = new Order();
        $this->favoriteModel = new Favorite();
    }
    
    /**
     * Display account page
     */
    public function index() {
        $isLoggedIn = AuthController::isLoggedIn();
        
        $customer = null;
        $orders = [];
        $address = null;
        $favorites = [];
        $favoriteDish = null;
        
        if ($isLoggedIn) {
            $customerId = $_SESSION['user_id'];
            error_log("AccountController: Getting customer with ID: " . $customerId);
            
            $customer = $this->customerModel->getById($customerId);
            
            if ($customer) {
                error_log("AccountController: Customer found: " . print_r($customer, true));
                $orders = $this->orderModel->getByCustomer($customerId);
                $favorites = $this->favoriteModel->getByCustomer($customerId);
                $favoriteDish = $this->orderModel->getMostOrderedDish($customerId);
                
                error_log("AccountController: Found " . count($favorites) . " favorites");
                error_log("AccountController: Favorites data: " . print_r($favorites, true));
                error_log("AccountController: Favorite dish: " . print_r($favoriteDish, true));
                
                // Address is now part of customer data
                $address = $customer;
            } else {
                error_log("AccountController: Customer NOT found for ID: " . $customerId);
            }
        }
        
        // Load view
        require_once VIEW_PATH . '/account/index.php';
    }
    
    /**
     * Get order details (AJAX)
     */
    public function getOrderDetails() {
        header('Content-Type: application/json');
        
        if (!AuthController::isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            return;
        }
        
        $orderId = $_GET['id'] ?? 0;
        
        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
            return;
        }
        
        $order = $this->orderModel->getById($orderId);
        $orderItems = $this->orderModel->getOrderItems($orderId);
        
        // Verify order belongs to logged in user
        if ($order && $order['customer_id'] == $_SESSION['user_id']) {
            echo json_encode([
                'success' => true,
                'order' => $order,
                'items' => $orderItems
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
        }
    }
}
