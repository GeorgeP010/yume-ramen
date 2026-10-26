<?php
$pageTitle = 'Over Ons - Yume Ramen';
$currentPage = 'about';
$additionalCSS = ['about.css'];
$additionalJS = ['script.js', 'cart.js'];
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Loading Screen -->
<div class="loading-screen" id="loadingScreen">
    <img src="public/img/Yume_logo.png" alt="Yume Ramen" class="loading-logo">
</div>

<?php require_once __DIR__ . '/../includes/nav.php'; ?>

<!-- About Hero Section -->
<section class="about-hero">
    <div class="about-hero-content">
        <span class="about-hero-label">私たちについて</span>
        <h1 class="about-hero-title">
            <span class="solid-text">ORIGINS OF</span><br>
            <span class="outline-text">YUME RAMEN</span>
        </h1>
        <p class="about-hero-subtitle">夢のラーメン</p>
    </div>
    <div class="about-hero-decoration">
        <div class="kanji-decoration">夢</div>
    </div>
</section>

<!-- Main About Section -->
<section class="about-main">
    <div class="about-main-container">
        <div class="about-intro">
            <h2 class="about-section-title">- ABOUT YUME -</h2>
            <p class="about-intro-text">
                <strong>Irasshaimase!</strong> Welkom bij Yume Ramen, waar onze passie voor authentieke Japanse ramen 
                tot leven komt. Ons verhaal begon met een droom - een droom om de beste ramen van Rotterdam te serveren.
            </p>
            <p class="about-intro-text">
                Ramen is de ultieme soul-food in de Japanse keuken – en wij zijn trots om dit te serveren in Rotterdam! 
                De basis van onze noedelsoepen zijn originele recepten en authentieke specerijen, die we speciaal uit Japan importeren. 
                Alleen de juiste noedels geven onze ramen de authentieke smaak, daarom komen ze helemaal uit Japan's noordelijkste 
                Hokkaido regio, waar ze worden geproduceerd en gefermenteerd onder strikte omstandigheden om onze soepen hun 
                sterke, onderscheidende smaak te geven.
            </p>
        </div>
    </div>

    <!-- Founders Section (full width) -->
    <div class="founders-section">
            <h2 class="about-section-title">- ONZE OPRICHTERS -</h2>

            <div class="founders-slider">
                <div class="founders-slide active" data-founder="edmilson">
                    <img src="public/img/Edmilson_about.png" alt="Edmilson - Mede-oprichter van Yume Ramen">
                    <div class="founder-overlay">
                        <span class="founder-label">Mede-oprichter</span>
                        <h3 class="founder-overlay-name">Edmilson</h3>
                        <p class="founder-overlay-text">
                            Creatieve technicus achter Yume Ramen, die moderne presentatie combineert met authentieke smaken.
                        </p>
                    </div>
                </div>

                <div class="founders-slide" data-founder="george">
                    <img src="public/img/George_about.png" alt="George - Mede-oprichter van Yume Ramen">
                    <div class="founder-overlay">
                        <span class="founder-label">Mede-oprichter</span>
                        <h3 class="founder-overlay-name">George</h3>
                        <p class="founder-overlay-text">
                            Ramen liefhebber met een passie voor Japanse keuken en de drijvende kracht achter onze bouillons.
                        </p>
                    </div>
                </div>

                <div class="founders-slider-controls">
                    <button class="founder-toggle-btn active" data-target="edmilson" aria-label="Vorige oprichter">‹</button>
                    <button class="founder-toggle-btn" data-target="george" aria-label="Volgende oprichter">›</button>
                </div>
            </div>
        </div>

    <!-- Ramen Highlight Section tussen oprichters en missie -->
    <section class="ramen-highlight">
        <h2 class="ramen-highlight-title">- ONZE SIGNATURE RAMEN -</h2>
        <p class="ramen-highlight-intro">
            Elke bowl bij Yume Ramen heeft zijn eigen verhaal. Ontdek de smaken achter onze populairste gerechten.
        </p>
        <div class="ramen-highlight-grid">
            <article class="ramen-card">
                <div class="ramen-card-image">
                    <img src="public/img/draak ramen.png" alt="Tonkotsu Ramen">
                </div>
                <div class="ramen-card-content">
                    <h3 class="ramen-card-name">Tonkotsu Ramen</h3>
                    <p class="ramen-card-tagline">Rijke & romige varkensbouillon</p>
                    <p class="ramen-card-text">
                        12 uur getrokken bouillon van varkensbotten, geserveerd met malse chashu, ajitsuke tamago, nori en bamboescheuten.
                        Diepe umami, volle smaak – dit is onze ultieme comfort bowl.
                    </p>
                </div>
            </article>
            <article class="ramen-card">
                <div class="ramen-card-image">
                    <img src="public/img/miso ramen.png" alt="Miso Ramen">
                </div>
                <div class="ramen-card-content">
                    <h3 class="ramen-card-name">Miso Ramen</h3>
                    <p class="ramen-card-tagline">Hartig & vegetarisch</p>
                    <p class="ramen-card-text">
                        Hartige miso-bouillon met gegrilde seizoensgroenten en tofu. Licht rokerig, vol van smaak en perfect in balans
                        tussen romig en fris.
                    </p>
                </div>
            </article>
            <article class="ramen-card">
                <div class="ramen-card-image">
                    <img src="public/img/spicy tentanmen ramen.png" alt="Spicy Tan Tan Ramen">
                </div>
                <div class="ramen-card-content">
                    <h3 class="ramen-card-name">Spicy Tan Tan Ramen</h3>
                    <p class="ramen-card-tagline">Voor wie van pit houdt</p>
                    <p class="ramen-card-text">
                        Romige sesam-bouillon met gekruid gehakt, chili-olie en knapperige toppings. Eerst zacht en nootachtig,
                        daarna een langzame, verslavende hitte.
                    </p>
                </div>
            </article>
        </div>
    </section>

    <!-- Kernwaarden sectie voor extra overzicht -->
    <section class="values-section">
        <h2 class="about-section-title">- WAAROM YUME RAMEN -</h2>
        <div class="values-grid">
            <div class="value-item">
                <div class="value-icon">🍜</div>
                <h3>Authentieke Bouillons</h3>
                <p>
                    12 uur lang getrokken bouillons, rechtstreeks geïnspireerd op Japanse ramen shops.
                    Geen shortcuts, alleen diepe umami‑smaak in elke bowl.
                </p>
            </div>
            <div class="value-item">
                <div class="value-icon">🥢</div>
                <h3>Japanse Noedels</h3>
                <p>
                    Noedels uit Hokkaido met de juiste bite, gecombineerd met lokale ingrediënten van topkwaliteit.
                </p>
            </div>
            <div class="value-item">
                <div class="value-icon">🌸</div>
                <h3>Yume Experience</h3>
                <p>
                    Een warme, speelse sfeer, high‑quality service en een loyalty systeem waarmee je spaart voor gratis ramen.
                </p>
            </div>
        </div>
    </section>

    <div class="about-main-container">
        <!-- Story Section -->
        <div class="story-section">
            <div class="story-content">
                <h2 class="about-section-title">- ONZE MISSIE -</h2>
                <p class="story-text">
                    Yume Ramen werd opgericht met één doel: om de beste authentieke Japanse ramen naar Rotterdam te brengen. 
                    George en Edmilson werkten samen aan de ontwikkeling van originele recepten, waarbij ze de traditionele 
                    Japanse technieken combineerden met lokale ingrediënten van de hoogste kwaliteit.
                </p>
                <p class="story-text">
                    Elke keer dat je Yume Ramen bezoekt, ontvang je punten met je eigen Yume key chain. Wanneer je genoeg 
                    punten hebt verzameld, geniet je van een gyoza of ramen op het huis. Vergeet niet je punten te claimen 
                    en mis je beloningen niet! Zie de loyalty pagina voor meer informatie.
                </p>
                <a href="account.php" class="btn-primary">WORD LID</a>
            </div>
            <div class="story-image">
                <img src="public/img/ramen1.png" alt="Yume Ramen Kitchen">
            </div>
        </div>

    </div>
</section>

<!-- Winkelwagen Float Button (zelfde als op home/menu) -->
<div class="cart-float" id="cartFloat" onclick="toggleCart()">
    <span class="cart-icon">🛒</span>
    <span class="cart-count" id="cartCount">0</span>
</div>

<!-- Winkelwagen Sidebar (gedeelde cart) -->
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
