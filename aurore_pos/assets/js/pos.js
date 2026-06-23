let cart = [];

function addToCart(id, name, price, maxStock) {
    let existing = cart.find(item => item.id === id);
    let newQty = existing ? existing.qty + 1 : 1;
    if (newQty > maxStock) {
        alert("Not enough stock! Only " + maxStock + " available.");
        return;
    }
    if (existing) {
        existing.qty++;
    } else {
        cart.push({ id: id, name: name, price: price, qty: 1, maxStock: maxStock });
    }
    updateCartDisplay();
}

function updateQuantity(index, newQty) {
    if (newQty < 1) {
        removeFromCart(index);
        return;
    }
    if (newQty > cart[index].maxStock) {
        alert("Cannot exceed available stock. Only " + cart[index].maxStock + " left.");
        return;
    }
    cart[index].qty = newQty;
    updateCartDisplay();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCartDisplay();
}

function clearCart() {
    cart = [];
    updateCartDisplay();
    document.getElementById('paymentAmount').value = '';
    document.getElementById('changeDisplay').innerHTML = '';
}

function updateCartDisplay() {
    let container = document.getElementById('cartItems');
    let total = 0;
    if (cart.length === 0) {
        container.innerHTML = '<div class="text-center text-muted py-4">No items in cart</div>';
        document.getElementById('cartTotal').innerText = "0.00";
        return;
    }
    let html = "";
    cart.forEach((item, idx) => {
        let subtotal = item.price * item.qty;
        total += subtotal;
        html += `
            <div class="cart-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>${escapeHtml(item.name)}</strong><br>
                    <small>$${item.price.toFixed(2)} × ${item.qty}</small>
                </div>
                <div class="text-end">
                    <div class="fw-bold">$${subtotal.toFixed(2)}</div>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${idx}, ${item.qty - 1})">−</button>
                        <span class="mx-1">${item.qty}</span>
                        <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${idx}, ${item.qty + 1})">+</button>
                        <button class="btn btn-sm btn-outline-danger ms-1" onclick="removeFromCart(${idx})">✕</button>
                    </div>
                </div>
            </div>
        `;
    });
    container.innerHTML = html;
    document.getElementById('cartTotal').innerText = total.toFixed(2);
    calculateChange();
}

function calculateChange() {
    let total = parseFloat(document.getElementById('cartTotal').innerText);
    let payment = parseFloat(document.getElementById('paymentAmount').value);
    let changeDiv = document.getElementById('changeDisplay');
    if (isNaN(payment)) {
        changeDiv.innerHTML = "";
        return;
    }
    if (payment >= total && total > 0) {
        let change = payment - total;
        changeDiv.innerHTML = `<span class="text-success">Change: $${change.toFixed(2)}</span>`;
    } else if (payment < total && payment > 0) {
        changeDiv.innerHTML = `<span class="text-danger">Insufficient: Need $${(total - payment).toFixed(2)} more</span>`;
    } else {
        changeDiv.innerHTML = "";
    }
}

function processPayment() {
    if (cart.length === 0) {
        alert("Cart is empty!");
        return;
    }
    let total = parseFloat(document.getElementById('cartTotal').innerText);
    let payment = parseFloat(document.getElementById('paymentAmount').value);
    if (isNaN(payment) || payment < total) {
        alert("Please enter a valid payment amount that covers the total.");
        return;
    }
    let form = document.getElementById('paymentForm');
    let cartInput = document.getElementById('cartDataInput');
    cartInput.value = JSON.stringify({ cart: cart, payment: payment });
    form.submit();
}

function escapeHtml(str) {
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

if (document.getElementById('paymentAmount')) {
    document.getElementById('paymentAmount').addEventListener('input', calculateChange);
}