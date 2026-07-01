<?php

namespace App\Models;

use CodeIgniter\Model;

class LoaNotificationModel extends Model
{
    protected $table = 'loa_notifications';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'loa_letter_id',
        'status',
        'sent_at',
        'sent_to_email',
    ];
}
