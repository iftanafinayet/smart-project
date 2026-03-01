<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Toko Enterprise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow-md w-96 border border-gray-200">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Selamat Datang</h2>
            <p class="text-gray-500 text-sm">Silakan masuk ke akun Anda</p>
        </div>
        
        <form id="loginForm">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" id="username" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition" 
                       placeholder="Masukkan username" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition" 
                       placeholder="••••••••" required>
            </div>
            <button type="submit" 
                    class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition shadow-sm">
                Login
            </button>
        </form>
    </div>

<script>
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        
        try {
            const response = await fetch('http://localhost:8080/api/v1/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password })
            });
            
            const result = await response.json();

            if (response.ok && result.status === 200) {
                // Ambil token dari root respon atau nested data
                const token = result.token || (result.data ? result.data.token : null);
                const roleIdFromRes = result.role_id || (result.data ? result.data.role_id : null);
                
                if (token) {
                    // Simpan data ke LocalStorage
                    localStorage.setItem('jwt_token', token);
                    localStorage.setItem('role_id', roleIdFromRes);
                    
                    alert('Login Berhasil!');

                    // REDIRECT BERDASARKAN ROLE
                    const roleId = parseInt(roleIdFromRes);
                    if (roleId === 1) {
                        window.location.href = '/dashboard'; // Admin langsung ke Dashboard
                    } else if (roleId === 2) {
                        window.location.href = '/sales'; // Kasir langsung ke POS (Halaman Jual)
                    } else {
                        window.location.href = '/warehouse/dashboard'; // Warehouse Role
                    }
                }
            } else {
                // Penanganan jika username/password salah
                alert('Login Gagal: ' + (result.message || 'Kredensial tidak valid'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal terhubung ke server. Pastikan API sudah berjalan.');
        }
    });
</script>
</body>
</html>