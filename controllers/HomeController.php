<?php
/**
 * Home Controller
 * Handles homepage
 */

require_once MODEL_PATH . '/MenuItem.php';

class HomeController {
    private $menuItemModel;
    
    public function __construct() {
        $this->menuItemModel = new MenuItem();
    }
    
    /**
     * Display homepage
     */
    public function index() {
        // Get featured items from database
        $featuredItems = $this->menuItemModel->getFeatured();
        
        // Load view
        require_once VIEW_PATH . '/home/index.php';
    }
}
