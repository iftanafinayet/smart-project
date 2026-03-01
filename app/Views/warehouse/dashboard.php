<?= $this->extend('layouts/warehouse') ?>

<?= $this->section('content') ?>
<div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-4xl font-black text-gray-900 tracking-tighter mb-2 uppercase">Statistik <span class="text-blue-600">Inventaris</span></h2>
    </div>
    <div id="last-update" class="text-[10px] font-black text-blue-600 bg-blue-50 px-6 py-3 rounded-2xl border border-blue-100 uppercase tracking-[0.2em] shadow-sm italic">
        Syncing Data...
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
    <div class="group bg-white p-8 rounded-[3rem] border border-gray-100 shadow-sm hover:border-red-500 transition-all duration-500">
        <div class="flex items-center gap-6">
            <div class="p-5 bg-red-50 text-red-600 rounded-[2rem] group-hover:bg-red-600 group-hover:text-white transition-all"><i data-lucide="alert-octagon" class="w-10 h-10"></i></div>
            <div>
                <p class="text-[11px] text-gray-400 font-black uppercase tracking-widest mb-1">Stok Kritis</p>
                <h3 id="low-stock-count" class="text-4xl font-black text-gray-900 tracking-tighter italic">0</h3>
            </div>
        </div>
    </div>
    
    <div class="group bg-white p-8 rounded-[3rem] border border-gray-100 shadow-sm hover:border-blue-500 transition-all">
        <div class="flex items-center gap-6">
            <div class="p-5 bg-blue-50 text-blue-600 rounded-[2rem] group-hover:bg-blue-600 group-hover:text-white transition-all"><i data-lucide="layers" class="w-10 h-10"></i></div>
            <div>
                <p class="text-[11px] text-gray-400 font-black uppercase tracking-widest mb-1">Katalog Produk</p>
                <h3 id="total-sku" class="text-4xl font-black text-gray-900 tracking-tighter italic">0</h3>
            </div>
        </div>
    </div>

    <div class="group bg-white p-8 rounded-[3rem] border border-gray-100 shadow-sm hover:border-green-500 transition-all">
        <div class="flex items-center gap-6">
            <div class="p-5 bg-green-50 text-green-600 rounded-[2rem] group-hover:bg-green-600 group-hover:text-white transition-all"><i data-lucide="box" class="w-10 h-10"></i></div>
            <div>
                <p class="text-[11px] text-gray-400 font-black uppercase tracking-widest mb-1">Total Unit Fisik</p>
                <h3 id="total-inventory" class="text-4xl font-black text-gray-900 tracking-tighter italic">0</h3>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-[3.5rem] shadow-xl border border-gray-100 overflow-hidden">
    <div class="p-10 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
        <h3 class="font-black text-gray-800 flex items-center gap-4 text-xl tracking-tighter uppercase">Prioritas Re-Stock</h3>
        <a href="/warehouse/inventory" class="bg-gray-900 text-white px-8 py-4 rounded-[1.5rem] text-xs font-black hover:bg-blue-600 transition-all shadow-xl active:scale-95 uppercase tracking-widest">
            Kelola Inventori
        </a>
    </div>
    
    <div class="p-6 overflow-x-auto">
        <table class="w-full text-left border-separate border-spacing-y-4">
            <thead>
                <tr class="text-gray-400 uppercase text-[11px] font-black tracking-[0.2em]">
                    <th class="px-10 py-4">Informasi Produk</th>
                    <th class="px-10 py-4 text-center">Status Gudang</th>
                    <th class="px-10 py-4 text-right">Tindakan</th>
                </tr>
            </thead>
            <tbody id="low-stock-list">
                <tr><td colspan="3" class="text-center py-20 italic font-black uppercase text-xs tracking-widest text-gray-300">Memindai Database...</td></tr>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    async function loadDashboard() {
        try {
            const response = await fetch(`${API_URL}products`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const result = await response.json();
            
            if (response.ok && result.data) {
                const products = result.data;
                const lowStockItems = products.filter(p => parseInt(p.current_stock || 0) < 5);
                const totalUnits = products.reduce((sum, p) => sum + parseInt(p.current_stock || 0), 0);

                document.getElementById('low-stock-count').innerText = lowStockItems.length;
                document.getElementById('total-sku').innerText = products.length;
                document.getElementById('total-inventory').innerText = totalUnits.toLocaleString('id-ID');
                document.getElementById('last-update').innerText = `SYNCED: ${new Date().toLocaleTimeString('id-ID')}`;

                const tbody = document.getElementById('low-stock-list');
                if (lowStockItems.length > 0) {
                    tbody.innerHTML = lowStockItems.map(item => `
                        <tr class="bg-white hover:bg-blue-50/30 transition-all duration-300 shadow-sm border border-gray-50">
                            <td class="px-10 py-6 rounded-l-[2.5rem] border-y border-l border-gray-100">
                                <div class="flex items-center gap-6">
                                    <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 group-hover:text-blue-600 italic">
                                        <i data-lucide="package-search" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <p class="font-black text-gray-900 uppercase text-sm italic">${item.product_name}</p>
                                        <span class="text-[10px] text-blue-500 font-black bg-blue-50 px-2 py-1 rounded-md uppercase italic">${item.sku}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-6 text-center border-y border-gray-100">
                                <span class="bg-red-50 text-red-600 px-6 py-2 rounded-2xl text-xs font-black uppercase italic">${item.current_stock} UNIT</span>
                            </td>
                            <td class="px-10 py-6 text-right rounded-r-[2.5rem] border-y border-r border-gray-100">
                                <a href="/warehouse/inventory" class="bg-gray-900 text-white px-6 py-3 rounded-2xl text-[10px] font-black hover:bg-blue-600 transition-all uppercase italic">Restock</a>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = `<tr><td colspan="3" class="text-center py-20 font-black uppercase text-xs italic text-green-500">Semua Stok Aman</td></tr>`;
                }
            }
            lucide.createIcons();
        } catch (error) { console.error('Error Syncing Dashboard:', error); }
    }
    loadDashboard();
    setInterval(loadDashboard, 60000); // Auto-refresh data tiap 1 menit
</script>
<?= $this->endSection() ?>