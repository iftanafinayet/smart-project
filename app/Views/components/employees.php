<div id="employeeModal" class="hidden fixed inset-0 bg-gray-900/40 backdrop-blur-sm overflow-y-auto h-full w-full flex justify-center items-center z-[60] transition-all">
    <div class="bg-white p-10 rounded-[3rem] shadow-2xl w-full max-w-md border border-gray-100 animate-in zoom-in duration-300">
        <div class="flex items-center gap-4 mb-8">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <h3 class="text-2xl font-black text-gray-800 tracking-tight" id="modalTitle">Tambah Karyawan</h3>
        </div>
        
        <form id="employeeForm" class="space-y-5">
            <input type="hidden" id="employeeId">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nama Lengkap</label>
                    <input type="text" id="fullName" placeholder="Masukkan nama asli staf" class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all font-bold text-sm" required>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Username</label>
                    <input type="text" id="username" placeholder="Untuk kredensial login" class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all font-bold text-sm" required>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1 italic">Kata Sandi</label>
                    <input type="password" id="password" placeholder="Minimal 8 karakter" class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all font-bold text-sm">
                    <p id="passwordHelp" class="text-[9px] text-gray-400 mt-1 ml-1 hidden italic">* Kosongkan jika tidak ingin mengubah sandi.</p>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Jabatan / Role Akses</label>
                    <select id="roleId" class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all font-bold text-sm appearance-none cursor-pointer" required>
                        <option value="">Pilih Role Karyawan</option>
                        <option value="1">Admin (Full Access)</option>
                        <option value="2">Kasir (Sales Only)</option>
                        <option value="3">Gudang (Inventory Only)</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-10">
                <button type="button" onclick="closeModal()" class="px-6 py-4 rounded-2xl text-xs font-black uppercase tracking-widest text-gray-400 hover:bg-gray-50 transition-all active:scale-95">Batal</button>
                <button type="submit" class="bg-gray-900 text-white px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg active:scale-95">Simpan Karyawan</button>
            </div>
        </form>
    </div>
</div>