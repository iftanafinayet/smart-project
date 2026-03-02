<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Warehouse Core' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; overflow-x: hidden; }
        /* Hilangkan paksa margin kiri yang biasanya disisakan untuk sidebar */
        main { margin-left: 0 !important; width: 100% !important; }
    </style>
</head>
<body class="bg-[#f8fafc] min-h-screen">
    
    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-100 px-8 py-4 flex justify-between items-center sticky top-0 z-50 shadow-sm no-print">
        <div class="flex items-center gap-3">
            <div class="bg-blue-600 p-2.5 rounded-2xl shadow-lg shadow-blue-100">
                <i data-lucide="layout-dashboard" class="w-5 h-5 text-white"></i>
            </div>
            <div>
                <h1 class="text-lg font-black text-gray-900 tracking-tight leading-none uppercase italic">Warehouse <span class="text-blue-600">Core</span></h1>
            </div>
        </div>
        <div class="flex gap-4">
             <a href="<?= base_url('dashboard') ?>" class="text-[10px] font-black uppercase text-gray-400 hover:text-blue-600 transition-all tracking-widest">Ke Admin Panel</a>
             <button onclick="logout()" class="text-[10px] font-black uppercase text-red-400 hover:text-red-600 transition-all tracking-widest">Keluar</button>
        </div>
    </nav>

    <main class="p-8 md:p-12 animate-in fade-in duration-700">
        <div class="max-w-[1440px] mx-auto">
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <script>
        lucide.createIcons();
        const API_URL = 'http://localhost:8080/api/v1/';
        const token = localStorage.getItem('jwt_token');

        if (!token) window.location.href = '/login';

        function logout() {
            if (confirm('Yakin ingin keluar?')) {
                localStorage.removeItem('jwt_token');
                window.location.href = '/login';
            }
        }
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>