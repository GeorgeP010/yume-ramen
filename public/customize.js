// Customize Page JavaScript

let customizeOptions = {
    broth: 'medium',
    noodles: 'medium',
    spice: 'medium',
    flavor: 'sprite', // voor dranken
    toppings: [],
    quantity: 1,
    specialInstructions: ''
};

let basePrice = parseFloat(document.querySelector('.customize-order-summary').dataset.basePrice || '0');
const deliveryFee = 2.50;

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    initializeCustomizePage();
});

function initializeCustomizePage() {
    // Thumbnail clicks
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.addEventListener('click', () => {
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
            const imageSrc = thumb.dataset.image;
            document.getElementById('customizeMainImage').src = imageSrc;
        });
    });

    // Customize button clicks
    document.querySelectorAll('.customize-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const option = btn.dataset.option;
            const value = btn.dataset.value;
            
            // Remove active from siblings
            btn.parentElement.querySelectorAll('.customize-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            customizeOptions[option] = value;
            updateOrderSummary();
        });
    });

    // Checkbox clicks
    document.querySelectorAll('.customize-checkbox input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const topping = checkbox.dataset.topping;
            const price = parseFloat(checkbox.dataset.price);
            
            if (checkbox.checked) {
                customizeOptions.toppings.push({ name: topping, price: price });
            } else {
                customizeOptions.toppings = customizeOptions.toppings.filter(t => t.name !== topping);
            }
            
            updateOrderSummary();
        });
    });

    // Quantity buttons
    document.getElementById('increaseQty').addEventListener('click', () => {
        customizeOptions.quantity++;
        document.getElementById('currentQuantity').textContent = customizeOptions.quantity;
        updateOrderSummary();
    });

    document.getElementById('decreaseQty').addEventListener('click', () => {
        if (customizeOptions.quantity > 1) {
            customizeOptions.quantity--;
            document.getElementById('currentQuantity').textContent = customizeOptions.quantity;
            updateOrderSummary();
        }
    });

    // Special instructions
    document.getElementById('specialInstructions').addEventListener('input', (e) => {
        customizeOptions.specialInstructions = e.target.value;
    });

    // Add to cart button
    document.getElementById('addToCartBtn').addEventListener('click', addToCart);

    // Remove item button
    document.getElementById('removeItem').addEventListener('click', () => {
        if (confirm('Weet je zeker dat je dit item wilt verwijderen?')) {
            window.location.href = 'menu.php';
        }
    });

    // Get base price from data attribute
    const summaryEl = document.getElementById('orderSummary');
    if (summaryEl && summaryEl.dataset.basePrice) {
        basePrice = parseFloat(summaryEl.dataset.basePrice);
    } else {
        // Fallback: get from PHP
        const orderSubtotal = document.getElementById('orderSubtotal').textContent;
        basePrice = parseFloat(orderSubtotal.replace('€', '').replace(',', '.'));
    }

    updateOrderSummary();
}

function updateOrderSummary() {
    // Update options display
    const optionsEl = document.getElementById('orderItemOptions');
    const options = [];
    
    if (customizeOptions.broth) {
        options.push(`Broth: ${customizeOptions.broth.charAt(0).toUpperCase() + customizeOptions.broth.slice(1)}`);
    }
    if (customizeOptions.noodles) {
        options.push(`Noodles: ${customizeOptions.noodles.charAt(0).toUpperCase() + customizeOptions.noodles.slice(1)}`);
    }
    if (customizeOptions.spice) {
        options.push(`Spice: ${customizeOptions.spice.charAt(0).toUpperCase() + customizeOptions.spice.replace('_', ' ')}`);
    }
    if (customizeOptions.flavor) {
        options.push(`Flavor: ${customizeOptions.flavor.charAt(0).toUpperCase() + customizeOptions.flavor.slice(1)}`);
    }
    
    customizeOptions.toppings.forEach(topping => {
        options.push(topping.name.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()));
    });
    
    optionsEl.innerHTML = options.length > 0 ? options.join('<br>') : '<span style="color: rgba(255,255,255,0.5);">Standard</span>';

    // Calculate totals
    const toppingsTotal = customizeOptions.toppings.reduce((sum, t) => sum + t.price, 0);
    const subtotal = (basePrice + toppingsTotal) * customizeOptions.quantity;
    const total = subtotal + deliveryFee;

    // Update totals
    document.getElementById('orderSubtotal').textContent = `€${subtotal.toFixed(2).replace('.', ',')}`;
    document.getElementById('orderTotal').textContent = `€${total.toFixed(2).replace('.', ',')}`;

    // Update quantity display
    document.getElementById('currentQuantity').textContent = customizeOptions.quantity;
    document.getElementById('orderItemCount').textContent = `${customizeOptions.quantity} ${customizeOptions.quantity === 1 ? 'item' : 'items'}`;

    // Free delivery message (if subtotal < 15)
    const freeDeliveryThreshold = 15.00;
    if (subtotal < freeDeliveryThreshold) {
        const needed = (freeDeliveryThreshold - subtotal).toFixed(2);
        document.getElementById('deliveryAmount').textContent = needed.replace('.', ',');
        document.getElementById('deliveryMessage').style.display = 'block';
    } else {
        document.getElementById('deliveryMessage').style.display = 'none';
    }
}

function addToCart() {
    // Get item name from page
    const itemName = document.querySelector('.customize-title').textContent;
    
    // Build cart item with all customizations
    const cartItem = {
        name: itemName,
        basePrice: basePrice,
        options: {
            broth: customizeOptions.broth,
            noodles: customizeOptions.noodles,
            spice: customizeOptions.spice,
            flavor: customizeOptions.flavor,
            toppings: customizeOptions.toppings
        },
        quantity: customizeOptions.quantity,
        specialInstructions: customizeOptions.specialInstructions,
        image: document.getElementById('customizeMainImage').src
    };

    // Calculate final price
    const toppingsTotal = customizeOptions.toppings.reduce((sum, t) => sum + t.price, 0);
    cartItem.price = basePrice + toppingsTotal;

    // Get existing cart from localStorage
    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    
    // Add to cart
    cart.push(cartItem);
    
    // Save to localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Show notification
    showNotification(`${customizeOptions.quantity}x ${itemName} toegevoegd aan winkelwagen!`);
    
    // Redirect to menu or checkout
    setTimeout(() => {
        window.location.href = 'menu.php';
    }, 1000);
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'customize-notification';
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 30px;
        background: var(--customize-accent);
        color: #000;
        padding: 16px 24px;
        border-radius: 10px;
        font-weight: 900;
        z-index: 10000;
        box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        animation: slideInRight 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 2000);
}

// Add animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
