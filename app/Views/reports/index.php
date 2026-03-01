<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
    <div class="mb-10">
        <h2 class="text-4xl font-black text-gray-900 tracking-tighter uppercase">Laporan <span class="text-blue-600">Penjualan</span></h2>
        <p class="text-gray-500 font-medium mt-1 italic">Pantau transaksi produk organisasi secara real-time.</p>
    </div>

    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 mb-10 flex flex-col md:flex-row gap-6 items-end">
        <div class="flex-1 w-full">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1 italic">Tanggal Mulai</label>
            <input type="date" id="startDate" class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all font-bold text-sm">
        </div>
        <div class="flex-1 w-full">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1 italic">Tanggal Selesai</label>
            <input type="date" id="endDate" class="w-full p-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all font-bold text-sm">
        </div>
        <button onclick="loadReport()" class="group bg-blue-600 text-white px-10 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all flex items-center gap-3 shadow-lg shadow-blue-100 active:scale-95 w-full md:w-auto justify-center">
            <i data-lucide="filter" class="w-4 h-4 group-hover:rotate-180 transition-transform"></i> Filter Laporan
        </button>
    </div>

    <div class="bg-white rounded-[3.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto p-6">
            <table class="w-full text-left border-separate border-spacing-y-3">
                <thead>
                    <tr class="text-gray-400 uppercase text-[10px] font-black tracking-[0.2em]">
                        <th class="px-8 py-4 text-center">Tanggal Transaksi</th>
                        <th class="px-8 py-4">Nomor Invoice</th>
                        <th class="px-8 py-4">Petugas / Kasir</th>
                        <th class="px-8 py-4 text-right">Total Pendapatan (Net)</th>
                    </tr>
                </thead>
                <tbody id="report-tbody">
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center text-gray-300 italic font-medium uppercase tracking-widest text-xs">Pilih periode tanggal untuk memuat data.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="bg-gray-50/50 p-10 border-t border-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-blue-100 text-blue-600 rounded-2xl">
                    <i data-lucide="wallet" class="w-6 h-6"></i>
                </div>
                <span class="text-sm font-black text-gray-400 uppercase tracking-widest italic text-center">Total Akumulasi Pendapatan</span>
            </div>
            <div id="total-revenue" class="text-4xl font-black text-blue-600 tracking-tighter italic">Rp 0</div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    async function loadReport() {
        const start = document.getElementById('startDate').value;
        const end = document.getElementById('endDate').value;
        const tbody = document.getElementById('report-tbody');

        if (!start || !end) return alert('Pilih rentang tanggal terlebih dahulu!');

        tbody.innerHTML = '<tr><td colspan="4" class="px-8 py-32 text-center text-gray-300 font-bold italic animate-pulse uppercase tracking-widest text-xs">Sinkronisasi data server...</td></tr>';

        try {
            const response = await fetch(`${API_BASE}/sales?start=${start}&end=${end}`, {
                headers: { 
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Gagal mengambil data laporan.');

            const result = await response.json();
            renderTable(result.data);
        } catch (error) {
            tbody.innerHTML = `<tr><td colspan="4" class="px-8 py-20 text-center text-red-500 font-black italic uppercase text-xs">${error.message}</td></tr>`;
        }
    }

    function renderTable(data) {
        const tbody = document.getElementById('report-tbody');
        const totalRevenue = document.getElementById('total-revenue');
        
        if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="px-8 py-20 text-center text-gray-400 font-medium italic">Data tidak ditemukan.</td></tr>';
            totalRevenue.innerText = 'Rp 0';
            return;
        }

        let total = 0;
        tbody.innerHTML = data.map(item => {
            const amount = parseFloat(item.total_net || 0);
            total += amount;
            return `
                <tr class="bg-white hover:bg-blue-50/30 transition-all duration-300 group shadow-sm">
                    <td class="px-8 py-5 rounded-l-2xl border-y border-l border-gray-50 text-center">
                        <span class="text-[10px] font-black text-gray-400 uppercase italic font-mono">${item.sale_date}</span>
                    </td>
                    <td class="px-8 py-5 border-y border-gray-50 font-black text-gray-800 uppercase text-xs tracking-tight group-hover:text-blue-600 italic">
                        ${item.invoice_number}
                    </td>
                    <td class="px-8 py-5 border-y border-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-tighter text-center">
                        ID: ${item.user_id || '-'}
                    </td>
                    <td class="px-8 py-5 rounded-r-2xl border-y border-r border-gray-50 text-right font-black text-gray-900 italic">
                        Rp ${amount.toLocaleString('id-ID')}
                    </td>
                </tr>
            `;
        }).join('');

        totalRevenue.innerText = `Rp ${total.toLocaleString('id-ID')}`;
        lucide.createIcons();
    }
</script>
<?= $this->endSection() ?>