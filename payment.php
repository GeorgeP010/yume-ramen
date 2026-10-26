<?php
/**
 * Simulated payment screen
 * - Shown direct after placing an order
 * - Lets you mark the order as "active" or "completed"
 */

session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Database.php';

$orderId = isset($_GET['order_id']) ? (int) $_GET['order_id'] : 0;

if ($orderId <= 0) {
    header('Location: menu.php');
    exit;
}

$db = Database::getInstance();
$conn = $db->getConnection();

// Handle status update POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newStatus = $_POST['payment_status'] ?? 'active';

    // Only allow known statuses
    if (!in_array($newStatus, ['active', 'completed'], true)) {
        $newStatus = 'active';
    }

    $updateSql = "UPDATE orders SET status = :status WHERE order_id = :order_id";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->execute([
        ':status' => $newStatus,
        ':order_id' => $orderId,
    ]);

    // After updating, go to the normal confirmation page
    header('Location: order_confirmation.php?order_id=' . $orderId);
    exit;
}

// Fetch order info for display
$sql = "SELECT * FROM orders WHERE order_id = :order_id LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute([':order_id' => $orderId]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: menu.php');
    exit;
}

$pageTitle = 'Betaling - Yume Ramen';
$currentPage = 'payment';
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
        <div class="checkout-layout payment-layout-center">
            <div class="checkout-main">
                <div class="checkout-card checkout-form payment-card-centered">
                    <h1 class="checkout-title">
                        <span class="solid-text">BETA</span><span class="outline-text">LING</span>
                    </h1>
                    <p class="checkout-subtitle">
                        Dit is een test betaalscherm dat een iDEAL‑pagina nabootst. Kies hieronder de uitkomst van de betaling.
                    </p>

                    <div class="payment-summary-box">
                        <div class="payment-row">
                            <span>Bestelnummer</span>
                            <span>#<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></span>
                        </div>
                        <div class="payment-row">
                            <span>Bedrag</span>
                            <span>€<?php echo number_format($order['total_amount'], 2, ',', '.'); ?></span>
                        </div>
                        <?php if (!empty($order['payment_method'])): ?>
                            <div class="payment-row">
                                <span>Betaalmethode</span>
                                <span><?php echo strtoupper($order['payment_method']); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="payment-row">
                            <span>Huidige status</span>
                            <span class="status-badge">
                                <?php echo htmlspecialchars($order['status'] ?? 'active'); ?>
                            </span>
                        </div>
                    </div>

                    <form method="post" class="payment-status-form">
                        <h3>Kies test betaalscenario</h3>
                        <p class="payment-help-text">
                            Deze opties veranderen alleen de status van de bestelling in de database.
                        </p>

                        <div class="payment-options">
                            <label class="payment-option">
                                <input type="radio" name="payment_status" value="active" checked>
                                <span class="payment-label">
                                    <span class="payment-title">Actief</span>
                                    <span class="payment-subtitle">
                                        Bestelling is aangemaakt maar nog niet afgerond (openstaand).
                                    </span>
                                </span>
                            </label>

                            <label class="payment-option">
                                <input type="radio" name="payment_status" value="completed">
                                <span class="payment-label">
                                    <span class="payment-title">Completed</span>
                                    <span class="payment-subtitle">
                                        Betaling geslaagd en bestelling volledig afgerond.
                                    </span>
                                </span>
                            </label>
                        </div>

                        <button type="submit" class="checkout-btn" style="margin-top: 20px;">
                            Bevestig testbetaling →
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.payment-summary-box {
    margin-top: 20px;
    margin-bottom: 30px;
    padding: 16px 18px;
    border-radius: 12px;
    background: rgba(0, 0, 0, 0.35);
    border: 1px solid rgba(255, 255, 255, 0.08);
}
.payment-row {
    display: flex;
    justify-content: space-between;
    padding: 4px 0;
    color: #fff;
    font-size: 0.95rem;
}
.payment-row span:first-child {
    opacity: 0.8;
}
.payment-status-form h3 {
    margin-top: 10px;
    margin-bottom: 10px;
    font-size: 1.1rem;
    color: var(--gold);
}
.payment-help-text {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.65);
    margin-bottom: 14px;
}
.payment-options .payment-option {
    display: block;
    padding: 12px 14px;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.15);
    background: rgba(0, 0, 0, 0.4);
    margin-bottom: 10px;
    cursor: pointer;
}
.payment-options input[type="radio"] {
    margin-right: 10px;
}
.payment-title {
    font-weight: 700;
    color: #fff;
}
.payment-subtitle {
    display: block;
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.7);
    margin-top: 2px;
}

/* Centreer de payment card in het midden van het scherm */
.payment-layout-center {
    justify-content: center;
}

.payment-card-centered {
    max-width: 520px;
    margin: 0 auto;
}
</style>

<?php require_once __DIR__ . '/views/includes/footer.php'; ?>

