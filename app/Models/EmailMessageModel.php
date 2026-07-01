<?php

namespace App\Models;

use CodeIgniter\Model;

class EmailMessageModel extends Model
{
    protected $table = 'email_messages';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'recipient_name',
        'recipient_email',
        'subject',
        'message',
        'template_id',
        'sent_by',
        'status',
        'error_message',
    ];
}
