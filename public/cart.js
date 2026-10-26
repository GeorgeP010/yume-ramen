// Cart System - gedeeld tussen home en menu pagina
// Load cart from localStorage if available
let cart = JSON.parse(localStorage.getItem('cart') || '[]');

// Constants voor bezorging
const CART_DELIVERY_COST = 2.50;
const CART_FREE_DELIVERY_THRESHOLD = 25.00;

// Toggle winkelwagen sidebar
function toggleCart() {
    const sidebar = document.getElementById('cartSidebar');
    if (!sidebar) return;
    sidebar.classList.toggle('open');
}

// Update winkelwagen display
function updateCart() {
    const cartItems = document.getElementById('cartItems');
    const cartCount = document.getElementById('cartCount');
    const cartTotalEl = document.getElementById('cartTotal');
    const cartSubtotalEl = document.getElementById('cartSubtotal');
    const cartDeliveryEl = document.getElementById('cartDelivery');
    const freeDeliveryBox = document.getElementById('cartFreeDelivery');
    
    // Als de basis-elementen ontbreken, stoppen we veilig
    if (!cartItems || !cartCount || !cartTotalEl) return;
    
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    if (cartCount) {
        cartCount.textContent = totalItems;
        cartCount.style.display = totalItems > 0 ? 'flex' : 'none';
    }
    
    if (cart.length === 0) {
        if (cartItems) {
            cartItems.innerHTML = '<p class="empty-cart">Je winkelwagen is leeg</p>';
        }
    } else {
        if (cartItems) {
            cartItems.innerHTML = cart.map((item, index) => {
            // Parse customizations voor display
            let customizationsHTML = '';
            if (item.customizations) {
                const customParts = [];
                
                // Ramen customizations
                if (item.customizations.broth && item.customizations.broth !== 'medium') {
                    customParts.push(`<span class="customization-tag">${item.customizations.broth.charAt(0).toUpperCase() + item.customizations.broth.slice(1)} Broth</span>`);
                }
                if (item.customizations.noodles && item.customizations.noodles !== 'soft') {
                    customParts.push(`<span class="customization-tag">${item.customizations.noodles.charAt(0).toUpperCase() + item.customizations.noodles.slice(1)} Noodles</span>`);
                }
                if (item.customizations.spice && item.customizations.spice !== 'mild') {
                    customParts.push(`<span class="customization-tag">${item.customizations.spice.charAt(0).toUpperCase() + item.customizations.spice.slice(1)}</span>`);
                }
                
                // Soda flavor
                if (item.customizations.flavor && item.customizations.flavor !== 'cola') {
                    customParts.push(`<span class="customization-tag">${item.customizations.flavor.charAt(0).toUpperCase() + item.customizations.flavor.slice(1)}</span>`);
                }
                
                // Toppings
                if (item.customizations.toppings && item.customizations.toppings.length > 0) {
                    item.customizations.toppings.forEach(topping => {
                        customParts.push(`<span class="customization-tag topping-tag">+ ${topping.name}</span>`);
                    });
                }
                
                if (customParts.length > 0) {
                    customizationsHTML = `<div class="cart-item-customizations">${customParts.join('')}</div>`;
                }
            }
            
            // Get image - use item.image if available, otherwise try to extract from name or use default
            const itemImage = item.image || (item.originalName ? getItemImage(item.originalName) : 'public/img/ramen1.png');
            
            return `
                <div class="cart-item">
                    <div class="cart-item-image">
                        <img src="${itemImage}" alt="${item.originalName || item.name}" onerror="this.src='public/img/ramen1.png'">
                    </div>
                    <div class="cart-item-content">
                        <div class="cart-item-header">
                            <h4 class="cart-item-name">${item.originalName || item.name}</h4>
                            <button class="cart-item-remove" onclick="removeFromCart(${index})" title="Verwijderen">×</button>
                        </div>
                        ${customizationsHTML}
                        <div class="cart-item-footer">
                            <div class="cart-item-quantity">
                                <button class="quantity-btn" onclick="decreaseCartQuantity(${index})" title="Minder">−</button>
                                <span class="quantity-value">${item.quantity}</span>
                                <button class="quantity-btn" onclick="increaseCartQuantity(${index})" title="Meer">+</button>
                            </div>
                            <div class="cart-item-price">
                                €${(item.price * item.quantity).toFixed(2).replace('.', ',')}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }).join('');
        }
    }

    // Bereken subtotal / bezorging / totaal
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const isFreeDelivery = subtotal >= CART_FREE_DELIVERY_THRESHOLD;
    const delivery = cart.length === 0 ? 0 : (isFreeDelivery ? 0 : CART_DELIVERY_COST);
    const total = subtotal + delivery;

    if (cartSubtotalEl) {
        cartSubtotalEl.textContent = `€${subtotal.toFixed(2).replace('.', ',')}`;
    }
    if (cartDeliveryEl) {
        cartDeliveryEl.textContent = delivery === 0 ? 'GRATIS' : `€${delivery.toFixed(2).replace('.', ',')}`;
        if (delivery === 0) {
            cartDeliveryEl.classList.add('free-delivery');
        } else {
            cartDeliveryEl.classList.remove('free-delivery');
        }
    }
    if (cartTotalEl) {
        cartTotalEl.textContent = `€${total.toFixed(2).replace('.', ',')}`;
    }

    // Free delivery voortgang
    if (freeDeliveryBox) {
        const amountNeeded = Math.max(0, CART_FREE_DELIVERY_THRESHOLD - subtotal);
        const progress = Math.min(100, (subtotal / CART_FREE_DELIVERY_THRESHOLD) * 100);

        // Als er geen items zijn, verberg de box volledig
        if (subtotal === 0) {
            freeDeliveryBox.style.display = 'none';
        } else {
            freeDeliveryBox.style.display = 'block';

            if (isFreeDelivery) {
                // Volledig gratis: groene melding
                freeDeliveryBox.classList.add('achieved');
                freeDeliveryBox.innerHTML = `
                    <div class="cart-free-delivery-text">Je hebt gratis bezorging!</div>
                    <div class="cart-progress-bar">
                        <div class="cart-progress-fill" id="cartProgressFill" style="width: 100%;"></div>
                    </div>
                `;
            } else {
                // Nog niet gratis: toon resterend bedrag + progress
                freeDeliveryBox.classList.remove('achieved');
                freeDeliveryBox.innerHTML = `
                    <div class="cart-free-delivery-text">
                        Voeg €<span id="cartFreeAmount">${amountNeeded.toFixed(2).replace('.', ',')}</span> toe voor gratis bezorging!
                    </div>
                    <div class="cart-progress-bar">
                        <div class="cart-progress-fill" id="cartProgressFill" style="width: ${progress}%;"></div>
                    </div>
                `;
            }
        }
    }
    
    // Save to localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
}

// Helper function om image te krijgen op basis van item naam (fallback)
function getItemImage(itemName) {
    // Probeer image te vinden via data attributes op de pagina
    const cardImage = document.querySelector(`[data-item-name="${itemName}"]`);
    if (cardImage && cardImage.dataset.itemImage) {
        return cardImage.dataset.itemImage;
    }
    // Fallback naar default image
    return 'public/img/ramen1.png';
}

// Increase quantity
function increaseCartQuantity(index) {
    if (cart[index]) {
        cart[index].quantity++;
        updateCart();
    }
}

// Decrease quantity
function decreaseCartQuantity(index) {
    if (cart[index]) {
        if (cart[index].quantity > 1) {
            cart[index].quantity--;
        } else {
            removeFromCart(index);
        }
        updateCart();
    }
}

// Verwijder item uit winkelwagen
function removeFromCart(index) {
    if (confirm('Weet je zeker dat je dit item wilt verwijderen?')) {
        cart.splice(index, 1);
        updateCart();
    }
}

// Update cart on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCart();

    // Klik buiten de cart om te sluiten
    document.addEventListener('click', function (e) {
        const sidebar = document.getElementById('cartSidebar');
        const cartFloat = document.getElementById('cartFloat');
        if (!sidebar) return;

        const isOpen = sidebar.classList.contains('open');
        const clickInsideSidebar = sidebar.contains(e.target);
        const clickOnToggle = cartFloat && cartFloat.contains(e.target);

        // Alleen sluiten als de cart open is, je buiten de sidebar klikt
        // en het niet de floating cart-knop zelf is.
        if (isOpen && !clickInsideSidebar && !clickOnToggle) {
            sidebar.classList.remove('open');
        }
    });
});
