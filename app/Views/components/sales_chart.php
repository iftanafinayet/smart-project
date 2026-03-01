<div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Penjualan Mingguan</h3>
    
    <?php if (empty($chartData)): ?>
        <div class="h-[300px] flex items-center justify-center text-gray-500">
            <p>Tidak ada data chart tersedia</p>
        </div>
    <?php else: ?>
        <div class="h-[300px]">
            <canvas id="weeklySalesChart"></canvas>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rawData = <?= json_encode($chartData) ?>;

    // 1. Format Tanggal (Mirip formatDateLabel di React)
    const formatDateLabel = (dateString) => {
        const date = new Date(dateString);
        const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        return `${days[date.getDay()]} ${date.getDate()}/${date.getMonth() + 1}`;
    };

    // 2. Format Rupiah (Mirip formatRupiah di React)
    const formatRupiah = (amount) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    };

    const labels = rawData.map(item => formatDateLabel(item.date));
    const amounts = rawData.map(item => item.total); // Sesuaikan key 'total' dari DashboardService

    const ctx = document.getElementById('weeklySalesChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Penjualan',
                data: amounts,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: 'rgb(59, 130, 246)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Penjualan: ' + formatRupiah(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>