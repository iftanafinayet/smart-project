<div class="w-64 bg-white h-screen fixed left-0 top-0 shadow-sm border-r border-gray-100">
    <div class="p-6">
        <h1 class="text-xl font-semibold text-gray-800">Dashboard Toko</h1>
    </div>
    
    <nav class="mt-6">
        <?php
        // Helper sederhana untuk mendeteksi menu aktif berdasarkan URL
        $uri = service('uri');
        $activeSegment = $uri->getSegment(2); // Menyesuaikan dengan /api/v1/dashboard atau /dashboard
        
        $menuItems = [
            ['name' => 'Dashboard', 'icon' => 'layout-dashboard', 'url' => base_url('dashboard'), 'slug' => 'dashboard'],
            ['name' => 'Produk', 'icon' => 'package', 'url' => base_url('products'), 'slug' => 'products'],
            ['name' => 'Karyawan', 'icon' => 'users', 'url' => base_url('employees'), 'slug' => 'employees'],
            ['name' => 'Laporan', 'icon' => 'bar-chart-3', 'url' => base_url('reports'), 'slug' => 'reports'],
        ];
        ?>

        <?php foreach ($menuItems as $item): ?>
            <?php 
                $isActive = ($activeSegment == $item['slug']); 
                $activeClass = $isActive 
                    ? 'text-gray-700 bg-blue-50 border-r-4 border-blue-600' 
                    : 'text-gray-600 hover:bg-gray-50 transition-colors';
            ?>
            <a href="<?= $item['url'] ?>" class="flex items-center px-6 py-3 <?= $activeClass ?>">
                <i data-lucide="<?= $item['icon'] ?>" class="w-5 h-5 mr-3"></i>
                <span class="text-sm font-medium"><?= $item['name'] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
</div>

<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>