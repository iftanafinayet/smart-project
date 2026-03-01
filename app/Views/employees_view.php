<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Karyawan - Smart POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-[#f4f7f6]">
    
    <?= view('components/sidebar') ?>

    <main class="ml-64 p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Manajemen Karyawan</h2>
            <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-blue-700 transition">
                <i data-lucide="plus"></i> Tambah Karyawan
            </button>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-3">Nama Lengkap</th>
                            <th class="px-4 py-3">Username</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="employee-table-body" class="divide-y divide-gray-100">
                        </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="employeeModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex justify-center items-center z-50">
        <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
            <h3 class="text-xl font-semibold mb-6 text-gray-800" id="modalTitle">Tambah Karyawan</h3>
            <form id="employeeForm">
                <input type="hidden" id="employeeId">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" id="fullName" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" class="mt-1 block w-full border border-gray-300 rounded-md p-2" placeholder="********">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Role</label>
                    <select id="roleId" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                        <option value="">Pilih Role</option>
                        <option value="1">Admin</option>
                        <option value="2">Kasir</option>
                        <option value="3">Gudang</option>
                    </select>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closeModal()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();
        const API_URL = 'http://localhost:8080/api/v1/';
        const token = localStorage.getItem('jwt_token');

        // --- Fungsi Load Data ---
        async function loadEmployees() {
            try {
                const response = await fetch(`${API_URL}users`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                
                if (!response.ok) throw new Error('Gagal mengambil data');
                
                const result = await response.json();
                const tbody = document.getElementById('employee-table-body');
                tbody.innerHTML = '';
                
                result.data.forEach(e => {
                    // Mapping angka role_id ke teks untuk tampilan tabel
                    let roleText = '';
                    if(e.role_id == 1) roleText = 'Admin';
                    else if(e.role_id == 2) roleText = 'Kasir';
                    else if(e.role_id == 3) roleText = 'Gudang';

                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">${e.full_name}</td>
                            <td class="px-4 py-3 text-gray-600">${e.username}</td>
                            <td class="px-4 py-3">${roleText}</td>
                            <td class="px-4 py-3 flex gap-2">
                                <button onclick="editEmployee(${e.id}, '${e.username}', '${e.full_name}', ${e.role_id})" class="text-blue-600 hover:text-blue-800">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                <button onclick="deleteEmployee(${e.id})" class="text-red-600 hover:text-red-800">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                lucide.createIcons();
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('employee-table-body').innerHTML = `<tr><td colspan="4" class="text-center py-4 text-red-500">Gagal memuat data. Periksa rute API.</td></tr>`;
            }
        }

        // --- Fungsi Modal ---
        function openModal(mode = 'add') {
            document.getElementById('employeeModal').classList.remove('hidden');
            if(mode === 'add') {
                document.getElementById('modalTitle').innerText = 'Tambah Karyawan';
                document.getElementById('employeeForm').reset();
                document.getElementById('employeeId').value = '';
                document.getElementById('password').required = true;
            }
        }

        function closeModal() {
            document.getElementById('employeeModal').classList.add('hidden');
        }

        // --- Fungsi Edit ---
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

        // --- Fungsi Submit (Create/Update) ---
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

        // --- Fungsi Hapus ---
        async function deleteEmployee(id) {
            if (confirm('Yakin ingin menghapus karyawan ini?')) {
                await fetch(`${API_URL}users/${id}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                loadEmployees();
            }
        }

        // Load data saat halaman dibuka
        loadEmployees();
    </script>
</body>
</html>