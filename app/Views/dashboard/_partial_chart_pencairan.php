<?php
/**
 * Partial: _partial_chart_pencairan.php
 *
 * Grafik "Reward Dicairkan" 7 hari terakhir untuk Dashboard Admin.
 * - Chart.js dimuat dari public/assets/js/chart.min.js (LOKAL, bukan CDN)
 *   agar demo di lokasi tanpa internet stabil tetap jalan.
 * - Empty state (Opsi 2): saat tabel pencairan_reward masih kosong,
 *   render icon + pesan, BUKAN grafik garis di angka 0.
 *
 * Data:
 *   - $pencairan_7hari        array  7 elemen (tanggal, label, total_coin)
 *   - $pencairan_total_record  int    0 = empty state, >0 = render chart
 */
$pencairan_7hari       = $pencairan_7hari ?? [];
$pencairan_total_record = (int) ($pencairan_total_record ?? 0);

// Encode data untuk JS (Chart.js butuh JSON)
$chartLabels = array_column($pencairan_7hari, 'label');
$chartData   = array_column($pencairan_7hari, 'total_coin');
$chartLabelsJson = json_encode($chartLabels, JSON_UNESCAPED_UNICODE);
$chartDataJson   = json_encode($chartData);
?>

<div class="chart-card">
    <div class="chart-header">
        <h5 class="mb-0">Reward Dicairkan</h5>
        <small class="text-muted">7 hari terakhir &middot; total nominal coin</small>
    </div>

    <?php if ($pencairan_total_record === 0): ?>
        <!-- ============== EMPTY STATE (Opsi 2) ============== -->
        <div class="chart-empty-state">
            <i class="bi bi-bar-chart"></i>
            <p class="mb-0">Belum ada transaksi pencairan</p>
            <small>Data akan muncul setelah user mengajukan pencairan dan Admin menandai "berhasil".</small>
        </div>
    <?php else: ?>
        <!-- ============== CHART ============== -->
        <div class="chart-container">
            <canvas id="pencairanChart"></canvas>
        </div>
    <?php endif; ?>
</div>

<style>
    .chart-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
    }
    .chart-header {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 4px;
    }
    .chart-header h5 {
        color: #0f172a;
        font-weight: 700;
    }
    .chart-empty-state {
        text-align: center;
        padding: 48px 16px;
        color: #94a3b8;
    }
    .chart-empty-state i {
        font-size: 3.5rem;
        color: #cbd5e1;
        display: block;
        margin-bottom: 12px;
    }
    .chart-empty-state p {
        font-size: 1rem;
        font-weight: 500;
        color: #64748b;
    }
    .chart-empty-state small {
        display: block;
        margin-top: 4px;
    }
    .chart-container {
        position: relative;
        height: 280px;
    }
</style>

<?php if ($pencairan_total_record > 0): ?>
<script src="<?= base_url('assets/js/chart.min.js') ?>"></script>
<script>
(function () {
    const canvas = document.getElementById('pencairanChart');
    if (! canvas) return;
    if (typeof Chart === 'undefined') {
        console.error('Chart.js belum dimuat dari public/assets/js/chart.min.js');
        return;
    }

    const labels = <?= $chartLabelsJson ?>;
    const data   = <?= $chartDataJson ?>;

    new Chart(canvas, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nominal Coin',
                data: data,
                borderColor: '#22C55E',
                backgroundColor: 'rgba(34, 197, 94, 0.12)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#22C55E',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            return ' ' + ctx.parsed.y.toLocaleString('id-ID') + ' coin';
                        },
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (val) {
                            return val.toLocaleString('id-ID');
                        },
                    },
                },
            },
        },
    });
})();
</script>
<?php endif; ?>