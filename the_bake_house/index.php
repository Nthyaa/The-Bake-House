<?php
$api_url = 'http://localhost/SI25I/the_bake_house/api';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🍞 The Bake House</title>
    <link rel="stylesheet" href="index.css">
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>
    <style>
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #fff; margin: 5% auto; padding: 20px; width: 350px; border-radius: 8px; display: flex; flex-direction: column; gap: 10px; }
        .btn-action { background-color: #27ae60; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-toggle { background: none; color: #2980b9; border: none; cursor: pointer; text-decoration: underline; }
        .order-item { border: 1px solid #ddd; padding: 10px; margin: 10px 0; border-radius: 8px; background: #f9f9f9; position: relative; }
        .order-item h4 { margin: 0 0 5px 0; }
        .order-item .status { font-weight: bold; }
        .status-pending { color: orange; }
        .status-paid { color: green; }
        .status-completed { color: blue; }
        .status-cancelled { color: red; }
        .admin-badge { background: purple; color: white; padding: 2px 10px; border-radius: 10px; font-size: 12px; }
        .cart-section { max-height: 600px; overflow-y: auto; }
        .order-actions button { margin: 2px; padding: 4px 10px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-status { background: #3498db; color: white; }
        .btn-status:hover { opacity: 0.8; }
        .btn-complete { background: #2ecc71; color: white; }
        .btn-cancel { background: #e74c3c; color: white; }
        .btn-delete { background: #c0392b; color: white; }
        .btn-delete:hover { opacity: 0.8; }
        .btn-detail { background: #9b59b6; color: white; }
        .btn-detail:hover { opacity: 0.8; }
        .btn-tambah-menu { background: #27ae60; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; width: 100%; }
        .btn-tambah-menu:hover { opacity: 0.9; }
        .product-card { border: 1px solid #ddd; padding: 10px; margin: 10px; border-radius: 8px; background: white; }
        .product-card.out-of-stock { opacity: 0.7; background: #f5f5f5; }
        .product-card .admin-controls { display: flex; gap: 5px; margin-top: 5px; flex-wrap: wrap; }
        .product-card .admin-controls button { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-edit { background: #3498db; color: white; }
        .btn-delete-product { background: #e74c3c; color: white; }
        .btn-add-cart { background: #27ae60; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-add-cart:disabled { background: #ccc; cursor: not-allowed; }
        .btn-reduce-stock { background: #e67e22; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-reduce-stock:hover { opacity: 0.8; }
        .btn-reduce-stock:disabled { background: #ccc; cursor: not-allowed; }
        .toast-msg { position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); background: #333; color: white; padding: 12px 24px; border-radius: 8px; z-index: 9999; font-size: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); }
        .btn-export { background: #27ae60; color: white; border: none; padding: 8px 20px; border-radius: 4px; cursor: pointer; }
        .btn-export:hover { opacity: 0.8; }
        .btn-reset { background: #e67e22; color: white; border: none; padding: 8px 20px; border-radius: 4px; cursor: pointer; }
        .btn-reset:hover { opacity: 0.8; }
        .detail-modal { display: none; position: fixed; z-index: 1001; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .detail-content { background-color: #fff; margin: 5% auto; padding: 20px; width: 500px; border-radius: 8px; max-height: 80vh; overflow-y: auto; }
        .detail-content h2 { margin-bottom: 15px; color: #2C3E50; border-bottom: 2px solid #9b59b6; padding-bottom: 10px; }
        .detail-item { border-bottom: 1px solid #eee; padding: 8px 0; display: flex; justify-content: space-between; }
        .detail-item .item-name { font-weight: bold; }
        .detail-item .item-qty { color: #666; }
        .detail-item .item-price { color: #27ae60; }
        .detail-total { margin-top: 15px; padding-top: 10px; border-top: 2px solid #9b59b6; font-weight: bold; font-size: 16px; }
        .btn-close-detail { background: #e74c3c; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; margin-top: 15px; }
        .btn-close-detail:hover { opacity: 0.8; }
        .detail-empty { color: #999; font-style: italic; }
        .admin-controls { display: flex; gap: 5px; margin-top: 5px; flex-wrap: wrap; justify-content: center; }
        .admin-controls button { font-size: 12px; }
    </style>
</head>
<body>

<div class="container">
    <header><h1>🥐 The Bake House</h1></header>

    <div class="auth-section">
        <div id="userDisplay" style="display:none; margin-bottom:10px;"></div>
        <button id="loginBtn">🔑 Login</button>
        <button id="logoutBtn" style="display:none;">🚪 Logout</button>
        <button id="adminPanelBtn" style="display:none; background-color: purple; color: white;">⚙️ Admin Panel</button>
    </div>

    <div class="shop-grid">
        <div class="products-section">
            <div class="section-title">🍞 Menu Hari Ini</div>
            <div id="productList"><p>Loading produk...</p></div>
        </div>
        <div class="cart-section">
            <div class="section-title" id="cartTitle">🛒 Keranjang</div>
            <div id="cartContainer"></div>
            <div id="cartSummary" style="display: none;">
                <p>Total: <span id="totalPrice">Rp 0</span></p>
                <button id="checkoutBtn" class="btn-action" style="width:100%;">Pesan Sekarang</button>
            </div>
        </div>
    </div>
</div>

<!-- Login Modal -->
<div class="modal" id="loginModal">
    <div class="modal-content">
        <h2 id="modalTitle">🔑 Login</h2>
        <input type="text" id="loginUsername" placeholder="Username">
        <input type="password" id="loginPassword" placeholder="Password">
        <input type="text" id="registerFullName" placeholder="Nama Lengkap" style="display:none;">
        <button class="btn-toggle" onclick="toggleMode()" id="toggleBtn">Belum punya akun? Daftar</button>
        <button class="btn-action" id="mainBtn" onclick="handleAuth()">Login</button>
        <button onclick="hideLogin()">Batal</button>
    </div>
</div>

<!-- Product Modal (Admin) -->
<div class="modal" id="productModal">
    <div class="modal-content">
        <h2 id="productModalTitle">Tambah Produk</h2>
        <input type="hidden" id="editProductId">
        <input type="text" id="prodName" placeholder="Nama Produk">
        <input type="number" id="prodPrice" placeholder="Harga">
        <input type="number" id="prodStock" placeholder="Stok">
        <input type="text" id="prodEmoji" placeholder="Emoji (e.g., 🍞)">
        <button class="btn-action" onclick="saveProduct()">Simpan Produk</button>
        <button onclick="document.getElementById('productModal').style.display='none'">Batal</button>
    </div>
</div>

<!-- Detail Order Modal -->
<div class="detail-modal" id="detailModal">
    <div class="detail-content">
        <h2>📋 Detail Order</h2>
        <div id="detailBody"></div>
        <button class="btn-close-detail" onclick="closeDetail()">Tutup</button>
    </div>
</div>

<script>
const API_URL = '<?php echo $api_url; ?>'; 
let cart = JSON.parse(localStorage.getItem("the_bake_house_cart")) || [];
let currentUser = JSON.parse(localStorage.getItem('user')) || null;
let isRegisterMode = false;
let orders = [];

document.addEventListener('DOMContentLoaded', () => {
    renderProducts();
    renderCart();
    updateUI();
    if (currentUser && currentUser.role === 'admin') {
        fetchOrders();
    }
});

// --- NOTIFIKASI ---
function showNotif(message) {
    const old = document.querySelector('.toast-msg');
    if (old) old.remove();
    
    const div = document.createElement('div');
    div.className = 'toast-msg';
    div.textContent = message;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 3000);
}

// --- UI & AUTH ---
function updateUI() {
    const loginBtn = document.getElementById('loginBtn');
    const logoutBtn = document.getElementById('logoutBtn');
    const adminBtn = document.getElementById('adminPanelBtn');
    const userDisplay = document.getElementById('userDisplay');
    const cartTitle = document.getElementById('cartTitle');
    
    const savedUser = localStorage.getItem('user');
    if (savedUser && !currentUser) {
        currentUser = JSON.parse(savedUser);
    }
    
    if (currentUser) {
        loginBtn.style.display = 'none';
        logoutBtn.style.display = 'inline-block';
        userDisplay.style.display = 'block';
        userDisplay.innerText = `👤 Halo, ${currentUser.full_name}`;
        
        if (currentUser.role === 'admin') {
            adminBtn.style.display = 'inline-block';
            cartTitle.innerText = '📋 Daftar Pesanan';
            renderProducts();
        } else {
            adminBtn.style.display = 'none';
            cartTitle.innerText = '🛒 Keranjang';
        }
    } else {
        loginBtn.style.display = 'inline-block';
        logoutBtn.style.display = 'none';
        adminBtn.style.display = 'none';
        userDisplay.style.display = 'none';
        cartTitle.innerText = '🛒 Keranjang';
    }
}

function toggleMode() {
    isRegisterMode = !isRegisterMode;
    document.getElementById('modalTitle').innerText = isRegisterMode ? "📝 Daftar Akun" : "🔑 Login";
    document.getElementById('mainBtn').innerText = isRegisterMode ? "Daftar Sekarang" : "Login";
    document.getElementById('toggleBtn').innerText = isRegisterMode ? "Sudah punya akun? Login" : "Belum punya akun? Daftar";
    document.getElementById('registerFullName').style.display = isRegisterMode ? "block" : "none";
}

async function handleAuth() {
    const username = document.getElementById('loginUsername').value;
    const password = document.getElementById('loginPassword').value;
    const full_name = document.getElementById('registerFullName').value;
    
    try {
        const res = await fetch(`${API_URL}${isRegisterMode ? '/register.php' : '/login.php'}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password, full_name })
        });
        const data = await res.json();
        if (res.ok) {
            if (!isRegisterMode) { 
                const userData = {
                    id: data.user.id || 1,
                    username: data.user.username || username,
                    full_name: data.user.full_name || full_name || username,
                    role: data.user.role || 'customer'
                };
                localStorage.setItem('user', JSON.stringify(userData)); 
                currentUser = userData; 
                hideLogin(); 
                updateUI();
                if (currentUser.role === 'admin') {
                    cart = [];
                    localStorage.removeItem('the_bake_house_cart');
                    renderCart();
                    fetchOrders();
                }
                renderProducts();
            } else { 
                alert("Daftar berhasil!"); 
                toggleMode(); 
            }
        } else { 
            alert(data.message || 'Terjadi kesalahan');
        }
    } catch(e) {
        alert('Gagal koneksi ke server!');
    }
}

// --- PRODUCT & ADMIN LOGIC ---
async function fetchProducts() {
    try { 
        const res = await fetch(`${API_URL}/products.php`); 
        return await res.json(); 
    } catch(e) { 
        return []; 
    }
}

// =============================================
// RENDER PRODUCTS - DENGAN TOMBOL KURANGI STOK
// =============================================
async function renderProducts() {
    const list = document.getElementById("productList");
    const products = await fetchProducts();
    
    if (products.length === 0) {
        list.innerHTML = '<p>Belum ada produk</p>';
        return;
    }
    
    let html = '';
    const isAdmin = currentUser && currentUser.role === 'admin';
    
    if (isAdmin) {
        html += `
            <div style="grid-column: 1 / -1; margin-bottom: 10px;">
                <button onclick="document.getElementById('productModal').style.display='block'" class="btn-tambah-menu">
                    ➕ Tambah Menu
                </button>
            </div>
        `;
    }
    
    html += products.map(prod => {
        const isOutOfStock = prod.stock <= 0;
        
        return `
        <div class="product-card ${isOutOfStock ? 'out-of-stock' : ''}">
            <h3>${prod.emoji || '🍞'} ${prod.name}</h3>
            <p>Rp ${parseInt(prod.price).toLocaleString()} | Stok: ${prod.stock}</p>
            ${isAdmin ? `
                <div class="admin-controls">
                    <button class="btn-edit" onclick="openEditModal(${prod.id}, '${prod.name}', ${prod.price}, ${prod.stock}, '${prod.emoji}')">✏️ Edit</button>
                    <button class="btn-delete-product" onclick="deleteProduct(${prod.id})">🗑️ Hapus</button>
                    <button class="btn-add-cart" onclick="addToCart(${prod.id}, '${prod.name}', ${prod.price})" ${isOutOfStock ? 'disabled' : ''}>
                        ${isOutOfStock ? 'Stok Habis' : '+ Tambah'}
                    </button>
                    <button class="btn-reduce-stock" onclick="reduceStock(${prod.id}, '${prod.name}', ${prod.stock})" ${isOutOfStock ? 'disabled' : ''}>
                        📦 Kurangi Stok
                    </button>
                </div>
            ` : `
                <button class="btn-add-cart" onclick="addToCart(${prod.id}, '${prod.name}', ${prod.price})" ${isOutOfStock ? 'disabled' : ''}>
                    ${isOutOfStock ? 'Stok Habis' : '+ Tambah'}
                </button>
            `}
        </div>
    `}).join('');
    
    list.innerHTML = html;
}

// =============================================
// SAVE PRODUCT - FIXED!
// =============================================
async function saveProduct() {
    const id = document.getElementById('editProductId').value;
    const name = document.getElementById('prodName').value;
    const price = document.getElementById('prodPrice').value;
    const stock = document.getElementById('prodStock').value;
    const emoji = document.getElementById('prodEmoji').value;
    
    if (!name) {
        alert('❌ Nama produk wajib diisi!');
        return;
    }
    
    const data = {
        id: id || undefined,
        name: name,
        price: parseInt(price) || 0,
        stock: parseInt(stock) || 0,
        emoji: emoji || '🍞'
    };
    
    console.log('📤 Menyimpan produk:', data);
    
    try {
        const res = await fetch(`${API_URL}/products.php`, {
            method: id ? 'PUT' : 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        
        if (res.ok) { 
            alert("✅ Berhasil!"); 
            document.getElementById('productModal').style.display = 'none'; 
            renderProducts(); 
        } else {
            const error = await res.json();
            alert('❌ Gagal menyimpan produk: ' + (error.message || 'Unknown error'));
        }
    } catch(e) {
        alert('❌ Error: ' + e.message);
    }
}

// =============================================
// DELETE PRODUCT - FIXED!
// =============================================
async function deleteProduct(id) {
    console.log('🗑️ Menghapus produk ID:', id);
    
    if (!id) {
        alert('❌ ID produk tidak valid!');
        return;
    }
    
    if (!confirm("⚠️ Hapus produk ini?")) return;
    
    try {
        console.log('📤 Mengirim request ke:', `${API_URL}/products.php`);
        
        const response = await fetch(`${API_URL}/products.php`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        });
        
        console.log('📥 Response status:', response.status);
        
        if (response.ok) {
            const data = await response.json();
            console.log('✅ Response:', data);
            alert('✅ Produk berhasil dihapus!');
            renderProducts();
        } else {
            const error = await response.json();
            console.error('❌ Error:', error);
            alert('❌ Gagal hapus produk: ' + (error.message || 'Unknown error'));
        }
    } catch(e) {
        console.error('❌ Exception:', e);
        alert('❌ Gagal hapus produk: ' + e.message);
    }
}

// =============================================
// REDUCE STOCK (Admin Only) - FIXED!
// =============================================
async function reduceStock(id, name, currentStock) {
    const qty = prompt(`📦 Stok saat ini: ${currentStock}\nMasukkan jumlah yang ingin dikurangi:`);
    if (qty === null) return;
    
    const quantity = parseInt(qty);
    if (isNaN(quantity) || quantity <= 0) {
        alert('❌ Jumlah harus angka positif!');
        return;
    }
    
    if (quantity > currentStock) {
        alert(`❌ Stok tidak cukup! Stok saat ini: ${currentStock}`);
        return;
    }
    
    if (!confirm(`⚠️ Kurangi stok ${name} sebanyak ${quantity}?\nStok: ${currentStock} → ${currentStock - quantity}`)) return;
    
    try {
        const newStock = currentStock - quantity;
        
        // Ambil data produk untuk mendapatkan harga asli
        const products = await fetchProducts();
        const product = products.find(p => p.id === id);
        if (!product) {
            alert('❌ Produk tidak ditemukan!');
            return;
        }
        
        const response = await fetch(`${API_URL}/products.php`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: id,
                name: product.name,
                price: product.price,      // ← HARGA ASLI
                stock: newStock,
                emoji: product.emoji || '🍞'
            })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            alert(`✅ Stok ${name} berhasil dikurangi!\n${currentStock} → ${newStock}`);
            renderProducts();
        } else {
            alert('❌ Gagal mengurangi stok: ' + (data.message || 'Unknown error'));
        }
    } catch(e) {
        alert('❌ Error: ' + e.message);
    }
}

// =============================================
// OPEN EDIT MODAL
// =============================================
function openEditModal(id, name, price, stock, emoji) {
    document.getElementById('editProductId').value = id;
    document.getElementById('prodName').value = name;
    document.getElementById('prodPrice').value = price;
    document.getElementById('prodStock').value = stock;
    document.getElementById('prodEmoji').value = emoji || '🍞';
    document.getElementById('productModal').style.display = 'block';
}

// --- CART ---
function addToCart(id, name, price) {
    const existing = cart.find(item => item.id === id);
    if (existing) {
        existing.quantity = (existing.quantity || 0) + 1;
    } else {
        cart.push({ id, name, price, quantity: 1 });
    }
    cart = cart.filter(item => item.quantity > 0);
    localStorage.setItem("the_bake_house_cart", JSON.stringify(cart));
    renderCart();
}

function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    localStorage.setItem("the_bake_house_cart", JSON.stringify(cart));
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cartContainer');
    const summary = document.getElementById('cartSummary');
    const isAdmin = currentUser && currentUser.role === 'admin';
    
    if (isAdmin) {
        renderOrders();
        return;
    }
    
    if (cart.length === 0) { 
        container.innerHTML = "🥨 Keranjang kosong"; 
        summary.style.display = "none"; 
        return; 
    }
    
    summary.style.display = "block";
    container.innerHTML = cart.map((item) => `
        <div style="border-bottom:1px solid #eee; padding:5px 0;">
            ${item.emoji || '🍞'} ${item.name} x${item.quantity || 1} 
            = Rp ${(item.price * (item.quantity || 1)).toLocaleString()}
            <button onclick="removeFromCart(${item.id})" style="color:red; margin-left:10px; background:none; border:none; cursor:pointer;">✕</button>
        </div>
    `).join('');
    
    const total = cart.reduce((s, i) => s + (i.price * (i.quantity || 1)), 0);
    document.getElementById('totalPrice').innerText = "Rp " + total.toLocaleString();
}

// --- ORDERS (Admin Only) ---
async function fetchOrders() {
    if (!currentUser || currentUser.role !== 'admin') return;
    
    try {
        const res = await fetch(`${API_URL}/orders.php`);
        if (!res.ok) throw new Error('Gagal fetch orders');
        orders = await res.json();
        orders.sort((a, b) => a.id - b.id);
        renderOrders();
    } catch(e) {
        console.error('Error fetching orders:', e);
        document.getElementById('cartContainer').innerHTML = '<p>Gagal memuat pesanan</p>';
    }
}

function renderOrders() {
    const container = document.getElementById('cartContainer');
    const summary = document.getElementById('cartSummary');
    const isAdmin = currentUser && currentUser.role === 'admin';
    
    if (!isAdmin) return;
    
    if (!orders || orders.length === 0) {
        container.innerHTML = "📋 Belum ada pesanan";
        summary.style.display = "none";
        return;
    }
    
    summary.style.display = "block";
    summary.innerHTML = `
        <div style="display:flex; gap:10px; margin-bottom:10px; flex-wrap:wrap;">
            <button onclick="exportExcel()" class="btn-export">
                📊 Export Excel
            </button>
            <button onclick="resetAllOrders()" class="btn-reset">
                🗑️ Reset Semua Order
            </button>
        </div>
    `;
    
    container.innerHTML = orders.map((order, index) => `
        <div class="order-item">
            <h4>#${index + 1}</h4>
            <p><strong>Order ID:</strong> ${order.id}</p>
            <p><strong>Customer:</strong> ${order.customer_name || order.username || 'Guest'}</p>
            <p><strong>Total:</strong> Rp ${parseInt(order.total_amount || order.grand_total).toLocaleString()}</p>
            <p><strong>Tanggal:</strong> ${order.created_at ? new Date(order.created_at).toLocaleString('id-ID') : '-'}</p>
            <p class="status status-${order.status || 'pending'}">
                <strong>Status:</strong> ${order.status || 'pending'}
            </p>
            <div class="order-actions">
                <button class="btn-status" onclick="updateOrderStatus(${order.id}, 'processing')">⏳ Proses</button>
                <button class="btn-complete" onclick="updateOrderStatus(${order.id}, 'completed')">✅ Selesai</button>
                <button class="btn-cancel" onclick="updateOrderStatus(${order.id}, 'cancelled')">❌ Batal</button>
                <button class="btn-delete" onclick="deleteOrder(${order.id})">🗑️ Hapus</button>
                <button class="btn-detail" onclick="showOrderDetail(${order.id})">📋 Detail</button>
            </div>
        </div>
    `).join('');
}

// --- SHOW ORDER DETAIL ---
function showOrderDetail(orderId) {
    const order = orders.find(o => o.id === orderId);
    if (!order) {
        alert('Order tidak ditemukan!');
        return;
    }
    
    const modal = document.getElementById('detailModal');
    const body = document.getElementById('detailBody');
    
    let html = `
        <div style="margin-bottom:15px;">
            <p><strong>Order ID:</strong> ${order.id}</p>
            <p><strong>Customer:</strong> ${order.customer_name || order.username || 'Guest'}</p>
            <p><strong>Total:</strong> Rp ${parseInt(order.total_amount || order.grand_total).toLocaleString()}</p>
            <p><strong>Status:</strong> <span class="status status-${order.status || 'pending'}">${order.status || 'pending'}</span></p>
            <p><strong>Tanggal:</strong> ${order.created_at ? new Date(order.created_at).toLocaleString('id-ID') : '-'}</p>
        </div>
        <h3 style="border-bottom:1px solid #ddd; padding-bottom:5px; color:#2C3E50;">📦 Item yang Dipesan</h3>
    `;
    
    if (order.items && order.items.length > 0) {
        order.items.forEach((item, index) => {
            const subtotal = (item.price || 0) * (item.quantity || 0);
            html += `
                <div class="detail-item">
                    <span class="item-name">${item.emoji || '🍞'} ${item.name || 'Produk'}</span>
                    <span class="item-qty">${item.quantity || 0} x Rp ${(item.price || 0).toLocaleString()}</span>
                    <span class="item-price">Rp ${subtotal.toLocaleString()}</span>
                </div>
            `;
        });
        
        const totalItem = order.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        html += `
            <div class="detail-total">
                <span>Total Item: ${order.items.length}</span>
                <span style="float:right;">Total: Rp ${totalItem.toLocaleString()}</span>
            </div>
        `;
    } else {
        html += `<p class="detail-empty">(Tidak ada item detail tersedia)</p>`;
    }
    
    body.innerHTML = html;
    modal.style.display = 'block';
}

function closeDetail() {
    document.getElementById('detailModal').style.display = 'none';
}

// --- RESET ALL ORDERS ---
async function resetAllOrders() {
    if (!confirm("⚠️ PERINGATAN! Ini akan MENGHAPUS SEMUA data order. Lanjutkan?")) return;
    if (!confirm("Yakin? Data yang dihapus TIDAK BISA DIKEMBALIKAN!")) return;
    
    try {
        const res = await fetch(`${API_URL}/orders.php`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ reset: true })
        });
        
        const data = await res.json();
        
        if (res.ok) {
            alert("✅ " + data.message);
            orders = [];
            renderOrders();
            showNotif("✅ Database order berhasil dibersihkan!");
        } else {
            alert('❌ Gagal reset: ' + (data.message || 'Unknown error'));
        }
    } catch(e) {
        alert('❌ Error: ' + e.message);
    }
}
// =============================================
// EXPORT EXCEL - PISAHKAN STATUS + CHART + MERGE
// =============================================
async function exportExcel() {
    try {
        const [ordersRes, productsRes] = await Promise.all([
            fetch(`${API_URL}/orders.php`),
            fetch(`${API_URL}/products.php`)
        ]);
        
        const orders = await ordersRes.json();
        const products = await productsRes.json();
        
        if (!orders || orders.length === 0) {
            alert('❌ Tidak ada data untuk di-export!');
            return;
        }
        
        // =============================================
        // 1. KELOMPOKKAN ORDER BERDASARKAN STATUS
        // =============================================
        const statusGroups = {
            'pending': { label: '⏳ Pending', orders: [], total: 0 },
            'processing': { label: '⚙️ Processing', orders: [], total: 0 },
            'completed': { label: '✅ Completed', orders: [], total: 0 },
            'cancelled': { label: '❌ Cancelled', orders: [], total: 0 },
            'paid': { label: '💰 Paid', orders: [], total: 0 }
        };
        
        orders.forEach(order => {
            const status = order.status || 'pending';
            if (statusGroups[status]) {
                statusGroups[status].orders.push(order);
                statusGroups[status].total += parseInt(order.total_amount || order.grand_total || 0);
            }
        });
        
        // =============================================
        // 2. HITUNG PENJUALAN PER PRODUK (HANYA COMPLETED)
        // =============================================
        const productSales = {};
        const completedOrders = statusGroups['completed'].orders;
        
        completedOrders.forEach(order => {
            if (order.items) {
                order.items.forEach(item => {
                    if (!productSales[item.product_id]) {
                        productSales[item.product_id] = {
                            name: item.name || `Produk ${item.product_id}`,
                            total_quantity: 0,
                            revenue: 0,
                            stock: 0
                        };
                    }
                    productSales[item.product_id].total_quantity += parseInt(item.quantity || 0);
                    productSales[item.product_id].revenue += parseInt(item.price || 0) * parseInt(item.quantity || 0);
                });
            }
        });
        
        // Tambahkan stok terakhir
        products.forEach(product => {
            if (productSales[product.id]) {
                productSales[product.id].stock = parseInt(product.stock || 0);
            }
        });
        
        const sortedProducts = Object.values(productSales).sort((a, b) => b.total_quantity - a.total_quantity);
        
        // =============================================
        // 3. BUAT WORKBOOK
        // =============================================
        const wb = XLSX.utils.book_new();
        
        // =============================================
        // SHEET 1: RINGKASAN PENJUALAN
        // =============================================
        const summaryData = [
            ['📊 LAPORAN PENJUALAN THE BAKE HOUSE'],
            [],
            ['TANGGAL:', new Date().toLocaleDateString('id-ID')],
            ['TOTAL ORDER:', orders.length],
            ['TOTAL PENDAPATAN:', 'Rp ' + orders.reduce((sum, o) => sum + parseInt(o.total_amount || o.grand_total || 0), 0).toLocaleString()],
            [],
            ['📋 RINGKASAN PER STATUS'],
            ['Status', 'Jumlah Order', 'Total Pendapatan (Rp)']
        ];
        
        let grandTotal = 0;
        let grandOrders = 0;
        Object.keys(statusGroups).forEach(key => {
            const group = statusGroups[key];
            if (group.orders.length > 0) {
                summaryData.push([group.label, group.orders.length, group.total]);
                grandTotal += group.total;
                grandOrders += group.orders.length;
            }
        });
        summaryData.push(['', '', '']);
        summaryData.push(['GRAND TOTAL', grandOrders, grandTotal]);
        
        const ws1 = XLSX.utils.aoa_to_sheet(summaryData);
        ws1['!cols'] = [{ wch: 25 }, { wch: 20 }, { wch: 25 }];
        ws1['!merges'] = [{ s: { r: 0, c: 0 }, e: { r: 0, c: 2 } }]; // MERGE JUDUL
        
        // Styling Summary
        for (let R = 0; R < summaryData.length; R++) {
            for (let C = 0; C < 3; C++) {
                const addr = XLSX.utils.encode_cell({ r: R, c: C });
                if (!ws1[addr]) ws1[addr] = { t: 's', v: '' };
                if (!ws1[addr].s) ws1[addr].s = {};
                ws1[addr].s.alignment = { horizontal: 'center', vertical: 'center' };
                ws1[addr].s.border = {
                    top: { style: 'thin' },
                    bottom: { style: 'thin' },
                    left: { style: 'thin' },
                    right: { style: 'thin' }
                };
            }
        }
        // Header Summary
        for (let C = 0; C < 3; C++) {
            const addr = XLSX.utils.encode_cell({ r: 7, c: C });
            if (!ws1[addr]) continue;
            ws1[addr].s.font = { bold: true, color: { rgb: "FFFFFF" }, sz: 12 };
            ws1[addr].s.fill = { fgColor: { rgb: "2C3E50" } };
        }
        // Judul Summary
        const titleAddr1 = XLSX.utils.encode_cell({ r: 0, c: 0 });
        if (ws1[titleAddr1]) {
            ws1[titleAddr1].s.font = { bold: true, sz: 16, color: { rgb: "2C3E50" } };
            ws1[titleAddr1].s.alignment = { horizontal: 'center', vertical: 'center' };
        }
        
        XLSX.utils.book_append_sheet(wb, ws1, 'Ringkasan');
        
        // =============================================
        // SHEET PER STATUS
        // =============================================
        Object.keys(statusGroups).forEach(key => {
            const group = statusGroups[key];
            if (group.orders.length === 0) return;
            
            const data = [];
            data.push([`📋 ${group.label} - ${group.orders.length} Order`]);
            data.push([]);
            data.push(['No', 'Order ID', 'Customer', 'Total (Rp)', 'Tanggal']);
            
            group.orders.forEach((order, index) => {
                const date = order.created_at ? new Date(order.created_at).toLocaleString('id-ID') : '-';
                data.push([
                    index + 1,
                    order.id,
                    order.customer_name || order.username || 'Guest',
                    parseInt(order.total_amount || order.grand_total || 0),
                    date
                ]);
            });
            
            data.push([]);
            data.push(['TOTAL', '', '', group.total, '']);
            
            const ws = XLSX.utils.aoa_to_sheet(data);
            ws['!cols'] = [{ wch: 10 }, { wch: 15 }, { wch: 25 }, { wch: 18 }, { wch: 25 }];
            ws['!merges'] = [{ s: { r: 0, c: 0 }, e: { r: 0, c: 4 } }]; // MERGE JUDUL
            
            // Styling
            const range = XLSX.utils.decode_range(ws['!ref']);
            for (let R = range.s.r; R <= range.e.r; R++) {
                for (let C = range.s.c; C <= range.e.c; C++) {
                    const addr = XLSX.utils.encode_cell({ r: R, c: C });
                    if (!ws[addr]) ws[addr] = { t: 's', v: '' };
                    if (!ws[addr].s) ws[addr].s = {};
                    ws[addr].s.alignment = { horizontal: 'center', vertical: 'center' };
                    ws[addr].s.border = {
                        top: { style: 'thin' },
                        bottom: { style: 'thin' },
                        left: { style: 'thin' },
                        right: { style: 'thin' }
                    };
                }
            }
            
            // Header
            for (let C = 0; C < 5; C++) {
                const addr = XLSX.utils.encode_cell({ r: 2, c: C });
                if (!ws[addr]) continue;
                ws[addr].s.font = { bold: true, color: { rgb: "FFFFFF" }, sz: 12 };
                ws[addr].s.fill = { fgColor: { rgb: "2C3E50" } };
            }
            
            // Judul
            const titleAddr = XLSX.utils.encode_cell({ r: 0, c: 0 });
            if (ws[titleAddr]) {
                ws[titleAddr].s.font = { bold: true, sz: 14, color: { rgb: "2C3E50" } };
                ws[titleAddr].s.alignment = { horizontal: 'center', vertical: 'center' };
            }
            
            // Total
            const totalRow = data.length - 1;
            for (let C = 0; C < 5; C++) {
                const addr = XLSX.utils.encode_cell({ r: totalRow, c: C });
                if (ws[addr]) {
                    ws[addr].s.font = { bold: true, color: { rgb: "C0392B" } };
                }
            }
            
            const safeName = group.label.replace(/[\[\]\:\*\?\/\\]/g, '').substring(0, 31);
            XLSX.utils.book_append_sheet(wb, ws, safeName);
        });
        
        // =============================================
        // SHEET: REKAP PRODUK (CHART DATA)
        // =============================================
        const chartData = [];
        chartData.push(['📊 REKAP PENJUALAN PRODUK (COMPLETED ONLY)']);
        chartData.push([]);
        chartData.push(['No', 'Produk', 'Total Terjual', 'Pendapatan (Rp)', 'Sisa Stok']);
        
        sortedProducts.forEach((prod, index) => {
            chartData.push([
                index + 1,
                prod.name,
                prod.total_quantity || 0,
                prod.revenue || 0,
                prod.stock || 0
            ]);
        });
        
        const totalSold = sortedProducts.reduce((sum, p) => sum + p.total_quantity, 0);
        const totalRevenue = sortedProducts.reduce((sum, p) => sum + p.revenue, 0);
        chartData.push([]);
        chartData.push(['TOTAL', '', totalSold, totalRevenue, '']);
        
        const wsChart = XLSX.utils.aoa_to_sheet(chartData);
        wsChart['!cols'] = [{ wch: 10 }, { wch: 25 }, { wch: 18 }, { wch: 18 }, { wch: 15 }];
        wsChart['!merges'] = [{ s: { r: 0, c: 0 }, e: { r: 0, c: 4 } }]; // MERGE JUDUL
        
        const rangeChart = XLSX.utils.decode_range(wsChart['!ref']);
        for (let R = rangeChart.s.r; R <= rangeChart.e.r; R++) {
            for (let C = rangeChart.s.c; C <= rangeChart.e.c; C++) {
                const addr = XLSX.utils.encode_cell({ r: R, c: C });
                if (!wsChart[addr]) wsChart[addr] = { t: 's', v: '' };
                if (!wsChart[addr].s) wsChart[addr].s = {};
                wsChart[addr].s.alignment = { horizontal: 'center', vertical: 'center' };
                wsChart[addr].s.border = {
                    top: { style: 'thin' },
                    bottom: { style: 'thin' },
                    left: { style: 'thin' },
                    right: { style: 'thin' }
                };
            }
        }
        
        for (let C = 0; C < 5; C++) {
            const addr = XLSX.utils.encode_cell({ r: 2, c: C });
            if (!wsChart[addr]) continue;
            wsChart[addr].s.font = { bold: true, color: { rgb: "FFFFFF" }, sz: 12 };
            wsChart[addr].s.fill = { fgColor: { rgb: "27AE60" } };
        }
        
        const titleChartAddr = XLSX.utils.encode_cell({ r: 0, c: 0 });
        if (wsChart[titleChartAddr]) {
            wsChart[titleChartAddr].s.font = { bold: true, sz: 14, color: { rgb: "27AE60" } };
            wsChart[titleChartAddr].s.alignment = { horizontal: 'center', vertical: 'center' };
        }
        
        XLSX.utils.book_append_sheet(wb, wsChart, 'Rekap Produk');
        
        // =============================================
        // 4. DOWNLOAD FILE
        // =============================================
        const today = new Date();
        const fileName = `Laporan_Penjualan_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}.xlsx`;
        const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
        
        const blob = new Blob([wbout], { type: 'application/octet-stream' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = fileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        const activeSheets = Object.keys(statusGroups).filter(k => statusGroups[k].orders.length > 0).length;
        showNotif(`✅ Berhasil export ${orders.length} data ke ${activeSheets + 2} sheet!`);
        
    } catch(e) {
        alert('❌ Gagal export data: ' + e.message);
        console.error(e);
    }
}

// --- ORDER ACTIONS ---
async function updateOrderStatus(orderId, status) {
    if (!confirm(`Ubah status pesanan #${orderId} menjadi "${status}"?`)) return;
    
    try {
        const res = await fetch(`${API_URL}/orders.php`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: orderId, status })
        });
        
        if (res.ok) {
            alert('Status berhasil diubah!');
            fetchOrders();
        } else {
            const data = await res.json();
            alert('Gagal mengubah status: ' + (data.message || 'Unknown error'));
        }
    } catch(e) {
        alert('Error: ' + e.message);
    }
}

async function deleteOrder(orderId) {
    if (!confirm(`Hapus pesanan #${orderId}?`)) return;
    
    try {
        const res = await fetch(`${API_URL}/orders.php`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: orderId })
        });
        
        if (res.ok) {
            alert('Pesanan berhasil dihapus!');
            fetchOrders();
        } else {
            const data = await res.json();
            alert('Gagal menghapus: ' + (data.message || 'Unknown error'));
        }
    } catch(e) {
        alert('Error: ' + e.message);
    }
}

// --- CHECKOUT (Stok Berkurang Otomatis) ---
async function handleCheckout() {
    if(!currentUser) { 
        alert("Login dulu ya!"); 
        document.getElementById('loginModal').style.display = 'block';
        return; 
    }
    if (cart.length === 0) {
        alert("Keranjang kosong!");
        return;
    }
    
    const validItems = cart.filter(item => item.quantity > 0);
    if (validItems.length === 0) {
        alert("Tidak ada item valid untuk dipesan!");
        return;
    }
    
    try {
        const products = await fetchProducts();
        for (const item of validItems) {
            const product = products.find(p => p.id === item.id);
            if (!product) {
                alert(`❌ Produk tidak ditemukan!`);
                return;
            }
            if (product.stock < item.quantity) {
                alert(`❌ Stok ${product.name} tidak mencukupi! (Stok: ${product.stock}, Pesan: ${item.quantity})`);
                return;
            }
        }
    } catch(e) {
        alert('❌ Gagal cek stok: ' + e.message);
        return;
    }
    
    try {
        const orderData = {
            items: validItems.map(item => ({
                product_id: item.id,
                quantity: item.quantity,
                price: item.price
            })),
            customer_name: currentUser.full_name || 'Guest',
            total_amount: validItems.reduce((s, i) => s + (i.price * i.quantity), 0)
        };
        
        const res = await fetch(`${API_URL}/orders.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(orderData)
        });
        
        if (res.ok) {
            alert("🎉 Pesanan berhasil!");
            cart = [];
            localStorage.removeItem('the_bake_house_cart');
            renderCart();
            renderProducts(); // Refresh stok
        } else {
            const data = await res.json();
            alert('❌ Gagal checkout: ' + (data.message || 'Unknown error'));
        }
    } catch(e) {
        alert('❌ Error: ' + e.message);
    }
}

// --- EVENT LISTENERS ---
document.getElementById('loginBtn').onclick = () => document.getElementById('loginModal').style.display = 'block';
document.getElementById('logoutBtn').onclick = () => { 
    localStorage.clear(); 
    currentUser = null;
    cart = [];
    orders = [];
    location.reload(); 
};
document.getElementById('adminPanelBtn').onclick = () => { 
    document.getElementById('productModal').style.display = 'block'; 
};
document.getElementById('checkoutBtn').onclick = handleCheckout;

// --- UTILS ---
function hideLogin() { document.getElementById('loginModal').style.display = 'none'; }

// Tutup modal jika klik di luar
window.onclick = function(event) {
    const modal = document.getElementById('detailModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

</script>
</body>
</html>