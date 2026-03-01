<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="mb-10">
        <h2 class="text-4xl font-black text-gray-900 tracking-tighter uppercase italic">Penerimaan <span class="text-blue-600 font-black">Barang (PO)</span></h2>
        <p class="text-gray-500 font-medium mt-1 italic">Kelola stok masuk dari supplier dan audit fisik barang organisasi.</p>
    </div>

    <div class="bg-white rounded-[3rem] shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <div class="p-8 overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-3">
                <thead>
                    <tr class="text-gray-400 uppercase text-[10px] font-black tracking-[0.2em]">
                        <th class="px-6 py-4">No. PO</th>
                        <th class="px-6 py-4">Supplier</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="po-table-body">
                    <tr><td colspan="5" class="text-center py-20 text-gray-300 italic animate-pulse">Menghubungkan data supplier...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="detailModal" class="hidden fixed inset-0 bg-gray-900/40 backdrop-blur-sm overflow-y-auto h-full w-full flex justify-center items-center z-[60] p-4">
        <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-2xl border border-gray-100 animate-in zoom-in duration-300">
            <div class="px-10 py-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-xl font-black text-gray-800 tracking-tighter uppercase">Detail Item <span class="text-blue-600">Pesanan</span></h3>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                    <i data-lucide="x-circle" class="w-6 h-6"></i>
                </button>
            </div>
            <div class="p-10">
                <table class="w-full text-left border-separate border-spacing-y-2">
                    <tbody id="detail-item-body"></tbody>
                </table>
                <div class="flex justify-end mt-8">
                    <button onclick="closeDetailModal()" class="bg-gray-900 text-white px-10 py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg active:scale-95">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Gunakan API_URL dari Layout Induk
    const PO_API = `${API_URL}purchase-orders`;

    async function loadPOs() {
        try {
            const response = await fetch(PO_API, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const result = await response.json();
            const tbody = document.getElementById('po-table-body');
            
            tbody.innerHTML = result.data.map(po => {
                const isReceived = po.status === 'Received';
                return `
                    <tr class="bg-white hover:bg-blue-50/30 transition-all duration-300 group shadow-sm">
                        <td class="px-6 py-5 rounded-l-2xl border-y border-l border-gray-50 font-black text-blue-600 italic">#${po.po_number}</td>
                        <td class="px-6 py-5 border-y border-gray-50 font-bold text-gray-800 uppercase text-xs">${po.supplier_name}</td>
                        <td class="px-6 py-5 border-y border-gray-50 text-[11px] font-mono text-gray-400">${po.po_date}</td>
                        <td class="px-6 py-5 border-y border-gray-50">
                            <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest ${isReceived ? 'bg-green-50 text-green-600' : 'bg-yellow-50 text-yellow-700 animate-pulse'}">
                                ${po.status}
                            </span>
                        </td>
                        <td class="px-6 py-5 rounded-r-2xl border-y border-r border-gray-50 text-right">
                            <div class="flex gap-2 justify-end">
                                <button onclick="viewDetail(${po.id})" class="p-2.5 bg-gray-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                                ${!isReceived ? `
                                    <button onclick="markAsReceived(${po.id})" class="bg-gray-900 text-white px-4 py-2 rounded-xl hover:bg-green-600 transition text-[10px] font-black uppercase tracking-widest">
                                        Terima
                                    </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
            lucide.createIcons();
        } catch (error) { console.error("Error load PO:", error); }
    }

    // Fungsi detail dan markAsReceived tetap sama, pastikan menggunakan API_URL
    loadPOs();
</script>
<?= $this->endSection() ?>