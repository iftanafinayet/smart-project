<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
    <div class="mb-10 flex justify-between items-end">
        <div>
            <h2 class="text-4xl font-black text-gray-900 tracking-tighter uppercase italic">Ringkasan <span class="text-blue-600 font-black">Bisnis</span></h2>
            <p class="text-gray-500 font-medium mt-1">Pantau performa penjualan dan inventaris organisasi Anda.</p>
        </div>
        <button onclick="logout()" class="group flex items-center gap-3 bg-white text-gray-400 hover:text-red-600 px-6 py-3 rounded-2xl shadow-sm border border-gray-100 transition-all font-black text-[10px] uppercase tracking-widest">
            <i data-lucide="log-out" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
            Logout
        </button>
    </div>

    <div id="stat-cards-container" class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
        <div class="h-32 bg-gray-100 animate-pulse rounded-[2.5rem]"></div>
        <div class="h-32 bg-gray-100 animate-pulse rounded-[2.5rem]"></div>
        <div class="h-32 bg-gray-100 animate-pulse rounded-[2.5rem]"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 p-10 bg-white rounded-[3rem] shadow-xl shadow-gray-200/50 border border-gray-100">
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-lg font-black text-gray-800 uppercase tracking-tighter">Tren <span class="text-blue-600">Penjualan</span> Mingguan</h3>
                <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
                    <i data-lucide="line-chart" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="relative h-[300px] w-full">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-10 rounded-[3rem] shadow-xl shadow-gray-200/50 border border-gray-100 flex flex-col">
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-lg font-black text-red-600 uppercase tracking-tighter italic">Stok Kritis</h3>
                <div class="p-3 bg-red-50 text-red-600 rounded-2xl animate-pulse">
                    <i data-lucide="alert-octagon" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="overflow-y-auto max-h-[300px] pr-2 custom-scrollbar">
                <table class="w-full text-left text-sm border-separate border-spacing-y-3">
                    <tbody id="low-stock-body">
                        </tbody>
                </table>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    /**
     * Mengambil data analitik dari API
     */
    async function fetchDashboardData() {
        try {
            const response = await fetch(`${API_URL}/dashboard`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            if (response.status === 403) {
                alert('Akses Ditolak: Role Anda tidak memiliki izin Admin.');
                window.location.href = '/kasir'; 
                return;
            }

            const result = await response.json();
            if (result.data) {
                renderDashboard(result.data);
            }
        } catch (error) {
            console.error('Error dashboard sync:', error);
        }
    }

    function renderDashboard(data) {
        // --- 1. Render Kartu Statistik ---
        const cardsContainer = document.getElementById('stat-cards-container');
        if (cardsContainer) {
            cardsContainer.innerHTML = `
                <div class="group bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 hover:border-blue-500 transition-all duration-500 shadow-xl shadow-gray-200/30">
                    <div class="flex items-center gap-6">
                        <div class="p-5 rounded-[1.5rem] bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 shadow-lg shadow-blue-100"><i data-lucide="trending-up" class="w-7 h-7"></i></div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Pendapatan Hari Ini</p>
                            <p class="text-3xl font-black text-gray-900 tracking-tighter italic font-mono">Rp ${Number(data.sales_today ?? 0).toLocaleString('id-ID')}</p>
                        </div>
                    </div>
                </div>
                <div class="group bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 hover:border-purple-500 transition-all duration-500 shadow-xl shadow-gray-200/30">
                    <div class="flex items-center gap-6">
                        <div class="p-5 rounded-[1.5rem] bg-purple-50 text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-all duration-500 shadow-lg shadow-purple-100"><i data-lucide="users" class="w-7 h-7"></i></div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Total SDM Organisasi</p>
                            <p class="text-3xl font-black text-gray-900 tracking-tighter italic">${data.total_employees ?? 0} <span class="text-xs font-medium text-gray-400 not-italic">Staf</span></p>
                        </div>
                    </div>
                </div>
                <div class="group bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 hover:border-red-500 transition-all duration-500 shadow-xl shadow-gray-200/30">
                    <div class="flex items-center gap-6">
                        <div class="p-5 rounded-[1.5rem] bg-red-50 text-red-600 group-hover:bg-red-600 group-hover:text-white transition-all duration-500 shadow-lg shadow-red-100"><i data-lucide="alert-triangle" class="w-7 h-7"></i></div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Butuh Re-stock</p>
                            <p class="text-3xl font-black text-gray-900 tracking-tighter italic">${data.low_stock_products ? data.low_stock_products.length : 0} <span class="text-xs font-medium text-gray-400 not-italic uppercase tracking-widest">Item</span></p>
                        </div>
                    </div>
                </div>
            `;
        }

        // --- 2. Render Grafik Penjualan ---
        const chartCanvas = document.getElementById('salesChart');
        if (chartCanvas && data.sales_chart_data) {
            new Chart(chartCanvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: data.sales_chart_data.labels || [],
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: data.sales_chart_data.values || [],
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.08)',
                        fill: true,
                        borderWidth: 5,
                        pointRadius: 6,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#2563eb',
                        pointBorderWidth: 3,
                        tension: 0.45
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { display: false },
                        x: { 
                            grid: { display: false }, 
                            border: { display: false },
                            ticks: { font: { weight: 'bold', size: 10 }, color: '#94a3b8' }
                        }
                    }
                }
            });
        }

        // --- 3. Render Tabel Stok Rendah ---
        const tableBody = document.getElementById('low-stock-body');
        if (tableBody && data.low_stock_products) {
            tableBody.innerHTML = data.low_stock_products.map(product => `
                <tr class="bg-gray-50/50 hover:bg-red-50 transition-colors duration-300 rounded-2xl overflow-hidden">
                    <td class="px-6 py-4 rounded-l-2xl border-l border-y border-gray-100">
                        <p class="font-black text-gray-800 uppercase text-[11px] tracking-tight">${product.product_name || 'Tanpa Nama'}</p>
                        <p class="text-[9px] text-gray-400 font-mono tracking-widest italic">${product.sku || 'N/A'}</p>
                    </td>
                    <td class="px-6 py-4 border-y border-gray-100 text-center">
                        <span class="text-xs font-black text-red-600 italic underline decoration-red-200 decoration-2">${product.current_stock ?? 0}</span>
                    </td>
                    <td class="px-6 py-4 text-right rounded-r-2xl border-r border-y border-gray-100">
                        <span class="bg-red-600 text-white text-[8px] font-black px-3 py-1.5 rounded-lg uppercase tracking-[0.15em] shadow-sm">
                            ${(product.current_stock ?? 0) <= 0 ? 'Out Of Stock' : 'Kritis'}
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
<?= $this->endSection() ?>