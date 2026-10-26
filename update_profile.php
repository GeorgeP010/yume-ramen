<?php
/**
 * Profile Update Handler
 * Handles profile and address updates using database
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/config/Database.php';

// Check if logged in
if (!AuthController::isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

header('Content-Type: application/json');

$db = Database::getInstance();
$conn = $db->getConnection();
$customerId = $_SESSION['user_id'];

// Get action
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update_profile':
        $name = trim($_POST['name'] ?? '');
        
        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Naam is verplicht']);
            exit;
        }
        
        try {
            $sql = "UPDATE customers SET name = :name WHERE customer_id = :id";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([
                ':name' => $name,
                ':id' => $customerId
            ]);
            
            if ($result) {
                // Update session
                $_SESSION['user_name'] = $name;
                echo json_encode(['success' => true, 'message' => 'Profiel bijgewerkt']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Fout bij bijwerken']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Database fout: ' . $e->getMessage()]);
        }
        break;
        
    case 'update_address':
        $street = trim($_POST['street'] ?? '');
        $postalCode = trim($_POST['postal_code'] ?? '');
        $city = trim($_POST['city'] ?? '');
        
        if (empty($street) || empty($postalCode) || empty($city)) {
            echo json_encode(['success' => false, 'message' => 'Alle velden zijn verplicht']);
            exit;
        }
        
        try {
            // Update address in customers table
            $sql = "UPDATE customers 
                    SET street = :street, postal_code = :postal_code, city = :city
                    WHERE customer_id = :customer_id";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([
                ':street' => $street,
                ':postal_code' => $postalCode,
                ':city' => $city,
                ':customer_id' => $customerId
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Adres bijgewerkt',
                    'street' => $street,
                    'postal_code' => $postalCode,
                    'city' => $city
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Fout bij bijwerken']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Database fout: ' . $e->getMessage()]);
        }
        break;
        
    case 'update_avatar':
        $profileIcon = intval($_POST['profile_icon'] ?? 0);
        
        // Validate icon number (1-8)
        if ($profileIcon < 1 || $profileIcon > 8) {
            echo json_encode(['success' => false, 'message' => 'Ongeldige icon selectie']);
            exit;
        }
        
        try {
            $sql = "UPDATE customers SET profile_icon = :profile_icon WHERE customer_id = :id";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([
                ':profile_icon' => $profileIcon,
                ':id' => $customerId
            ]);
            
            if ($result) {
                // Sla ook op in de sessie zodat de view het direct kan gebruiken
                $_SESSION['profile_icon'] = $profileIcon;
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Avatar bijgewerkt',
                    'profile_icon' => $profileIcon
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Fout bij bijwerken']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Database fout: ' . $e->getMessage()]);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
