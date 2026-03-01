<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Toko - Smart POS Enterprise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .chart-container { position: relative; height: 300px; width: 100%; }
    </style>
</head>
<body class="min-h-screen bg-[#f8fafc]">

    <?= view('components/sidebar') ?>

    <main class="ml-64 p-10 animate-in fade-in duration-700">
        
        <div class="mb-10 flex justify-between items-end">
            <div>
                <h2 class="text-4xl font-black text-gray-900 tracking-tighter">Ringkasan <span class="text-blue-600">Bisnis</span></h2>
                <p class="text-gray-500 font-medium mt-1">Pantau performa penjualan dan inventaris organisasi Anda.</p>
            </div>
            <button onclick="logout()" class="group flex items-center gap-3 bg-white text-gray-400 hover:text-red-600 px-6 py-3 rounded-2xl shadow-sm border border-gray-100 transition-all font-bold text-sm">
                <i data-lucide="log-out" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
                Logout
            </button>
        </div>

        <div id="stat-cards-container" class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
            <div class="h-32 bg-gray-100 animate-pulse rounded-[2rem]"></div>
            <div class="h-32 bg-gray-100 animate-pulse rounded-[2rem]"></div>
            <div class="h-32 bg-gray-100 animate-pulse rounded-[2rem]"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 p-8 bg-white rounded-[2.5rem] shadow-sm border border-gray-100" id="chart-container">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight">Tren Penjualan</h3>
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                        <i data-lucide="line-chart" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <div id="table-container" class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight text-red-600">Stok Kritis</h3>
                    <div class="p-2 bg-red-50 text-red-600 rounded-xl">
                        <i data-lucide="alert-octagon" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="overflow-y-auto max-h-[300px] pr-2">
                    <table class="w-full text-left text-sm border-separate border-spacing-y-2">
                        <tbody id="low-stock-body" class="space-y-2">
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
    lucide.createIcons();

    async function fetchDashboardData() {
        const token = localStorage.getItem('jwt_token');
        if (!token) { window.location.href = '/login'; return; }

        try {
            const response = await fetch('http://localhost:8080/api/v1/dashboard', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            if (response.status === 401) {
                localStorage.removeItem('jwt_token');
                window.location.href = '/login';
                return;
            }

            if (response.status === 403) {
                alert('Akses Ditolak: Anda tidak memiliki izin Admin.');
                window.location.href = '/sales'; // Redirect ke kasir jika bukan admin
                return;
            }

            const result = await response.json();
            if (result.data) {
                renderDashboard(result.data);
            }
        } catch (error) {
            console.error('Error fetching dashboard data:', error);
        }
    }

    function renderDashboard(data) {
        // --- 1. Render Kartu Statistik (UI Baru) ---
        const cardsContainer = document.getElementById('stat-cards-container');
        if (cardsContainer) {
            const salesToday = data.sales_today ?? 0;
            const totalEmployees = data.total_employees ?? 0;
            const lowStockCount = data.low_stock_products ? data.low_stock_products.length : 0;

            cardsContainer.innerHTML = `
                <div class="group bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 hover:border-green-500 transition-all duration-300">
                    <div class="flex items-center gap-5">
                        <div class="p-4 rounded-2xl bg-green-50 text-green-600 group-hover:bg-green-600 group-hover:text-white transition-all"><i data-lucide="trending-up"></i></div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Penjualan Hari Ini</p>
                            <p class="text-2xl font-black text-gray-900">Rp ${Number(salesToday).toLocaleString('id-ID')}</p>
                        </div>
                    </div>
                </div>
                <div class="group bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 hover:border-blue-500 transition-all duration-300">
                    <div class="flex items-center gap-5">
                        <div class="p-4 rounded-2xl bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all"><i data-lucide="users"></i></div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total SDM</p>
                            <p class="text-2xl font-black text-gray-900">${totalEmployees} Karyawan</p>
                        </div>
                    </div>
                </div>
                <div class="group bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 hover:border-red-500 transition-all duration-300">
                    <div class="flex items-center gap-5">
                        <div class="p-4 rounded-2xl bg-red-50 text-red-600 group-hover:bg-red-600 group-hover:text-white transition-all"><i data-lucide="alert-triangle"></i></div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Perlu Re-stock</p>
                            <p class="text-2xl font-black text-gray-900">${lowStockCount} Item</p>
                        </div>
                    </div>
                </div>
            `;
        }

        // --- 2. Render Grafik Penjualan (Styling Baru) ---
        const chartCanvas = document.getElementById('salesChart');
        if (chartCanvas && data.sales_chart_data) {
            const ctx = chartCanvas.getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.sales_chart_data.labels || [],
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: data.sales_chart_data.values || [],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.05)',
                        fill: true,
                        borderWidth: 4,
                        pointRadius: 4,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#3b82f6',
                        pointBorderWidth: 2,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { display: false },
                        x: { grid: { display: false }, border: { display: false } }
                    }
                }
            });
        }

        // --- 3. Render Tabel Stok Rendah (UI List Baru) ---
        const tableBody = document.getElementById('low-stock-body');
        if (tableBody && data.low_stock_products) {
            tableBody.innerHTML = data.low_stock_products.map(product => `
                <tr class="bg-gray-50/50 rounded-2xl overflow-hidden shadow-sm">
                    <td class="px-5 py-4 rounded-l-2xl">
                        <p class="font-bold text-gray-800 uppercase text-xs">${product.product_name || 'Tanpa Nama'}</p>
                        <p class="text-[10px] text-gray-400 font-mono tracking-tighter">${product.sku || 'N/A'}</p>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <span class="text-xs font-black text-red-600">${product.current_stock ?? 0}</span>
                    </td>
                    <td class="px-5 py-4 text-right rounded-r-2xl">
                        <span class="bg-red-100 text-red-700 text-[9px] font-black px-2 py-1 rounded-lg uppercase tracking-widest">
                            ${(product.current_stock ?? 0) <= 0 ? 'Habis' : 'Kritis'}
                        </span>
                    </td>
                </tr>
            `).join('');
        }

        lucide.createIcons();
    }

    function logout() {
        localStorage.removeItem('jwt_token');
        window.location.href = '/login';
    }

    fetchDashboardData();
</script>
</body>
</html>