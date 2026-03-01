<div class="w-64 bg-white h-screen fixed left-0 top-0 shadow-sm border-r border-gray-100 z-50">
    <div class="p-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="bg-blue-600 p-2 rounded-xl shadow-lg shadow-blue-100">
                <i data-lucide="layout-grid" class="w-5 h-5 text-white"></i>
            </div>
            <h1 class="text-xl font-black text-gray-900 tracking-tighter">Smart <span class="text-blue-600">POS</span></h1>
        </div>
        <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] ml-1">Admin Control</p>
    </div>
    
    <nav class="mt-4 px-4">
        <?php
        $uri = service('uri');
        // Menggunakan getSegment(1) jika URL Anda adalah localhost:8080/dashboard
        $activeSegment = $uri->getSegment(1); 
        
        $menuItems = [
            ['name' => 'Dashboard', 'icon' => 'home', 'url' => base_url('dashboard'), 'slug' => 'dashboard'],
            ['name' => 'Produk', 'icon' => 'package', 'url' => base_url('products'), 'slug' => 'products'],
            ['name' => 'Karyawan', 'icon' => 'users', 'url' => base_url('employees'), 'slug' => 'employees'],
            ['name' => 'Laporan', 'icon' => 'file-bar-chart', 'url' => base_url('reports'), 'slug' => 'reports'],
        ];
        ?>

        <div class="space-y-2">
            <?php foreach ($menuItems as $item): ?>
                <?php 
                    $isActive = ($activeSegment == $item['slug']); 
                    // Tampilan item aktif dengan sudut melengkung dan shadow tipis agar serupa dengan Dashboard Gudang
                    $activeClass = $isActive 
                        ? 'text-blue-600 bg-blue-50/50 shadow-sm ring-1 ring-blue-100' 
                        : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200';
                ?>
                <a href="<?= $item['url'] ?>" class="flex items-center px-5 py-3.5 rounded-2xl group <?= $activeClass ?>">
                    <div class="<?= $isActive ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' ?> transition-colors">
                        <i data-lucide="<?= $item['icon'] ?>" class="w-5 h-5 mr-4"></i>
                    </div>
                    <span class="text-xs font-black uppercase tracking-widest"><?= $item['name'] ?></span>
                    
                    <?php if ($isActive): ?>
                        <div class="ml-auto w-1.5 h-1.5 bg-blue-600 rounded-full shadow-lg shadow-blue-400"></div>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="mt-10 pt-6 border-t border-gray-50 px-2">
            <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.3em] mb-4 ml-3 text-center">Warehouse Access</p>
            <a href="<?= base_url('warehouse/dashboard') ?>" class="flex items-center px-5 py-4 rounded-[1.5rem] bg-gray-900 text-white hover:bg-blue-600 transition-all shadow-xl shadow-gray-200 group">
                <i data-lucide="box" class="w-5 h-5 mr-4 text-blue-400 group-hover:text-white"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Panel Gudang</span>
            </a>
        </div>
    </nav>
</div>

<script>
    // Memastikan ikon Lucide ter-render dengan benar setelah DOM dimuat
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>