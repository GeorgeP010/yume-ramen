<nav>
    <a href="index.php" class="nav-logo-link" aria-label="Yume Ramen Home">
        <img src="public/img/Yume_logo.png" alt="Yume Ramen Logo" class="nav-logo">
    </a>
    
    <ul class="nav-links">
        <li>
            <a href="index.php" class="<?php echo (isset($currentPage) && $currentPage === 'home') ? 'active' : ''; ?>">
                HOME
            </a>
        </li>
        <li>
            <a href="menu.php" class="<?php echo (isset($currentPage) && $currentPage === 'menu') ? 'active' : ''; ?>">
                MENU
            </a>
        </li>
        <li>
            <a href="about.php" class="<?php echo (isset($currentPage) && $currentPage === 'about') ? 'active' : ''; ?>">
                ABOUT
            </a>
        </li>
        <li>
            <a href="contact.php" class="<?php echo (isset($currentPage) && $currentPage === 'contact') ? 'active' : ''; ?>">
                CONTACT
            </a>
        </li>
    </ul>

    
    <!-- Account Icon -->
    <a href="account.php" class="account-icon <?php echo (isset($currentPage) && $currentPage === 'account') ? 'active' : ''; ?>" aria-label="Account">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
        </svg>
    </a>
    
    <!-- Hamburger Menu Button -->
    <button class="hamburger" id="hamburger" aria-label="Menu">
        <span></span>
        <span></span>
        <span></span>
    </button>
</nav>

<!-- Fullscreen Menu Overlay -->
<div class="menu-overlay" id="menuOverlay">
    <img src="public/img/Yume_icon.png" alt="Yume Icon" class="menu-icon">
    <img src="public/img/Yume_logo.png" alt="Yume Logo" class="menu-logo">
    <img src="public/img/nav_foto.png" alt="Ramen Bowl" class="menu-image">
    <button class="menu-close" id="menuClose">✕</button>
    <nav class="fullscreen-nav">
        <a href="index.php"
           class="menu-link <?php echo (isset($currentPage) && $currentPage === 'home') ? 'active' : ''; ?>">
            HOME
        </a>
        <a href="menu.php"
           class="menu-link <?php echo (isset($currentPage) && $currentPage === 'menu') ? 'active' : ''; ?>">
            MENU
        </a>
        <a href="about.php"
           class="menu-link <?php echo (isset($currentPage) && $currentPage === 'about') ? 'active' : ''; ?>">
            ABOUT
        </a>
        <a href="contact.php"
           class="menu-link <?php echo (isset($currentPage) && $currentPage === 'contact') ? 'active' : ''; ?>">
            CONTACT
        </a>
    </nav>
    
    <div class="menu-footer">
        <div class="menu-social">
            <a href="#">Instagram</a>
            <a href="#">Facebook</a>
        </div>
        <p class="menu-info">夢 • Crafted in Rotterdam</p>
    </div>
</div>
