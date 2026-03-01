<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventaris & Label Barcode - Gudang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* CSS Khusus Cetak */
        @media print {
            body * { visibility: hidden; }
            #printArea, #printArea * { visibility: visible; }
            #printArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                text-align: center;
            }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-[#f4f7f6] min-h-screen">
    
    <nav class="bg-white border-b border-gray-100 px-8 py-4 flex justify-between items-center sticky top-0 z-50 shadow-sm no-print">
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-3">
                <div class="bg-blue-600 p-2 rounded-lg">
                    <i data-lucide="package" class="w-5 h-5 text-white"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-800 tracking-tight">Warehouse <span class="text-blue-600">Inventory</span></h1>
            </div>
            <a href="/warehouse/dashboard" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition text-sm font-medium border-l pl-6 border-gray-200">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Dashboard
            </a>
        </div>
        
        <div class="flex items-center gap-4">
            <button onclick="logout()" class="flex items-center gap-2 text-red-500 hover:bg-red-50 px-4 py-2 rounded-xl transition font-medium text-sm">
                <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
            </button>
        </div>
    </nav>

    <main class="max-w-[1200px] mx-auto p-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 no-print">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">Inventaris Produk</h2>
                <p class="text-gray-500 text-sm">Kelola stok dan cetak label barcode barang organisasi</p>
            </div>
            
            <div class="flex items-center gap-3">
                <label class="text-sm font-medium text-gray-600">Filter Stok:</label>
                <select id="stockFilter" onchange="loadProducts()" 
                        class="bg-white border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 block p-3 outline-none shadow-sm transition">
                    <option value="all">Semua Produk</option>
                    <option value="low_stock">Stok Rendah (< 10)</option>
                </select>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden no-print">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-400 uppercase text-[10px] font-black tracking-widest">
                        <tr>
                            <th class="px-8 py-4">Nama Produk</th>
                            <th class="px-8 py-4 text-center">Stok Saat Ini</th>
                            <th class="px-8 py-4 text-center">Harga Jual</th>
                            <th class="px-8 py-4 text-center">SKU / Kode</th>
                            <th class="px-8 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="product-table-body" class="divide-y divide-gray-50">
                        <tr><td colspan="5" class="text-center py-20 text-gray-400">Memuat data inventaris...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="printModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 no-print">
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden">
            <div class="p-10 text-center" id="printArea">
                <h3 id="modalProductName" class="text-xl font-black text-gray-800 mb-4 uppercase tracking-tight">Nama Produk</h3>
                <div class="flex justify-center p-6 bg-gray-50 rounded-[2rem] mb-6">
                    <svg id="barcode-preview"></svg>
                </div>
                <p class="text-[10px] text-gray-400 font-medium italic">Smart POS - CCIT FTUI Organisasi</p>
            </div>
            <div class="flex gap-4 p-8 bg-gray-50">
                <button onclick="closePrintModal()" class="flex-1 px-6 py-3 bg-white border border-gray-200 text-gray-600 rounded-2xl hover:bg-gray-100 font-bold transition">
                    Batal
                </button>
                <button onclick="window.print()" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 font-bold transition flex items-center justify-center gap-2 shadow-lg shadow-blue-100">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak Label
                </button>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
        const API_URL = 'http://localhost:8080/api/v1/products';
        const token = localStorage.getItem('jwt_token');

        // Proteksi Sesi
        if (!token) window.location.href = '/login';

        function logout() {
            if (confirm('Yakin ingin keluar?')) {
                localStorage.removeItem('jwt_token');
                localStorage.removeItem('role_id');
                window.location.href = '/login';
            }
        }

        // --- Fungsi Load Data Produk ---
        async function loadProducts() {
            const filterValue = document.getElementById('stockFilter').value;
            const tbody = document.getElementById('product-table-body');
            
            try {
                const response = await fetch(`${API_URL}`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const result = await response.json();
                tbody.innerHTML = '';

                if (!result.data || result.data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center py-20 text-gray-400 italic">Data inventaris kosong.</td></tr>`;
                    return;
                }

                // Filter data manual berdasarkan pilihan stok rendah
                let filteredData = result.data;
                if (filterValue === 'low_stock') {
                    filteredData = result.data.filter(p => parseInt(p.current_stock || 0) < 10);
                }

                filteredData.forEach(product => {
                    const stock = parseInt(product.current_stock || 0);
                    const price = parseInt(product.selling_price || 0);
                    const isLow = stock < 10;
                    const productCode = product.sku || 'PROD-' + product.id;

                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-8 py-5">
                                <p class="font-bold text-gray-800 leading-tight">${product.product_name || product.name}</p>
                                <span class="text-[10px] text-gray-400 uppercase font-medium">${product.unit || 'Pcs'}</span>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="px-3 py-1 rounded-full font-black text-xs ${isLow ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600'}">
                                    ${stock}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-center font-bold text-gray-700">
                                Rp ${price.toLocaleString('id-ID')}
                            </td>
                            <td class="px-8 py-5 text-center font-mono text-blue-600 text-xs font-black tracking-tighter">${productCode}</td>
                            <td class="px-8 py-5 text-right">
                                <button onclick="openPrintModal('${productCode}', '${product.product_name || product.name}')" 
                                        class="inline-flex items-center gap-2 bg-white border border-gray-200 px-4 py-2 rounded-xl text-blue-600 hover:bg-blue-600 hover:text-white hover:border-transparent transition font-bold text-[10px] uppercase tracking-wider shadow-sm">
                                    <i data-lucide="barcode" class="w-3.5 h-3.5"></i> Cetak Label
                                </button>
                            </td>
                        </tr>
                    `;
                });
                lucide.createIcons();
            } catch (error) {
                console.error('Error load products:', error);
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-20 text-red-500 font-bold">Gagal mengambil data dari server.</td></tr>`;
            }
        }

        // --- Fungsi Modal & Barcode ---
        function openPrintModal(code, name) {
            document.getElementById('modalProductName').innerText = name;
            document.getElementById('printModal').classList.remove('hidden');
            
            JsBarcode("#barcode-preview", code, {
                format: "CODE128",
                lineColor: "#000",
                width: 2.5,
                height: 60,
                displayValue: true,
                fontSize: 16,
                fontOptions: "bold",
                margin: 15
            });
        }

        function closePrintModal() {
            document.getElementById('printModal').classList.add('hidden');
        }

        loadProducts();
    </script>
</body>
</html>