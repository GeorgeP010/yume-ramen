// Account System JavaScript - PHP Backend Version

// Initialize helpers zodat dit ook werkt als het script onderaan de pagina wordt geladen
function onReady(fn) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fn);
    } else {
        fn();
    }
}

// Initialize
onReady(() => {
    initializeEventListeners();
    initAvatarSelection();
});

// Initialize all event listeners
function initializeEventListeners() {
    // Auth tabs
    const authTabs = document.querySelectorAll('.auth-tab');
    authTabs.forEach(tab => {
        tab.addEventListener('click', () => switchAuthTab(tab));
    });

    // Login form
    const loginForm = document.getElementById('loginFormElement');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }

    // Register form
    const registerForm = document.getElementById('registerFormElement');
    if (registerForm) {
        registerForm.addEventListener('submit', handleRegister);
    }

    // Profile tabs
    const profileTabs = document.querySelectorAll('.profile-tab');
    profileTabs.forEach(tab => {
        tab.addEventListener('click', () => switchProfileTab(tab));
    });

    // Order filters
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => filterOrders(btn));
    });

    // Order detail cards
    attachOrderCardListeners();

    // Profile edit form
    const editProfileForm = document.getElementById('editProfileForm');
    if (editProfileForm) {
        editProfileForm.addEventListener('submit', handleProfileUpdate);
    }

    // Address edit form
    const editAddressForm = document.getElementById('editAddressForm');
    if (editAddressForm) {
        editAddressForm.addEventListener('submit', handleAddressUpdate);
    }
}

// Switch between login and register tabs
function switchAuthTab(clickedTab) {
    const tabs = document.querySelectorAll('.auth-tab');
    const forms = document.querySelectorAll('.auth-form');

    tabs.forEach(tab => tab.classList.remove('active'));
    clickedTab.classList.add('active');

    const targetTab = clickedTab.dataset.tab;
    forms.forEach(form => {
        form.classList.remove('active');
        if (form.id === targetTab + 'Form') {
            form.classList.add('active');
        }
    });
}

// Handle login
function handleLogin(e) {
    e.preventDefault();

    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;
    const errorMsg = document.getElementById('loginError');

    const formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);
    formData.append('action', 'login');

    fetch('auth.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect || 'account.php';
            } else {
                errorMsg.textContent = data.message;
                errorMsg.classList.add('show');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorMsg.textContent = 'Er is een fout opgetreden';
            errorMsg.classList.add('show');
        });
}

// Handle registration
function handleRegister(e) {
    e.preventDefault();

    const name = document.getElementById('registerName').value;
    const email = document.getElementById('registerEmail').value;
    const password = document.getElementById('registerPassword').value;
    const passwordConfirm = document.getElementById('registerPasswordConfirm').value;
    const acceptTerms = document.getElementById('acceptTerms').checked;
    const errorMsg = document.getElementById('registerError');

    if (password !== passwordConfirm) {
        errorMsg.textContent = 'Wachtwoorden komen niet overeen';
        errorMsg.classList.add('show');
        return;
    }

    if (!acceptTerms) {
        errorMsg.textContent = 'Je moet akkoord gaan met de voorwaarden';
        errorMsg.classList.add('show');
        return;
    }

    const formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    formData.append('password', password);
    formData.append('password_confirm', passwordConfirm);
    formData.append('action', 'register');

    fetch('auth.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect || 'account.php';
            } else {
                errorMsg.textContent = data.message;
                errorMsg.classList.add('show');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorMsg.textContent = 'Er is een fout opgetreden';
            errorMsg.classList.add('show');
        });
}

// Switch profile tabs
function switchProfileTab(clickedTab) {
    const tabs = document.querySelectorAll('.profile-tab');
    const contents = document.querySelectorAll('.profile-tab-content');

    tabs.forEach(tab => tab.classList.remove('active'));
    clickedTab.classList.add('active');

    const targetTab = clickedTab.dataset.tab;
    contents.forEach(content => {
        content.classList.remove('active');
        if (content.id === targetTab + 'Tab') {
            content.classList.add('active');
        }
    });
}

// Filter orders
function filterOrders(clickedBtn) {
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => btn.classList.remove('active'));
    clickedBtn.classList.add('active');

    const filter = clickedBtn.dataset.filter;
    const orderCards = document.querySelectorAll('.order-card');

    orderCards.forEach(card => {
        const status = card.dataset.status; // bv. 'active', 'pending', 'ready', 'delivered'

        if (filter === 'all') {
            card.style.display = 'block';
            return;
        }

        const isActiveStatus = ['active', 'pending', 'confirmed', 'preparing'].includes(status);
        const isCompletedStatus = ['ready', 'delivered', 'completed'].includes(status);

        if (filter === 'active' && isActiveStatus) {
            card.style.display = 'block';
        } else if (filter === 'completed' && isCompletedStatus) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Attach click handlers to order cards
function attachOrderCardListeners() {
    const orderCards = document.querySelectorAll('.order-card');
    orderCards.forEach(card => {
        card.addEventListener('click', () => {
            const orderId = card.dataset.orderId;
            if (orderId) {
                loadOrderDetails(orderId);
            }
        });
    });
}

// Bewaar laatst geladen orderitems voor "bestel opnieuw"
let lastOrderItems = [];
let lastOrderInfo = null;

// Load order details from backend
function loadOrderDetails(orderId) {
    fetch(`account.php?action=orderDetails&id=${encodeURIComponent(orderId)}`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Order details error:', data.message);
                return;
            }
            populateOrderModal(data.order, data.items);
            openOrderModal();
        })
        .catch(err => {
            console.error('Order details fetch error:', err);
        });
}

// Fill modal with order data
function populateOrderModal(order, items) {
    const titleEl = document.getElementById('orderModalTitle');
    const metaEl = document.getElementById('orderModalMeta');
    const itemsEl = document.getElementById('orderModalItems');
    const addrSection = document.getElementById('orderModalAddressSection');
    const addrEl = document.getElementById('orderModalAddress');
    const notesSection = document.getElementById('orderModalNotesSection');
    const notesEl = document.getElementById('orderModalNotes');

    if (!titleEl || !metaEl || !itemsEl) return;

    const orderNumber = order.order_number || `#${order.order_id}`;
    const status = order.order_status || order.status || 'pending';
    const createdAt = order.created_at || order.order_date || null;
    const total = order.total_amount || order.total || 0;
    const payment = order.payment_method || '-';

    titleEl.textContent = `Bestelling ${orderNumber}`;

    const dateText = createdAt ? new Date(createdAt).toLocaleString('nl-NL') : '-';
    metaEl.textContent = `Datum: ${dateText} • Status: ${status} • Totaal: €${Number(total).toFixed(2)} • Betaalwijze: ${payment}`;

    // Items
    itemsEl.innerHTML = '';
    if (items && items.length) {
        // Sla items en order op voor "bestel opnieuw"
        lastOrderItems = items;
        lastOrderInfo = order;

        items.forEach(item => {
            const row = document.createElement('div');
            row.className = 'order-modal-item';
            const qty = item.quantity || 1;
            const price = item.unit_price || item.price || 0;
            const subtotal = item.subtotal || qty * price;
            row.innerHTML = `
                <div>
                    <strong>${item.name || item.item_name}</strong>
                    <span class="order-modal-item-meta">${qty} × €${Number(price).toFixed(2)}</span>
                </div>
                <div class="order-modal-item-total">€${Number(subtotal).toFixed(2)}</div>
            `;
            itemsEl.appendChild(row);
        });
    } else {
        itemsEl.innerHTML = '<p class="order-modal-empty">Geen items gevonden voor deze bestelling.</p>';
    }

    // Address (optional)
    const address = order.delivery_address || null;
    if (address && addrSection && addrEl) {
        addrSection.style.display = 'block';
        addrEl.textContent = address;
    } else if (addrSection) {
        addrSection.style.display = 'none';
    }

    // Notes (optional)
    const notes = order.notes || null;
    if (notes && notesSection && notesEl) {
        notesSection.style.display = 'block';
        notesEl.textContent = notes;
    } else if (notesSection) {
        notesSection.style.display = 'none';
    }

    // Close handlers
    const closeBtn = document.getElementById('orderModalClose');
    const backdrop = document.getElementById('orderModalBackdrop');
    if (closeBtn) {
        closeBtn.onclick = closeOrderModal;
    }
    if (backdrop) {
        backdrop.onclick = closeOrderModal;
    }

    // Reorder knop
    const reorderBtn = document.getElementById('orderModalReorder');
    if (reorderBtn) {
        reorderBtn.onclick = reorderLastOrder;
    }
}

function openOrderModal() {
    const modal = document.getElementById('orderDetailModal');
    if (modal) modal.classList.remove('hidden');
}

function closeOrderModal() {
    const modal = document.getElementById('orderDetailModal');
    if (modal) modal.classList.add('hidden');
}

// Voeg laatste bestelling opnieuw toe aan de winkelwagen
function reorderLastOrder() {
    if (!Array.isArray(lastOrderItems) || lastOrderItems.length === 0) {
        console.warn('Geen orderitems gevonden om opnieuw te bestellen.');
        return;
    }

    if (typeof cart === 'undefined') {
        console.error('Cart systeem (cart.js) niet gevonden.');
        return;
    }

    lastOrderItems.forEach(item => {
        const name = item.name || item.item_name || 'Besteld item';
        const price = parseFloat(item.unit_price || item.price || 0);
        const quantity = item.quantity || 1;

        // Zoek bestaand item zonder customizations
        const existing = cart.find(c => c.name === name && !c.customizations);
        if (existing) {
            existing.quantity += quantity;
        } else {
            cart.push({
                name,
                price,
                quantity,
                image: null,
                originalName: name
            });
        }
    });

    if (typeof updateCart === 'function') {
        updateCart();
    }

    // Open de cart sidebar zodat de gebruiker meteen ziet wat er is toegevoegd
    const sidebar = document.getElementById('cartSidebar');
    if (sidebar && !sidebar.classList.contains('open') && typeof toggleCart === 'function') {
        toggleCart();
    }

    if (typeof showNotification === 'function') {
        const totaal = lastOrderInfo && (lastOrderInfo.total_amount || lastOrderInfo.total)
            ? Number(lastOrderInfo.total_amount || lastOrderInfo.total).toFixed(2)
            : '';
        const suffix = totaal ? ` (totaal €${totaal})` : '';
        showNotification(`Bestelling opnieuw toegevoegd aan je winkelwagen${suffix}`);
    }
}

// Profile Edit Functions
function showEditProfile() {
    document.getElementById('profileInfoDisplay').style.display = 'none';
    document.getElementById('profileInfoEdit').style.display = 'block';
}

function cancelEditProfile() {
    document.getElementById('profileInfoDisplay').style.display = 'block';
    document.getElementById('profileInfoEdit').style.display = 'none';
}

// Handle profile update
function handleProfileUpdate(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append('action', 'update_profile');
    formData.append('name', document.getElementById('editName').value);

    fetch('update_profile.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update display
                document.getElementById('displayName').textContent = document.getElementById('editName').value;
                document.getElementById('userName').textContent = document.getElementById('editName').value;
                cancelEditProfile();
            } else {
                document.getElementById('profileEditError').textContent = data.message;
                document.getElementById('profileEditError').classList.add('show');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('profileEditError').textContent = 'Er is een fout opgetreden';
            document.getElementById('profileEditError').classList.add('show');
        });
}

// Address Edit Functions
function showEditAddress() {
    document.getElementById('addressDisplay').style.display = 'none';
    document.getElementById('addressEdit').style.display = 'block';
}

function cancelEditAddress() {
    document.getElementById('addressDisplay').style.display = 'block';
    document.getElementById('addressEdit').style.display = 'none';
}

// Handle address update
function handleAddressUpdate(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append('action', 'update_address');
    formData.append('street', document.getElementById('editAddress').value);
    formData.append('postal_code', document.getElementById('editZipcode').value);
    formData.append('city', document.getElementById('editCity').value);

    fetch('update_profile.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update display
                document.getElementById('displayAddress').textContent = data.street;
                document.getElementById('displayZipcode').textContent = data.postal_code;
                document.getElementById('displayCity').textContent = data.city;
                cancelEditAddress();
            } else {
                document.getElementById('addressEditError').textContent = data.message;
                document.getElementById('addressEditError').classList.add('show');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('addressEditError').textContent = 'Er is een fout opgetreden';
            document.getElementById('addressEditError').classList.add('show');
        });
}

// Avatar Selection Functions
function initAvatarSelection() {
    const changeAvatarBtn = document.getElementById('changeAvatarBtn');
    const avatarModal = document.getElementById('avatarModal');
    const closeAvatarModal = document.getElementById('closeAvatarModal');
    const avatarGrid = document.getElementById('avatarGrid');

    // Open avatar modal
    if (changeAvatarBtn) {
        changeAvatarBtn.addEventListener('click', () => {
            avatarModal.classList.remove('hidden');
            loadAvatarOptions();
        });
    }

    // Close avatar modal
    if (closeAvatarModal) {
        closeAvatarModal.addEventListener('click', () => {
            avatarModal.classList.add('hidden');
        });
    }

    // Close on backdrop click
    if (avatarModal) {
        avatarModal.addEventListener('click', (e) => {
            if (e.target === avatarModal) {
                avatarModal.classList.add('hidden');
            }
        });
    }
}

// Load avatar options (8 icons)
function loadAvatarOptions() {
    const avatarGrid = document.getElementById('avatarGrid');
    if (!avatarGrid) return;

    avatarGrid.innerHTML = '';

    // Create 8 avatar options
    for (let i = 1; i <= 8; i++) {
        const avatarOption = document.createElement('div');
        avatarOption.className = 'avatar-option';
        avatarOption.innerHTML = `<img src="public/img/icon${i}.jpg" alt="Icon ${i}">`;
        avatarOption.addEventListener('click', () => selectAvatar(i));
        avatarGrid.appendChild(avatarOption);
    }
}

// Select avatar
function selectAvatar(iconNumber) {
    const formData = new FormData();
    formData.append('action', 'update_avatar');
    formData.append('profile_icon', iconNumber);

    fetch('update_profile.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update avatar display
                const userAvatar = document.getElementById('userAvatar');
                if (userAvatar) {
                    userAvatar.src = `public/img/icon${iconNumber}.jpg`;
                }

                // Close modal
                const avatarModal = document.getElementById('avatarModal');
                if (avatarModal) {
                    avatarModal.classList.add('hidden');
                }
            } else {
                console.error('Avatar update failed:', data.message);
                alert('Er is een fout opgetreden bij het updaten van je avatar');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Er is een fout opgetreden');
        });
}

