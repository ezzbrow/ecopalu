<?php
/**
 * Partial: _partial_kalender.php
 *
 * Kalender bulanan untuk dashboard (User / Bank Sampah / Admin).
 * Highlight visual: hari operasional penjemputan (Rabu & Sabtu).
 *
 * Dipakai oleh 3 dashboard dengan cara:
 *   <?= $this->include('dashboard/_partial_kalender', ['tahun' => ..., 'bulan' => ...]) ?>
 *
 * Data opsional (default: bulan & tahun sekarang):
 *   - $tahun  int
 *   - $bulan  int
 */
$tahun = $tahun ?? (int) date('Y');
$bulan = $bulan ?? (int) date('n');

$kalender = getKalenderBulan($tahun, $bulan);
$namaBulan = namaBulanIndonesia($bulan);
?>

<div class="kalender-wrapper">
    <div class="kalender-header">
        <h5 class="mb-0">Kalender Penjemputan</h5>
        <span class="kalender-subtitle"><?= esc($namaBulan) ?> <?= esc((string) $tahun) ?></span>
    </div>

    <div class="kalender-legend">
        <span class="legend-item"><span class="legend-dot operasional"></span> Hari operasional (Rabu &amp; Sabtu)</span>
        <span class="legend-item"><span class="legend-dot today"></span> Hari ini</span>
    </div>

    <div class="kalender-grid">
        <?php
        $dayHeaders = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        foreach ($dayHeaders as $dh):
            $isOps = ($dh === 'Rab' || $dh === 'Sab');
        ?>
            <div class="kalender-cell kalender-cell-header <?= $isOps ? 'header-operasional' : '' ?>">
                <?= esc($dh) ?>
            </div>
        <?php endforeach; ?>

        <?php foreach ($kalender as $week): ?>
            <?php foreach ($week as $cell): ?>
                <?php
                    $classes = ['kalender-cell'];
                    if (! $cell['in_month']) $classes[] = 'out-of-month';
                    if ($cell['is_today'])   $classes[] = 'today';
                    if ($cell['is_operasional']) $classes[] = 'operasional';
                ?>
                <div class="<?= implode(' ', $classes) ?>"
                     title="<?= $cell['is_operasional'] ? 'Hari operasional penjemputan' : '' ?>">
                    <span class="kalender-day"><?= (int) $cell['day'] ?></span>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .kalender-wrapper {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }
    .kalender-header {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .kalender-header h5 {
        color: #0f172a;
        font-weight: 700;
    }
    .kalender-subtitle {
        color: #64748b;
        font-size: 0.95rem;
        font-weight: 500;
    }
    .kalender-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 16px;
        font-size: 0.78rem;
        color: #475569;
    }
    .legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .legend-dot {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 3px;
    }
    .legend-dot.operasional { background: #DCFCE7; border: 1px solid #22C55E; }
    .legend-dot.today { background: #FEF3C7; border: 1px solid #F59E0B; }

    .kalender-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 1px;
        background: #e2e8f0;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }
    .kalender-cell {
        background: white;
        min-height: 72px;
        padding: 8px;
        display: flex;
        align-items: flex-start;
        justify-content: flex-end;
        font-size: 0.88rem;
        color: #0f172a;
        position: relative;
    }
    .kalender-cell-header {
        background: #f8fafc;
        min-height: auto;
        padding: 8px;
        font-weight: 600;
        font-size: 0.78rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        align-items: center;
        justify-content: center;
    }
    .kalender-cell-header.header-operasional {
        background: #DCFCE7;
        color: #15803D;
    }
    .kalender-cell.out-of-month {
        background: #f8fafc;
        color: #cbd5e1;
    }
    .kalender-cell.operasional {
        background: #F0FDF4;
    }
    .kalender-cell.operasional .kalender-day {
        font-weight: 600;
        color: #15803D;
    }
    .kalender-cell.today {
        background: #FEF3C7;
        border: 2px solid #F59E0B;
    }
    .kalender-cell.today .kalender-day {
        font-weight: 700;
        color: #B45309;
    }
    .kalender-cell.operasional.today {
        background: linear-gradient(135deg, #FEF3C7 0%, #F0FDF4 100%);
    }
    .kalender-day {
        line-height: 1;
    }
    @media (max-width: 576px) {
        .kalender-cell { min-height: 56px; padding: 4px; font-size: 0.8rem; }
    }
</style>