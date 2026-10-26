<?php
// Stap 2: Overzicht voor het plaatsen van de bestelling
$pageTitle = 'Controleer je bestelling - Yume Ramen';
$currentPage = 'checkout';
$additionalCSS = ['checkout.css'];
$additionalJS = ['checkout.js'];
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Loading Screen -->
<div class="loading-screen" id="loadingScreen">
    <img src="public/img/Yume_logo.png" alt="Yume Ramen" class="loading-logo">
</div>

<?php require_once __DIR__ . '/../includes/nav.php'; ?>

<!-- Animated Background -->
<div id="sakura-container"></div>
<div class="blue-glow"></div>

<section class="checkout-section">
    <div class="container">
        <div class="checkout-header">
            <h1><span class="solid-text">AFRE</span><span class="outline-text">KENEN</span></h1>
            <p class="checkout-subtitle">Stap 2: overzicht &amp; bevestigen</p>
        </div>

        <!-- Step indicator (stap 3 actief zoals bij Yume) -->
        <div class="checkout-steps">
            <div class="checkout-step-indicator completed" data-step="1">
                <span class="step-number">1</span>
                <span class="step-label">Gegevens</span>
            </div>
            <div class="checkout-step-indicator completed" data-step="2">
                <span class="step-number">2</span>
                <span class="step-label">Betaling</span>
            </div>
            <div class="checkout-step-indicator active" data-step="3">
                <span class="step-number">3</span>
                <span class="step-label">Overzicht</span>
            </div>
        </div>

        <div class="checkout-layout">
            <!-- Links: Overzicht gegevens + definitief plaatsen -->
            <div class="checkout-main">
                <div class="checkout-card checkout-form">
                    <h2>Controleer je gegevens</h2>

                    <div class="review-box">
                        <div class="review-row">
                            <span class="review-label">Naam</span>
                            <span class="review-value">
                                <?php echo htmlspecialchars($checkoutData['name'] ?? ''); ?>
                            </span>
                        </div>
                        <div class="review-row">
                            <span class="review-label">Adres</span>
                            <span class="review-value">
                                <?php 
                                $street = $checkoutData['street'] ?? '';
                                $postal = $checkoutData['postal_code'] ?? '';
                                $city   = $checkoutData['city'] ?? '';
                                echo htmlspecialchars(trim("$street, $postal $city"));
                                ?>
                            </span>
                        </div>
                        <div class="review-row">
                            <span class="review-label">Bezorgmoment</span>
                            <span class="review-value">
                                <?php echo htmlspecialchars($checkoutData['delivery_time_display'] ?? 'Zo snel mogelijk (25–35 min)'); ?>
                            </span>
                        </div>
                        <div class="review-row">
                            <span class="review-label">Betaalmethode</span>
                            <span class="review-value">
                                <?php
                                $method = $checkoutData['payment_method'] ?? 'ideal';
                                if ($method === 'card') {
                                    echo 'Credit Card';
                                } elseif ($method === 'paypal') {
                                    echo 'PayPal';
                                } else {
                                    echo 'iDEAL';
                                }
                                ?>
                            </span>
                        </div>
                        <?php if (!empty($checkoutData['notes'])): ?>
                        <div class="review-row">
                            <span class="review-label">Opmerkingen</span>
                            <span class="review-value">
                                <?php echo nl2br(htmlspecialchars($checkoutData['notes'])); ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Korte samenvatting van je bestelling (client-side gevuld uit winkelwagen) -->
                    <div class="form-section">
                        <h3>Je gerechten</h3>
                        <div class="review-box" id="reviewOrderItems">
                            <p style="font-size:0.9rem;color:rgba(255,255,255,0.7);">
                                Je geselecteerde items worden hier geladen…
                            </p>
                        </div>
                    </div>

                    <div class="step-buttons" style="margin-top: 20px;">
                        <a href="checkout.php" class="secondary-btn">← Gegevens aanpassen</a>

                        <!-- Definitief plaatsen: gebruikt hetzelfde JS als voorheen -->
                        <form id="checkoutForm" method="post" style="flex: 1; text-align: right;">
                            <input type="hidden" name="name" value="<?php echo htmlspecialchars($checkoutData['name'] ?? ''); ?>">
                            <input type="hidden" name="email" value="<?php echo htmlspecialchars($checkoutData['email'] ?? ''); ?>">
                            <input type="hidden" name="phone" value="<?php echo htmlspecialchars($checkoutData['phone'] ?? ''); ?>">
                            <input type="hidden" name="street" value="<?php echo htmlspecialchars($checkoutData['street'] ?? ''); ?>">
                            <input type="hidden" name="postal_code" value="<?php echo htmlspecialchars($checkoutData['postal_code'] ?? ''); ?>">
                            <input type="hidden" name="city" value="<?php echo htmlspecialchars($checkoutData['city'] ?? ''); ?>">
                            <input type="hidden" name="payment_method" value="<?php echo htmlspecialchars($checkoutData['payment_method'] ?? 'ideal'); ?>">
                            <input type="hidden" name="notes" value="<?php echo htmlspecialchars($checkoutData['notes'] ?? ''); ?>">

                            <button type="submit" class="checkout-btn" id="placeOrderBtn">
                                <span class="btn-text">Bestelling plaatsen</span>
                                <span class="btn-loader" style="display: none;">⏳ Verwerken...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Rechts: Order Summary (zelfde als op stap 1) -->
            <aside class="checkout-summary">
                <div class="checkout-card order-summary">
                    <div class="order-summary-header">
                        <span class="order-icon">🛒</span>
                        <h2>JE BESTELLING</h2>
                        <span class="order-count" id="orderCount">0 items</span>
                    </div>
                    
                    <div class="free-delivery-progress">
                        <div class="progress-bar-wrapper">
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" id="deliveryProgressFill"></div>
                            </div>
                        </div>
                        <div class="free-delivery-message" id="freeDeliveryMessage">
                            Bestel nog €<span id="freeDeliveryAmount">25,00</span> voor gratis verzending!
                        </div>
                    </div>
                    
                    <div id="checkoutCartItems" class="checkout-items">
                        <p class="empty-message">Je winkelwagen is leeg</p>
                    </div>
                    
                    <div class="order-totals">
                        <div class="total-row">
                            <span>Subtotaal:</span>
                            <span id="subtotal">€0,00</span>
                        </div>
                        <div class="total-row">
                            <span>Bezorgkosten:</span>
                            <span id="deliveryCost">€2,50</span>
                        </div>
                        <div class="total-row total-final">
                            <span>TOTAAL:</span>
                            <span id="totalAmount">€2,50</span>
                        </div>
                        <div class="points-earned">
                            <span class="points-icon">⭐</span>
                            <span class="points-text">Je verdient <strong id="pointsEarned">0 punten</strong> bij deze bestelling</span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

