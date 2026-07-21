<?php
/**
 * Partial: _partial_notifikasi.php
 *
 * Dropdown notifikasi untuk topbar dashboard. Dipakai oleh layouts/dashboard.php
 * via $this->include('dashboard/_partial_notifikasi', ['notifList' => $notifList]).
 *
 * Behavior read/unread per klik (sesuai spec CLAUDE.md):
 *   - Badge merah di topbar = jumlah unread
 *   - Dropdown list = 10 notifikasi terbaru (unread di atas, sorted by created_at DESC)
 *   - Klik notifikasi → GET /notification/mark/$id → redirect back → unread count turun
 *   - Tombol "Tandai semua dibaca" → GET /notification/mark-all
 *   - TIDAK auto-mark-all saat dropdown dibuka (sesuai spec)
 *
 * Data yang dibutuhkan:
 *   - $notifList : array of notification rows (dari NotificationModel::getForUser($userId))
 */
$notifList = $notifList ?? [];
$userId    = (int) (session('user_id') ?? 0);

// Helper kecil: format relative time
$timeAgo = static function (string $dt): string {
    if (! $dt) return '';
    $ts   = strtotime($dt);
    $diff = time() - $ts;
    if ($diff < 60)        return $diff . ' detik lalu';
    if ($diff < 3600)      return floor($diff / 60) . ' menit lalu';
    if ($diff < 86400)     return floor($diff / 3600) . ' jam lalu';
    if ($diff < 604800)    return floor($diff / 86400) . ' hari lalu';
    return date('d M Y', $ts);
};
?>

<div class="notif-dropdown" id="notifDropdown" style="display:none;">
    <div class="notif-header">
        <strong>Notifikasi</strong>
        <?php if (! empty($notifList)): ?>
            <form action="<?= base_url('notification/mark-all') ?>" method="post" class="m-0">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none">Tandai semua dibaca</button>
            </form>
        <?php endif; ?>
    </div>

    <div class="notif-list">
        <?php if (empty($notifList)): ?>
            <div class="notif-empty">Belum ada notifikasi.</div>
        <?php else: ?>
            <?php foreach ($notifList as $n): ?>
                <?php
                    $isUnread = (int) $n['is_read'] === 0;
                    $action   = base_url('notification/mark/' . (int) $n['id']);
                ?>
                <form action="<?= $action ?>" method="post" class="notif-form">
                    <?= csrf_field() ?>
                    <button type="submit" class="notif-item-btn <?= $isUnread ? 'unread' : '' ?>">
                        <div class="notif-title">
                            <?php if ($isUnread): ?>
                                <span class="notif-dot"></span>
                            <?php endif; ?>
                            <?= esc($n['judul']) ?>
                        </div>
                        <div class="notif-msg"><?= esc($n['pesan']) ?></div>
                        <div class="notif-time"><?= esc($timeAgo($n['created_at'])) ?></div>
                    </button>
                </form>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
    .notif-dropdown {
        position: absolute;
        right: 0;
        top: calc(100% + 8px);
        width: 360px;
        max-height: 480px;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
        z-index: 1100;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .notif-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .notif-list {
        overflow-y: auto;
        max-height: 420px;
    }
    .notif-empty {
        padding: 24px 16px;
        text-align: center;
        color: #94a3b8;
        font-size: 0.9rem;
    }
    .notif-form {
        margin: 0;
    }
    .notif-item-btn {
        display: block;
        width: 100%;
        text-align: left;
        padding: 12px 16px;
        border: none;
        border-bottom: 1px solid #f1f5f9;
        background: white;
        color: #0f172a;
        cursor: pointer;
        transition: background 0.15s;
        font-family: inherit;
    }
    .notif-item-btn:hover {
        background: #f8fafc;
    }
    .notif-item-btn.unread {
        background: #f0fdf4;
    }
    .notif-item-btn.unread:hover {
        background: #dcfce7;
    }
    .notif-title {
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #0f172a;
    }
    .notif-dot {
        width: 8px;
        height: 8px;
        background: #ef4444;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .notif-msg {
        font-size: 0.82rem;
        color: #475569;
        margin-top: 2px;
        line-height: 1.35;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .notif-time {
        font-size: 0.72rem;
        color: #94a3b8;
        margin-top: 4px;
    }
</style>

<script>
(function () {
    const btn  = document.getElementById('notifBtn');
    const drop = document.getElementById('notifDropdown');
    if (! btn || ! drop) return;

    // Toggle dropdown (TIDAK auto-mark-read saat dibuka — sesuai spec)
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        drop.style.display = drop.style.display === 'none' ? 'block' : 'none';
    });

    // Klik di luar → tutup
    document.addEventListener('click', function () {
        drop.style.display = 'none';
    });
    drop.addEventListener('click', function (e) {
        e.stopPropagation();
    });
})();
</script>