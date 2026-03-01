<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Smart POS Enterprise' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Custom Scrollbar untuk area konten */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body class="min-h-screen bg-[#f8fafc] flex">
    
    <?= view('components/sidebar') ?>

    <main class="flex-1 ml-64 p-10 animate-in fade-in duration-700 min-h-screen custom-scrollbar">
        <div class="max-w-7xl mx-auto">
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <script>
        // Inisialisasi Icon Lucide secara global
        lucide.createIcons();

        // Variabel Lingkungan API (MERN/PHP Backend Sinkron)
        const API_URL = 'http://localhost:8080/api/v1/';
        const token = localStorage.getItem('jwt_token');

        // Proteksi Halaman: Redirect jika tidak ada token
        if (!token && !window.location.pathname.includes('/login')) {
            window.location.href = '/login';
        }

        /**
         * Fungsi Logout Global
         */
        function logout() {
            if (confirm('Apakah Anda yakin ingin keluar dari sistem organisasi?')) {
                localStorage.removeItem('jwt_token');
                window.location.href = '/login';
            }
        }
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>