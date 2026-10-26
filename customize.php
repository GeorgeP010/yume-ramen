<?php
/**
 * Customize Page - Product customization with order summary
 */

// Start session
session_start();

// Load configuration
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Database.php';
require_once MODEL_PATH . '/MenuItem.php';

// Get item ID or name from URL
$itemId = $_GET['id'] ?? null;
$itemName = $_GET['item'] ?? null;

$menuItemModel = new MenuItem();
$item = null;

if ($itemId) {
    $item = $menuItemModel->getById($itemId);
} elseif ($itemName) {
    // Find by name
    $allItems = $menuItemModel->getAll();
    foreach ($allItems as $menuItem) {
        if (strtolower($menuItem['name']) === strtolower(urldecode($itemName))) {
            $item = $menuItem;
            break;
        }
    }
}

if (!$item) {
    header('Location: menu.php');
    exit;
}

// Determine if it's a ramen (needs customization) or drink (needs flavor choice)
$isRamen = strtolower($item['category'] ?? '') === 'ramen';
$isDrink = strtolower($item['category'] ?? '') === 'dranken';
$needsFlavorChoice = $isDrink && (stripos($item['name'], 'soda') !== false || stripos($item['name'], 'frisdrank') !== false);

$pageTitle = 'Customize - ' . htmlspecialchars($item['name']) . ' - Yume Ramen';
$currentPage = 'menu';
$additionalCSS = ['customize.css'];
$additionalJS = ['customize.js'];
require_once __DIR__ . '/views/includes/header.php';
?>

<!-- Loading Screen -->
<div class="loading-screen" id="loadingScreen">
    <img src="public/img/Yume_logo.png" alt="Yume Ramen" class="loading-logo">
</div>

<?php require_once __DIR__ . '/views/includes/nav.php'; ?>

<!-- Customize Page -->
<section class="customize-section">
    <div class="customize-container">
        
        <!-- Left: Product Image & Info -->
        <div class="customize-main">
            <div class="customize-image-section">
                <div class="customize-main-image">
                    <img src="public/<?php echo htmlspecialchars($item['image_url'] ?? 'img/ramen1.png'); ?>" 
                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                         id="customizeMainImage">
                </div>
                <?php if ($isRamen): ?>
                <!-- Thumbnails voor ramen -->
                <div class="customize-thumbnails">
                    <div class="thumbnail active" data-image="public/<?php echo htmlspecialchars($item['image_url'] ?? 'img/ramen1.png'); ?>">
                        <img src="public/<?php echo htmlspecialchars($item['image_url'] ?? 'img/ramen1.png'); ?>" alt="View 1">
                    </div>
                    <div class="thumbnail" data-image="public/img/ramen1.png">
                        <img src="public/img/ramen1.png" alt="View 2">
                    </div>
                    <div class="thumbnail" data-image="public/img/draak ramen.png">
                        <img src="public/img/draak ramen.png" alt="View 3">
                    </div>
                    <div class="thumbnail" data-image="public/img/miso ramen.png">
                        <img src="public/img/miso ramen.png" alt="View 4">
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="customize-info-section">
                <div class="customize-meta">
                    <span class="meta-item">10 mins prep</span>
                    <span class="meta-item">Serves 1</span>
                    <?php if ($isRamen): ?>
                    <span class="meta-item">630 cal</span>
                    <?php endif; ?>
                </div>

                <h1 class="customize-title"><?php echo htmlspecialchars($item['name']); ?></h1>
                <p class="customize-description"><?php echo htmlspecialchars($item['description'] ?? ''); ?></p>

                <?php if ($isRamen): ?>
                <!-- Customize Options voor Ramen -->
                <div class="customize-options">
                    <h2 class="customize-options-title">CUSTOMIZE YOUR ORDER</h2>

                    <!-- Broth Richness -->
                    <div class="customize-group">
                        <label class="customize-label">Broth Richness</label>
                        <div class="customize-buttons">
                            <button class="customize-btn" data-option="broth" data-value="light">
                                Light
                                <span class="jp-text">あっさり</span>
                            </button>
                            <button class="customize-btn active" data-option="broth" data-value="medium">
                                Medium
                                <span class="jp-text">中濃</span>
                            </button>
                            <button class="customize-btn" data-option="broth" data-value="rich">
                                Rich
                                <span class="jp-text">こってり</span>
                            </button>
                        </div>
                    </div>

                    <!-- Noodle Firmness -->
                    <div class="customize-group">
                        <label class="customize-label">Noodle Firmness</label>
                        <div class="customize-buttons">
                            <button class="customize-btn" data-option="noodles" data-value="soft">
                                Soft
                                <span class="jp-text">やわらか</span>
                            </button>
                            <button class="customize-btn active" data-option="noodles" data-value="medium">
                                Medium
                                <span class="jp-text">中</span>
                            </button>
                            <button class="customize-btn" data-option="noodles" data-value="firm">
                                Firm (Al Dente)
                                <span class="jp-text">かため</span>
                            </button>
                        </div>
                    </div>

                    <!-- Spice Level -->
                    <div class="customize-group">
                        <label class="customize-label">Spice Level</label>
                        <div class="customize-buttons">
                            <button class="customize-btn" data-option="spice" data-value="mild">
                                Mild
                                <span class="jp-text">甘口</span>
                            </button>
                            <button class="customize-btn active" data-option="spice" data-value="medium">
                                Medium
                                <span class="jp-text">中辛</span>
                            </button>
                            <button class="customize-btn" data-option="spice" data-value="hot">
                                Hot
                                <span class="jp-text">辛口</span>
                            </button>
                            <button class="customize-btn" data-option="spice" data-value="extra_hot">
                                Extra Hot
                                <span class="jp-text">激辛</span>
                            </button>
                        </div>
                    </div>

                    <!-- Extra Toppings -->
                    <div class="customize-group">
                        <label class="customize-label">EXTRA TOPPINGS <span class="jp-text">トッピング</span></label>
                        <div class="customize-checkboxes">
                            <label class="customize-checkbox">
                                <input type="checkbox" data-topping="extra_chashu" data-price="3.00">
                                <span class="checkbox-label">Extra Chashu</span>
                                <span class="checkbox-price">+€3.00</span>
                            </label>
                            <label class="customize-checkbox">
                                <input type="checkbox" data-topping="soft_egg" data-price="1.00">
                                <span class="checkbox-label">Soft-Boiled Egg</span>
                                <span class="checkbox-price">+€1.00</span>
                            </label>
                            <label class="customize-checkbox">
                                <input type="checkbox" data-topping="extra_noodles" data-price="2.00">
                                <span class="checkbox-label">Extra Noodles</span>
                                <span class="checkbox-price">+€2.00</span>
                            </label>
                            <label class="customize-checkbox">
                                <input type="checkbox" data-topping="nori_sheets" data-price="1.00">
                                <span class="checkbox-label">Nori Sheets</span>
                                <span class="checkbox-price">+€1.00</span>
                            </label>
                            <label class="customize-checkbox">
                                <input type="checkbox" data-topping="sweet_corn" data-price="1.00">
                                <span class="checkbox-label">Sweet Corn</span>
                                <span class="checkbox-price">+€1.00</span>
                            </label>
                            <label class="customize-checkbox">
                                <input type="checkbox" data-topping="bamboo_shoots" data-price="1.00">
                                <span class="checkbox-label">Bamboo Shoots</span>
                                <span class="checkbox-price">+€1.00</span>
                            </label>
                            <label class="customize-checkbox">
                                <input type="checkbox" data-topping="green_onions" data-price="0.00">
                                <span class="checkbox-label">Green Onions</span>
                                <span class="checkbox-price">Free</span>
                            </label>
                            <label class="customize-checkbox">
                                <input type="checkbox" data-topping="shiitake_mushrooms" data-price="2.00">
                                <span class="checkbox-label">Shiitake Mushrooms</span>
                                <span class="checkbox-price">+€2.00</span>
                            </label>
                            <label class="customize-checkbox">
                                <input type="checkbox" data-topping="butter" data-price="0.50">
                                <span class="checkbox-label">Butter</span>
                                <span class="checkbox-price">+€0.50</span>
                            </label>
                            <label class="customize-checkbox">
                                <input type="checkbox" data-topping="roasted_garlic" data-price="1.00">
                                <span class="checkbox-label">Roasted Garlic</span>
                                <span class="checkbox-price">+€1.00</span>
                            </label>
                        </div>
                    </div>
                </div>
                <?php elseif ($needsFlavorChoice): ?>
                <!-- Flavor Choice voor Soda/Frisdrank -->
                <div class="customize-options">
                    <h2 class="customize-options-title">KIES JE SMAAK</h2>
                    <div class="customize-group">
                        <div class="customize-buttons">
                            <button class="customize-btn" data-option="flavor" data-value="cola">
                                Cola
                            </button>
                            <button class="customize-btn" data-option="flavor" data-value="fanta">
                                Fanta
                            </button>
                            <button class="customize-btn active" data-option="flavor" data-value="sprite">
                                Sprite
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Special Instructions -->
                <div class="customize-group">
                    <label class="customize-label">Special Instructions (Optional)</label>
                    <textarea id="specialInstructions" 
                              placeholder="Bijv. allergieën, voorkeur bezorgtijd, etc." 
                              rows="3"></textarea>
                </div>
            </div>
        </div>

        <!-- Right: Sticky Order Summary -->
        <aside class="customize-order-summary" id="orderSummary" data-base-price="<?php echo $item['price']; ?>">
            <div class="order-summary-header">
                <h3>YOUR ORDER</h3>
                <span class="order-item-count" id="orderItemCount">1 item</span>
            </div>

            <div class="order-summary-content">
                <div class="order-summary-item" id="orderSummaryItem">
                    <div class="order-item-image">
                        <img src="public/<?php echo htmlspecialchars($item['image_url'] ?? 'img/ramen1.png'); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>">
                    </div>
                    <div class="order-item-details">
                        <h4 class="order-item-name"><?php echo htmlspecialchars($item['name']); ?></h4>
                        <div class="order-item-options" id="orderItemOptions">
                            <!-- Customize opties worden hier dynamisch toegevoegd -->
                        </div>
                        <div class="order-item-quantity">
                            <button class="qty-btn" id="decreaseQty">-</button>
                            <span id="currentQuantity">1</span>
                            <button class="qty-btn" id="increaseQty">+</button>
                        </div>
                    </div>
                    <button class="order-item-remove" id="removeItem">×</button>
                </div>

                <div class="order-summary-delivery" id="deliveryMessage" style="display: none;">
                    <p>Add €<span id="deliveryAmount">0.00</span> more for free delivery!</p>
                </div>
            </div>

            <div class="order-summary-footer">
                <div class="order-summary-totals">
                    <div class="order-total-row">
                        <span>Subtotal:</span>
                        <span id="orderSubtotal">€<?php echo number_format($item['price'], 2, ',', '.'); ?></span>
                    </div>
                    <div class="order-total-row">
                        <span>Delivery:</span>
                        <span id="orderDelivery">€2,50</span>
                    </div>
                    <div class="order-total-row order-total-final">
                        <span>TOTAL:</span>
                        <span id="orderTotal">€<?php echo number_format($item['price'] + 2.50, 2, ',', '.'); ?></span>
                    </div>
                </div>
                <div class="order-summary-estimate">
                    <p>Estimated Delivery: <strong>25-35 minutes</strong></p>
                </div>
                <button class="order-checkout-btn" id="addToCartBtn">
                    Add to Cart →
                </button>
            </div>
        </aside>
    </div>
</section>

<?php require_once __DIR__ . '/views/includes/footer.php'; ?>
