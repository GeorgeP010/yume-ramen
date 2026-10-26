<?php
$pageTitle = 'Menu - Yume Ramen';
$currentPage = 'menu';
$additionalCSS = ['menu.css'];
$additionalJS = ['cart.js', 'menu.js'];
require_once __DIR__ . '/../includes/header.php';

// Prepare auto-open item data for JavaScript
$autoOpenData = null;
if (isset($autoOpenItem) && $autoOpenItem) {
    $ingredientsArray = [];
    if (isset($autoOpenItem['ingredients']) && is_array($autoOpenItem['ingredients'])) {
        foreach ($autoOpenItem['ingredients'] as $ing) {
            if (is_array($ing) && isset($ing['name'])) {
                $ingredientsArray[] = $ing['name'];
            } elseif (is_string($ing)) {
                $ingredientsArray[] = $ing;
            }
        }
    } elseif (isset($autoOpenItem['ingredients']) && is_string($autoOpenItem['ingredients'])) {
        $ingredientsArray = explode(', ', $autoOpenItem['ingredients']);
    }
    
    // If no ingredients found, try to get from ingredients column if it exists
    if (empty($ingredientsArray) && isset($autoOpenItem['ingredients']) && is_string($autoOpenItem['ingredients'])) {
        $ingredientsArray = array_filter(explode(', ', $autoOpenItem['ingredients']));
    }
    
    $autoOpenData = [
        'name' => $autoOpenItem['name'],
        'image' => 'public/' . ($autoOpenItem['image_url'] ?? 'img/ramen1.png'),
        'price' => floatval($autoOpenItem['price']),
        'description' => $autoOpenItem['description'] ?? '',
        'ingredients' => $ingredientsArray,
        'badge' => $autoOpenItem['badge'] ?? '',
        'category' => strtolower($autoOpenItem['category'] ?? '')
    ];
}
?>

<script>
// Pass auto-open data to JavaScript
<?php if ($autoOpenData): ?>
window.autoOpenItemData = <?php echo json_encode($autoOpenData); ?>;
<?php endif; ?>
</script>

<!-- Loading Screen -->
<div class="loading-screen" id="loadingScreen">
    <img src="public/img/Yume_logo.png" alt="Yume Ramen" class="loading-logo">
</div>

<?php require_once __DIR__ . '/../includes/nav.php'; ?>

<!-- Animated Background -->
<div id="sakura-container"></div>
<div class="blue-glow"></div>

<section class="menu-hero">
    <!-- Decoratieve afbeelding links boven -->
    <div class="menu-decoration-image">
        <img src="public/img/nav_foto3.png" alt="Ramen Bowl">
    </div>
    
    <!-- Decoratieve afbeelding rechts boven -->
    <div class="menu-decoration-image-right">
        <img src="public/img/nav_foto4.png" alt="Ramen Bowl">
    </div>
    
    <div class="container">
        <div class="menu-header">
            <p class="jp-label">メニュー</p>
            <h1 class="menu-title">
                <span class="solid-text">ONS</span>
                <span class="outline-text">MENU</span>
            </h1>
            <p class="menu-subtitle">Ontdek onze authentieke ramen collectie</p>
            <a href="public/img/yume_menukaart.pdf" target="_blank" class="download-menu-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                BEKIJK MENU
            </a>
        </div>
    </div>
</section>

<section class="menu-items">
    <div class="container">
        
        <?php 
        // Debug: Show what we have
        if (empty($menuItems)) {
            echo "<p style='color: red; text-align: center;'>Geen menu items gevonden in database</p>";
        } else {
            // Group items by category
            $ramenItems = [];
            $sidesItems = [];
            $drinksItems = [];
            
            foreach ($menuItems as $item) {
                $category = strtolower($item['category'] ?? '');
                if ($category === 'ramen') {
                    $ramenItems[] = $item;
                } elseif ($category === 'bijgerechten') {
                    $sidesItems[] = $item;
                } elseif ($category === 'dranken') {
                    $drinksItems[] = $item;
                }
            }
        ?>
        
        <!-- Ramen Section -->
        <?php if (!empty($ramenItems)): ?>
        <div class="menu-grid">
            <?php foreach ($ramenItems as $item): ?>
                <?php 
                // Convert comma-separated ingredients to JavaScript array
                $ingredientsStr = $item['ingredients'] ?? '';
                $ingredientsArray = !empty($ingredientsStr) ? explode(', ', $ingredientsStr) : [];
                $ingredientsJson = json_encode($ingredientsArray);
                $itemCategory = strtolower($item['category'] ?? 'ramen');
                ?>
                <?php 
                $isFavorite = isset($favoriteNames) && in_array($item['name'], $favoriteNames);
                ?>
                <div class="menu-card">
                    <div class="card-image" data-item-name="<?php echo htmlspecialchars($item['name']); ?>" 
                         data-item-image="public/<?php echo htmlspecialchars($item['image_url'] ?? 'img/ramen1.png'); ?>"
                         data-item-price="<?php echo $item['price']; ?>"
                         data-item-description="<?php echo htmlspecialchars($item['description']); ?>"
                         data-item-ingredients='<?php echo $ingredientsJson; ?>'
                         data-item-badge="<?php echo htmlspecialchars($item['badge'] ?? ''); ?>"
                         data-item-category="<?php echo htmlspecialchars($itemCategory); ?>">
                        <img src="public/<?php echo htmlspecialchars($item['image_url'] ?? 'img/ramen1.png'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button class="favorite-btn <?php echo $isFavorite ? 'active' : ''; ?>" 
                                    data-item-name="<?php echo htmlspecialchars($item['name']); ?>"
                                    aria-label="Favoriet">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                            </button>
                        <?php endif; ?>
                        <?php if (!empty($item['badge'])): ?>
                            <?php if ($item['badge'] === 'POPULAIR'): ?>
                                <div class="card-badge">POPULAIR</div>
                            <?php elseif ($item['badge'] === 'EXTRA PIKANT'): ?>
                                <div class="card-badge hot">EXTRA PIKANT</div>
                            <?php elseif ($item['badge'] === 'VEGGIE'): ?>
                                <div class="card-badge vegan">VEGGIE</div>
                            <?php else: ?>
                                <div class="card-badge"><?php echo htmlspecialchars($item['badge']); ?></div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="card-content" data-item-name="<?php echo htmlspecialchars($item['name']); ?>" 
                         data-item-image="public/<?php echo htmlspecialchars($item['image_url'] ?? 'img/ramen1.png'); ?>"
                         data-item-price="<?php echo $item['price']; ?>"
                         data-item-description="<?php echo htmlspecialchars($item['description']); ?>"
                         data-item-ingredients='<?php echo $ingredientsJson; ?>'
                         data-item-badge="<?php echo htmlspecialchars($item['badge'] ?? ''); ?>"
                         data-item-category="<?php echo htmlspecialchars($itemCategory); ?>">
                        <h3 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="card-description"><?php echo htmlspecialchars($item['description']); ?></p>
                        <div class="card-footer">
                            <span class="card-price">€<?php echo number_format($item['price'], 2, ',', '.'); ?></span>
                            <button class="view-details">DETAILS</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php } // End of else from empty check ?>

        <!-- Bijgerechten Section -->
        <?php if (!empty($sidesItems)): ?>
        <div class="menu-section-title">
            <h2><span class="solid-text">BIJ</span><span class="outline-text">GERECHTEN</span></h2>
        </div>

        <div class="menu-grid">
            <?php foreach ($sidesItems as $item): ?>
                <?php 
                $ingredientsStr = $item['ingredients'] ?? '';
                $ingredientsArray = !empty($ingredientsStr) ? explode(', ', $ingredientsStr) : [];
                $ingredientsJson = json_encode($ingredientsArray);
                $isFavorite = isset($favoriteNames) && in_array($item['name'], $favoriteNames);
                $itemCategory = strtolower($item['category'] ?? 'bijgerechten');
                ?>
                <div class="menu-card">
                    <div class="card-image" data-item-name="<?php echo htmlspecialchars($item['name']); ?>" 
                         data-item-image="public/<?php echo htmlspecialchars($item['image_url'] ?? 'img/ramen1.png'); ?>"
                         data-item-price="<?php echo $item['price']; ?>"
                         data-item-description="<?php echo htmlspecialchars($item['description']); ?>"
                         data-item-ingredients='<?php echo $ingredientsJson; ?>'
                         data-item-badge="<?php echo htmlspecialchars($item['badge'] ?? ''); ?>"
                         data-item-category="<?php echo htmlspecialchars($itemCategory); ?>">
                        <img src="public/<?php echo htmlspecialchars($item['image_url'] ?? 'img/ramen1.png'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button class="favorite-btn <?php echo $isFavorite ? 'active' : ''; ?>" 
                                    data-item-name="<?php echo htmlspecialchars($item['name']); ?>"
                                    aria-label="Favoriet">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                            </button>
                        <?php endif; ?>
                        <?php if (!empty($item['badge']) && $item['badge'] === 'VEGGIE'): ?>
                            <div class="card-badge vegan">VEGGIE</div>
                        <?php endif; ?>
                    </div>
                    <div class="card-content" data-item-name="<?php echo htmlspecialchars($item['name']); ?>" 
                         data-item-image="public/<?php echo htmlspecialchars($item['image_url'] ?? 'img/ramen1.png'); ?>"
                         data-item-price="<?php echo $item['price']; ?>"
                         data-item-description="<?php echo htmlspecialchars($item['description']); ?>"
                         data-item-ingredients='<?php echo $ingredientsJson; ?>'
                         data-item-badge="<?php echo htmlspecialchars($item['badge'] ?? ''); ?>"
                         data-item-category="<?php echo htmlspecialchars($itemCategory); ?>">
                        <h3 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="card-description"><?php echo htmlspecialchars($item['description']); ?></p>
                        <div class="card-footer">
                            <span class="card-price">€<?php echo number_format($item['price'], 2, ',', '.'); ?></span>
                            <button class="view-details">DETAILS</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Dranken Section -->
        <?php if (!empty($drinksItems)): ?>
        <div class="menu-section-title">
            <h2><span class="solid-text">DRAN</span><span class="outline-text">KEN</span></h2>
        </div>

        <div class="menu-grid">
            <?php foreach ($drinksItems as $item): ?>
                <?php 
                $ingredientsStr = $item['ingredients'] ?? '';
                $ingredientsArray = !empty($ingredientsStr) ? explode(', ', $ingredientsStr) : [];
                $ingredientsJson = json_encode($ingredientsArray);
                $isFavorite = isset($favoriteNames) && in_array($item['name'], $favoriteNames);
                $itemCategory = strtolower($item['category'] ?? 'dranken');
                ?>
                <div class="menu-card">
                    <div class="card-image" data-item-name="<?php echo htmlspecialchars($item['name']); ?>" 
                         data-item-image="public/<?php echo htmlspecialchars($item['image_url'] ?? 'img/ramen1.png'); ?>"
                         data-item-price="<?php echo $item['price']; ?>"
                         data-item-description="<?php echo htmlspecialchars($item['description']); ?>"
                         data-item-ingredients='<?php echo $ingredientsJson; ?>'
                         data-item-badge="<?php echo htmlspecialchars($item['badge'] ?? ''); ?>"
                         data-item-category="<?php echo htmlspecialchars($itemCategory); ?>">
                        <img src="public/<?php echo htmlspecialchars($item['image_url'] ?? 'img/ramen1.png'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button class="favorite-btn <?php echo $isFavorite ? 'active' : ''; ?>" 
                                    data-item-name="<?php echo htmlspecialchars($item['name']); ?>"
                                    aria-label="Favoriet">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                            </button>
                        <?php endif; ?>
                        <?php if (!empty($item['badge'])): ?>
                            <div class="card-badge"><?php echo htmlspecialchars($item['badge']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="card-content" data-item-name="<?php echo htmlspecialchars($item['name']); ?>" 
                         data-item-image="public/<?php echo htmlspecialchars($item['image_url'] ?? 'img/ramen1.png'); ?>"
                         data-item-price="<?php echo $item['price']; ?>"
                         data-item-description="<?php echo htmlspecialchars($item['description']); ?>"
                         data-item-ingredients='<?php echo $ingredientsJson; ?>'
                         data-item-badge="<?php echo htmlspecialchars($item['badge'] ?? ''); ?>"
                         data-item-category="<?php echo htmlspecialchars($itemCategory); ?>">
                        <h3 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="card-description"><?php echo htmlspecialchars($item['description']); ?></p>
                        <div class="card-footer">
                            <span class="card-price">€<?php echo number_format($item['price'], 2, ',', '.'); ?></span>
                            <button class="view-details">DETAILS</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- Winkelwagen Float Button -->
<div class="cart-float" id="cartFloat" onclick="toggleCart()">
    <span class="cart-icon">🛒</span>
    <span class="cart-count" id="cartCount">0</span>
</div>

<!-- Winkelwagen Sidebar -->
<div class="cart-sidebar" id="cartSidebar">
    <div class="cart-header">
        <h3>Je Bestelling</h3>
        <button class="cart-close" onclick="toggleCart()">✕</button>
    </div>
    <div class="cart-items" id="cartItems">
        <p class="empty-cart">Je winkelwagen is leeg</p>
    </div>
    <div class="cart-footer">
        <div class="cart-free-delivery" id="cartFreeDelivery">
            <div class="cart-free-delivery-text">
                Voeg €<span id="cartFreeAmount">0,00</span> toe voor gratis bezorging!
            </div>
            <div class="cart-progress-bar">
                <div class="cart-progress-fill" id="cartProgressFill"></div>
            </div>
        </div>
        <div class="cart-totals">
            <div class="cart-total-row">
                <span>Subtotaal:</span>
                <span id="cartSubtotal">€0,00</span>
            </div>
            <div class="cart-total-row">
                <span>Bezorging:</span>
                <span id="cartDelivery">€2,50</span>
            </div>
            <div class="cart-total-row cart-total-main">
                <span>TOTAAL:</span>
                <span id="cartTotal">€0,00</span>
            </div>
        </div>
        <a href="checkout.php" class="checkout-btn">BESTELLEN</a>
    </div>
</div>

<!-- Detail Modal met Customization -->
<div class="detail-modal" id="detailModal">
    <div class="detail-modal-backdrop" onclick="closeDetail()"></div>
    <div class="detail-modal-container">
        <button class="detail-close" onclick="closeDetail()">✕</button>
        
        <div class="detail-modal-layout">
            <!-- Links: Foto + Customization -->
            <div class="detail-left">
                <div class="detail-image-section">
                    <img id="detailImage" src="" alt="">
                </div>
                
                <div class="customization-section" id="customizationSection">
                    <h3 class="customization-title">CUSTOMIZE YOUR ORDER</h3>
                    
                    <!-- Broth Richness (alleen voor ramen) -->
                    <div class="customization-group" id="brothGroup" style="display: none;">
                        <label class="customization-label">Broth Richness</label>
                        <div class="customization-options">
                            <button class="customization-option" data-option="broth" data-value="light">Light</button>
                            <button class="customization-option active" data-option="broth" data-value="medium">Medium</button>
                            <button class="customization-option" data-option="broth" data-value="rich">Rich</button>
                        </div>
                    </div>
                    
                    <!-- Noodle Firmness (alleen voor ramen) -->
                    <div class="customization-group" id="noodleGroup" style="display: none;">
                        <label class="customization-label">Noodle Firmness</label>
                        <div class="customization-options">
                            <button class="customization-option active" data-option="noodles" data-value="soft">Soft</button>
                            <button class="customization-option" data-option="noodles" data-value="medium">Medium</button>
                            <button class="customization-option" data-option="noodles" data-value="firm">Firm (Al Dente)</button>
                        </div>
                    </div>
                    
                    <!-- Spice Level (alleen voor ramen) -->
                    <div class="customization-group" id="spiceGroup" style="display: none;">
                        <label class="customization-label">Spice Level</label>
                        <div class="customization-options">
                            <button class="customization-option active" data-option="spice" data-value="mild">Mild</button>
                            <button class="customization-option" data-option="spice" data-value="medium">Medium</button>
                            <button class="customization-option" data-option="spice" data-value="hot">Hot</button>
                            <button class="customization-option" data-option="spice" data-value="extra_hot">Extra Hot</button>
                        </div>
                    </div>
                    
                    <!-- Smaakkeuze (voor dranken zoals soda) -->
                    <div class="customization-group" id="flavorGroup" style="display: none;">
                        <label class="customization-label">Smaak</label>
                        <div class="customization-options">
                            <button class="customization-option active" data-option="flavor" data-value="cola">Cola</button>
                            <button class="customization-option" data-option="flavor" data-value="fanta">Fanta</button>
                            <button class="customization-option" data-option="flavor" data-value="sprite">Sprite</button>
                        </div>
                    </div>
                    
                    <!-- Extra Toppings (alleen voor ramen) -->
                    <div class="customization-group" id="toppingsGroup" style="display: none;">
                        <label class="customization-label">EXTRA TOPPINGS</label>
                        <div class="toppings-grid" id="toppingsGrid">
                            <!-- Wordt dynamisch gevuld -->
                        </div>
                    </div>
                    
                    <!-- Special Instructions -->
                    <div class="customization-group">
                        <label class="customization-label">Special Instructions (Optional)</label>
                        <textarea id="specialInstructions" rows="3" placeholder="Any allergies or special requests..."></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Rechts: Order Summary (sticky) -->
            <div class="detail-right">
                <div class="order-summary-sticky">
                    <h3 class="order-summary-title">YOUR ORDER</h3>
                    
                    <div class="order-item-summary">
                        <div class="order-item-name" id="orderItemName"></div>
                        <div class="order-item-base-price" id="orderItemBasePrice"></div>
                        
                        <div class="order-customizations" id="orderCustomizations"></div>
                        
                        <div class="order-item-extras" id="orderItemExtras"></div>
                    </div>
                    
                    <div class="order-totals-summary">
                        <div class="order-summary-row">
                            <span>Subtotal:</span>
                            <span id="orderSubtotal">€0,00</span>
                        </div>
                        <div class="order-summary-row">
                            <span>Delivery:</span>
                            <span>€2,50</span>
                        </div>
                        <div class="order-summary-row order-total-row">
                            <span>TOTAL:</span>
                            <span id="orderTotal">€0,00</span>
                        </div>
                    </div>
                    
                    <div class="quantity-control-summary">
                        <button class="quantity-btn" onclick="decreaseQuantity()">-</button>
                        <span class="quantity-value" id="quantityValue">1</span>
                        <button class="quantity-btn" onclick="increaseQuantity()">+</button>
                    </div>
                    
                    <button class="add-to-cart-btn-summary" onclick="addFromDetail()">ADD TO CART</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
