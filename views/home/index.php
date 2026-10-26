<?php
$pageTitle = 'Yume Ramen - Authentieke Japanse Ramen in Rotterdam';
$currentPage = 'home';
$additionalJS = ['cart.js'];
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Loading Screen -->
<div class="loading-screen" id="loadingScreen">
    <img src="public/img/Yume_logo.png" alt="Yume Ramen" class="loading-logo">
</div>

<?php require_once __DIR__ . '/../includes/nav.php'; ?>

<section class="hero">
    
    <div id="sakura-container"></div> 
    <div class="blue-glow"></div> 
    <div class="bg-kanji">夢</div> 
    
    <div class="content-box">
        
        <p class="jp-label">夢のラーメン</p>
        
        <h1 class="big-title">
            <span class="solid-text">YUME</span><br>
            <span class="outline-text">RAMEN</span>
        </h1>

        <p class="slogan">PROEF DE KRACHT VAN DE DRAAK</p>

        <div class="buttons">
            <a href="menu.php" class="btn-primary">NU BESTELLEN</a>
            <a href="public/img/yume_menukaart.pdf" target="_blank" class="btn-text">BEKIJK MENU</a>
        </div>
    </div>

    <!-- Ramen Bowl Rechts -->
    <div class="ramen-showcase">
        <div class="steam-container">
            <div class="steam steam-1"></div>
            <div class="steam steam-2"></div>
            <div class="steam steam-3"></div>
        </div>
        <img src="public/img/ramen1.png" alt="Yume Ramen Bowl" class="ramen-image">
        <div class="price-tag">
            <span class="price-label">VANAF</span>
            <span class="price-amount">€12,90</span>
        </div>
    </div>

</section>

<!-- Populaire Gerechten Sectie -->
<section class="featured-section">
    <div class="section-container">
        <div class="section-header">
            <span class="section-label">人気メニュー</span>
            <h2 class="section-title">
                <span class="solid-text">POPULAIRE</span>
                <span class="outline-text">GERECHTEN</span>
            </h2>
        </div>
        
        <div class="featured-grid">
            <?php if (!empty($featuredItems)): ?>
                <?php foreach ($featuredItems as $item): ?>
                    <?php $itemName = urlencode($item['name']); ?>
                    <div class="featured-item" onclick="window.location.href='menu.php?item=<?php echo $itemName; ?>'">
                        <div class="featured-image">
                            <img src="public/<?php echo htmlspecialchars($item['image_url'] ?? 'img/ramen1.png'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <span class="featured-badge">
                                <?php 
                                $spiceLevel = $item['spice_level'] ?? 'mild';
                                $isVegetarian = isset($item['is_vegetarian']) ? (bool)$item['is_vegetarian'] : false;
                                
                                if ($spiceLevel === 'extra_spicy') {
                                    echo 'EXTRA PIKANT';
                                } elseif ($isVegetarian) {
                                    echo 'VEGGIE';
                                } else {
                                    echo 'BESTSELLER';
                                }
                                ?>
                            </span>
                        </div>
                        <div class="featured-info">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p><?php echo htmlspecialchars($item['description']); ?></p>
                            <div class="featured-price">€<?php echo number_format($item['price'], 2, ',', '.'); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback als database leeg is - toon 3 voorbeeld items -->
                <div class="featured-item" onclick="window.location.href='menu.php?item=Tonkotsu%20Ramen'">
                    <div class="featured-image">
                        <img src="public/img/ramen1.png" alt="Tonkotsu Ramen">
                        <span class="featured-badge">BESTSELLER</span>
                    </div>
                    <div class="featured-info">
                        <h3>Tonkotsu Ramen</h3>
                        <p>Rijke varkensbouillon met chashu, ajitsuke tamago en nori</p>
                        <div class="featured-price">€12,90</div>
                    </div>
                </div>
                <div class="featured-item" onclick="window.location.href='menu.php?item=Miso%20Ramen'">
                    <div class="featured-image">
                        <img src="public/img/miso ramen.png" alt="Miso Ramen">
                        <span class="featured-badge">VEGGIE</span>
                    </div>
                    <div class="featured-info">
                        <h3>Miso Ramen</h3>
                        <p>Hartige miso bouillon met gegrilde groenten en tofu</p>
                        <div class="featured-price">€13,00</div>
                    </div>
                </div>
                <div class="featured-item" onclick="window.location.href='menu.php?item=Spicy%20Tan%20Tan%20Ramen'">
                    <div class="featured-image">
                        <img src="public/img/spicy tentanmen ramen.png" alt="Spicy Tan Tan Ramen">
                        <span class="featured-badge hot">EXTRA PIKANT</span>
                    </div>
                    <div class="featured-info">
                        <h3>Spicy Tan Tan Ramen</h3>
                        <p>Pittige sesam bouillon met gehakt en chili olie</p>
                        <div class="featured-price">€15,00</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="section-cta">
            <a href="menu.php" class="cta-link">BEKIJK VOLLEDIG MENU →</a>
        </div>
    </div>
</section>

<!-- Scroll Video Sectie -->
<section class="video-scroll-section" id="videoScrollSection">
    <div class="video-container-scroll">
        <video id="scrollVideo" muted loop playsinline>
            <source src="public/video/ramen_video.mp4" type="video/mp4">
            Je browser ondersteunt geen video.
        </video>
    </div>
</section>

<!-- Over Ons Sectie -->
<section class="about-section">
    <div class="about-container">
        <div class="about-content">
            <span class="section-label">私たちについて</span>
            <h2 class="section-title">
                <span class="solid-text">AUTHENTIEK</span><br>
                <span class="outline-text">JAPANS</span>
            </h2>
            <p class="about-text">
                Bij Yume Ramen serveren we authentieke Japanse ramen met een moderne twist. 
                Onze bouillon kookt minimaal 12 uur voor de perfecte smaak. 
                Elke bowl wordt met liefde bereid, vers op bestelling.
            </p>
            <div class="about-features">
                <div class="feature-item">
                    <div class="feature-icon">🍜</div>
                    <div class="feature-text">
                        <h4>Verse Noedels</h4>
                        <p>Dagelijks vers gemaakt</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🔥</div>
                    <div class="feature-text">
                        <h4>12u Bouillon</h4>
                        <p>Langzaam gekookt</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🥢</div>
                    <div class="feature-text">
                        <h4>Authentiek</h4>
                        <p>Traditioneel Japans</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="about-image">
            <img src="public/img/ramen1.png" alt="Yume Ramen Kitchen">
        </div>
    </div>
</section>

<!-- Bestel Sectie -->
<section class="order-section">
    <div class="order-container">
        <span class="section-label">注文する</span>
        <h2 class="section-title">
            <span class="solid-text">BESTELLEN</span>
            <span class="outline-text">IS SIMPEL</span>
        </h2>
        <p class="order-subtitle">Kies je favoriete ramen en bestel direct online</p>
        
        <!-- Decoratieve cirkel met afbeelding -->
        <div class="order-circle-decoration">
            <img src="public/img/nav_foto2.png" alt="Ramen Bowl">
        </div>
        
        <div class="order-steps">
            <div class="step">
                <div class="step-number">01</div>
                <h3>Kies je ramen</h3>
                <p>Bekijk ons menu en selecteer je favoriete bowl</p>
            </div>
            <div class="step">
                <div class="step-number">02</div>
                <h3>Personaliseer</h3>
                <p>Extra toppings, pikantheit naar wens</p>
            </div>
            <div class="step">
                <div class="step-number">03</div>
                <h3>Ophalen of bezorgen</h3>
                <p>Klaar binnen 15 minuten of thuisbezorgd</p>
            </div>
        </div>
        
        <a href="menu.php" class="btn-primary large">START BESTELLING</a>
    </div>
</section>

<!-- Locatie Sectie -->
<section class="location-section">
    <div class="location-container">
        <div class="location-info">
            <span class="section-label">場所 • LOCATIE</span>
            <h2 class="section-title">
                <span class="solid-text">BEZOEK</span><br>
                <span class="outline-text">ONS</span>
            </h2>
            <div class="location-details">
                <div class="detail-item">
                    <div class="detail-icon">📍</div>
                    <div class="detail-text">
                        <h4>Adres</h4>
                        <p>Heer Bokelweg 255</p>
                        <p>3032 AD Rotterdam</p>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-icon">🕐</div>
                    <div class="detail-text">
                        <h4>Openingstijden</h4>
                        <p>Ma - Vr: 11:00 - 22:00</p>
                        <p>Za - Zo: 12:00 - 23:00</p>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-icon">📞</div>
                    <div class="detail-text">
                        <h4>Contact</h4>
                        <p>010 123 4567</p>
                        <p>info@yumeramen.nl</p>
                    </div>
                </div>
            </div>
            <a href="https://www.google.com/maps/search/?api=1&query=Heer+Bokelweg+255+Rotterdam" target="_blank" class="btn-primary">ROUTEBESCHRIJVING</a>
        </div>
        <div class="location-map">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2460.5426574638856!2d4.527820976947392!3d51.941937171876556!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c433c4f0c0b7f7%3A0x6c0c0c0c0c0c0c0!2sHeer%20Bokelweg%20255%2C%203032%20AD%20Rotterdam!5e0!3m2!1snl!2snl!4v1234567890123!5m2!1snl!2snl" 
                width="100%" 
                height="100%" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
