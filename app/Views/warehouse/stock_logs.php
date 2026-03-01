<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
        <div>
            <h2 class="text-4xl font-black text-gray-900 tracking-tighter uppercase italic">Log Pergerakan <span class="text-blue-600 font-black">Barang</span></h2>
            <p class="text-gray-500 font-medium mt-1 italic">Audit trail untuk setiap perubahan stok inventaris organisasi.</p>
        </div>
        
        <div class="relative w-full md:w-80 group">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-blue-600 transition-colors">
                <i data-lucide="search" class="w-5 h-5"></i>
            </span>
            <input type="text" id="logSearch" onkeyup="filterLogs()" 
                   placeholder="Cari produk atau referensi..." 
                   class="w-full pl-12 pr-6 py-4 border border-gray-100 rounded-[1.5rem] text-sm font-bold outline-none focus:ring-2 focus:ring-blue-500 bg-white shadow-xl shadow-gray-200/30 transition-all uppercase tracking-tighter">
        </div>
    </div>

    <div class="bg-white rounded-[3rem] shadow-2xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto p-6">
            <table class="w-full text-left border-separate border-spacing-y-3">
                <thead>
                    <tr class="text-gray-400 uppercase text-[10px] font-black tracking-[0.2em]">
                        <th class="px-8 py-4 text-center">Waktu Terjadi</th>
                        <th class="px-8 py-4">Informasi Produk</th>
                        <th class="px-8 py-4 text-center">Tipe Arus</th>
                        <th class="px-8 py-4 text-center">Jumlah (Qty)</th>
                        <th class="px-8 py-4">No. Referensi</th>
                    </tr>
                </thead>
                <tbody id="log-table-body">
                    <tr><td colspan="5" class="text-center py-24 text-gray-300 italic font-black uppercase tracking-widest text-xs animate-pulse">Menyelaraskan riwayat inventaris...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    /**
     * Memuat data log dari API stock-logs
     */
    async function loadLogs() {
        const tbody = document.getElementById('log-table-body');
        try {
            const response = await fetch(`${API_URL}stock-logs`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const result = await response.json();
            
            if (!result.data || result.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-20 text-gray-400 font-black italic uppercase text-xs">Belum ada mutasi stok terdeteksi.</td></tr>`;
                return;
            }

            tbody.innerHTML = result.data.map(log => {
                const isIn = log.type.toLowerCase() === 'in';
                return `
                    <tr class="bg-white hover:bg-blue-50/30 transition-all duration-300 group shadow-sm">
                        <td class="px-8 py-5 rounded-l-2xl border-y border-l border-gray-50 text-gray-400 text-[10px] font-black text-center italic">
                            ${log.created_at}
                        </td>
                        <td class="px-8 py-5 border-y border-gray-50 font-black text-gray-800 uppercase text-xs tracking-tight">
                            ${log.product_name || 'Produk #'+log.product_id}
                        </td>
                        <td class="px-8 py-5 border-y border-gray-50 text-center">
                            <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest italic ${isIn ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600'}">
                                ${isIn ? 'Barang Masuk' : 'Barang Keluar'}
                            </span>
                        </td>
                        <td class="px-8 py-5 border-y border-gray-50 text-center font-black text-sm italic ${isIn ? 'text-green-600' : 'text-red-600'}">
                            ${isIn ? '+' : '-'}${log.qty}
                        </td>
                        <td class="px-8 py-5 rounded-r-2xl border-y border-r border-gray-50 font-mono text-[10px] text-blue-500 font-black tracking-widest">
                            ${log.reference_no || '-'}
                        </td>
                    </tr>
                `;
            }).join('');
            lucide.createIcons();
        } catch (error) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-20 text-red-500 font-black italic uppercase text-xs">Kegagalan sinkronisasi audit trail.</td></tr>`;
        }
    }

    /**
     * Filter pencarian sisi klien
     */
    function filterLogs() {
        const input = document.getElementById("logSearch").value.toLowerCase();
        const rows = document.querySelectorAll("#log-table-body tr");
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            if (row.cells.length > 1) {
                row.style.display = text.includes(input) ? "" : "none";
            }
        });
    }

    loadLogs();
</script>
<?= $this->endSection() ?>