<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Gudang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-[#f8fafc] min-h-screen">

    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-100 px-8 py-4 flex justify-between items-center sticky top-0 z-50 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="bg-blue-600 p-2.5 rounded-2xl shadow-lg shadow-blue-100">
                <i data-lucide="layout-dashboard" class="w-5 h-5 text-white"></i>
            </div>
            <div>
                <h1 class="text-lg font-black text-gray-900 tracking-tight leading-none">Warehouse <span class="text-blue-600">Core</span></h1>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Smart POS System</p>
            </div>
        </div>
        
        <div class="flex items-center gap-6">
            <div class="hidden md:flex items-center gap-3 text-sm text-gray-500 bg-gray-50 px-5 py-2.5 rounded-2xl border border-gray-100">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span id="user-display" class="font-bold">Gudang Organisasi</span>
            </div>
            <button onclick="logout()" class="group flex items-center gap-2 text-gray-400 hover:text-red-500 transition-all font-bold text-sm">
                <i data-lucide="log-out" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i> Keluar
            </button>
        </div>
    </nav>

    <main class="max-w-[1400px] mx-auto p-8">
        <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h2 class="text-4xl font-black text-gray-900 tracking-tighter mb-2">Statistik <span class="text-blue-600">Inventaris</span></h2>
            </div>
            <div id="last-update" class="text-[10px] font-black text-blue-600 bg-blue-50 px-6 py-3 rounded-2xl border border-blue-100 uppercase tracking-[0.2em] shadow-sm">
                Syncing Data...
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="group bg-white p-8 rounded-[3rem] border border-gray-100 shadow-sm hover:border-red-500 hover:shadow-2xl hover:shadow-red-100 transition-all duration-500">
                <div class="flex items-center gap-6">
                    <div class="p-5 bg-red-50 text-red-600 rounded-[2rem] group-hover:bg-red-600 group-hover:text-white transition-all duration-500 rotate-3 group-hover:rotate-0">
                        <i data-lucide="alert-octagon" class="w-10 h-10"></i>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 font-black uppercase tracking-[0.3em] mb-1">Stok Kritis (< 5)</p>
                        <h3 id="low-stock-count" class="text-4xl font-black text-gray-900 tracking-tighter">0</h3>
                    </div>
                </div>
            </div>
            
            <div class="group bg-white p-8 rounded-[3rem] border border-gray-100 shadow-sm hover:border-blue-500 hover:shadow-2xl hover:shadow-blue-100 transition-all duration-500">
                <div class="flex items-center gap-6">
                    <div class="p-5 bg-blue-50 text-blue-600 rounded-[2rem] group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 -rotate-3 group-hover:rotate-0">
                        <i data-lucide="layers" class="w-10 h-10"></i>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 font-black uppercase tracking-[0.3em] mb-1">Katalog Produk</p>
                        <h3 id="total-sku" class="text-4xl font-black text-gray-900 tracking-tighter">0</h3>
                    </div>
                </div>
            </div>

            <div class="group bg-white p-8 rounded-[3rem] border border-gray-100 shadow-sm hover:border-green-500 hover:shadow-2xl hover:shadow-green-100 transition-all duration-500">
                <div class="flex items-center gap-6">
                    <div class="p-5 bg-green-50 text-green-600 rounded-[2rem] group-hover:bg-green-600 group-hover:text-white transition-all duration-500 rotate-6 group-hover:rotate-0">
                        <i data-lucide="box" class="w-10 h-10"></i>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 font-black uppercase tracking-[0.3em] mb-1">Total Unit Fisik</p>
                        <h3 id="total-inventory" class="text-4xl font-black text-gray-900 tracking-tighter">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[3.5rem] shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
            <div class="p-10 border-b border-gray-50 flex flex-col md:flex-row justify-between items-center gap-6 bg-gradient-to-r from-gray-50 to-white">
                <h3 class="font-black text-gray-800 flex items-center gap-4 text-xl tracking-tight uppercase">
                    <span class="w-3 h-10 bg-orange-500 rounded-full shadow-lg shadow-orange-200"></span>
                    Prioritas Re-Stock
                </h3>
                <div class="flex gap-4">
                    <a href="/warehouse/inventory" class="group flex items-center gap-3 bg-gray-900 text-white px-8 py-4 rounded-[1.5rem] text-xs font-black hover:bg-blue-600 transition-all shadow-xl shadow-gray-200 active:scale-95">
                        KELOLA INVENTORI <i data-lucide="chevron-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
            
            <div class="overflow-x-auto p-6">
                <table class="w-full text-left border-separate border-spacing-y-4">
                    <thead>
                        <tr class="text-gray-400 uppercase text-[11px] font-black tracking-[0.2em]">
                            <th class="px-10 py-4">Informasi Produk</th>
                            <th class="px-10 py-4 text-center">Status Gudang</th>
                            <th class="px-10 py-4 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody id="low-stock-list">
                        <tr><td colspan="3" class="text-center py-32 text-gray-300 font-bold italic animate-pulse">Memindai Database...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();
        const API_BASE = 'http://localhost:8080/api/v1';
        const token = localStorage.getItem('jwt_token');

        if (!token) window.location.href = '/login';

        function logout() {
            if (confirm('Yakin ingin keluar dari Warehouse Core?')) {
                localStorage.removeItem('jwt_token');
                localStorage.removeItem('role_id');
                window.location.href = '/login';
            }
        }

        async function loadDashboard() {
            try {
                // Fetch data dari ProductController
                const response = await fetch(`${API_BASE}/products`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const result = await response.json();
                
                if (response.ok && result.data) {
                    const products = result.data;
                    
                    // Filter berdasarkan current_stock sesuai skema database
                    const lowStockItems = products.filter(p => parseInt(p.current_stock || 0) < 5);
                    const totalUnits = products.reduce((sum, p) => sum + parseInt(p.current_stock || 0), 0);

                    // Update UI Widgets
                    document.getElementById('low-stock-count').innerText = lowStockItems.length;
                    document.getElementById('total-sku').innerText = products.length;
                    document.getElementById('total-inventory').innerText = totalUnits.toLocaleString('id-ID');
                    document.getElementById('last-update').innerText = `SYNCED: ${new Date().toLocaleTimeString('id-ID')}`;

                    const tbody = document.getElementById('low-stock-list');
                    if (lowStockItems.length > 0) {
                        tbody.innerHTML = lowStockItems.map(item => `
                            <tr class="bg-white hover:bg-blue-50/50 transition-all duration-300 group shadow-sm border border-gray-50 rounded-3xl overflow-hidden">
                                <td class="px-10 py-6 rounded-l-[2.5rem] border-y border-l border-gray-100">
                                    <div class="flex items-center gap-6">
                                        <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 group-hover:bg-white group-hover:text-blue-600 transition-all shadow-inner">
                                            <i data-lucide="package-search" class="w-7 h-7"></i>
                                        </div>
                                        <div>
                                            <p class="font-black text-gray-900 uppercase text-sm tracking-tight mb-1">${item.product_name || item.name}</p>
                                            <span class="text-[10px] text-blue-500 font-black bg-blue-50 px-2 py-1 rounded-md tracking-tighter uppercase">${item.sku || 'ITEM-NEW'}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-center border-y border-gray-100">
                                    <div class="inline-flex flex-col items-center">
                                        <span class="text-[10px] font-black text-red-400 uppercase tracking-widest mb-1">Stock Left</span>
                                        <span class="bg-red-50 text-red-600 px-6 py-2 rounded-2xl text-xs font-black shadow-sm border border-red-100">
                                            ${item.current_stock || 0} UNIT
                                        </span>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-right rounded-r-[2.5rem] border-y border-r border-gray-100">
                                    <a href="/warehouse/inventory" class="inline-flex items-center gap-2 bg-gray-900 text-white px-6 py-3 rounded-2xl text-[10px] font-black hover:bg-blue-600 transition-all shadow-lg hover:shadow-blue-200 uppercase tracking-widest">
                                        RESTOCK <i data-lucide="plus-circle" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        `).join('');
                    } else {
                        tbody.innerHTML = `<tr><td colspan="3" class="text-center py-32">
                            <div class="flex flex-col items-center gap-6">
                                <div class="w-24 h-24 bg-green-50 text-green-500 rounded-[2.5rem] flex items-center justify-center shadow-inner">
                                    <i data-lucide="shield-check" class="w-12 h-12"></i>
                                </div>
                                <div class="text-center">
                                    <p class="text-gray-900 font-black text-lg uppercase tracking-tight">Gudang Aman Terkendali</p>
                                    <p class="text-gray-400 text-xs font-medium italic">Semua stok produk berada di atas batas kritis.</p>
                                </div>
                            </div>
                        </td></tr>`;
                    }
                }
                lucide.createIcons();
            } catch (error) {
                console.error('Core Dashboard Error:', error);
            }
        }

        // Initialize
        loadDashboard();
        
        // Auto-refresh setiap 1 menit untuk monitoring real-time
        setInterval(loadDashboard, 60000);
    </script>
</body>
</html>