<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk - Smart POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-[#f4f7f6]">
    
    <?= view('components/sidebar') ?>

    <main class="ml-64 p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Manajemen Produk</h2>
            <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-blue-700 transition">
                <i data-lucide="plus"></i> Tambah Produk
            </button>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-3">SKU</th>
                            <th class="px-4 py-3">Nama Produk</th>
                            <th class="px-4 py-3">Harga Jual</th>
                            <th class="px-4 py-3">Stok</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="product-table-body" class="divide-y divide-gray-100">
                        </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="productModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex justify-center items-center z-50">
        <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
            <h3 class="text-xl font-semibold mb-6 text-gray-800" id="modalTitle">Tambah Produk</h3>
            <form id="productForm">
                <input type="hidden" id="productId">
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4 col-span-2">
                        <label class="block text-sm font-medium text-gray-700">SKU</label>
                        <input type="text" id="sku" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                    </div>
                    <div class="mb-4 col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Nama Produk</label>
                        <input type="text" id="productName" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Harga Jual</label>
                        <input type="number" id="sellingPrice" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Stok</label>
                        <input type="number" id="currentStock" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closeModal()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();
        const API_URL = 'http://localhost:8080/api/v1/';
        const token = localStorage.getItem('jwt_token');

        // Fungsi Helper untuk memuat data
        async function loadProducts() {
            const response = await fetch(`${API_URL}products`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const result = await response.json();
            const tbody = document.getElementById('product-table-body');
            tbody.innerHTML = '';
            
            result.data.forEach(p => {
                tbody.innerHTML += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-600">${p.sku}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">${p.product_name}</td>
                        <td class="px-4 py-3">Rp ${Number(p.selling_price).toLocaleString('id-ID')}</td>
                        <td class="px-4 py-3">${p.current_stock}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <button onclick="editProduct(${p.id}, '${p.sku}', '${p.product_name}', ${p.selling_price}, ${p.current_stock})" class="text-blue-600 hover:text-blue-800">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </button>
                            <button onclick="deleteProduct(${p.id})" class="text-red-600 hover:text-red-800">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            lucide.createIcons();
        }

        // --- Fungsi Modal ---
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

        // --- Fungsi Edit ---
        function editProduct(id, sku, name, price, stock) {
            document.getElementById('modalTitle').innerText = 'Edit Produk';
            document.getElementById('productId').value = id;
            document.getElementById('sku').value = sku;
            document.getElementById('productName').value = name;
            document.getElementById('sellingPrice').value = price;
            document.getElementById('currentStock').value = stock;
            openModal('edit');
        }

        // --- Fungsi Submit (Create/Update) ---
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
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            closeModal();
            loadProducts();
        });

        // --- Fungsi Hapus ---
        async function deleteProduct(id) {
            if (confirm('Yakin ingin menghapus produk ini?')) {
                await fetch(`${API_URL}products/${id}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                loadProducts();
            }
        }

        // Load data saat halaman dibuka
        loadProducts();
    </script>
</body>
</html>