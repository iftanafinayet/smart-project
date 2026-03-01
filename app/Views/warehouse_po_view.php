<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Gudang - Purchase Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#f4f7f6] min-h-screen">
    <?= view('components/sidebar') ?>

    <main class="ml-64 p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Penerimaan Barang (PO)</h2>
            <p class="text-gray-600">Kelola stok masuk dari supplier</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-6 py-4">No. PO</th>
                        <th class="px-6 py-4">Supplier</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="po-table-body" class="divide-y divide-gray-100">
                    </tbody>
            </table>
        </div>
    </main>

    <div id="detailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex justify-center items-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">Detail Item Pesanan</h3>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto border rounded-lg">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-[10px] font-bold">
                            <tr>
                                <th class="px-4 py-3">Nama Barang</th>
                                <th class="px-4 py-3 text-center">Qty Pesanan</th>
                                <th class="px-4 py-3 text-right">Harga Satuan</th>
                            </tr>
                        </thead>
                        <tbody id="detail-item-body" class="divide-y divide-gray-100">
                            </tbody>
                    </table>
                </div>
                <div class="flex justify-end mt-6">
                    <button onclick="closeDetailModal()" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 transition font-medium">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
        const API_URL = 'http://localhost:8080/api/v1/purchase-orders';
        const token = localStorage.getItem('jwt_token');

        // --- Load Daftar PO ---
        async function loadPOs() {
            try {
                const response = await fetch(API_URL, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const result = await response.json();
                const tbody = document.getElementById('po-table-body');
                tbody.innerHTML = '';

                result.data.forEach(po => {
                    const isReceived = po.status === 'Received';
                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-bold text-blue-600">#${po.po_number}</td>
                            <td class="px-6 py-4 text-gray-700">${po.supplier_name}</td>
                            <td class="px-6 py-4 text-gray-600">${po.po_date}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase ${isReceived ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}">
                                    ${po.status}
                                </span>
                            </td>
                            <td class="px-6 py-4 flex gap-3 justify-center">
                                <button onclick="viewDetail(${po.id})" class="text-blue-600 hover:text-blue-800 flex items-center gap-1 font-medium">
                                    <i data-lucide="eye" class="w-4 h-4"></i> Cek Item
                                </button>
                                ${!isReceived ? `
                                    <button onclick="markAsReceived(${po.id})" class="bg-green-600 text-white px-4 py-1.5 rounded-lg hover:bg-green-700 transition text-xs font-semibold">
                                        Terima Barang
                                    </button>
                                ` : '<span class="text-gray-400 italic text-xs">Selesai</span>'}
                            </td>
                        </tr>
                    `;
                });
                lucide.createIcons();
            } catch (error) {
                console.error("Gagal memuat PO:", error);
            }
        }

        // --- Lihat Detail Item ---
        async function viewDetail(poId) {
            try {
                const response = await fetch(`${API_URL}/${poId}`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const result = await response.json();
                const tbody = document.getElementById('detail-item-body');
                tbody.innerHTML = '';

                if (result.data) {
                    result.data.forEach(item => {
                        tbody.innerHTML += `
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-800">${item.product_name}</td>
                                <td class="px-4 py-3 text-center text-gray-700 font-semibold">${item.qty}</td>
                                <td class="px-4 py-3 text-right text-gray-600">Rp ${parseFloat(item.cost_per_unit).toLocaleString('id-ID')}</td>
                            </tr>
                        `;
                    });
                    document.getElementById('detailModal').classList.remove('hidden');
                    lucide.createIcons();
                }
            } catch (error) {
                alert("Gagal memuat detail item.");
            }
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        // --- Konfirmasi Penerimaan ---
        async function markAsReceived(id) {
            if(confirm('Apakah Anda sudah mengecek fisik barang dan ingin menambahkannya ke stok?')) {
                try {
                    const response = await fetch(`${API_URL}/receive/${id}`, {
                        method: 'PUT',
                        headers: { 'Authorization': `Bearer ${token}` }
                    });
                    const result = await response.json();
                    
                    if(response.ok) {
                        alert(result.message);
                        loadPOs();
                    } else {
                        alert(result.messages.error || "Gagal update status.");
                    }
                } catch (error) {
                    console.error("Error:", error);
                }
            }
        }

        loadPOs();
    </script>
</body>
</html>