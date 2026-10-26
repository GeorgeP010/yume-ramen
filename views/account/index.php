<?php
$pageTitle = 'Mijn Account - Yume Ramen';
$currentPage = 'account';
$additionalCSS = ['account.css'];
$additionalJS = ['account.js', 'cart.js'];
require_once __DIR__ . '/../includes/header.php';
?>

<?php require_once __DIR__ . '/../includes/nav.php'; ?>

<!-- Account Container -->
<div class="account-container">
    <div class="bg-kanji-account">夢</div>

    <?php if (!$isLoggedIn): ?>
    <!-- Login/Register Section (Shown when not logged in) -->
    <div class="auth-section" id="authSection">
        <div class="auth-tabs">
            <button class="auth-tab active" data-tab="login">Inloggen</button>
            <button class="auth-tab" data-tab="register">Registreren</button>
        </div>

        <!-- Login Form -->
        <div class="auth-form active" id="loginForm">
            <h2>Welkom Terug!</h2>
            <p class="auth-subtitle">Log in om je bestellingen te bekijken</p>
            
            <form id="loginFormElement">
                <div class="form-group">
                    <label for="loginEmail">E-mailadres</label>
                    <input type="email" id="loginEmail" name="email" required placeholder="voorbeeld@email.nl" autocomplete="email">
                </div>
                
                <div class="form-group">
                    <label for="loginPassword">Wachtwoord</label>
                    <input type="password" id="loginPassword" name="password" required placeholder="••••••••" autocomplete="current-password">
                </div>
                
                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" id="rememberMe">
                        <span>Onthoud mij</span>
                    </label>
                    <a href="#" class="forgot-password">Wachtwoord vergeten?</a>
                </div>
                
                <button type="submit" class="btn-primary">Inloggen</button>
                
                <p class="error-message" id="loginError"></p>
                
                <div class="auth-divider">
                    <span>of</span>
                </div>
                
                <p class="auth-hint">Test: test@test.nl / test</p>
            </form>
        </div>

        <!-- Register Form -->
        <div class="auth-form" id="registerForm">
            <h2>Maak een Account</h2>
            <p class="auth-subtitle">Word lid van de Yume familie</p>
            
            <form id="registerFormElement">
                <div class="form-group">
                    <label for="registerName">Naam</label>
                    <input type="text" id="registerName" name="name" required placeholder="Je volledige naam">
                </div>
                
                <div class="form-group">
                    <label for="registerEmail">E-mailadres</label>
                    <input type="email" id="registerEmail" name="email" required placeholder="voorbeeld@email.nl">
                </div>
                
                <div class="form-group">
                    <label for="registerPassword">Wachtwoord</label>
                    <input type="password" id="registerPassword" name="password" required placeholder="Minimaal 6 tekens">
                </div>
                
                <div class="form-group">
                    <label for="registerPasswordConfirm">Bevestig Wachtwoord</label>
                    <input type="password" id="registerPasswordConfirm" name="password_confirm" required placeholder="Herhaal je wachtwoord">
                </div>
                
                <label class="checkbox-label" style="margin-bottom: 20px;">
                    <input type="checkbox" id="acceptTerms" required>
                    <span>Ik ga akkoord met de voorwaarden</span>
                </label>
                
                <button type="submit" class="btn-primary">Account Aanmaken</button>
                
                <p class="error-message" id="registerError"></p>
            </form>
        </div>
    </div>

    <?php else: ?>
    <!-- Profile Section (Shown when logged in) -->
    <div class="profile-section" id="profileSection">
        
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-avatar-container">
                <?php 
                // Lees eerst uit de sessie (laatste keuze), daarna uit de database
                $sessionIcon = isset($_SESSION['profile_icon']) ? (int) $_SESSION['profile_icon'] : null;
                if ($sessionIcon >= 1 && $sessionIcon <= 8) {
                    $profileIcon = $sessionIcon;
                } elseif (isset($customer['profile_icon']) && $customer['profile_icon'] >= 1 && $customer['profile_icon'] <= 8) {
                    $profileIcon = (int) $customer['profile_icon'];
                } else {
                    $profileIcon = null;
                }

                $avatarSrc = $profileIcon 
                    ? "public/img/icon{$profileIcon}.jpg" 
                    : "https://api.dicebear.com/7.x/avataaars/svg?seed=" . urlencode($customer['email'] ?? 'default');
                ?>
                <img id="userAvatar" src="<?php echo htmlspecialchars($avatarSrc); ?>" alt="Avatar" class="profile-avatar">
                <button class="change-avatar-btn" id="changeAvatarBtn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                        <circle cx="12" cy="13" r="4"></circle>
                    </svg>
                </button>
            </div>
            
            <div class="profile-info">
                <h1 id="userName"><?php echo htmlspecialchars($customer['name'] ?? 'Gebruiker'); ?></h1>
                <p id="userEmail"><?php echo htmlspecialchars($customer['email'] ?? ''); ?></p>
                <span class="member-badge">夢 YUME MEMBER</span>
            </div>

            <button class="logout-btn" id="logoutBtn" onclick="window.location.href='auth.php?action=logout'">Uitloggen</button>
        </div>

        <!-- Avatar Selection Modal -->
        <div class="avatar-modal hidden" id="avatarModal">
            <div class="avatar-modal-content">
                <button class="modal-close" id="closeAvatarModal">✕</button>
                <h2>Kies je Avatar</h2>
                <p class="modal-subtitle">Selecteer een anime karakter</p>
                
                <div class="avatar-grid" id="avatarGrid">
                    <!-- Avatars worden hier dynamisch toegevoegd -->
                </div>
            </div>
        </div>

        <!-- Profile Tabs -->
        <div class="profile-tabs">
            <button class="profile-tab active" data-tab="orders">Mijn Bestellingen</button>
            <button class="profile-tab" data-tab="info">Account Info</button>
            <button class="profile-tab" data-tab="favorites">Favorieten</button>
        </div>

        <!-- Profile Content -->
        <div class="profile-content">
            
            <!-- Orders Tab -->
            <div class="profile-tab-content active" id="ordersTab">
                <div class="orders-header">
                    <h2>Mijn Bestellingen</h2>
                    <div class="order-filters">
                        <button class="filter-btn active" data-filter="all">Alle</button>
                        <button class="filter-btn" data-filter="active">Actief</button>
                        <button class="filter-btn" data-filter="completed">Afgerond</button>
                    </div>
                </div>
                
                <div class="orders-list" id="ordersList">
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <?php
                                // Ondersteun zowel 'order_status' als 'status' kolomnamen
                                $orderStatus = $order['order_status'] ?? ($order['status'] ?? 'pending');
                            ?>
                            <div class="order-card" 
                                 data-status="<?php echo htmlspecialchars($orderStatus); ?>" 
                                 data-order-id="<?php echo (int) $order['order_id']; ?>">
                                <div class="order-header">
                                    <div>
                                        <h3>Bestelling #<?php echo htmlspecialchars($order['order_number']); ?></h3>
                                        <p class="order-date">
                                            <?php
                                                // Sommige databases gebruiken 'created_at', andere 'order_date'
                                                $orderDate = $order['created_at'] ?? ($order['order_date'] ?? null);
                                                echo $orderDate ? date('d-m-Y H:i', strtotime($orderDate)) : '-';
                                            ?>
                                        </p>
                                    </div>
                                    <span class="order-status status-<?php echo htmlspecialchars($orderStatus); ?>">
                                        <?php 
                                        $statusLabels = [
                                            'pending' => 'In afwachting',
                                            'confirmed' => 'Bevestigd',
                                            'preparing' => 'In bereiding',
                                            'ready' => 'Klaar',
                                            'delivered' => 'Bezorgd',
                                            'cancelled' => 'Geannuleerd'
                                        ];
                                        echo $statusLabels[$orderStatus] ?? $orderStatus;
                                        ?>
                                    </span>
                                </div>
                                <div class="order-details">
                                    <p><span class="order-items-count"><?php echo (int) $order['item_count']; ?></span> items</p>
                                    <p class="order-total">Totaal: <strong>€<?php echo number_format($order['total_amount'], 2, ',', '.'); ?></strong></p>
                                </div>
                                <p class="order-more">Klik om details te bekijken en opnieuw te bestellen</p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="empty-state">Je hebt nog geen bestellingen geplaatst</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Account Info Tab -->
            <div class="profile-tab-content" id="infoTab">
                <h2>Account Informatie</h2>
                
                <div class="info-cards">
                    <div class="info-card">
                        <h3>Persoonlijke Gegevens</h3>
                        <div id="profileInfoDisplay">
                            <div class="info-row">
                                <span class="info-label">Naam:</span>
                                <span class="info-value" id="displayName"><?php echo htmlspecialchars($customer['name'] ?? 'Onbekend'); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">E-mail:</span>
                                <span class="info-value" id="displayEmail"><?php echo htmlspecialchars($customer['email'] ?? ''); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Lid sinds:</span>
                                <span class="info-value" id="memberSince"><?php echo isset($customer['member_since']) ? date('d-m-Y', strtotime($customer['member_since'])) : '-'; ?></span>
                            </div>
                            <button class="btn-secondary" onclick="showEditProfile()">Bewerken</button>
                        </div>
                        
                        <div id="profileInfoEdit" style="display: none;">
                            <form id="editProfileForm">
                                <div class="form-group">
                                    <label>Naam:</label>
                                    <input type="text" id="editName" name="name" value="<?php echo htmlspecialchars($customer['name'] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>E-mail:</label>
                                    <input type="email" value="<?php echo htmlspecialchars($customer['email'] ?? ''); ?>" disabled>
                                    <small style="color: #888;">Email kan niet worden gewijzigd</small>
                                </div>
                                <div style="display: flex; gap: 10px; margin-top: 15px;">
                                    <button type="submit" class="btn-primary">Opslaan</button>
                                    <button type="button" class="btn-secondary" onclick="cancelEditProfile()">Annuleren</button>
                                </div>
                                <p class="error-message" id="profileEditError"></p>
                            </form>
                        </div>
                    </div>

                    <div class="info-card">
                        <h3>Bezorgadres</h3>
                        <div id="addressDisplay">
                            <div class="info-row">
                                <span class="info-label">Adres:</span>
                                <span class="info-value" id="displayAddress"><?php echo isset($address['street']) ? htmlspecialchars($address['street']) : 'Nog niet ingesteld'; ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Postcode:</span>
                                <span class="info-value" id="displayZipcode"><?php echo isset($address['postal_code']) ? htmlspecialchars($address['postal_code']) : '-'; ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Stad:</span>
                                <span class="info-value" id="displayCity"><?php echo isset($address['city']) ? htmlspecialchars($address['city']) : '-'; ?></span>
                            </div>
                            <button class="btn-secondary" onclick="showEditAddress()">Toevoegen/Bewerken</button>
                        </div>
                        
                        <div id="addressEdit" style="display: none;">
                            <form id="editAddressForm">
                                <div class="form-group">
                                    <label>Adres:</label>
                                    <input type="text" id="editAddress" name="address" placeholder="Straat + huisnummer" value="<?php echo isset($address['street']) ? htmlspecialchars($address['street']) : ''; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Postcode:</label>
                                    <input type="text" id="editZipcode" name="zipcode" placeholder="1234 AB" value="<?php echo isset($address['postal_code']) ? htmlspecialchars($address['postal_code']) : ''; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Stad:</label>
                                    <input type="text" id="editCity" name="city" placeholder="Rotterdam" value="<?php echo isset($address['city']) ? htmlspecialchars($address['city']) : ''; ?>" required>
                                </div>
                                <div style="display: flex; gap: 10px; margin-top: 15px;">
                                    <button type="submit" class="btn-primary">Opslaan</button>
                                    <button type="button" class="btn-secondary" onclick="cancelEditAddress()">Annuleren</button>
                                </div>
                                <p class="error-message" id="addressEditError"></p>
                            </form>
                        </div>
                    </div>

                    <div class="info-card">
                        <h3>Statistieken</h3>
                        <div class="info-row">
                            <span class="info-label">Totaal besteld:</span>
                            <span class="info-value highlight" id="totalOrders"><?php echo count($orders); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Meest besteld:</span>
                            <span class="info-value" id="favoriteDish">
                                <?php 
                                if (isset($favoriteDish) && $favoriteDish) {
                                    echo htmlspecialchars($favoriteDish['item_name']) . ' (' . (int)$favoriteDish['total_quantity'] . 'x)';
                                } else {
                                    echo '-';
                                }
                                ?>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Favorieten:</span>
                            <span class="info-value highlight" id="favoriteCount"><?php echo count($favorites); ?> ❤️</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Punten:</span>
                                <span class="info-value highlight" id="points">
                                    <?php echo isset($customer['loyalty_points']) ? (int) $customer['loyalty_points'] : 0; ?> 🌸
                                </span>
                        </div>
                    </div>
                </div>
            </div>

                <!-- Favorites Tab -->
                <div class="profile-tab-content" id="favoritesTab">
                    <h2>Mijn Favorieten</h2>
                    <div class="favorites-grid" id="favoritesList">
                        <?php if (!empty($favorites)): ?>
                            <?php foreach ($favorites as $favorite): ?>
                                <?php 
                                $itemName = urlencode($favorite['name']);
                                $itemImage = urlencode($favorite['image_url'] ?? 'img/ramen1.png');
                                $itemPrice = $favorite['price'] ?? 0;
                                $itemDescription = urlencode($favorite['description'] ?? '');
                                $itemIngredients = isset($favorite['ingredients']) ? urlencode(json_encode(explode(', ', $favorite['ingredients']))) : '';
                                $itemBadge = urlencode($favorite['badge'] ?? '');
                                ?>
                                <div class="favorite-item" onclick="window.location.href='menu.php?item=<?php echo $itemName; ?>'">
                                    <div class="favorite-image">
                                        <img src="public/<?php echo htmlspecialchars($favorite['image_url'] ?? 'img/ramen1.png'); ?>" alt="<?php echo htmlspecialchars($favorite['name']); ?>">
                                    </div>
                                    <div class="favorite-info">
                                        <h4><?php echo htmlspecialchars($favorite['name']); ?></h4>
                                        <p><?php echo htmlspecialchars($favorite['description']); ?></p>
                                        <div class="favorite-price">€<?php echo number_format($favorite['price'], 2, ',', '.'); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="empty-state">Je hebt nog geen favorieten toegevoegd</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Order Detail Modal -->
                <div class="order-modal hidden" id="orderDetailModal">
                    <div class="order-modal-backdrop" id="orderModalBackdrop"></div>
                    <div class="order-modal-content">
                        <button class="order-modal-close" id="orderModalClose">✕</button>
                        <h2 id="orderModalTitle">Bestelling</h2>
                        <p class="order-modal-meta" id="orderModalMeta"></p>

                        <div class="order-modal-section">
                            <h3>Items</h3>
                            <div id="orderModalItems" class="order-modal-items"></div>
                        </div>

                        <div class="order-modal-section" id="orderModalAddressSection" style="display:none;">
                            <h3>Bezorgadres</h3>
                            <p id="orderModalAddress"></p>
                        </div>

                        <div class="order-modal-section" id="orderModalNotesSection" style="display:none;">
                            <h3>Opmerkingen</h3>
                            <p id="orderModalNotes"></p>
                        </div>

                        <div class="order-modal-section order-modal-actions">
                            <button type="button" class="btn-secondary" onclick="closeOrderModal()">Sluiten</button>
                            <button type="button" class="btn-primary" id="orderModalReorder">Bestel deze bestelling opnieuw</button>
                        </div>
                    </div>
                </div>

        </div>
    </div>
    <?php endif; ?>

<!-- Winkelwagen Float Button (zelfde als op home/menu) -->
<div class="cart-float" id="cartFloat" onclick="toggleCart()">
    <span class="cart-icon">🛒</span>
    <span class="cart-count" id="cartCount">0</span>
</div>

<!-- Winkelwagen Sidebar (zelfde structuur als home/menu voor consistente cart.js functionaliteit) -->
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
