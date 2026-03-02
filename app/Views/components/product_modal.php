<div id="productModal" class="hidden fixed inset-0 bg-gray-900/40 backdrop-blur-sm overflow-y-auto h-full w-full flex justify-center items-center z-[60]">
    <div class="bg-white p-10 rounded-[3.5rem] shadow-2xl w-full max-w-lg border border-gray-100 animate-in zoom-in duration-300">
        <div class="flex items-center gap-4 mb-8">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
                <i data-lucide="package-plus" class="w-6 h-6"></i>
            </div>
            <h3 class="text-2xl font-black text-gray-900 tracking-tighter uppercase italic" id="modalTitle">Tambah Produk</h3>
        </div>
        
        <form id="productForm" class="space-y-5">
            <input type="hidden" id="productId">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nama Produk</label>
                    <input type="text" id="productName" placeholder="Contoh: Jaket Himpunan CCIT" class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all font-bold text-sm" required>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Kode SKU / Barcode</label>
                    <input type="text" id="sku" placeholder="JHM-001" class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all font-mono font-black text-xs uppercase tracking-widest" required>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Satuan (Unit)</label>
                    <select id="unit" class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all font-bold text-sm appearance-none cursor-pointer">
                        <option value="Pcs">Pcs</option>
                        <option value="Pack">Pack</option>
                        <option value="Lusin">Lusin</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Harga Jual (Rp)</label>
                    <input type="number" id="sellingPrice" placeholder="0" class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all font-bold text-sm" required>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Stok Saat Ini</label>
                    <input type="number" id="currentStock" placeholder="0" class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all font-bold text-sm" required>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-10">
                <button type="button" onclick="closeModal()" class="px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest text-gray-400 hover:bg-gray-50 transition-all active:scale-95">Batal</button>
                <button type="submit" class="bg-gray-900 text-white px-10 py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-blue-100 active:scale-95">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>