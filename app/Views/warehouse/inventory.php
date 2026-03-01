<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4 no-print">
        <div>
            <h2 class="text-4xl font-black text-gray-900 tracking-tighter uppercase italic">Inventaris <span class="text-blue-600">Produk</span></h2>
            <p class="text-gray-500 font-medium mt-1 italic">Kelola stok dan cetak label barcode merchandise organisasi.</p>
        </div>
        
        <div class="flex items-center gap-4 bg-white p-2 rounded-2xl border border-gray-100 shadow-sm">
            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-3">Filter Stok:</label>
            <select id="stockFilter" onchange="loadProducts()" 
                    class="bg-gray-50 border-none text-gray-900 text-xs font-black rounded-xl focus:ring-0 block p-3 outline-none transition cursor-pointer uppercase tracking-tighter">
                <option value="all">Semua Produk</option>
                <option value="low_stock">Stok Rendah (< 10)</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-[3rem] shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden no-print">
        <div class="overflow-x-auto p-6">
            <table class="w-full text-left border-separate border-spacing-y-3">
                <thead>
                    <tr class="text-gray-400 uppercase text-[10px] font-black tracking-[0.2em]">
                        <th class="px-8 py-4">Informasi Produk</th>
                        <th class="px-8 py-4 text-center">Stok</th>
                        <th class="px-8 py-4 text-center">Harga Jual</th>
                        <th class="px-8 py-4 text-center">SKU / Barcode</th>
                        <th class="px-8 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="product-table-body">
                    <tr><td colspan="5" class="text-center py-20 text-gray-300 italic font-bold uppercase tracking-widest text-xs">Memindai database inventaris...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="printModal" class="hidden fixed inset-0 bg-gray-900/40 backdrop-blur-sm flex items-center justify-center z-[70] p-4 no-print">
        <div class="bg-white rounded-[3.5rem] shadow-2xl w-full max-w-md overflow-hidden border border-gray-100 animate-in zoom-in duration-300">
            <div class="p-12 text-center" id="printArea">
                <h3 id="modalProductName" class="text-xl font-black text-gray-800 mb-6 uppercase tracking-tight italic">Nama Produk</h3>
                <div class="flex justify-center p-8 bg-gray-50 rounded-[2.5rem] mb-6 shadow-inner border border-gray-100">
                    <svg id="barcode-preview"></svg>
                </div>
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Smart POS - CCIT FTUI Organisasi</p>
            </div>
            <div class="flex gap-4 p-8 bg-gray-50/50 border-t border-gray-50">
                <button onclick="closePrintModal()" class="flex-1 px-6 py-4 bg-white text-gray-400 rounded-2xl hover:bg-gray-100 font-black text-xs uppercase tracking-widest transition-all">
                    Batal
                </button>
                <button onclick="window.print()" class="flex-1 px-6 py-4 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 font-black text-xs uppercase tracking-widest transition-all flex items-center justify-center gap-3 shadow-lg shadow-blue-100 active:scale-95">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak Label
                </button>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
    /**
     * Memuat data inventaris produk secara modular
     */
    async function loadProducts() {
        const filterValue = document.getElementById('stockFilter').value;
        const tbody = document.getElementById('product-table-body');
        
        try {
            const response = await fetch(`${API_URL}products`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const result = await response.json();
            
            if (!result.data || result.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-20 text-gray-400 italic uppercase font-black text-xs">Inventaris Kosong.</td></tr>`;
                return;
            }

            let filteredData = result.data;
            if (filterValue === 'low_stock') {
                filteredData = result.data.filter(p => parseInt(p.current_stock || 0) < 10);
            }

            tbody.innerHTML = filteredData.map(product => {
                const stock = parseInt(product.current_stock || 0);
                const isLow = stock < 10;
                const productCode = product.sku || 'PROD-' + product.id;

                return `
                    <tr class="bg-white hover:bg-blue-50/30 transition-all duration-300 group shadow-sm">
                        <td class="px-8 py-5 rounded-l-2xl border-y border-l border-gray-50">
                            <p class="font-black text-gray-800 uppercase text-xs tracking-tight mb-0.5">${product.product_name}</p>
                            <span class="text-[9px] text-gray-400 uppercase font-black tracking-widest italic">${product.unit || 'Unit'}</span>
                        </td>
                        <td class="px-8 py-5 text-center border-y border-gray-50">
                            <span class="px-3 py-1 rounded-lg font-black text-[10px] uppercase italic ${isLow ? 'bg-red-50 text-red-600 animate-pulse' : 'bg-green-50 text-green-600'}">
                                ${stock} Tersedia
                            </span>
                        </td>
                        <td class="px-8 py-5 text-center border-y border-gray-50 font-black text-gray-700 italic text-sm">
                            Rp ${parseInt(product.selling_price || 0).toLocaleString('id-ID')}
                        </td>
                        <td class="px-8 py-5 text-center border-y border-gray-50 font-mono text-blue-600 text-[10px] font-black tracking-widest">
                            ${productCode}
                        </td>
                        <td class="px-8 py-5 rounded-r-2xl border-y border-r border-gray-50 text-right">
                            <button onclick="openPrintModal('${productCode}', '${product.product_name}')" 
                                    class="inline-flex items-center gap-2 bg-gray-900 text-white px-5 py-3 rounded-xl hover:bg-blue-600 transition-all font-black text-[9px] uppercase tracking-widest shadow-lg active:scale-95">
                                <i data-lucide="barcode" class="w-3.5 h-3.5"></i> Cetak Label
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
            lucide.createIcons();
        } catch (error) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-20 text-red-500 font-black italic uppercase text-xs">Gagal sinkronisasi data server.</td></tr>`;
        }
    }

    function openPrintModal(code, name) {
        document.getElementById('modalProductName').innerText = name;
        document.getElementById('printModal').classList.remove('hidden');
        
        JsBarcode("#barcode-preview", code, {
            format: "CODE128",
            lineColor: "#000",
            width: 2.5,
            height: 60,
            displayValue: true,
            fontSize: 16,
            fontOptions: "bold",
            margin: 15
        });
    }

    function closePrintModal() {
        document.getElementById('printModal').classList.add('hidden');
    }

    loadProducts();
</script>
<?= $this->endSection() ?>