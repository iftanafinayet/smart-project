<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Smart POS Enterprise' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#f8fafc] min-h-screen flex">
    
    <?= view('components/sidebar') ?>

    <main class="flex-1 p-10 ml-64 animate-in fade-in duration-700">
        <div class="max-w-7xl mx-auto">
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <script>
        // Inisialisasi ikon secara global
        lucide.createIcons();
        const API_URL = 'http://localhost:8080/api/v1/';
        const token = localStorage.getItem('jwt_token');

        // Proteksi sederhana jika token hilang
        if (!token && !window.location.pathname.includes('/login')) {
            window.location.href = '/login';
        }
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>