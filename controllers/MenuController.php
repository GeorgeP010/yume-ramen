<?php
/**
 * Menu Controller
 * Handles menu page
 */

require_once MODEL_PATH . '/MenuItem.php';
require_once MODEL_PATH . '/Category.php';
require_once MODEL_PATH . '/Favorite.php';

class MenuController {
    private $menuItemModel;
    private $categoryModel;
    private $favoriteModel;
    
    public function __construct() {
        $this->menuItemModel = new MenuItem();
        $this->categoryModel = new Category();
        $this->favoriteModel = new Favorite();
    }
    
    /**
     * Display menu page
     */
    public function index() {
        // Get all menu items and categories
        $menuItems = $this->menuItemModel->getAll();
        $categories = $this->categoryModel->getActive();
        
        // Get favorite names if user is logged in
        $favoriteNames = [];
        if (isset($_SESSION['user_id'])) {
            $favoriteNames = $this->favoriteModel->getFavoriteNames($_SESSION['user_id']);
        }
        
        // Check if there's an item parameter to auto-open
        $autoOpenItem = null;
        if (isset($_GET['item']) && !empty($_GET['item'])) {
            $itemName = urldecode($_GET['item']);
            // Find the menu item by name
            foreach ($menuItems as $item) {
                if (strtolower($item['name']) === strtolower($itemName)) {
                    $autoOpenItem = $item;
                    // Get ingredients for this item
                    $ingredients = $this->menuItemModel->getIngredients($item['item_id']);
                    $autoOpenItem['ingredients'] = $ingredients;
                    break;
                }
            }
        }
        
        // Debug logging
        error_log("MenuController: Found " . count($menuItems) . " menu items");
        error_log("MenuController: Found " . count($categories) . " categories");
        
        if (count($menuItems) > 0) {
            error_log("MenuController: First item: " . print_r($menuItems[0], true));
        }
        
        // Load view (view will handle grouping by category)
        require_once VIEW_PATH . '/menu/index.php';
    }
    
    /**
     * Toggle favorite (AJAX)
     */
    public function toggleFavorite() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Je moet ingelogd zijn']);
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $itemName = $data['item_name'] ?? '';
        
        if (empty($itemName)) {
            echo json_encode(['success' => false, 'message' => 'Invalid item name']);
            return;
        }
        
        $customerId = $_SESSION['user_id'];
        $isFavorite = $this->favoriteModel->isFavorite($customerId, $itemName);
        
        if ($isFavorite) {
            $result = $this->favoriteModel->remove($customerId, $itemName);
            $message = 'Favoriet verwijderd';
            $newFavoriteState = false;
        } else {
            $result = $this->favoriteModel->add($customerId, $itemName);
            $message = 'Toegevoegd aan favorieten';
            $newFavoriteState = true;
        }
        
        if ($result !== false) {
            echo json_encode([
                'success' => true,
                'is_favorite' => $newFavoriteState,
                'message' => $message
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Er ging iets mis bij het opslaan van de favoriet'
            ]);
        }
    }
    
    /**
     * Get menu item details (AJAX)
     */
    public function getItemDetails() {
        header('Content-Type: application/json');
        
        $itemId = $_GET['id'] ?? 0;
        
        if (!$itemId) {
            echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
            return;
        }
        
        $item = $this->menuItemModel->getById($itemId);
        $ingredients = $this->menuItemModel->getIngredients($itemId);
        
        if ($item) {
            echo json_encode([
                'success' => true,
                'item' => $item,
                'ingredients' => $ingredients
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Item not found']);
        }
    }
}
