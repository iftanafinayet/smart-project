<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan - Smart POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#f4f7f6] min-h-screen flex">
    <?= view('components/sidebar') ?>

    <main class="flex-1 p-8 ml-64">
        <div class="max-w-6xl mx-auto">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-800">Laporan Penjualan</h2>
                <p class="text-gray-500">Pantau transaksi produk organisasi Anda secara real-time.</p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8 flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" id="startDate" class="w-full p-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                    <input type="date" id="endDate" class="w-full p-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <button onclick="loadReport()" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition flex items-center gap-2 shadow-lg shadow-blue-100">
                    <i data-lucide="filter" class="w-4 h-4"></i> Filter Laporan
                </button>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Tanggal</th>
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">No. Invoice</th>
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">ID Kasir</th>
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Total Net</th>
                        </tr>
                    </thead>
                    <tbody id="report-tbody" class="divide-y divide-gray-50">
                        <tr>
                            <td colspan="4" class="p-10 text-center text-gray-400">Silakan pilih tanggal dan klik Filter.</td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold border-t border-gray-100">
                        <tr>
                            <td colspan="3" class="p-4 text-right text-gray-600 uppercase text-xs tracking-widest">Total Pendapatan:</td>
                            <td id="total-revenue" class="p-4 text-right text-blue-600 text-xl font-black italic">Rp 0</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();
        const API_BASE = 'http://localhost:8080/api/v1';
        const token = localStorage.getItem('jwt_token');

        // Proteksi Sesi: Jika token hilang, kembalikan ke login
        if (!token) window.location.href = '/login';

        async function loadReport() {
            const start = document.getElementById('startDate').value;
            const end = document.getElementById('endDate').value;
            const tbody = document.getElementById('report-tbody');

            if (!start || !end) return alert('Pilih rentang tanggal terlebih dahulu!');

            tbody.innerHTML = '<tr><td colspan="4" class="p-10 text-center text-gray-400 animate-pulse">Memuat data laporan...</td></tr>';

            try {
                const response = await fetch(`${API_BASE}/sales?start=${start}&end=${end}`, {
                    headers: { 
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    if (response.status === 403) throw new Error('Akses ditolak. Pastikan role Anda memiliki izin laporan.');
                    throw new Error('Gagal mengambil data dari server');
                }

                const result = await response.json();
                renderTable(result.data);
            } catch (error) {
                console.error('Error:', error);
                tbody.innerHTML = `<tr><td colspan="4" class="p-10 text-center text-red-500 font-bold italic">${error.message}</td></tr>`;
            }
        }

        function renderTable(data) {
            const tbody = document.getElementById('report-tbody');
            const totalRevenue = document.getElementById('total-revenue');
            
            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="p-10 text-center text-gray-400">Tidak ada transaksi ditemukan pada rentang tanggal tersebut.</td></tr>';
                totalRevenue.innerText = 'Rp 0';
                return;
            }

            let total = 0;
            tbody.innerHTML = data.map(item => {
                const amount = parseFloat(item.total_net || 0); // Gunakan float untuk DECIMAL database
                total += amount;
                return `
                    <tr class="hover:bg-blue-50/30 transition group">
                        <td class="p-4 text-xs text-gray-500 text-center font-medium">${item.sale_date}</td>
                        <td class="p-4 text-sm font-bold text-gray-800 group-hover:text-blue-600 transition">${item.invoice_number}</td>
                        <td class="p-4 text-sm text-gray-500">User ID: ${item.user_id || '-'}</td>
                        <td class="p-4 text-sm font-black text-right text-gray-900">Rp ${amount.toLocaleString('id-ID')}</td>
                    </tr>
                `;
            }).join('');

            totalRevenue.innerText = `Rp ${total.toLocaleString('id-ID')}`;
        }
    </script>
</body>
</html>