<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kasir POS - Smart POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#f4f7f6] min-h-screen">
    <nav class="bg-white border-b border-gray-100 px-8 py-4 flex justify-between items-center sticky top-0 z-50 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="bg-blue-600 p-2 rounded-lg">
                <i data-lucide="shopping-bag" class="w-5 h-5 text-white"></i>
            </div>
            <h1 class="text-xl font-bold text-gray-800 tracking-tight">Smart <span class="text-blue-600">POS</span></h1>
        </div>
        
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 bg-gray-50 px-4 py-2 rounded-full border border-gray-100">
                <i data-lucide="user" class="w-4 h-4"></i>
                <span id="user-display">Kasir Organisasi</span>
            </div>
            <button onclick="logout()" class="flex items-center gap-2 text-red-500 hover:bg-red-50 px-4 py-2 rounded-xl transition font-medium">
                <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
            </button>
        </div>
    </nav>

    <main class="p-8 flex gap-8 max-w-[1600px] mx-auto">
        <div class="flex-[2.5] flex flex-col">
            <div class="mb-8 flex justify-between items-end">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-1">Pilih Produk</h2>
                    <p class="text-gray-500 text-sm">Klik produk organisasi untuk menambahkan ke keranjang.</p>
                </div>
                <div class="relative w-80">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </span>
                    <input type="text" id="searchProduct" onkeyup="filterProducts()" placeholder="Cari Kode atau Nama Barang..." 
                           class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-200 bg-white shadow-sm focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>
            </div>

            <div id="product-grid" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6 overflow-y-auto max-h-[70vh] pr-2">
                </div>
        </div>

        <div class="flex-1 bg-white rounded-[2.5rem] shadow-xl border border-gray-100 flex flex-col sticky top-28 h-[82vh]">
            <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-900 flex items-center gap-3 text-lg">
                    <i data-lucide="shopping-cart" class="w-6 h-6 text-blue-600"></i> Keranjang
                </h3>
                <span id="item-count" class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-xs font-bold">0 Item</span>
            </div>

            <div id="cart-items" class="flex-1 overflow-y-auto p-8 space-y-6 text-sm">
                </div>

            <div class="p-8 bg-gray-50 rounded-b-[2.5rem] border-t border-gray-100 space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-900 font-bold text-lg">Total Akhir</span>
                    <span id="total-amount-display" class="font-black text-2xl text-blue-600">Rp 0</span>
                </div>
                
                <div class="pt-2 space-y-3">
                    <input type="number" id="cash-input" oninput="calculateChange()" placeholder="Uang Bayar (Rp)" 
                           class="w-full p-4 rounded-2xl border border-gray-200 bg-white text-xl font-bold focus:ring-2 focus:ring-blue-500 outline-none shadow-inner">
                    
                    <div class="flex justify-between p-4 rounded-2xl bg-white border border-gray-100 shadow-sm">
                        <span class="text-sm font-medium text-gray-500">Kembalian:</span>
                        <span id="change-display" class="font-bold">Rp 0</span>
                    </div>
                </div>
                
                <button onclick="processPayment()" class="w-full bg-blue-600 text-white py-5 rounded-[1.5rem] font-black text-lg hover:bg-blue-700 transition transform active:scale-95 shadow-lg shadow-blue-200 mt-2">
                    KONFIRMASI BAYAR
                </button>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();
        let products = [];
        let cart = [];
        const API_BASE = 'http://localhost:8080/api/v1';
        const token = localStorage.getItem('jwt_token');

        if (!token) window.location.href = '/login';

        function logout() {
            if (confirm('Yakin ingin keluar?')) {
                localStorage.removeItem('jwt_token');
                localStorage.removeItem('role_id');
                window.location.href = '/login';
            }
        }

        async function loadProducts() {
            try {
                const res = await fetch(`${API_BASE}/products`, { 
                    headers: { 'Authorization': `Bearer ${token}` } 
                });
                const result = await res.json();
                if (res.ok && result.data) {
                    products = result.data;
                    renderProducts(products);
                }
            } catch (err) { console.error('Fetch error:', err); }
        }

        function renderProducts(items) {
            const grid = document.getElementById('product-grid');
            if (!items || items.length === 0) {
                grid.innerHTML = '<div class="col-span-full py-20 text-center text-gray-400">Barang tidak ditemukan.</div>';
                return;
            }

            grid.innerHTML = items.map(p => {
                // Sesuai skema database organisasi: product_name, selling_price, current_stock
                const name  = p.product_name || "Produk Tanpa Nama";
                const price = parseFloat(p.selling_price || 0); 
                const stock = parseInt(p.current_stock || 0);
                const sku   = p.sku || "N/A";

                return `
                    <div onclick="addToCart(${p.id})" class="bg-white p-5 rounded-[2rem] shadow-sm border border-transparent hover:border-blue-500 hover:shadow-xl cursor-pointer transition-all duration-300 group">
                        <div class="w-full h-32 bg-gray-50 rounded-2xl mb-4 flex flex-col items-center justify-center text-blue-500 group-hover:bg-blue-50 transition">
                             <span class="text-[10px] font-black opacity-30 mb-2 tracking-widest uppercase">${sku}</span>
                             <i data-lucide="package" class="w-10 h-10"></i>
                        </div>
                        <h4 class="font-bold text-gray-800 truncate mb-1 text-base uppercase tracking-tight">${name}</h4>
                        <div class="flex justify-between items-center mt-2">
                            <p class="text-blue-600 font-black text-sm">Rp ${price.toLocaleString('id-ID')}</p>
                            <span class="text-[10px] px-2 py-0.5 rounded-full ${stock < 5 ? 'bg-red-100 text-red-600 font-bold' : 'bg-gray-100 text-gray-500'}">Stok: ${stock}</span>
                        </div>
                    </div>
                `;
            }).join('');
            lucide.createIcons();
        }

        function filterProducts() {
            const q = document.getElementById('searchProduct').value.toLowerCase();
            const filtered = products.filter(p => 
                (p.product_name && p.product_name.toLowerCase().includes(q)) || 
                (p.sku && p.sku.toLowerCase().includes(q))
            );
            renderProducts(filtered);
        }

        function addToCart(id) {
            const product = products.find(p => p.id == id);
            if (!product) return;

            const name  = product.product_name;
            const price = parseFloat(product.selling_price || 0);
            const stock = parseInt(product.current_stock || 0);

            if (stock <= 0) return alert('Stok barang organisasi sudah habis!');
            
            const existing = cart.find(c => c.product_id == id);
            if (existing) {
                if (existing.qty >= stock) return alert('Batas ketersediaan stok tercapai!');
                existing.qty++;
            } else {
                cart.push({ product_id: product.id, name, price, qty: 1 });
            }
            renderCart();
        }

        function renderCart() {
            const container = document.getElementById('cart-items');
            const counter = document.getElementById('item-count');
            let total = 0;
            
            if (cart.length === 0) {
                counter.innerText = "0 Item";
                container.innerHTML = `<div class="flex flex-col items-center justify-center h-full opacity-20 py-10"><i data-lucide="shopping-cart" class="w-12 h-12 mb-2"></i><p class="font-bold text-xs uppercase tracking-widest">Keranjang Kosong</p></div>`;
            } else {
                counter.innerText = `${cart.length} Item`;
                container.innerHTML = cart.map((item, index) => {
                    const subtotal = item.price * item.qty;
                    total += subtotal;
                    return `
                        <div class="flex justify-between items-center bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
                            <div class="flex-1">
                                <p class="font-bold text-gray-800 mb-1 uppercase text-xs">${item.name}</p>
                                <p class="text-[11px] text-gray-400">${item.qty} x Rp ${item.price.toLocaleString('id-ID')}</p>
                            </div>
                            <div class="flex flex-col items-end">
                                <button onclick="cart.splice(${index}, 1); renderCart();" class="text-red-400 mb-1 hover:text-red-600 transition">
                                    <i data-lucide="minus-circle" class="w-4 h-4"></i>
                                </button>
                                <p class="text-xs font-black text-gray-900 italic">Rp ${subtotal.toLocaleString('id-ID')}</p>
                            </div>
                        </div>
                    `;
                }).join('');
            }
            document.getElementById('total-amount-display').innerText = `Rp ${total.toLocaleString('id-ID')}`;
            lucide.createIcons();
            calculateChange();
        }

        function calculateChange() {
            const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            const cash = parseInt(document.getElementById('cash-input').value) || 0;
            const change = cash - total;
            const display = document.getElementById('change-display');
            display.innerText = `Rp ${Math.max(0, change).toLocaleString('id-ID')}`;
            display.className = change < 0 ? "text-red-500 font-bold" : "text-green-600 font-black text-base";
        }

        async function processPayment() {
    const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    const cash = parseInt(document.getElementById('cash-input').value) || 0;
    
    if (cart.length === 0) return alert('Keranjang masih kosong!');
    if (cash < total) return alert('Uang bayar tidak mencukupi total transaksi!');

    const payload = {
        total_gross: total,
        total_net: total,
        discount_total: 0,
        items: cart.map(i => ({ 
            product_id: i.product_id, 
            qty: i.qty, 
            price: i.price 
        }))
    };

    try {
        // Baris 244 yang sebelumnya menyebabkan Error 500
        const res = await fetch(`${API_BASE}/sales`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'Authorization': `Bearer ${token}` 
            },
            body: JSON.stringify(payload)
        });
        
        const result = await res.json();

        if (res.ok) {
            alert('Transaksi Berhasil! Stok produk organisasi telah diperbarui.');
            cart = [];
            document.getElementById('cash-input').value = '';
            renderCart();
            loadProducts(); // Refresh tampilan stok
        } else {
            // Menangkap pesan error dari blok try-catch backend
            alert('Gagal: ' + (result.messages?.error || 'Terjadi kesalahan pada server.'));
        }
    } catch (err) { 
        console.error("Fetch Error:", err);
        alert('Koneksi ke server terputus.'); 
    }
}

        loadProducts();
    </script>
</body>
</html>