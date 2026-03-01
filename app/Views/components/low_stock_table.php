<div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Produk Stok Rendah</h3>
    
    <?php if (empty($products)): ?>
        <p class="text-gray-500 text-center py-4">Tidak ada produk dengan stok rendah</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-200 text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        <th class="py-3 px-4">Nama Produk</th>
                        <th class="py-3 px-4">Sisa Stok</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($products as $product): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-4 text-sm text-gray-800 font-medium">
                            <?= esc($product['product_name']) ?>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-600">
                            <?= esc($product['current_stock']) ?>
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-red-100 text-red-800">
                                Habis
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>