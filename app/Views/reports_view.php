<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Smart POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-[#f4f7f6]">
    
    <?= view('components/sidebar') ?>

    <main class="ml-64 p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Laporan Penjualan</h2>
            <p class="text-gray-600">Ringkasan aktivitas penjualan toko</p>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                    <input type="date" id="startDate" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                    <input type="date" id="endDate" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
                <div class="flex items-end">
                    <button onclick="loadReport()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 w-full flex items-center justify-center gap-2">
                        <i data-lucide="filter" class="w-4 h-4"></i> Filter Laporan
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-3">ID Transaksi</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">ID User</th>
                            <th class="px-4 py-3 text-right">Total Net</th>
                        </tr>
                    </thead>
                    <tbody id="report-table-body" class="divide-y divide-gray-100">
                        </tbody>
                    <tfoot>
                        <tr class="font-semibold text-gray-900 bg-gray-50">
                            <td colspan="3" class="px-4 py-3 text-right">Total Pendapatan:</td>
                            <td id="total-revenue" class="px-4 py-3 text-right text-lg text-blue-600">Rp 0</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();
        const API_URL = 'http://localhost:8080/api/v1/';
        const token = localStorage.getItem('jwt_token');

        // Set tanggal hari ini sebagai default
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('startDate').value = today;
        document.getElementById('endDate').value = today;

        async function loadReport() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const tbody = document.getElementById('report-table-body');
            const totalRevenueEl = document.getElementById('total-revenue');

            tbody.innerHTML = `<tr><td colspan="4" class="text-center py-4">Memuat data...</td></tr>`;
            totalRevenueEl.innerText = `Rp 0`;

            try {
                const response = await fetch(`${API_URL}sales?start=${startDate}&end=${endDate}`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                if (!response.ok) throw new Error('Gagal mengambil data laporan');

                const result = await response.json();
                tbody.innerHTML = '';
                let totalRevenue = 0;

                // Safe Handling Data
                if (!result.data || !Array.isArray(result.data) || result.data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-gray-500">Tidak ada data untuk rentang tanggal ini.</td></tr>`;
                } else {
                    result.data.forEach(sale => {
                        // FIX: Menggunakan total_net dan sale_date sesuai struktur tabel
                        const total = parseFloat(sale.total_net) || 0;
                        totalRevenue += total;

                        tbody.innerHTML += `
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">#${sale.id}</td>
                                <td class="px-4 py-3 text-gray-600">${sale.sale_date}</td>
                                <td class="px-4 py-3">${sale.user_id}</td>
                                <td class="px-4 py-3 text-right font-semibold">
                                    Rp ${total.toLocaleString('id-ID')}
                                </td>
                            </tr>
                        `;
                    });
                }

                totalRevenueEl.innerText = `Rp ${totalRevenue.toLocaleString('id-ID')}`;

            } catch (error) {
                console.error('Error:', error);
                tbody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-red-500">Gagal memuat data. Periksa SaleController.</td></tr>`;
            }
        }

        // Muat data saat halaman pertama kali dibuka
        loadReport();
    </script>
</body>
</html>