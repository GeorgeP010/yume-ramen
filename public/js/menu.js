// Loading screen - fade out when page loads
window.addEventListener('load', () => {
    const loadingScreen = document.getElementById('loadingScreen');
    setTimeout(() => {
        loadingScreen.classList.add('fade-out');
    }, 800); // Kort tonen van logo
});

// Menu System JavaScript

// Winkelwagen systeem
// Cart (array) en basisfuncties worden geladen via cart.js
let currentItem = null;
let currentQuantity = 1;
let currentCustomizations = {
    broth: 'medium',
    noodles: 'soft',
    spice: 'mild',
    flavor: 'cola',
    toppings: [],
    specialInstructions: ''
};

// Toppings data voor ramen
const ramenToppings = [
    { name: 'Extra Chashu', price: 3.00 },
    { name: 'Soft-Boiled Egg', price: 1.00 },
    { name: 'Extra Noodles', price: 2.00 },
    { name: 'Nori Sheets', price: 1.00 },
    { name: 'Sweet Corn', price: 1.00 },
    { name: 'Bamboo Shoots', price: 1.00 },
    { name: 'Green Onions', price: 0 },
    { name: 'Shiitake Mushrooms', price: 2.00 },
    { name: 'Butter', price: 0.50 },
    { name: 'Roasted Garlic', price: 1.00 }
];

// Detail Modal functies
// Open detail modal met customization
function openDetail(name, image, price, description, ingredientsArray, badge, category = '') {
    const modal = document.getElementById('detailModal');
    
    // Reset customizations
    currentCustomizations = {
        broth: 'medium',
        noodles: 'soft',
        spice: 'mild',
        flavor: 'cola',
        toppings: [],
        specialInstructions: ''
    };
    currentQuantity = 1;
    
    // Update image
    document.getElementById('detailImage').src = image;
    
    // Update order summary
    document.getElementById('orderItemName').textContent = name;
    document.getElementById('orderItemBasePrice').textContent = `€${price.toFixed(2).replace('.', ',')}`;
    
    // Detecteer item type (ramen vs drank)
    const normalizedCategory = (category || '').toLowerCase();
    const lowerName = name.toLowerCase();
    const isRamen = normalizedCategory === 'ramen' || lowerName.includes('ramen');
    const isSoda = name.toLowerCase().includes('soda') || name.toLowerCase().includes('frisdrank');
    
    // Toon/verberg customization groepen
    document.getElementById('brothGroup').style.display = isRamen ? 'block' : 'none';
    document.getElementById('noodleGroup').style.display = isRamen ? 'block' : 'none';
    document.getElementById('spiceGroup').style.display = isRamen ? 'block' : 'none';
    document.getElementById('flavorGroup').style.display = isSoda ? 'block' : 'none';
    document.getElementById('toppingsGroup').style.display = isRamen ? 'block' : 'none';
    
    // Genereer toppings voor ramen
    if (isRamen) {
        generateToppings();
    }
    
    // Reset alle customization buttons
    document.querySelectorAll('.customization-option').forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.option === 'broth' && btn.dataset.value === 'medium') btn.classList.add('active');
        if (btn.dataset.option === 'noodles' && btn.dataset.value === 'soft') btn.classList.add('active');
        if (btn.dataset.option === 'spice' && btn.dataset.value === 'mild') btn.classList.add('active');
        if (btn.dataset.option === 'flavor' && btn.dataset.value === 'cola') btn.classList.add('active');
    });
    
    // Reset quantity en special instructions
    document.getElementById('quantityValue').textContent = '1';
    document.getElementById('specialInstructions').value = '';
    
    // Store current item
    currentItem = { name, price, image, isRamen, isSoda };
    
    // Update order summary
    updateOrderSummary();
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Genereer toppings checkboxes
function generateToppings() {
    const toppingsGrid = document.getElementById('toppingsGrid');
    toppingsGrid.innerHTML = '';
    
    ramenToppings.forEach(topping => {
        const checkbox = document.createElement('label');
        checkbox.className = 'topping-checkbox';
        checkbox.innerHTML = `
            <input type="checkbox" data-topping="${topping.name}" data-price="${topping.price}">
            <span class="topping-label">${topping.name}</span>
            <span class="topping-price">${topping.price > 0 ? '+€' + topping.price.toFixed(2) : 'Free'}</span>
        `;
        
        checkbox.addEventListener('change', function() {
            const checkboxInput = this.querySelector('input[type="checkbox"]');
            if (checkboxInput.checked) {
                this.classList.add('checked');
                if (!currentCustomizations.toppings.find(t => t.name === topping.name)) {
                    currentCustomizations.toppings.push({ name: topping.name, price: topping.price });
                }
            } else {
                this.classList.remove('checked');
                currentCustomizations.toppings = currentCustomizations.toppings.filter(t => t.name !== topping.name);
            }
            updateOrderSummary();
        });
        
        toppingsGrid.appendChild(checkbox);
    });
}

// Update order summary met customization info
function updateOrderSummary() {
    if (!currentItem) return;
    
    const customizationsEl = document.getElementById('orderCustomizations');
    const extrasEl = document.getElementById('orderItemExtras');
    const subtotalEl = document.getElementById('orderSubtotal');
    const totalEl = document.getElementById('orderTotal');
    
    customizationsEl.innerHTML = '';
    extrasEl.innerHTML = '';
    
    // Toon customizations
    if (currentItem.isRamen) {
        const customizations = [];
        if (currentCustomizations.broth) customizations.push({ label: 'Broth', value: currentCustomizations.broth });
        if (currentCustomizations.noodles) customizations.push({ label: 'Noodles', value: currentCustomizations.noodles });
        if (currentCustomizations.spice) customizations.push({ label: 'Spice', value: currentCustomizations.spice });
        
        customizations.forEach(custom => {
            const div = document.createElement('div');
            div.className = 'customization-item';
            div.innerHTML = `<span>${custom.label}:</span><span>${custom.value.charAt(0).toUpperCase() + custom.value.slice(1)}</span>`;
            customizationsEl.appendChild(div);
        });
    }
    
    if (currentItem.isSoda && currentCustomizations.flavor) {
        const div = document.createElement('div');
        div.className = 'customization-item';
        div.innerHTML = `<span>Smaak:</span><span>${currentCustomizations.flavor.charAt(0).toUpperCase() + currentCustomizations.flavor.slice(1)}</span>`;
        customizationsEl.appendChild(div);
    }
    
    // Toon extra toppings
    if (currentCustomizations.toppings.length > 0) {
        currentCustomizations.toppings.forEach(topping => {
            const div = document.createElement('div');
            div.className = 'extra-item';
            div.innerHTML = `<span>${topping.name}</span><span>+€${topping.price.toFixed(2)}</span>`;
            extrasEl.appendChild(div);
        });
    }
    
    // Bereken totaal
    let basePrice = currentItem.price;
    let extrasTotal = currentCustomizations.toppings.reduce((sum, t) => sum + t.price, 0);
    let subtotal = (basePrice + extrasTotal) * currentQuantity;
    let delivery = 2.50;
    let total = subtotal + delivery;
    
    subtotalEl.textContent = `€${subtotal.toFixed(2).replace('.', ',')}`;
    totalEl.textContent = `€${total.toFixed(2).replace('.', ',')}`;
}

function closeDetail() {
    document.getElementById('detailModal').classList.remove('active');
    document.body.style.overflow = 'auto';
    currentItem = null;
    currentQuantity = 1;
    currentCustomizations = {
        broth: 'medium',
        noodles: 'soft',
        spice: 'mild',
        flavor: 'cola',
        toppings: [],
        specialInstructions: ''
    };
    // Reset alle checkboxes
    document.querySelectorAll('.topping-checkbox input[type="checkbox"]').forEach(cb => {
        cb.checked = false;
        cb.closest('.topping-checkbox').classList.remove('checked');
    });
}

function increaseQuantity() {
    currentQuantity++;
    document.getElementById('quantityValue').textContent = currentQuantity;
    updateOrderSummary();
}

function decreaseQuantity() {
    if (currentQuantity > 1) {
        currentQuantity--;
        document.getElementById('quantityValue').textContent = currentQuantity;
        updateOrderSummary();
    }
}

function addFromDetail() {
    if (!currentItem) return;
    
    // Bereken totaalprijs inclusief extras
    let extrasTotal = currentCustomizations.toppings.reduce((sum, t) => sum + t.price, 0);
    let itemPrice = currentItem.price + extrasTotal;
    
    // Maak item naam met customizations
    let itemName = currentItem.name;
    if (currentItem.isRamen) {
        const customParts = [];
        if (currentCustomizations.broth !== 'medium') customParts.push(currentCustomizations.broth + ' broth');
        if (currentCustomizations.noodles !== 'soft') customParts.push(currentCustomizations.noodles + ' noodles');
        if (currentCustomizations.spice !== 'mild') customParts.push(currentCustomizations.spice);
        if (currentCustomizations.toppings.length > 0) {
            customParts.push('+ ' + currentCustomizations.toppings.map(t => t.name).join(', '));
        }
        if (customParts.length > 0) {
            itemName += ' (' + customParts.join(', ') + ')';
        }
    }
    if (currentItem.isSoda && currentCustomizations.flavor !== 'cola') {
        itemName += ' (' + currentCustomizations.flavor + ')';
    }
    
    // Check of item al in winkelwagen zit (exacte match nodig vanwege customizations)
    const existingItem = cart.find(item => item.name === itemName && 
        JSON.stringify(item.customizations) === JSON.stringify(currentCustomizations));
    
    if (existingItem) {
        existingItem.quantity += currentQuantity;
    } else {
        cart.push({
            name: itemName,
            price: itemPrice,
            quantity: currentQuantity,
            customizations: { ...currentCustomizations },
            basePrice: currentItem.price,
            image: currentItem.image,
            originalName: currentItem.name
        });
    }
    
    updateCart();
    closeDetail();
    showNotification(`${currentQuantity}x ${currentItem.name} toegevoegd!`);
}

// Setup customization option clicks
document.addEventListener('DOMContentLoaded', function() {
    // Customization option buttons
    document.querySelectorAll('.customization-option').forEach(btn => {
        btn.addEventListener('click', function() {
            const option = this.dataset.option;
            const value = this.dataset.value;
            
            // Remove active van alle siblings
            this.parentElement.querySelectorAll('.customization-option').forEach(b => b.classList.remove('active'));
            // Add active aan geklikte button
            this.classList.add('active');
            
            // Update customization
            currentCustomizations[option] = value;
            updateOrderSummary();
        });
    });
    
    // Special instructions
    const specialInstructions = document.getElementById('specialInstructions');
    if (specialInstructions) {
        specialInstructions.addEventListener('input', function() {
            currentCustomizations.specialInstructions = this.value;
        });
    }
});

// Close modal bij klik buiten content
document.addEventListener('click', function (e) {
    const modal = document.getElementById('detailModal');
    const backdrop = document.querySelector('.detail-modal-backdrop');
    if (e.target === modal || e.target === backdrop) {
        closeDetail();
    }
});

// ESC toets om modal te sluiten
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeDetail();
    }
});


// Voeg item toe aan winkelwagen (zonder extra customizations)
function addToCart(name, price, image) {
    // Check of item al in winkelwagen zit
    const existingItem = cart.find(item => item.name === name && !item.customizations);

    if (existingItem) {
        existingItem.quantity++;
    } else {
        // Probeer image te vinden als niet meegegeven
        let itemImage = image;
        if (!itemImage) {
            const cardImage = document.querySelector(`[data-item-name="${name}"]`);
            if (cardImage && cardImage.dataset.itemImage) {
                itemImage = cardImage.dataset.itemImage;
            } else {
                itemImage = 'public/img/ramen1.png';
            }
        }
        
        cart.push({
            name: name,
            price: price,
            quantity: 1,
            image: itemImage,
            originalName: name
        });
    }

    // Werk winkelwagen UI bij (cart.js) en toon melding
    if (typeof updateCart === 'function') {
        updateCart();
    }
    if (typeof showNotification === 'function') {
        showNotification(`${name} toegevoegd!`);
    }
}

// Hamburger menu functionaliteit (alleen voor menu.html)
const menuHamburger = document.getElementById('hamburger');
const navMenuOverlay = document.getElementById('navMenuOverlay');
const navMenuClose = document.getElementById('navMenuClose');
const navMenuLinks = document.querySelectorAll('.nav-menu-link');

// Check of elementen bestaan
if (menuHamburger && navMenuOverlay && navMenuClose) {
    // Open menu met animatie
    menuHamburger.addEventListener('click', () => {
        navMenuOverlay.classList.add('active');
        menuHamburger.classList.add('active');
        document.body.style.overflow = 'hidden';
    });

    // Sluit menu met close button
    navMenuClose.addEventListener('click', (e) => {
        e.preventDefault();
        navMenuOverlay.classList.remove('active');
        menuHamburger.classList.remove('active');
        document.body.style.overflow = '';
    });

    // Sluit menu bij klik op link met fade-out animatie
    navMenuLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');

            // Als je al op menu.html bent en op MENU klikt
            if (href === 'menu.html') {
                e.preventDefault();
                navMenuOverlay.classList.remove('active');
                menuHamburger.classList.remove('active');
                document.body.style.overflow = '';
                return;
            }

            // Voor alle andere links - smooth page transition
            e.preventDefault();

            // Smooth fade out animatie
            const navLinks = document.querySelectorAll('.nav-menu-link');
            const navLogo = document.querySelector('.nav-menu-logo');
            const navIcon = document.querySelector('.nav-menu-icon');
            const navImage = document.querySelector('.nav-menu-image');
            const navClose = document.querySelector('.nav-menu-close');
            const navFooter = document.querySelector('.nav-menu-footer');

            // Fade out alle elementen met stagger effect
            navLinks.forEach((l, index) => {
                l.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                l.style.transitionDelay = `${index * 0.05}s`;
                l.style.opacity = '0';
                l.style.transform = 'translateY(-20px)';
            });

            if (navLogo) navLogo.style.opacity = '0';
            if (navIcon) navIcon.style.opacity = '0';
            if (navImage) {
                navImage.style.transition = 'opacity 0.4s ease';
                navImage.style.opacity = '0';
            }
            if (navClose) navClose.style.opacity = '0';
            if (navFooter) navFooter.style.opacity = '0';

            // Verander achtergrond naar zwart voor smooth transition
            setTimeout(() => {
                navMenuOverlay.style.transition = 'background 0.4s ease';
                navMenuOverlay.style.background = '#000000';
            }, 200);

            // Sla status op voor loading screen
            sessionStorage.setItem('showLoading', 'true');

            // Navigeer naar nieuwe pagina
            setTimeout(() => {
                window.location.href = href;
            }, 600);
        });
    });
}

// Scroll animaties voor menu items - eerder en smoother zoals home pagina
function handleScrollAnimations() {
    const menuCards = document.querySelectorAll('.menu-card');
    const decorationImage = document.querySelector('.menu-decoration-image');
    const menuSectionTitles = document.querySelectorAll('.menu-section-title');

    // Observer met eerdere trigger - zoals home pagina
    const observerOptions = {
        threshold: 0.05, // Start al bij 5% zichtbaarheid
        rootMargin: '0px 0px -20px 0px' // Trigger eerder tijdens scroll
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-active');
            }
        });
    }, observerOptions);

    // Observeer alle menu cards met smooth fade-in
    menuCards.forEach((card, index) => {
        card.classList.add('fade-in-item');
        // Stagger delay voor mooi effect
        card.style.transitionDelay = `${index * 0.08}s`;
        observer.observe(card);
    });

    // Observeer section titles
    menuSectionTitles.forEach(title => {
        title.classList.add('fade-in-section');
        observer.observe(title);
    });

    // Fade-in effect voor nav_foto3
    if (decorationImage) {
        decorationImage.style.opacity = '0';
        decorationImage.style.transform = 'translateX(-30px)';
        decorationImage.style.transition = 'opacity 1s ease-out 0.3s, transform 1s ease-out 0.3s';

        setTimeout(() => {
            decorationImage.style.opacity = '1';
            decorationImage.style.transform = 'translateX(0)';
        }, 100);
    }
}

// Start scroll animaties wanneer pagina geladen is
window.addEventListener('load', () => {
    handleScrollAnimations();
    setupMenuCardClicks();
    setupFavoriteButtons();
    
    // Auto-open detail modal if item parameter is present
    autoOpenItemDetail();
});

// Auto-open item detail from URL parameter
function autoOpenItemDetail() {
    // Check if there's auto-open data from PHP
    if (typeof window.autoOpenItemData !== 'undefined' && window.autoOpenItemData) {
        const item = window.autoOpenItemData;
        // Small delay to ensure page is fully loaded
        setTimeout(() => {
            openDetail(
                item.name,
                item.image,
                item.price,
                item.description,
                item.ingredients || [],
                item.badge || '',
                item.category || ''
            );
        }, 500);
    }
}

// Setup menu card clicks voor detail modal
function setupMenuCardClicks() {
    // Card image clicks
    document.querySelectorAll('.card-image').forEach(card => {
        card.addEventListener('click', function(e) {
            // Als je op de favorite button klikt, niet de modal openen
            if (e.target.closest('.favorite-btn') || e.target.classList.contains('favorite-btn')) {
                return;
            }
            
            const name = this.dataset.itemName;
            const image = this.dataset.itemImage;
            const price = parseFloat(this.dataset.itemPrice);
            const description = this.dataset.itemDescription;
            const ingredients = JSON.parse(this.dataset.itemIngredients || '[]');
            const badge = this.dataset.itemBadge || '';
            const category = this.dataset.itemCategory || '';
            
            openDetail(name, image, price, description, ingredients, badge, category);
        });
    });
    
    // Card content clicks
    document.querySelectorAll('.card-content').forEach(card => {
        card.addEventListener('click', function(e) {
            // Als je op een button klikt, niet de modal openen
            if (e.target.closest('button')) {
                return;
            }
            
            const name = this.dataset.itemName;
            const image = this.dataset.itemImage;
            const price = parseFloat(this.dataset.itemPrice);
            const description = this.dataset.itemDescription;
            const ingredients = JSON.parse(this.dataset.itemIngredients || '[]');
            const badge = this.dataset.itemBadge || '';
            const category = this.dataset.itemCategory || '';
            
            openDetail(name, image, price, description, ingredients, badge, category);
        });
    });
    
    // Details button clicks
    document.querySelectorAll('.view-details').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const card = this.closest('.card-content');
            if (card) {
                const name = card.dataset.itemName;
                const image = card.dataset.itemImage;
                const price = parseFloat(card.dataset.itemPrice);
                const description = card.dataset.itemDescription;
                const ingredients = JSON.parse(card.dataset.itemIngredients || '[]');
                const badge = card.dataset.itemBadge || '';
                const category = card.dataset.itemCategory || '';
                
                openDetail(name, image, price, description, ingredients, badge, category);
            }
        });
    });
}

// Setup favorite buttons
function setupFavoriteButtons() {
    document.querySelectorAll('.favorite-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            const itemName = this.dataset.itemName;
            if (itemName) {
                toggleFavorite(itemName, this, e);
            }
            return false;
        }, true); // Use capture phase to catch event early
    });
}

// Favorieten functionaliteit
async function toggleFavorite(itemName, buttonElement, event) {
    // Stop event propagation en prevent default
    if (event) {
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();
    }
    
    console.log('Toggle favorite for:', itemName); // Debug
    
    try {
        const response = await fetch('api/toggle_favorite.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ item_name: itemName })
        });

        const data = await response.json();
        console.log('Response:', data); // Debug

        if (data.success) {
            // Toggle active class
            if (data.is_favorite) {
                buttonElement.classList.add('active');
            } else {
                buttonElement.classList.remove('active');
            }
            
            // Show notification
            showNotification(data.message);
        } else {
            if (data.message.includes('ingelogd')) {
                alert('Je moet ingelogd zijn om favorieten toe te voegen. Ga naar je account pagina om in te loggen.');
            } else {
                showNotification('Er ging iets mis: ' + data.message);
            }
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Er ging iets mis bij het toevoegen van de favoriet');
    }
    
    return false;
}
