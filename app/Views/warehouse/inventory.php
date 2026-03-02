<?= $this->extend('layouts/warehouse') ?>

<?= $this->section('content') ?>
<div class="flex justify-between items-center mb-10 no-print">
    <div>
        <h2 class="text-4xl font-black text-gray-900 tracking-tighter uppercase italic">Gudang <span class="text-blue-600">Inventaris</span></h2>
        <p class="text-gray-500 font-medium italic">Manajemen stok fisik dan barcode label organisasi.</p>
    </div>
    <div class="flex gap-4">
        <button onclick="loadProducts()" class="p-4 bg-white border border-gray-100 rounded-2xl text-gray-400 hover:text-blue-600 transition-all shadow-sm">
            <i data-lucide="refresh-cw" class="w-5 h-5"></i>
        </button>
        <a href="<?= base_url('warehouse/dashboard') ?>" class="bg-gray-900 text-white px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg active:scale-95">Kembali</a>
    </div>
</div>

<div class="bg-white rounded-[3rem] shadow-xl border border-gray-100 overflow-hidden no-print">
    <div class="p-8 overflow-x-auto">
        <table class="w-full text-left border-separate border-spacing-y-3">
            <thead>
                <tr class="text-gray-400 uppercase text-[10px] font-black tracking-widest">
                    <th class="px-8 py-4">Informasi Produk</th>
                    <th class="px-8 py-4 text-center">Stok Fisik</th>
                    <th class="px-8 py-4 text-center">SKU / Kode</th>
                    <th class="px-8 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody id="product-table-body">
                <tr><td colspan="4" class="text-center py-24 italic font-black text-gray-300 animate-pulse">Menghubungkan Database...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<?= view('components/product_modal') ?>

<div id="printModal" class="hidden fixed inset-0 bg-gray-900/40 backdrop-blur-sm flex items-center justify-center z-[70] p-4 no-print">
    <div class="bg-white rounded-[3.5rem] p-12 shadow-2xl w-full max-w-md border border-gray-100 text-center animate-in zoom-in duration-300">
        <div id="printArea">
            <h3 id="modalProductName" class="text-xl font-black text-gray-800 mb-6 uppercase italic">Product Name</h3>
            <div class="flex justify-center p-6 bg-gray-50 rounded-[2rem] mb-6 border border-gray-100"><svg id="barcode-preview"></svg></div>
            <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest">Smart POS Enterprise</p>
        </div>
        <div class="flex gap-4 mt-8">
            <button onclick="closePrintModal()" class="flex-1 py-4 bg-gray-100 text-gray-400 rounded-2xl font-black text-xs uppercase transition-all">Batal</button>
            <button onclick="window.print()" class="flex-1 py-4 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase shadow-lg shadow-blue-100 active:scale-95">Cetak Label</button>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
    let allProducts = [];

    async function loadProducts() {
        try {
            const res = await fetch(`${API_URL}products`, { headers: { 'Authorization': `Bearer ${token}` } });
            const result = await res.json();
            allProducts = result.data;

            const tbody = document.getElementById('product-table-body');
            tbody.innerHTML = allProducts.map(p => `
                <tr class="bg-white hover:bg-blue-50/30 transition-all duration-300 shadow-sm">
                    <td class="px-8 py-6 rounded-l-2xl border-y border-l border-gray-50 font-black text-gray-800 uppercase text-xs italic">${p.product_name}</td>
                    <td class="px-8 py-6 border-y border-gray-50 text-center">
                        <span class="px-4 py-1.5 rounded-lg font-black text-[10px] uppercase italic ${parseInt(p.current_stock) < 10 ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600'}">${p.current_stock} UNIT</span>
                    </td>
                    <td class="px-8 py-6 border-y border-gray-50 text-center font-mono text-blue-600 text-[10px] font-black">${p.sku}</td>
                    <td class="px-8 py-6 rounded-r-2xl border-y border-r border-gray-50 text-right">
                        <div class="flex gap-2 justify-end">
                            <button onclick="openEditModal(${p.id})" class="bg-gray-900 text-white px-5 py-2.5 rounded-xl text-[9px] font-black uppercase hover:bg-blue-600 transition-all shadow-lg active:scale-95">Update Stok</button>
                            <button onclick="openPrintModal('${p.sku}', '${p.product_name}')" class="p-2.5 bg-gray-50 text-gray-400 hover:text-blue-600 rounded-xl transition-all"><i data-lucide="barcode" class="w-4 h-4"></i></button>
                        </div>
                    </td>
                </tr>
            `).join('');
            lucide.createIcons();
            handleDeepLink();
        } catch (error) { console.error("Load Error:", error); }
    }

    // 2. Fungsi Buka Modal Edit
    function openEditModal(id) {
        const p = allProducts.find(item => item.id == id);
        if (p) {
            document.getElementById('modalTitle').innerText = 'Update Stok Produk';
            document.getElementById('productId').value = p.id;
            document.getElementById('sku').value = p.sku;
            document.getElementById('productName').value = p.product_name;
            document.getElementById('sellingPrice').value = p.selling_price;
            document.getElementById('currentStock').value = p.current_stock;
            document.getElementById('productModal').classList.remove('hidden');
        }
    }

    // 3. Menangani Submit Form (FUNGSI EDIT DI SINI)
    const productForm = document.getElementById('productForm');
    if (productForm) {
        productForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const id = document.getElementById('productId').value;
            const data = {
                sku: document.getElementById('sku').value,
                product_name: document.getElementById('productName').value,
                selling_price: document.getElementById('sellingPrice').value,
                current_stock: document.getElementById('currentStock').value
            };

            try {
                const response = await fetch(`${API_URL}products/${id}`, {
                    method: 'PUT', // Method PUT untuk update data
                    headers: { 
                        'Authorization': `Bearer ${token}`, 
                        'Content-Type': 'application/json' 
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    alert("Data stok berhasil diperbarui!");
                    closeModal();
                    loadProducts();
                } else {
                    alert("Gagal memperbarui data.");
                }
            } catch (err) { alert("Koneksi API Gagal."); }
        });
    }

    // 4. Utilitas Lainnya
    function handleDeepLink() {
        const urlParams = new URLSearchParams(window.location.search);
        const editId = urlParams.get('edit');
        if (editId) openEditModal(editId);
    }

    function openPrintModal(code, name) {
        document.getElementById('modalProductName').innerText = name;
        document.getElementById('printModal').classList.remove('hidden');
        JsBarcode("#barcode-preview", code, { format: "CODE128", width: 2.5, height: 60, displayValue: true });
    }

    function closeModal() { document.getElementById('productModal').classList.add('hidden'); }
    function closePrintModal() { document.getElementById('printModal').classList.add('hidden'); }

    loadProducts();
</script>
<?= $this->endSection() ?>