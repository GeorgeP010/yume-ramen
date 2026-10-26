<?php
$pageTitle = 'Contact - Yume Ramen';
$currentPage = 'contact';
$additionalCSS = ['contact.css'];
$additionalJS = ['script.js', 'cart.js'];
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Loading Screen -->
<div class="loading-screen" id="loadingScreen">
    <img src="public/img/Yume_logo.png" alt="Yume Ramen" class="loading-logo">
</div>

<?php require_once __DIR__ . '/../includes/nav.php'; ?>

<!-- Contact Hero Section -->
<section class="contact-hero">
    <div class="contact-hero-content">
        <span class="contact-hero-label">お問い合わせ</span>
        <h1 class="contact-hero-title">
            <span class="solid-text">CONTACT</span><br>
            <span class="outline-text">ONS</span>
        </h1>
        <p class="contact-hero-subtitle">Neem contact met ons op</p>
    </div>
</section>

<!-- Contact Main Section -->
<section class="contact-main">
    <div class="contact-container">
        <!-- Contact Info & Form Grid -->
        <div class="contact-grid">
            <!-- Left: Contact Information -->
            <div class="contact-info">
                <h2 class="contact-section-title">- CONTACTGEGEVENS -</h2>
                
                <div class="contact-details">
                    <div class="contact-detail-item">
                        <div class="contact-icon">📍</div>
                        <div class="contact-detail-text">
                            <h3>Adres</h3>
                            <p>Heer Bokelweg 255</p>
                            <p>3032 AD Rotterdam</p>
                        </div>
                    </div>
                    
                    <div class="contact-detail-item">
                        <div class="contact-icon">📞</div>
                        <div class="contact-detail-text">
                            <h3>Telefoon</h3>
                            <p>010 123 4567</p>
                            <p class="contact-note">Voor reserveringen: +31 10 123 4567</p>
                        </div>
                    </div>
                    
                    <div class="contact-detail-item">
                        <div class="contact-icon">✉️</div>
                        <div class="contact-detail-text">
                            <h3>Email</h3>
                            <p>info@yumeramen.nl</p>
                            <p class="contact-note">(niet voor reserveringen)</p>
                        </div>
                    </div>
                    
                    <div class="contact-detail-item">
                        <div class="contact-icon">🕐</div>
                        <div class="contact-detail-text">
                            <h3>Openingstijden</h3>
                            <div class="opening-hours-list">
                                <div class="hours-row">
                                    <span>Maandag - Vrijdag</span>
                                    <span>11:00 - 22:00</span>
                                </div>
                                <div class="hours-row">
                                    <span>Zaterdag - Zondag</span>
                                    <span>12:00 - 23:00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right: Contact Form -->
            <div class="contact-form-wrapper">
                <h2 class="contact-section-title">- STUUR ONS EEN BERICHT -</h2>
                <form class="contact-form" id="contactForm">
                    <div class="form-group">
                        <label for="name">Naam *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Telefoon</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Onderwerp *</label>
                        <select id="subject" name="subject" required>
                            <option value="">Selecteer een onderwerp</option>
                            <option value="reservation">Reservering</option>
                            <option value="question">Vraag</option>
                            <option value="complaint">Klacht</option>
                            <option value="feedback">Feedback</option>
                            <option value="other">Anders</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Bericht *</label>
                        <textarea id="message" name="message" rows="6" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn-primary">VERSTUUR</button>
                </form>
            </div>
        </div>
        
        <!-- Map Section -->
        <div class="contact-map-section">
            <h2 class="contact-section-title">- VIND ONS -</h2>
            <div class="contact-map">
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
            <a href="https://www.google.com/maps/search/?api=1&query=Heer+Bokelweg+255+Rotterdam" target="_blank" class="btn-primary">ROUTEBESCHRIJVING</a>
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

<script>
// Contact form handling
document.getElementById('contactForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form data
    const formData = new FormData(this);
    
    // Simple validation
    if (!formData.get('name') || !formData.get('email') || !formData.get('message')) {
        alert('Vul alle verplichte velden in');
        return;
    }
    
    // For now, just show an alert (you can add actual form submission later)
    alert('Bedankt voor je bericht! We nemen zo snel mogelijk contact met je op.');
    this.reset();
});
</script>
