<?php
/**
 * Checkout Controller
 * Handles checkout page and order processing
 */

class CheckoutController {
    
    public function __construct() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Display checkout page
     */
    public function index() {
        // Basisgegevens uit database (indien ingelogd)
        $customer = null;
        $address = null;

        if (isset($_SESSION['customer_id'])) {
            require_once MODEL_PATH . '/Customer.php';
            $customerModel = new Customer();
            $customer = $customerModel->getById($_SESSION['customer_id']);

            $address = [
                'street' => $customer['street'] ?? '',
                'postal_code' => $customer['postal_code'] ?? '',
                'city' => $customer['city'] ?? ''
            ];
        }

        // Eventuele concept-checkout uit vorige stap (als gebruiker terugkomt van overzicht)
        $checkoutData = $_SESSION['checkout_draft'] ?? [];

        // Laad stap 1 view (adres + betaling)
        require_once VIEW_PATH . '/checkout/index.php';
    }

    /**
     * Step 2: overzicht van ingevulde gegevens
     */
    public function review() {
        // Verzamel en bewaar formulierdata in sessie
        $checkoutData = [
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'street' => trim($_POST['street'] ?? ''),
            'postal_code' => trim($_POST['postal_code'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'delivery_time' => $_POST['delivery_time'] ?? 'asap',
            'delivery_time_custom' => $_POST['delivery_time_custom'] ?? '',
            'notes' => trim($_POST['notes'] ?? ''),
            'payment_method' => $_POST['payment_method'] ?? 'ideal',
        ];

        // Afgeleide weergavetekst voor bezorgmoment
        if ($checkoutData['delivery_time'] === 'later' && $checkoutData['delivery_time_custom']) {
            $checkoutData['delivery_time_display'] = 'Op gekozen tijd: ' . $checkoutData['delivery_time_custom'];
        } else {
            $checkoutData['delivery_time_display'] = 'Zo snel mogelijk (25–35 min)';
        }

        $_SESSION['checkout_draft'] = $checkoutData;

        // Laad overzichtspagina
        require_once VIEW_PATH . '/checkout/review.php';
    }
}
