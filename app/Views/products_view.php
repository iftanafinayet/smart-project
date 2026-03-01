<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk - Smart POS Enterprise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-[#f8fafc]">
    
    <?= view('components/sidebar') ?>

    <main class="ml-64 p-10 animate-in fade-in duration-700">
        <div class="flex justify-between items-end mb-10">
            <div>
                <h2 class="text-4xl font-black text-gray-900 tracking-tighter">Katalog <span class="text-blue-600">Produk</span></h2>
                <p class="text-gray-500 font-medium mt-1">Kelola stok merchandise dan produk organisasi Anda.</p>
            </div>
            <button onclick="openModal()" class="group bg-blue-600 text-white px-8 py-4 rounded-2xl flex items-center gap-3 hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 active:scale-95">
                <i data-lucide="plus-circle" class="w-5 h-5 group-hover:rotate-90 transition-transform"></i> 
                <span class="text-xs font-black uppercase tracking-widest">Tambah Produk</span>
            </button>
        </div>
        
        <div class="bg-white rounded-[3rem] shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto p-6">
                <table class="w-full text-left border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-gray-400 uppercase text-[10px] font-black tracking-[0.2em]">
                            <th class="px-8 py-4">Informasi SKU</th>
                            <th class="px-8 py-4">Nama Produk</th>
                            <th class="px-8 py-4">Harga Jual</th>
                            <th class="px-8 py-4 text-center">Stok</th>
                            <th class="px-8 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="product-table-body">
                        <tr><td colspan="5" class="text-center py-20 text-gray-300 italic animate-pulse">Menghubungkan ke database...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="productModal" class="hidden fixed inset-0 bg-gray-900/40 backdrop-blur-sm overflow-y-auto h-full w-full flex justify-center items-center z-[60] transition-all">
        <div class="bg-white p-10 rounded-[3rem] shadow-2xl w-full max-w-md border border-gray-100 animate-in zoom-in duration-300">
            <div class="flex items-center gap-4 mb-8">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
                    <i data-lucide="package-plus" class="w-6 h-6"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-800 tracking-tight" id="modalTitle">Tambah Produk</h3>
            </div>
            
            <form id="productForm" class="space-y-5">
                <input type="hidden" id="productId">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Kode SKU</label>
                        <input type="text" id="sku" placeholder="Contoh: JHM-001" class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all font-bold text-sm" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nama Produk</label>
                        <input type="text" id="productName" placeholder="Nama Barang" class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all font-bold text-sm" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Harga (Rp)</label>
                            <input type="number" id="sellingPrice" class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all font-bold text-sm" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Stok Awal</label>
                            <input type="number" id="currentStock" class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all font-bold text-sm" required>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-10">
                    <button type="button" onclick="closeModal()" class="px-6 py-4 rounded-2xl text-xs font-black uppercase tracking-widest text-gray-400 hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="bg-gray-900 text-white px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg active:scale-95">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Logika JavaScript tetap sama
        lucide.createIcons();
        const API_URL = 'http://localhost:8080/api/v1/';
        const token = localStorage.getItem('jwt_token');

        async function loadProducts() {
            try {
                const response = await fetch(`${API_URL}products`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const result = await response.json();
                const tbody = document.getElementById('product-table-body');
                
                if (result.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-20 text-gray-400 italic">Belum ada produk terdaftar.</td></tr>';
                    return;
                }

                tbody.innerHTML = result.data.map(p => `
                    <tr class="bg-white hover:bg-blue-50/30 transition-all duration-300 group shadow-sm">
                        <td class="px-8 py-5 rounded-l-2xl border-y border-l border-gray-50">
                            <span class="text-[10px] font-black text-blue-500 bg-blue-50 px-3 py-1 rounded-lg uppercase tracking-tighter">${p.sku}</span>
                        </td>
                        <td class="px-8 py-5 border-y border-gray-50 font-bold text-gray-800 uppercase text-xs tracking-tight">${p.product_name}</td>
                        <td class="px-8 py-5 border-y border-gray-50 text-sm font-black text-gray-900">Rp ${Number(p.selling_price).toLocaleString('id-ID')}</td>
                        <td class="px-8 py-5 border-y border-gray-50 text-center">
                            <span class="px-3 py-1 rounded-lg text-xs font-black ${p.current_stock < 5 ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600'}">
                                ${p.current_stock}
                            </span>
                        </td>
                        <td class="px-8 py-5 rounded-r-2xl border-y border-r border-gray-50 text-right">
                            <div class="flex justify-end gap-2">
                                <button onclick="editProduct(${p.id}, '${p.sku}', '${p.product_name}', ${p.selling_price}, ${p.current_stock})" class="p-2 bg-gray-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </button>
                                <button onclick="deleteProduct(${p.id})" class="p-2 bg-gray-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');
                lucide.createIcons();
            } catch (err) { console.error(err); }
        }

        function openModal(mode = 'add') {
            document.getElementById('productModal').classList.remove('hidden');
            if(mode === 'add') {
                document.getElementById('modalTitle').innerText = 'Tambah Produk';
                document.getElementById('productForm').reset();
                document.getElementById('productId').value = '';
            }
        }

        function closeModal() {
            document.getElementById('productModal').classList.add('hidden');
        }

        function editProduct(id, sku, name, price, stock) {
            document.getElementById('modalTitle').innerText = 'Edit Produk';
            document.getElementById('productId').value = id;
            document.getElementById('sku').value = sku;
            document.getElementById('productName').value = name;
            document.getElementById('sellingPrice').value = price;
            document.getElementById('currentStock').value = stock;
            openModal('edit');
        }

        document.getElementById('productForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('productId').value;
            const data = {
                sku: document.getElementById('sku').value,
                product_name: document.getElementById('productName').value,
                selling_price: document.getElementById('sellingPrice').value,
                current_stock: document.getElementById('currentStock').value
            };

            const method = id ? 'PUT' : 'POST';
            const url = id ? `${API_URL}products/${id}` : `${API_URL}products`;

            await fetch(url, {
                method: method,
                headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            closeModal();
            loadProducts();
        });

        async function deleteProduct(id) {
            if (confirm('Yakin ingin menghapus produk ini?')) {
                await fetch(`${API_URL}products/${id}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                loadProducts();
            }
        }

        loadProducts();
    </script>
</body>
</html>