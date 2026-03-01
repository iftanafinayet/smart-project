<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Gudang - Smart POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#f4f7f6] min-h-screen">

    <nav class="bg-white border-b border-gray-100 px-8 py-4 flex justify-between items-center sticky top-0 z-50 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="bg-blue-600 p-2 rounded-lg">
                <i data-lucide="package" class="w-5 h-5 text-white"></i>
            </div>
            <h1 class="text-xl font-bold text-gray-800 tracking-tight">Warehouse <span class="text-blue-600">Panel</span></h1>
        </div>
        
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 bg-gray-50 px-4 py-2 rounded-full border border-gray-100">
                <i data-lucide="user" class="w-4 h-4"></i>
                <span id="user-display">Petugas Gudang</span>
            </div>
            <button onclick="logout()" class="flex items-center gap-2 text-red-500 hover:bg-red-50 px-4 py-2 rounded-xl transition font-medium">
                <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
            </button>
        </div>
    </nav>

    <main class="max-w-[1200px] mx-auto p-8">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Dashboard Gudang</h2>
            <p class="text-gray-500 text-sm">Ringkasan operasional dan kontrol inventaris organisasi</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="p-3 bg-orange-100 text-orange-600 rounded-xl">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Stok Rendah (<5)</p>
                    <h3 id="low-stock-count" class="text-2xl font-black text-gray-800">0</h3>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="p-3 bg-blue-100 text-blue-600 rounded-xl">
                    <i data-lucide="history" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total SKU Produk</p>
                    <h3 id="total-sku" class="text-2xl font-black text-gray-800">0</h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="p-3 bg-green-100 text-green-600 rounded-xl">
                    <i data-lucide="layers" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Unit Tersedia</p>
                    <h3 id="total-inventory" class="text-2xl font-black text-gray-800">0</h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 flex items-center gap-3">
                    <i data-lucide="list-checks" class="w-5 h-5 text-orange-500"></i> 
                    Daftar Barang Harus Re-stock
                </h3>
                <div class="flex gap-4">
                    <a href="/warehouse/inventory" class="text-blue-600 text-sm font-bold hover:underline">Kelola Inventori</a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-400 uppercase text-[10px] font-black tracking-widest">
                        <tr>
                            <th class="px-8 py-4">Nama Produk</th>
                            <th class="px-8 py-4 text-center">Sisa Stok</th>
                            <th class="px-8 py-4 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody id="low-stock-list" class="divide-y divide-gray-50">
                        <tr><td colspan="3" class="text-center py-20 text-gray-400">Memuat data gudang...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();
        const API_BASE = 'http://localhost:8080/api/v1';
        const token = localStorage.getItem('jwt_token');

        // Proteksi Sesi & Logout
        if (!token) window.location.href = '/login';

        function logout() {
            if (confirm('Yakin ingin keluar dari sistem gudang?')) {
                localStorage.removeItem('jwt_token');
                localStorage.removeItem('role_id');
                window.location.href = '/login';
            }
        }

        async function loadDashboard() {
            try {
                // Mengambil data produk untuk dihitung statistiknya
                const response = await fetch(`${API_BASE}/products`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const result = await response.json();
                
                if (response.ok && result.data) {
                    const products = result.data;
                    
                    // Filter Produk Stok Rendah (< 5)
                    const lowStockItems = products.filter(p => parseInt(p.current_stock || 0) < 5);
                    const totalUnits = products.reduce((sum, p) => sum + parseInt(p.current_stock || 0), 0);

                    // Update Widget Angka
                    document.getElementById('low-stock-count').innerText = lowStockItems.length;
                    document.getElementById('total-sku').innerText = products.length;
                    document.getElementById('total-inventory').innerText = totalUnits;

                    // Render Tabel Produk Kritis
                    const tbody = document.getElementById('low-stock-list');
                    if (lowStockItems.length > 0) {
                        tbody.innerHTML = lowStockItems.map(item => `
                            <tr class="hover:bg-gray-50 transition group">
                                <td class="px-8 py-4">
                                    <p class="font-bold text-gray-800">${item.product_name || item.name}</p>
                                    <span class="text-[10px] text-gray-400 font-mono">${item.sku || 'SKU-000'}</span>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="bg-red-50 text-red-600 px-3 py-1 rounded-full text-xs font-black">
                                        ${item.current_stock || 0}
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <a href="/warehouse/inventory" class="bg-blue-600 text-white px-4 py-2 rounded-xl text-[10px] font-black hover:bg-blue-700 transition shadow-md shadow-blue-100 uppercase">
                                        Update Stok
                                    </a>
                                </td>
                            </tr>
                        `).join('');
                    } else {
                        tbody.innerHTML = `<tr><td colspan="3" class="text-center py-20 text-green-500 font-bold">✅ Semua stok aman. Tidak ada barang di bawah 5 unit.</td></tr>`;
                    }
                }
                lucide.createIcons();
            } catch (error) {
                console.error('Error load dashboard:', error);
            }
        }

        loadDashboard();
    </script>
</body>
</html>