<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Stok - Smart POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#f4f7f6] min-h-screen">
    <?= view('components/sidebar') ?>

    <main class="ml-64 p-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Log Pergerakan Barang</h2>
                <p class="text-gray-500 text-sm">Audit trail untuk setiap perubahan stok inventaris</p>
            </div>
            
            <div class="relative w-full md:w-72">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" id="logSearch" onkeyup="filterLogs()" 
                       placeholder="Cari produk atau referensi..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500 bg-white shadow-sm transition">
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4 text-center">Waktu Terjadi</th>
                            <th class="px-6 py-4">Produk</th>
                            <th class="px-6 py-4 text-center">Tipe Arus</th>
                            <th class="px-6 py-4 text-center">Jumlah (Qty)</th>
                            <th class="px-6 py-4">No. Referensi</th>
                        </tr>
                    </thead>
                    <tbody id="log-table-body" class="divide-y divide-gray-100">
                        </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();
        const API_URL = 'http://localhost:8080/api/v1/stock-logs';
        const token = localStorage.getItem('jwt_token');

        // --- Fungsi Load Data dari API ---
        async function loadLogs() {
            const tbody = document.getElementById('log-table-body');
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-10 text-gray-400">Mengambil data...</td></tr>`;

            try {
                const response = await fetch(API_URL, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const result = await response.json();
                tbody.innerHTML = '';

                if (!result.data || result.data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center py-10 text-gray-400 italic">Belum ada riwayat pergerakan stok.</td></tr>`;
                    return;
                }

                result.data.forEach(log => {
                    const isIn = log.type === 'In';
                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-6 py-4 text-gray-500 text-xs text-center">${log.created_at}</td>
                            <td class="px-6 py-4 font-semibold text-gray-800">${log.product_name || 'Produk #'+log.product_id}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase ${isIn ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">
                                    ${isIn ? 'Masuk' : 'Keluar'}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold ${isIn ? 'text-green-600' : 'text-red-600'}">
                                ${isIn ? '+' : '-'}${log.qty}
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-gray-600">
                                ${log.reference_no || '-'}
                            </td>
                        </tr>
                    `;
                });
                lucide.createIcons();
            } catch (error) {
                console.error('Gagal memuat log:', error);
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-10 text-red-500 font-medium">Gagal mengambil data dari server.</td></tr>`;
            }
        }

        // --- Fungsi Filter Pencarian (Client-side) ---
        function filterLogs() {
            const input = document.getElementById("logSearch").value.toLowerCase();
            const rows = document.querySelectorAll("#log-table-body tr");
            
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                // Sembunyikan baris jika tidak mengandung teks pencarian, abaikan baris kosong/pesan error
                if (row.cells.length > 1) {
                    row.style.display = text.includes(input) ? "" : "none";
                }
            });
        }

        // Jalankan saat halaman dimuat
        loadLogs();
    </script>
</body>
</html>