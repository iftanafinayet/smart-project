<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Toko - Enterprise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-[#f4f7f6]">

    <?= view('components/sidebar') ?>

    <main class="ml-64 p-8">
        
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-2">Dashboard</h2>
                <p class="text-gray-600">Selamat datang kembali! Ini adalah ringkasan toko Anda hari ini.</p>
            </div>
            <button onclick="logout()" class="flex items-center gap-2 bg-white text-red-600 px-4 py-2 rounded-lg shadow-sm hover:bg-red-50">
                <i data-lucide="log-out"></i>
                Logout
            </button>
        </div>

        <div id="stat-cards-container" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            </div>

        <div class="mb-8 p-6 bg-white rounded-xl shadow-sm" id="chart-container">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Grafik Penjualan</h3>
            <canvas id="salesChart"></canvas>
        </div>

        <div id="table-container" class="bg-white p-6 rounded-xl shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Stok Rendah</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Stok</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody id="low-stock-body">
                        </tbody>
                </table>
            </div>
        </div>
        
    </main>

    <script>
    // Inisialisasi ikon Lucide pertama kali
    lucide.createIcons();

    // 🔐 Ambil Data dengan JWT Token
    async function fetchDashboardData() {
        const token = localStorage.getItem('jwt_token');

        if (!token) {
            window.location.href = '/login';
            return;
        }

        try {
            const response = await fetch('http://localhost:8080/api/v1/dashboard', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            // Handle error berdasarkan status code
            if (response.status === 401) {
                localStorage.removeItem('jwt_token');
                window.location.href = '/login';
                return;
            }

            if (response.status === 403) {
                alert('Akses Ditolak: Anda tidak memiliki izin Admin.');
                window.location.href = '/dashboard';
            }

            const result = await response.json();
            
            // Cek apakah data ada sebelum memanggil fungsi render
            if (result.data) {
                renderDashboard(result.data);
            } else {
                console.error('Data tidak ditemukan dalam respon API:', result);
            }
        } catch (error) {
            console.error('Error fetching dashboard data:', error);
        }
    }

    // Fungsi untuk merender data ke dalam elemen DOM
    function renderDashboard(data) {
        // --- 1. Render Kartu Statistik (dengan pengecekan data) ---
        const cardsContainer = document.getElementById('stat-cards-container');
        if (cardsContainer) {
            // Gunakan optional chaining (?.) untuk menghindari error jika data tidak ada
            const salesToday = data.sales_today ?? 0;
            const totalEmployees = data.total_employees ?? 0;
            const lowStockCount = data.low_stock_products ? data.low_stock_products.length : 0;

            cardsContainer.innerHTML = `
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="p-3 rounded-full bg-green-100 text-green-600"><i data-lucide="trending-up"></i></div>
                    <div>
                        <p class="text-sm text-gray-500">Penjualan Hari Ini</p>
                        <p class="text-2xl font-bold text-gray-900">Rp ${Number(salesToday).toLocaleString('id-ID')}</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600"><i data-lucide="users"></i></div>
                    <div>
                        <p class="text-sm text-gray-500">Total Karyawan</p>
                        <p class="text-2xl font-bold text-gray-900">${totalEmployees}</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="p-3 rounded-full bg-red-100 text-red-600"><i data-lucide="alert-triangle"></i></div>
                    <div>
                        <p class="text-sm text-gray-500">Stok Rendah</p>
                        <p class="text-2xl font-bold text-gray-900">${lowStockCount} Item</p>
                    </div>
                </div>
            `;
        }

        // --- 2. Render Grafik Penjualan ---
        const chartCanvas = document.getElementById('salesChart');
        if (chartCanvas && data.sales_chart_data) {
            const ctx = chartCanvas.getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.sales_chart_data.labels || [],
                    datasets: [{
                        label: 'Penjualan (Rp)',
                        data: data.sales_chart_data.values || [],
                        borderColor: 'rgb(59, 130, 246)',
                        tension: 0.3
                    }]
                }
            });
        }

        // --- 3. Render Tabel Stok Rendah ---
        const tableBody = document.getElementById('low-stock-body');
        if (tableBody && data.low_stock_products) {
            tableBody.innerHTML = data.low_stock_products.map(product => `
                <tr class="border-b">
                    <td class="px-4 py-3 font-medium text-gray-900">${product.product_name || 'Tanpa Nama'}</td>
                    <td class="px-4 py-3">${product.current_stock ?? 0}</td>
                    <td class="px-4 py-3">
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">
                            ${(product.current_stock ?? 0) <= 0 ? 'Habis' : 'Segera Habis'}
                        </span>
                    </td>
                </tr>
            `).join('');
        }

        // PENTING: Re-render ikon setelah elemen ditambahkan ke DOM
        lucide.createIcons();
    }

    // 🚪 Fungsi Logout
    function logout() {
        localStorage.removeItem('jwt_token');
        window.location.href = '/login';
    }

    // Jalankan saat halaman dimuat
    fetchDashboardData();
</script>
</body>
</html>