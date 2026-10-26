<?php
/**
 * Category Model
 * Beheer van menu categorieën
 */

require_once CONFIG_PATH . '/Database.php';

class Category {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get alle categorieën
     */
    public function getAll() {
        $sql = "SELECT * FROM categories ORDER BY display_order";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get alleen actieve categorieën
     */
    public function getActive() {
        $sql = "SELECT * FROM categories WHERE is_active = 1 ORDER BY display_order";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get category by ID
     */
    public function getById($categoryId) {
        $sql = "SELECT * FROM categories WHERE category_id = :id LIMIT 1";
        return $this->db->fetchOne($sql, [':id' => $categoryId]);
    }
}
