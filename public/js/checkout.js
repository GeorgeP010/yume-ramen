// Checkout Page JavaScript

// Load cart from localStorage
let cart = JSON.parse(localStorage.getItem('cart') || '[]');

// Constants
const DELIVERY_COST = 2.50;
const FREE_DELIVERY_THRESHOLD = 25.00;
const POINTS_PER_EURO = 1; // 1 punt per euro

// Function to initialize checkout
function initializeCheckout() {
    // Reload cart from localStorage to ensure we have the latest
    cart = JSON.parse(localStorage.getItem('cart') || '[]');

    // Setup UI and data bindings
    displayCartItems();
    calculateTotals();
    setupCheckoutSteps();
    setupFormHandling();
    fillReviewOrderItems();
}

// Initialize immediately if DOM is ready, otherwise wait
(function() {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeCheckout);
    } else {
        initializeCheckout();
    }
    
    // Also run on window load as backup
    window.addEventListener('load', function() {
        setTimeout(function() {
            cart = JSON.parse(localStorage.getItem('cart') || '[]');
            displayCartItems();
            calculateTotals();
        }, 100);
    });
})();

// Force recalculation after a short delay to catch any timing issues
setTimeout(function() {
    cart = JSON.parse(localStorage.getItem('cart') || '[]');
    if (cart.length > 0) {
        displayCartItems();
        calculateTotals();
    }
}, 200);

// Listen for cart changes (if cart is updated from another tab/window)
window.addEventListener('storage', (e) => {
    if (e.key === 'cart') {
        cart = JSON.parse(e.newValue || '[]');
        displayCartItems();
        calculateTotals();
    }
});

// Also check cart periodically in case it's updated in the same window
setInterval(() => {
    const storedCart = JSON.parse(localStorage.getItem('cart') || '[]');
    if (JSON.stringify(storedCart) !== JSON.stringify(cart)) {
        cart = storedCart;
        displayCartItems();
        calculateTotals();
    }
}, 500);

/**
 * Multi-step checkout handling
 */
// Delivery time toggle + slots voor stap 1 formulier
function setupCheckoutSteps() {
    const deliveryTimeRadios = document.querySelectorAll('input[name="delivery_time"]');
    const deliveryTimeSelect = document.getElementById('deliveryTimeSelect');
    const deliveryTimeSlotsContainer = document.getElementById('deliveryTimeSlots');
    const deliveryTimeInput = document.getElementById('deliveryTime');

    function generateTimeSlots() {
        if (!deliveryTimeSlotsContainer || !deliveryTimeInput) return;
        
        // Clear existing
        deliveryTimeSlotsContainer.innerHTML = '';

        const startHour = 11; // 11:00
        const endHour = 22;   // 22:00
        const stepMinutes = 15;

        for (let hour = startHour; hour <= endHour; hour++) {
            for (let min = 0; min < 60; min += stepMinutes) {
                const labelHour = hour.toString().padStart(2, '0');
                const labelMin = min.toString().padStart(2, '0');
                const value = `${labelHour}:${labelMin}`;

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'time-slot-btn';
                btn.textContent = value;

                btn.addEventListener('click', () => {
                    // Remove active from others
                    deliveryTimeSlotsContainer.querySelectorAll('.time-slot-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    deliveryTimeInput.value = value;
                });

                deliveryTimeSlotsContainer.appendChild(btn);
            }
        }
    }

    // Generate slots once on init
    generateTimeSlots();
    if (deliveryTimeRadios && deliveryTimeSelect) {
        deliveryTimeRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                if (radio.value === 'later' && radio.checked) {
                    deliveryTimeSelect.style.display = 'block';
                    // Ensure we have a selected slot; if none, pick first
                    const firstBtn = deliveryTimeSlotsContainer?.querySelector('.time-slot-btn');
                    if (firstBtn && !deliveryTimeInput.value) {
                        firstBtn.click();
                    }
                } else if (radio.value === 'asap' && radio.checked) {
                    deliveryTimeSelect.style.display = 'none';
                    if (deliveryTimeInput) deliveryTimeInput.value = '';
                }
            });
        });
    }
}

/**
 * Display cart items in checkout
 */
function displayCartItems() {
    const cartContainer = document.getElementById('checkoutCartItems');
    const orderCount = document.getElementById('orderCount');
    
    // Reload cart from localStorage
    cart = JSON.parse(localStorage.getItem('cart') || '[]');

    if (!cartContainer) return;
    
    // Update item count
    const totalItems = cart.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0);
    if (orderCount) {
        orderCount.textContent = totalItems + (totalItems === 1 ? ' item' : ' items');
    }
    
    if (cart.length === 0) {
        cartContainer.innerHTML = '<p class="empty-message">Je winkelwagen is leeg</p>';
        const placeOrderBtn = document.getElementById('placeOrderBtn');
        if (placeOrderBtn) {
            placeOrderBtn.disabled = true;
        }
        return;
    }

    cartContainer.innerHTML = cart.map((item, index) => {
        const price = parseFloat(item.price) || 0;
        const quantity = parseInt(item.quantity) || 0;
        const itemImage = item.image || (item.originalName ? getItemImage(item.originalName) : 'public/img/ramen1.png');
        
        // Parse customizations for display
        let customizationsHTML = '';
        if (item.customizations) {
            const customParts = [];
            if (item.customizations.broth && item.customizations.broth !== 'medium') {
                customParts.push(item.customizations.broth);
            }
            if (item.customizations.noodles && item.customizations.noodles !== 'soft') {
                customParts.push(item.customizations.noodles + ' noodles');
            }
            if (item.customizations.spice && item.customizations.spice !== 'mild') {
                customParts.push(item.customizations.spice);
            }
            if (item.customizations.toppings && item.customizations.toppings.length > 0) {
                customParts.push('+ ' + item.customizations.toppings.map(t => t.name).join(', '));
            }
            if (customParts.length > 0) {
                customizationsHTML = `<div class="item-customizations">${customParts.join(', ')}</div>`;
            }
        }
        
        return `
            <div class="checkout-item">
                <div class="checkout-item-image">
                    <img src="${itemImage}" alt="${item.originalName || item.name}" onerror="this.src='public/img/ramen1.png'">
                </div>
                <div class="checkout-item-details">
                    <h4 class="item-name">${item.originalName || item.name}</h4>
                    ${customizationsHTML}
                    <div class="item-controls">
                        <button class="quantity-btn minus" onclick="updateCheckoutQuantity(${index}, -1)">−</button>
                        <span class="quantity-value">${quantity}</span>
                        <button class="quantity-btn plus" onclick="updateCheckoutQuantity(${index}, 1)">+</button>
                        <button class="remove-item" onclick="removeCheckoutItem(${index})" title="Verwijderen">🗑️</button>
                    </div>
                </div>
                <div class="checkout-item-price">
                    €${(price * quantity).toFixed(2).replace('.', ',')}
                </div>
            </div>
        `;
    }).join('');
}

// Update quantity in checkout
function updateCheckoutQuantity(index, change) {
    if (cart[index]) {
        const newQuantity = cart[index].quantity + change;
        if (newQuantity > 0) {
            cart[index].quantity = newQuantity;
            localStorage.setItem('cart', JSON.stringify(cart));
            displayCartItems();
            calculateTotals();
        } else {
            removeCheckoutItem(index);
        }
    }
}

// Vul op de overzichtspagina (review.php) een simpele lijst van bestelde items
function fillReviewOrderItems() {
    const container = document.getElementById('reviewOrderItems');
    if (!container) return;

    const cartData = JSON.parse(localStorage.getItem('cart') || '[]');
    if (!cartData.length) {
        container.innerHTML = '<p style="font-size:0.9rem;color:rgba(255,255,255,0.6);">Je winkelwagen is leeg.</p>';
        return;
    }

    const html = cartData.map(item => {
        const qty = parseInt(item.quantity) || 0;
        const lineTotal = (item.price * qty).toFixed(2).replace('.', ',');
        return `
            <div class="review-row">
                <span class="review-label">${qty}× ${item.originalName || item.name}</span>
                <span class="review-value">€${lineTotal}</span>
            </div>
        `;
    }).join('');

    container.innerHTML = html;
}

// Remove item from checkout
function removeCheckoutItem(index) {
    if (confirm('Weet je zeker dat je dit item wilt verwijderen?')) {
        cart.splice(index, 1);
        localStorage.setItem('cart', JSON.stringify(cart));
        displayCartItems();
        calculateTotals();
    }
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

// Make functions globally available
window.updateCheckoutQuantity = updateCheckoutQuantity;
window.removeCheckoutItem = removeCheckoutItem;

/**
 * Calculate and display totals
 */
function calculateTotals() {
    // Always reload cart from localStorage
    cart = JSON.parse(localStorage.getItem('cart') || '[]');
    
    // Calculate subtotal - ensure prices are numbers
    const subtotal = cart.reduce((sum, item) => {
        const price = parseFloat(item.price) || 0;
        const quantity = parseInt(item.quantity) || 0;
        return sum + (price * quantity);
    }, 0);
    
    // Debug logging
    console.log('Checkout calculation:', {
        cartItems: cart.length,
        subtotal: subtotal,
        threshold: FREE_DELIVERY_THRESHOLD
    });
    
    const isFreeDelivery = subtotal >= FREE_DELIVERY_THRESHOLD;
    const deliveryCost = isFreeDelivery ? 0 : DELIVERY_COST;
    const total = subtotal + deliveryCost;
    const pointsEarned = Math.floor(total * POINTS_PER_EURO);
    const amountNeededForFreeDelivery = Math.max(0, FREE_DELIVERY_THRESHOLD - subtotal);
    
    // Calculate progress percentage (max 100%)
    const progressPercentage = Math.min(100, (subtotal / FREE_DELIVERY_THRESHOLD) * 100);

    // Update subtotal
    const subtotalElement = document.getElementById('subtotal');
    if (subtotalElement) {
        subtotalElement.textContent = `€${subtotal.toFixed(2).replace('.', ',')}`;
    }
    
    // Update free delivery progress bar
    const progressFill = document.getElementById('deliveryProgressFill');
    const freeDeliveryMessage = document.getElementById('freeDeliveryMessage');
    
    if (progressFill) {
        progressFill.style.width = `${progressPercentage}%`;
        if (isFreeDelivery) {
            progressFill.classList.add('achieved');
        } else {
            progressFill.classList.remove('achieved');
        }
    }
    
    if (freeDeliveryMessage) {
        if (isFreeDelivery) {
            // Toon succesbericht wanneer drempel is bereikt
            freeDeliveryMessage.innerHTML = '<span class="free-delivery-success">✓ Je hebt gratis verzending!</span>';
            freeDeliveryMessage.classList.add('achieved');
        } else {
            // Herstel standaardtekst en update resterend bedrag
            const amountText = amountNeededForFreeDelivery.toFixed(2).replace('.', ',');
            freeDeliveryMessage.innerHTML = 'Bestel nog €<span id="freeDeliveryAmount">' + amountText + '</span> voor gratis verzending!';
            freeDeliveryMessage.classList.remove('achieved');
        }
    }
    
    // Update delivery cost
    const deliveryCostElement = document.getElementById('deliveryCost');
    if (deliveryCostElement) {
        if (isFreeDelivery) {
            deliveryCostElement.textContent = 'GRATIS';
            deliveryCostElement.classList.add('free-delivery');
        } else {
            deliveryCostElement.textContent = `€${deliveryCost.toFixed(2).replace('.', ',')}`;
            deliveryCostElement.classList.remove('free-delivery');
        }
    }
    
    // Update total
    const totalElement = document.getElementById('totalAmount');
    if (totalElement) {
        totalElement.textContent = `€${total.toFixed(2).replace('.', ',')}`;
    }
    
    // Update points display
    const pointsDisplay = document.getElementById('pointsEarned');
    if (pointsDisplay) {
        pointsDisplay.textContent = `${pointsEarned} punten`;
    }
    
}

/**
 * Setup form submission handling
 */
function setupFormHandling() {
    const form = document.getElementById('checkoutForm');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (cart.length === 0) {
            alert('Je winkelwagen is leeg!');
            return;
        }

        // Get form data
        const formData = new FormData(form);
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const isFreeDelivery = subtotal >= FREE_DELIVERY_THRESHOLD;
        const deliveryCost = isFreeDelivery ? 0 : DELIVERY_COST;
        const total = subtotal + deliveryCost;
        
        const orderData = {
            cart: cart,
            customer: {
                name: formData.get('name'),
                email: formData.get('email'),
                phone: formData.get('phone')
            },
            delivery_address: {
                street: formData.get('street'),
                postal_code: formData.get('postal_code'),
                city: formData.get('city')
            },
            payment_method: formData.get('payment_method'),
            notes: formData.get('notes') || '',
            subtotal: subtotal,
            delivery_cost: deliveryCost,
            total: total
        };

        // Show loading state
        const btn = document.getElementById('placeOrderBtn');
        const btnText = btn.querySelector('.btn-text');
        const btnLoader = btn.querySelector('.btn-loader');

        btn.disabled = true;
        btnText.style.display = 'none';
        btnLoader.style.display = 'inline';

        try {
            // Send order to backend
            const response = await fetch('process_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(orderData)
            });

            const result = await response.json();

            if (result.success) {
                // Clear cart (we gaan nu naar een test-betalingsscherm)
                localStorage.removeItem('cart');

                // Redirect to simulated payment screen
                window.location.href = `payment.php?order_id=${result.order_id}`;
            } else {
                alert('Fout bij plaatsen bestelling: ' + (result.message || 'Onbekende fout'));
                btn.disabled = false;
                btnText.style.display = 'inline';
                btnLoader.style.display = 'none';
            }
        } catch (error) {
            console.error('Order error:', error);
            alert('Er ging iets mis bij het plaatsen van je bestelling. Probeer het opnieuw.');
            btn.disabled = false;
            btnText.style.display = 'inline';
            btnLoader.style.display = 'none';
        }
    });
}
