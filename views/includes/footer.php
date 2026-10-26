<!-- Footer -->
<footer class="footer">
    <div class="footer-content">
        <div class="footer-logo-column">
            <img src="public/img/Yume_logo.png" alt="Yume Ramen" class="footer-logo">
        </div>
        
        <div class="footer-column">
            <h4>NAVIGATION</h4>
            <a href="index.php">HOME</a>
            <a href="about.php">ABOUT</a>
            <a href="menu.php">MENU</a>
            <a href="#">DELIVERY</a>
            <a href="#">JOBS</a>
            <a href="#">LOYALTY & GIFTCARD</a>
            <a href="#">WEBSHOP</a>
            <a href="#">LOCATIONS</a>
            <a href="contact.php">CONTACT</a>
            <a href="account.php">BECOME A MEMBER</a>
        </div>
        
        <div class="footer-column">
            <h4>CONTACT</h4>
            <p>Witte de Withstraat 123</p>
            <p>3012 BR Rotterdam</p>
            <p>+31 10 316 3190</p>
            <p>info@yumeramen.nl <span class="footer-note">(not for reservations)</span></p>
            <p style="margin-top: 20px;">For reservations: +31 10 316 3190</p>
        </div>
        
        <div class="footer-column">
            <h4>HOURS</h4>
            <div class="opening-hours">
                <div class="hours-row">
                    <span>Monday</span>
                    <span>12:00 - 21:30</span>
                </div>
                <div class="hours-row">
                    <span>Tuesday</span>
                    <span>12:00 - 21:30</span>
                </div>
                <div class="hours-row">
                    <span>Wednesday</span>
                    <span>12:00 - 21:30</span>
                </div>
                <div class="hours-row">
                    <span>Thursday</span>
                    <span>12:00 - 21:30</span>
                </div>
                <div class="hours-row">
                    <span>Friday</span>
                    <span>12:00 - 22:00</span>
                </div>
                <div class="hours-row">
                    <span>Saturday</span>
                    <span>12:00 - 22:00</span>
                </div>
                <div class="hours-row">
                    <span>Sunday</span>
                    <span>12:00 - 21:00</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>© <?php echo date('Y'); ?> Yume Ramen Noodles | Rotterdam.</p>
        <p class="footer-credit">Site built by The Pixel Bakery</p>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/animejs@3.2.2/lib/anime.min.js"></script>
<script src="public/js/script.js"></script>
<?php if (isset($additionalJS)): ?>
    <?php foreach ($additionalJS as $js): ?>
        <script src="public/js/<?php echo $js; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
