<?php

namespace App\Models;

use CodeIgniter\Model;

class WhatsappMessageModel extends Model
{
    protected $table = 'whatsapp_messages';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['recipient_name', 'phone_number', 'message', 'template_id', 'wa_url', 'sent_by'];
}
