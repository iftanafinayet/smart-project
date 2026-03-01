<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Karyawan - Smart POS Enterprise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-[#f8fafc]">
    
    <?= view('components/sidebar') ?>

    <main class="ml-64 p-10 animate-in fade-in duration-700">
        <div class="flex justify-between items-end mb-10">
            <div>
                <h2 class="text-4xl font-black text-gray-900 tracking-tighter">Manajemen <span class="text-blue-600">SDM</span></h2>
                <p class="text-gray-500 font-medium mt-1">Kelola hak akses Admin, Kasir, dan Petugas Gudang organisasi.</p>
            </div>
            <button onclick="openModal()" class="group bg-blue-600 text-white px-8 py-4 rounded-2xl flex items-center gap-3 hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 active:scale-95">
                <i data-lucide="user-plus" class="w-5 h-5 group-hover:scale-110 transition-transform"></i> 
                <span class="text-xs font-black uppercase tracking-widest">Tambah Karyawan</span>
            </button>
        </div>
        
        <div class="bg-white rounded-[3rem] shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto p-6">
                <table class="w-full text-left border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-gray-400 uppercase text-[10px] font-black tracking-[0.2em]">
                            <th class="px-8 py-4">Nama Lengkap</th>
                            <th class="px-8 py-4">Username</th>
                            <th class="px-8 py-4">Hak Akses / Role</th>
                            <th class="px-8 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="employee-table-body">
                        <tr><td colspan="4" class="text-center py-20 text-gray-300 italic animate-pulse">Sinkronisasi data karyawan...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

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
                        <input type="text" id="fullName" placeholder="Masukkan nama asli" class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all font-bold text-sm" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Username</label>
                        <input type="text" id="username" placeholder="Untuk login sistem" class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all font-bold text-sm" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Kata Sandi</label>
                        <input type="password" id="password" placeholder="Minimal 8 karakter" class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all font-bold text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Jabatan / Role</label>
                        <select id="roleId" class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all font-bold text-sm appearance-none cursor-pointer" required>
                            <option value="">Pilih Role Akses</option>
                            <option value="1">Admin</option>
                            <option value="2">Kasir</option>
                            <option value="3">Gudang</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-10">
                    <button type="button" onclick="closeModal()" class="px-6 py-4 rounded-2xl text-xs font-black uppercase tracking-widest text-gray-400 hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="bg-gray-900 text-white px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg active:scale-95">Simpan Karyawan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Logika JavaScript tetap sama sesuai instruksi
        lucide.createIcons();
        const API_URL = 'http://localhost:8080/api/v1/';
        const token = localStorage.getItem('jwt_token');

        async function loadEmployees() {
            try {
                const response = await fetch(`${API_URL}users`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                
                if (!response.ok) throw new Error('Gagal mengambil data');
                
                const result = await response.json();
                const tbody = document.getElementById('employee-table-body');
                
                if (result.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center py-20 text-gray-400 italic font-medium">Belum ada karyawan terdaftar.</td></tr>';
                    return;
                }

                tbody.innerHTML = result.data.map(e => {
                    let roleText = '';
                    let roleColor = '';
                    if(e.role_id == 1) { roleText = 'Admin'; roleColor = 'bg-purple-50 text-purple-600'; }
                    else if(e.role_id == 2) { roleText = 'Kasir'; roleColor = 'bg-blue-50 text-blue-600'; }
                    else if(e.role_id == 3) { roleText = 'Gudang'; roleColor = 'bg-orange-50 text-orange-600'; }

                    return `
                        <tr class="bg-white hover:bg-gray-50/50 transition-all duration-300 group shadow-sm">
                            <td class="px-8 py-5 rounded-l-2xl border-y border-l border-gray-50">
                                <p class="font-black text-gray-800 uppercase text-xs tracking-tight">${e.full_name}</p>
                            </td>
                            <td class="px-8 py-5 border-y border-gray-50 text-sm font-medium text-gray-400 font-mono tracking-tighter italic">@${e.username}</td>
                            <td class="px-8 py-5 border-y border-gray-50">
                                <span class="${roleColor} px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest">
                                    ${roleText}
                                </span>
                            </td>
                            <td class="px-8 py-5 rounded-r-2xl border-y border-r border-gray-50 text-right">
                                <div class="flex justify-end gap-2">
                                    <button onclick="editEmployee(${e.id}, '${e.username}', '${e.full_name}', ${e.role_id})" class="p-2.5 bg-gray-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="deleteEmployee(${e.id})" class="p-2.5 bg-gray-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                        <i data-lucide="user-minus" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');
                lucide.createIcons();
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('employee-table-body').innerHTML = `<tr><td colspan="4" class="text-center py-20 text-red-500 font-bold italic">Terjadi kegagalan sinkronisasi API.</td></tr>`;
            }
        }

        function openModal(mode = 'add') {
            document.getElementById('employeeModal').classList.remove('hidden');
            if(mode === 'add') {
                document.getElementById('modalTitle').innerText = 'Tambah Karyawan';
                document.getElementById('employeeForm').reset();
                document.getElementById('employeeId').value = '';
                document.getElementById('password').required = true;
                document.getElementById('password').placeholder = 'Minimal 8 karakter';
            }
        }

        function closeModal() {
            document.getElementById('employeeModal').classList.add('hidden');
        }

        function editEmployee(id, username, fullName, roleId) {
            document.getElementById('modalTitle').innerText = 'Edit Karyawan';
            document.getElementById('employeeId').value = id;
            document.getElementById('username').value = username;
            document.getElementById('fullName').value = fullName;
            document.getElementById('roleId').value = roleId;
            document.getElementById('password').placeholder = 'Kosongkan jika tidak diubah';
            document.getElementById('password').required = false;
            openModal('edit');
        }

        document.getElementById('employeeForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('employeeId').value;
            const data = {
                username: document.getElementById('username').value,
                full_name: document.getElementById('fullName').value,
                password: document.getElementById('password').value,
                role_id: document.getElementById('roleId').value
            };

            const method = id ? 'PUT' : 'POST';
            const url = id ? `${API_URL}users/${id}` : `${API_URL}users`;

            await fetch(url, {
                method: method,
                headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            closeModal();
            loadEmployees();
        });

        async function deleteEmployee(id) {
            if (confirm('Hapus akses karyawan ini dari sistem organisasi?')) {
                await fetch(`${API_URL}users/${id}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                loadEmployees();
            }
        }

        loadEmployees();
    </script>
</body>
</html>