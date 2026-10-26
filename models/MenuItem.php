<?php
/**
 * MenuItem Model
 * Beheer van menu items
 */

require_once CONFIG_PATH . '/Database.php';

class MenuItem {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get alle menu items
     */
    public function getAll() {
        $sql = "SELECT item_id, name as name, description, price, 
                       image_url, category, badge, is_available,
                       ingredients, created_at
                FROM menu_items
                ORDER BY 
                    CASE 
                        WHEN category = 'ramen' THEN 1
                        WHEN category = 'bijgerechten' THEN 2
                        WHEN category = 'dranken' THEN 3
                        ELSE 4
                    END,
                    name";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get menu items by category
     */
    public function getByCategory($category) {
        $sql = "SELECT item_id, name as name, description, price, 
                       image_url, category, badge, is_available,
                       created_at
                FROM menu_items
                WHERE category = :category
                ORDER BY name";
        
        return $this->db->fetchAll($sql, [':category' => $category]);
    }
    
    /**
     * Get featured menu items (always returns 3 items)
     */
    public function getFeatured() {
        // Haal eerst featured items op
        $sql = "SELECT item_id, name as name, description, price, 
                       image_url, category_id, spice_level, is_vegetarian, 
                       is_featured, is_available, created_at
                FROM menu_items
                WHERE is_featured = 1 AND is_available = 1
                ORDER BY item_id
                LIMIT 3";
        
        $featured = $this->db->fetchAll($sql);
        
        // Als er minder dan 3 featured items zijn, vul aan met andere beschikbare items
        if (count($featured) < 3) {
            $needed = 3 - count($featured);
            $featuredIds = array_column($featured, 'item_id');
            
            $sqlAdditional = "SELECT item_id, name as name, description, price, 
                                    image_url, category_id, spice_level, is_vegetarian, 
                                    is_featured, is_available, created_at
                             FROM menu_items
                             WHERE is_available = 1";
            
            $params = [];
            if (!empty($featuredIds)) {
                $placeholders = [];
                foreach ($featuredIds as $index => $id) {
                    $key = ':id' . $index;
                    $placeholders[] = $key;
                    $params[$key] = $id;
                }
                $sqlAdditional .= " AND item_id NOT IN (" . implode(',', $placeholders) . ")";
            }
            
            $sqlAdditional .= " ORDER BY item_id LIMIT " . (int)$needed;
            
            $additional = $this->db->fetchAll($sqlAdditional, $params);
            $featured = array_merge($featured, $additional);
        }
        
        return array_slice($featured, 0, 3);
    }
    
    /**
     * Get menu item by ID
     */
    public function getById($itemId) {
        $sql = "SELECT mi.*, c.name as category_name
                FROM menu_items mi
                LEFT JOIN categories c ON mi.category_id = c.category_id
                WHERE mi.item_id = :item_id
                LIMIT 1";
        
        return $this->db->fetchOne($sql, [':item_id' => $itemId]);
    }
    
    /**
     * Get ingredients for menu item
     */
    public function getIngredients($itemId) {
        $sql = "SELECT i.name, mii.quantity, i.unit
                FROM menu_item_ingredients mii
                JOIN ingredients i ON mii.ingredient_id = i.ingredient_id
                WHERE mii.item_id = :item_id";
        
        return $this->db->fetchAll($sql, [':item_id' => $itemId]);
    }
    
    /**
     * Search menu items
     */
    public function search($searchTerm) {
        $sql = "SELECT mi.*, c.name as category_name
                FROM menu_items mi
                LEFT JOIN categories c ON mi.category_id = c.category_id
                WHERE (mi.name LIKE :search OR mi.description LIKE :search)
                AND mi.is_available = 1
                ORDER BY mi.name";
        
        $searchParam = '%' . $searchTerm . '%';
        return $this->db->fetchAll($sql, [':search' => $searchParam]);
    }
}
