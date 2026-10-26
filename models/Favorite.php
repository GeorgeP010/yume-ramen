<?php
/**
 * Favorite Model
 * Beheer van klant favorieten
 */

require_once CONFIG_PATH . '/Database.php';

class Favorite {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Voeg favoriet toe
     */
    public function add($customerId, $itemName) {
        // Check eerst of het al bestaat
        if ($this->isFavorite($customerId, $itemName)) {
            error_log("Favorite::add - Already exists, skipping");
            return true; // Al bestaat, return true
        }
        
        $sql = "INSERT INTO favorites (customer_id, item_name) 
                VALUES (:customer_id, :item_name)";
        
        error_log("Favorite::add - customer_id: $customerId, item_name: $itemName");
        try {
            $result = $this->db->insert($sql, [
                ':customer_id' => $customerId,
                ':item_name' => $itemName
            ]);
            error_log("Favorite::add - result: " . var_export($result, true));
            return $result;
        } catch (Exception $e) {
            error_log("Favorite::add - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verwijder favoriet
     */
    public function remove($customerId, $itemName) {
        $sql = "DELETE FROM favorites 
                WHERE customer_id = :customer_id AND item_name = :item_name";
        
        return $this->db->query($sql, [
            ':customer_id' => $customerId,
            ':item_name' => $itemName
        ]);
    }
    
    /**
     * Check of item favoriet is
     */
    public function isFavorite($customerId, $itemName) {
        $sql = "SELECT favorite_id FROM favorites 
                WHERE customer_id = :customer_id AND item_name = :item_name 
                LIMIT 1";
        
        $result = $this->db->fetchOne($sql, [
            ':customer_id' => $customerId,
            ':item_name' => $itemName
        ]);
        
        return !empty($result);
    }
    
    /**
     * Get alle favorieten van een klant
     */
    public function getByCustomer($customerId) {
        // Eerst alle favorieten ophalen
        $sql = "SELECT f.item_name, f.created_at as favorited_at
                FROM favorites f
                WHERE f.customer_id = :customer_id
                ORDER BY f.created_at DESC";
        
        error_log("Favorite::getByCustomer - customer_id: $customerId");
        $favorites = $this->db->fetchAll($sql, [':customer_id' => $customerId]);
        error_log("Favorite::getByCustomer - found " . count($favorites) . " favorites from favorites table");
        
        // Voor elke favoriet, probeer menu item details op te halen
        foreach ($favorites as &$favorite) {
            $itemName = $favorite['item_name'];
            
            // Probeer menu item details op te halen (eerst exact match, dan case-insensitive)
            $menuItemSql = "SELECT item_id, name, description, price, 
                                  image_url, category_id, spice_level, 
                                  is_vegetarian, badge
                           FROM menu_items 
                           WHERE name = :name 
                           LIMIT 1";
            
            $menuItem = $this->db->fetchOne($menuItemSql, [':name' => $itemName]);
            
            // Als niet gevonden, probeer case-insensitive match
            if (!$menuItem) {
                $menuItemSql = "SELECT item_id, name, description, price, 
                                      image_url, category_id, spice_level, 
                                      is_vegetarian, badge
                               FROM menu_items 
                               WHERE LOWER(name) = LOWER(:name) 
                               LIMIT 1";
                
                $menuItem = $this->db->fetchOne($menuItemSql, [':name' => $itemName]);
            }
            
            // Als nog steeds niet gevonden, probeer partial match (bevat de naam)
            if (!$menuItem) {
                $menuItemSql = "SELECT item_id, name, description, price, 
                                      image_url, category_id, spice_level, 
                                      is_vegetarian, badge
                               FROM menu_items 
                               WHERE LOWER(name) LIKE LOWER(:name) 
                               LIMIT 1";
                
                $menuItem = $this->db->fetchOne($menuItemSql, [':name' => '%' . $itemName . '%']);
            }
            
            if ($menuItem) {
                // Menu item gevonden, gebruik de details
                $favorite['item_id'] = $menuItem['item_id'];
                $favorite['name'] = $menuItem['name'];
                $favorite['description'] = $menuItem['description'] ?? '';
                $favorite['price'] = $menuItem['price'] ?? 0;
                $favorite['image_url'] = $menuItem['image_url'] ?? 'img/ramen1.png';
                $favorite['category_id'] = $menuItem['category_id'] ?? null;
                $favorite['spice_level'] = $menuItem['spice_level'] ?? null;
                $favorite['is_vegetarian'] = $menuItem['is_vegetarian'] ?? false;
                $favorite['badge'] = $menuItem['badge'] ?? '';
            } else {
                // Menu item niet gevonden, gebruik alleen de naam
                $favorite['name'] = $itemName;
                $favorite['description'] = '';
                $favorite['price'] = 0;
                $favorite['image_url'] = 'img/ramen1.png';
                $favorite['item_id'] = null;
                $favorite['category_id'] = null;
                $favorite['spice_level'] = null;
                $favorite['is_vegetarian'] = false;
                $favorite['badge'] = '';
            }
        }
        
        error_log("Favorite::getByCustomer - returning " . count($favorites) . " favorites");
        return $favorites;
    }
    
    /**
     * Get favoriet namen van een klant (voor checken welke items favoriet zijn)
     */
    public function getFavoriteNames($customerId) {
        $sql = "SELECT item_name FROM favorites 
                WHERE customer_id = :customer_id";
        
        $results = $this->db->fetchAll($sql, [':customer_id' => $customerId]);
        return array_column($results, 'item_name');
    }
}
