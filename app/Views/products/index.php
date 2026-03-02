<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="flex justify-between items-end mb-10">
        <div>
            <h2 class="text-4xl font-black text-gray-900 tracking-tighter uppercase italic">Katalog <span class="text-blue-600">Produk</span></h2>
            <p class="text-gray-500 font-medium mt-1 italic">Kelola stok merchandise dan produk organisasi.</p>
        </div>
        <button onclick="openModal('add')" class="group bg-blue-600 text-white px-8 py-4 rounded-2xl flex items-center gap-3 hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 active:scale-95">
            <i data-lucide="plus-circle" class="w-5 h-5 group-hover:rotate-90 transition-transform"></i> 
            <span class="text-xs font-black uppercase tracking-widest">Tambah Produk</span>
        </button>
    </div>
    
    <div class="bg-white rounded-[3rem] shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <div class="p-6 overflow-x-auto">
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
                    <tr><td colspan="5" class="text-center py-20 text-gray-300 italic animate-pulse font-bold uppercase tracking-widest text-xs">Menghubungkan database...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <?= view('components/product_modal') ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    /**
     * Memuat data produk
     */
    async function loadProducts() {
        try {
            const response = await fetch(`${API_URL}products`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const result = await response.json();
            const tbody = document.getElementById('product-table-body');
            
            if (!result.data || result.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-20 text-gray-400 font-bold italic uppercase tracking-widest text-xs">Belum ada produk terdaftar.</td></tr>';
                return;
            }

            tbody.innerHTML = result.data.map(p => `
                <tr class="bg-white hover:bg-blue-50/30 transition-all duration-300 group shadow-sm">
                    <td class="px-8 py-5 rounded-l-2xl border-y border-l border-gray-50">
                        <span class="text-[10px] font-black text-blue-500 bg-blue-50 px-3 py-1 rounded-lg uppercase tracking-tighter">${p.sku}</span>
                    </td>
                    <td class="px-8 py-5 border-y border-gray-50 font-bold text-gray-800 uppercase text-xs tracking-tight">${p.product_name}</td>
                    <td class="px-8 py-5 border-y border-gray-50 text-sm font-black text-gray-900 italic">Rp ${Number(p.selling_price).toLocaleString('id-ID')}</td>
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
        } catch (err) { 
            console.error(err); 
        }
    }

    loadProducts();

    /**
     * Manajemen Modal
     */
    function openModal(mode = 'add') {
        const modal = document.getElementById('productModal');
        if (modal) {
            modal.classList.remove('hidden');
            if (mode === 'add') {
                document.getElementById('modalTitle').innerText = 'Tambah Produk';
                document.getElementById('productForm').reset();
                document.getElementById('productId').value = '';
            }
        }
    }

    function closeModal() { 
        const modal = document.getElementById('productModal');
        if (modal) modal.classList.add('hidden'); 
    }

    function editProduct(id, sku, name, price, stock) {
        openModal('edit');
        document.getElementById('modalTitle').innerText = 'Edit Produk';
        document.getElementById('productId').value = id;
        document.getElementById('sku').value = sku;
        document.getElementById('productName').value = name;
        document.getElementById('sellingPrice').value = price;
        document.getElementById('currentStock').value = stock;
    }

    /**
     * Submit Form (Tambah/Edit)
     */
    const pForm = document.getElementById('productForm');
    if (pForm) {
        pForm.addEventListener('submit', async function(e) {
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

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: { 
                        'Authorization': `Bearer ${token}`, 
                        'Content-Type': 'application/json' 
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    closeModal();
                    loadProducts();
                }
            } catch (err) { alert("Gagal menyimpan data."); }
        });
    }

    async function deleteProduct(id) {
        if (confirm('Yakin ingin menghapus produk organisasi ini?')) {
            await fetch(`${API_URL}products/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}` }
            });
            loadProducts();
        }
    }
</script>
<?= $this->endSection() ?>