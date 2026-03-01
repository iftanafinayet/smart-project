<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
    <div class="flex justify-between items-end mb-10">
        <div>
            <h2 class="text-4xl font-black text-gray-900 tracking-tighter uppercase">Manajemen <span class="text-blue-600">SDM</span></h2>
            <p class="text-gray-500 font-medium mt-1 italic text-center md:text-left">Kelola hak akses Admin, Kasir, dan Petugas Gudang organisasi.</p>
        </div>
        <button onclick="openModal()" class="group bg-blue-600 text-white px-8 py-4 rounded-2xl flex items-center gap-3 hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 active:scale-95">
            <i data-lucide="user-plus" class="w-5 h-5 group-hover:scale-110 transition-transform"></i> 
            <span class="text-xs font-black uppercase tracking-widest">Tambah Karyawan</span>
        </button>
    </div>
    
    <div class="bg-white rounded-[3rem] shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <div class="p-6 overflow-x-auto">
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
                    <tr><td colspan="4" class="text-center py-20 text-gray-300 italic animate-pulse font-bold uppercase tracking-widest text-xs">Sinkronisasi data karyawan...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <?= view('components/employees') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    /**
     * Mengambil data staf organisasi dari API
     */
    async function loadEmployees() {
        try {
            const response = await fetch(`${API_URL}users`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            
            if (!response.ok) throw new Error('Gagal menyinkronkan data');
            
            const result = await response.json();
            const tbody = document.getElementById('employee-table-body');
            
            if (!result.data || result.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center py-20 text-gray-400 italic font-bold uppercase tracking-widest text-xs">Belum ada karyawan terdaftar.</td></tr>';
                return;
            }

            tbody.innerHTML = result.data.map(e => {
                let roleText = '';
                let roleColor = '';
                // Menentukan label role berdasarkan role_id
                if(e.role_id == 1) { roleText = 'Admin'; roleColor = 'bg-purple-50 text-purple-600'; }
                else if(e.role_id == 2) { roleText = 'Kasir'; roleColor = 'bg-blue-50 text-blue-600'; }
                else if(e.role_id == 3) { roleText = 'Gudang'; roleColor = 'bg-orange-50 text-orange-600'; }

                return `
                    <tr class="bg-white hover:bg-gray-50/50 transition-all duration-300 group shadow-sm">
                        <td class="px-8 py-5 rounded-l-2xl border-y border-l border-gray-50 font-black text-gray-800 uppercase text-xs tracking-tight">
                            ${e.full_name}
                        </td>
                        <td class="px-8 py-5 border-y border-gray-50 text-sm font-medium text-gray-400 font-mono tracking-tighter italic">@${e.username}</td>
                        <td class="px-8 py-5 border-y border-gray-50">
                            <span class="${roleColor} px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest italic">
                                ${roleText}
                            </span>
                        </td>
                        <td class="px-8 py-5 rounded-r-2xl border-y border-r border-gray-50 text-right">
                            <div class="flex justify-end gap-2">
                                <button onclick="editEmployee(${e.id}, '${e.username}', '${e.full_name}', ${e.role_id})" class="p-2.5 bg-gray-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm active:scale-95">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </button>
                                <button onclick="deleteEmployee(${e.id})" class="p-2.5 bg-gray-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm active:scale-95">
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
            document.getElementById('employee-table-body').innerHTML = `<tr><td colspan="4" class="text-center py-20 text-red-500 font-black italic uppercase tracking-widest text-xs">Terjadi kegagalan sinkronisasi API.</td></tr>`;
        }
    }

    // Fungsi Pengaturan Modal
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

    function closeModal() { document.getElementById('employeeModal').classList.add('hidden'); }

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

    // Handler Pengiriman Form
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
            headers: { 
                'Authorization': `Bearer ${token}`, 
                'Content-Type': 'application/json' 
            },
            body: JSON.stringify(data)
        });

        closeModal();
        loadEmployees();
    });

    /**
     * Menghapus hak akses karyawan dari sistem
     */
    async function deleteEmployee(id) {
        if (confirm('Hapus akses karyawan ini dari sistem organisasi?')) {
            await fetch(`${API_URL}users/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}` }
            });
            loadEmployees();
        }
    }

    // Inisialisasi awal pemuatan data
    loadEmployees();
</script>
<?= $this->endSection() ?>