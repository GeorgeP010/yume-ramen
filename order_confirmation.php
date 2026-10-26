<?php
/**
 * Order Confirmation Page
 */

// Start session
session_start();

// Load configuration
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Database.php';

// Get order ID from URL
$orderId = $_GET['order_id'] ?? null;

if (!$orderId) {
    header('Location: menu.php');
    exit;
}

// Fetch order details
$db = Database::getInstance();
$conn = $db->getConnection();

$sql = "SELECT * FROM orders WHERE order_id = :order_id LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute([':order_id' => $orderId]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: menu.php');
    exit;
}

// Fetch order items
$itemsSql = "SELECT * FROM order_items WHERE order_id = :order_id";
$itemsStmt = $conn->prepare($itemsSql);
$itemsStmt->execute([':order_id' => $orderId]);
$orderItems = $itemsStmt->fetchAll();

// Loyalty points earned for this order (1 punt per euro, afgerond naar beneden)
$pointsEarned = (int) floor((float) ($order['total_amount'] ?? 0));

$pageTitle = 'Bestelling Bevestigd - Yume Ramen';
$currentPage = 'confirmation';
$additionalCSS = ['checkout.css'];
require_once __DIR__ . '/views/includes/header.php';
?>

<!-- Loading Screen -->
<div class="loading-screen" id="loadingScreen">
    <img src="public/img/Yume_logo.png" alt="Yume Ramen" class="loading-logo">
</div>

<?php require_once __DIR__ . '/views/includes/nav.php'; ?>

<!-- Animated Background -->
<div id="sakura-container"></div>
<div class="blue-glow"></div>

<section class="checkout-section">
    <div class="container">
        <div class="confirmation-container">
            <div class="confirmation-icon">🍜</div>
            
            <h1 class="confirmation-title">
                <span class="solid-text">BESTELLING</span>
                <span class="outline-text">BEVESTIGD</span>
            </h1>
            
            <p class="confirmation-subtitle">Bedankt voor je bestelling!</p>
            
            <div class="order-number">
                <span>Bestelnummer:</span>
                <strong>#<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></strong>
            </div>
            
            <div class="checkout-card confirmation-card">
                <h3 class="confirmation-section-title">Bestellingsdetails</h3>
                
                <div class="order-info">
                    <div class="info-row">
                        <span>Status:</span>
                        <span class="status-badge"><?php echo ucfirst($order['status'] === 'active' ? 'Besteld' : $order['status']); ?></span>
                    </div>
                    <div class="info-row">
                        <span>Besteldatum:</span>
                        <span><?php echo date('d-m-Y', strtotime($order['order_date'])); ?></span>
                    </div>
                    <?php if (!empty($order['payment_method'])): ?>
                    <div class="info-row">
                        <span>Betaalmethode:</span>
                        <span><?php echo strtoupper($order['payment_method']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <h4 class="confirmation-section-title">Bestelde Items</h4>
                <?php foreach ($orderItems as $item): ?>
                    <div class="checkout-item">
                        <div class="checkout-item-info">
                            <h4><?php echo htmlspecialchars($item['item_name']); ?></h4>
                            <?php if (!empty($item['special_requests'])): ?>
                                <p class="item-customizations" style="font-size: 0.85rem; color: rgba(255,255,255,0.7); margin-top: 4px;">
                                    <?php echo htmlspecialchars($item['special_requests']); ?>
                                </p>
                            <?php endif; ?>
                            <p><?php echo $item['quantity']; ?>x €<?php echo number_format($item['price'], 2, ',', '.'); ?></p>
                        </div>
                        <div class="checkout-item-price">
                            €<?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="order-totals confirmation-totals">
                    <div class="total-row total-final">
                        <span>Totaal betaald:</span>
                        <span>€<?php echo number_format($order['total_amount'], 2, ',', '.'); ?></span>
                    </div>
                    <?php if ($pointsEarned > 0): ?>
                    <div class="loyalty-earned-row">
                        <span class="loyalty-icon">⭐</span>
                        <span class="loyalty-text">
                            Je hebt <strong><?php echo $pointsEarned; ?> punten</strong> verdiend met deze bestelling.
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <h4 class="confirmation-section-title">Bezorgadres</h4>
                <div class="address-box">
                    <?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?>
                </div>
                
                <?php if (!empty($order['notes'])): ?>
                    <h4 class="confirmation-section-title">Opmerkingen</h4>
                    <p class="confirmation-notes"><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="confirmation-actions">
                <a href="menu.php" class="btn-secondary">Terug naar Menu</a>
                <?php if (isset($_SESSION['customer_id'])): ?>
                    <a href="account.php" class="btn-primary">Mijn Bestellingen</a>
                <?php endif; ?>
            </div>
            
            <p class="delivery-estimate">
                🚚 Geschatte bezorgtijd: <strong>30-45 minuten</strong>
            </p>
        </div>
    </div>
</section>

<style>
.confirmation-container {
    text-align: center;
    max-width: 900px;
    margin: 20px auto 0;
}

.confirmation-icon {
    font-size: 4rem;
    margin-bottom: 16px;
    animation: scaleIn 0.5s ease;
}

@keyframes scaleIn {
    from { transform: scale(0); }
    to { transform: scale(1); }
}

.confirmation-title {
    font-size: clamp(2rem, 5vw, 3.5rem);
    margin-bottom: 10px;
    letter-spacing: 0.08em;
}

.confirmation-subtitle {
    color: rgba(255, 255, 255, 0.7);
    font-size: 1.2rem;
    margin-bottom: 30px;
}

.order-number {
    display: inline-block;
    padding: 15px 30px;
    background: rgba(0, 102, 204, 0.22);
    border: 2px solid var(--gold);
    border-radius: 10px;
    margin-bottom: 30px;
}

.order-number strong {
    color: var(--gold);
    font-size: 1.5rem;
    margin-left: 10px;
}

.confirmation-card {
    max-width: 700px;
    margin: 30px auto;
}

.confirmation-section-title {
    margin-top: 30px;
    margin-bottom: 15px;
    color: var(--gold);
    font-size: 1.2rem;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

.confirmation-totals {
    margin-top: 20px;
}

.order-info {
    margin-bottom: 20px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    color: #fff;
}

.status-badge {
    background: rgba(0, 102, 204, 0.25);
    color: var(--gold);
    padding: 4px 12px;
    border-radius: 20px;
    font-weight: 600;
    text-transform: capitalize;
}

.address-box {
    background: rgba(255, 255, 255, 0.05);
    padding: 15px;
    border-radius: 8px;
    color: #ccc;
    text-align: left;
}

.confirmation-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin: 40px 0;
}

.btn-primary, .btn-secondary {
    padding: 15px 30px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 900;
    transition: all 0.3s ease;
}

.btn-primary {
    background: var(--gold);
    color: var(--black);
}

.btn-secondary {
    background: rgba(0, 0, 0, 0.4);
    border: 2px solid var(--blue);
    color: #fff;
}

.btn-primary:hover, .btn-secondary:hover {
    transform: translateY(-2px);
}

.delivery-estimate {
    color: rgba(255, 255, 255, 0.7);
    font-size: 1.1rem;
    margin-top: 30px;
}

.delivery-estimate strong {
    color: var(--gold);
}

.confirmation-notes {
    color: rgba(255, 255, 255, 0.85);
    text-align: left;
}

@media (max-width: 768px) {
    .confirmation-card {
        margin: 20px auto;
        padding: 20px;
    }

    .confirmation-actions {
        flex-direction: column;
    }
}
</style>

<?php require_once __DIR__ . '/views/includes/footer.php'; ?>
