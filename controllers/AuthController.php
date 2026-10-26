<?php
/**
 * Authentication Controller
 * Handles login, register, logout
 */

require_once MODEL_PATH . '/Customer.php';

class AuthController {
    private $customerModel;
    
    public function __construct() {
        $this->customerModel = new Customer();
    }
    
    /**
     * Handle login POST request
     */
    public function login() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Debug logging
        error_log("Login attempt for email: " . $email);
        
        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email en wachtwoord zijn verplicht']);
            return;
        }
        
        try {
            $customer = $this->customerModel->login($email, $password);
            
            if ($customer) {
                $_SESSION['user_id'] = $customer['customer_id'];
                $_SESSION['user_email'] = $customer['email'];
                $_SESSION['user_name'] = $customer['name'];
                $_SESSION['logged_in'] = true;
                
                error_log("Login successful for: " . $email);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Login succesvol',
                    'redirect' => 'account.php'
                ]);
            } else {
                error_log("Login failed for: " . $email);
                echo json_encode(['success' => false, 'message' => 'Onjuiste email of wachtwoord']);
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Er is een fout opgetreden: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Handle register POST request
     */
    public function register() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        // Validatie
        if (empty($name) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Alle velden zijn verplicht']);
            return;
        }
        
        if ($password !== $passwordConfirm) {
            echo json_encode(['success' => false, 'message' => 'Wachtwoorden komen niet overeen']);
            return;
        }
        
        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Wachtwoord moet minimaal 6 tekens zijn']);
            return;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Ongeldig email adres']);
            return;
        }
        
        // Check if email already exists
        if ($this->customerModel->emailExists($email)) {
            echo json_encode(['success' => false, 'message' => 'Dit email adres is al geregistreerd']);
            return;
        }
        
        // Register customer
        $customerId = $this->customerModel->register([
            'name' => $name,
            'email' => $email,
            'password' => $password
        ]);
        
        if ($customerId) {
            // Auto login after registration
            $customer = $this->customerModel->getById($customerId);
            $_SESSION['user_id'] = $customer['customer_id'];
            $_SESSION['user_email'] = $customer['email'];
            $_SESSION['user_name'] = $customer['name'];
            $_SESSION['logged_in'] = true;
            
            echo json_encode([
                'success' => true, 
                'message' => 'Account succesvol aangemaakt',
                'redirect' => 'account.php'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Er is iets misgegaan bij het aanmaken van je account']);
        }
    }
    
    /**
     * Handle logout
     */
    public function logout() {
        session_destroy();
        header('Location: index.php');
        exit;
    }
    
    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Require login (redirect if not logged in)
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: account.php');
            exit;
        }
    }
}
