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
}