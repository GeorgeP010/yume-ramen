<?php
$pageTitle = 'Afrekenen - Yume Ramen';
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
            <p class="checkout-subtitle">Stap 1: gegevens &amp; betaling</p>
        </div>

        <div class="checkout-layout">
            <div class="checkout-main">
                <!-- Left: Multi-step form -->
                <div class="checkout-card checkout-form">
                    <!-- Stap 1 formulier: stuurt naar overzicht (stap 2) -->
                    <form id="checkoutStep1Form" method="post" action="checkout.php">
                    <!-- STEP 1: Address & delivery time -->
                    <div class="checkout-step active" data-step="1">
                        <!-- Delivery Address -->
                        <div class="form-section">
                        <h3>Bezorgadres</h3>
                        
                        <?php if ($customer): ?>
                            <p class="form-note">Ingelogd als: <strong><?php echo htmlspecialchars($customer['name']); ?></strong></p>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="customerName">Naam *</label>
                            <input type="text" id="customerName" name="name" required 
                                   value="<?php echo htmlspecialchars($customer['name'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required 
                                   value="<?php echo htmlspecialchars($customer['email'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Telefoonnummer *</label>
                            <input type="tel" id="phone" name="phone" required 
                                   placeholder="06-12345678">
                        </div>
                        
                        <div class="form-group">
                            <label for="street">Straat + Huisnummer *</label>
                            <input type="text" id="street" name="street" required 
                                   value="<?php echo htmlspecialchars($address['street'] ?? ''); ?>"
                                   placeholder="Voorbeeldstraat 123">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="postalCode">Postcode *</label>
                                <input type="text" id="postalCode" name="postal_code" required 
                                       value="<?php echo htmlspecialchars($address['postal_code'] ?? ''); ?>"
                                       placeholder="1234 AB">
                            </div>
                            
                            <div class="form-group">
                                <label for="city">Plaats *</label>
                                <input type="text" id="city" name="city" required 
                                       value="<?php echo htmlspecialchars($address['city'] ?? ''); ?>"
                                       placeholder="Amsterdam">
                            </div>
                        </div>

                        <!-- Delivery time -->
                        <div class="form-section">
                            <h3>Bezorgmoment</h3>
                            <div class="delivery-time-options">
                                <label class="delivery-time-option">
                                    <input type="radio" name="delivery_time" value="asap" checked>
                                    <span>Zo snel mogelijk (25–35 min)</span>
                                </label>
                                <label class="delivery-time-option">
                                    <input type="radio" name="delivery_time" value="later">
                                    <span>Kies zelf een tijd</span>
                                </label>
                            </div>
                            <div class="form-group delivery-time-select" id="deliveryTimeSelect" style="display: none;">
                                <label for="deliveryTime">Gewenste tijd</label>
                                <!-- Hidden input that will be filled when a slot is chosen -->
                                <input type="hidden" id="deliveryTime" name="delivery_time_custom">
                                <div class="time-slot-list" id="deliveryTimeSlots">
                                    <!-- Time slot buttons are generated via JavaScript -->
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="form-section">
                            <h3>Opmerkingen (optioneel)</h3>
                            <textarea id="notes" name="notes" rows="3"
                                      placeholder="Bijv. allergieën, bel niet aanbellen, etc."></textarea>
                        </div>
                    </div>

                    <!-- STEP 2: Payment (same page, below address) -->
                    <div class="checkout-step" data-step="2">
                        <div class="form-section">
                            <h3>Betaalmethode</h3>
                            
                            <div class="payment-methods">
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="ideal" checked>
                                    <span class="payment-label">
                                        <span class="payment-icon"></span>
                                        <span class="payment-text">
                                            <span class="payment-title">iDEAL</span>
                                            <span class="payment-subtitle">Betaal direct met je eigen bank</span>
                                        </span>
                                    </span>
                                </label>
                                
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="card">
                                    <span class="payment-label">
                                        <span class="payment-icon"></span>
                                        <span class="payment-text">
                                            <span class="payment-title">Credit Card</span>
                                            <span class="payment-subtitle">Visa, Mastercard, Amex</span>
                                        </span>
                                    </span>
                                </label>
                                
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="paypal">
                                    <span class="payment-label">
                                        <span class="payment-icon"></span>
                                        <span class="payment-text">
                                            <span class="payment-title">PayPal</span>
                                            <span class="payment-subtitle">Betaal veilig via je PayPal-account</span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Verstuur stap 1 naar server voor overzicht (stap 2) -->
                        <button type="submit" class="checkout-btn">Doorgaan naar overzicht →</button>
                    </div>

                    <!-- STEP 3: Review + place order -->
                    <div class="checkout-step" data-step="3" id="reviewStep">
                        <div class="form-section">
                            <h3>Controleer je gegevens</h3>
                            <div class="review-box">
                                <div class="review-row">
                                    <span class="review-label">Naam</span>
                                    <span class="review-value" id="reviewName"></span>
                                </div>
                                <div class="review-row">
                                    <span class="review-label">Adres</span>
                                    <span class="review-value" id="reviewAddress"></span>
                                </div>
                                <div class="review-row">
                                    <span class="review-label">Bezorgmoment</span>
                                    <span class="review-value" id="reviewDeliveryTime"></span>
                                </div>
                                <div class="review-row">
                                    <span class="review-label">Betaalmethode</span>
                                    <span class="review-value" id="reviewPayment"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="checkout-btn" id="placeOrderBtn">
                            <span class="btn-text">Bestelling plaatsen</span>
                            <span class="btn-loader" style="display: none;">⏳ Verwerken...</span>
                        </button>
                    </div>
                    </form>
                </div>
            </div>
            
            <!-- Right: Order Summary (apart panel rechts) -->
            <aside class="checkout-summary">
                <div class="checkout-card order-summary">
                    <div class="order-summary-header">
                        <span class="order-icon">🛒</span>
                        <h2>JE BESTELLING</h2>
                        <span class="order-count" id="orderCount">0 items</span>
                    </div>
                    
                    <!-- Free Delivery Progress Bar -->
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
