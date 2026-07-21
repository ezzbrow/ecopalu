<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table         = 'notifications';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = false; // created_at diisi manual via helper

    protected $allowedFields = [
        'recipient_user_id',
        'recipient_role',
        'judul',
        'pesan',
        'tipe',
        'ref_id',
        'is_read',
        'read_at',
        'created_at',
    ];

    /**
     * Ambil notifikasi untuk user tertentu (limit default 10, diurut unread dulu).
     */
    public function getForUser(int $userId, int $limit = 10): array
    {
        return $this
            ->where('recipient_user_id', $userId)
            ->orderBy('is_read', 'ASC')   // unread (0) lebih dulu
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Tandai notifikasi sebagai sudah dibaca (validasi ownership di controller).
     */
    public function markRead(int $id, int $userIdExpected): bool
    {
        $row = $this->find($id);
        if (! $row) {
            return false;
        }
        if ((int) $row['recipient_user_id'] !== $userIdExpected) {
            return false;
        }
        if ((int) $row['is_read'] === 1) {
            return true; // idempotent
        }
        return (bool) $this->update($id, [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s'),
        ]);
    }
}