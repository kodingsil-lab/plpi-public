<?php

namespace App\Models;

use CodeIgniter\Model;

class WhatsappTemplateModel extends Model
{
    protected $table = 'whatsapp_templates';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['name', 'code', 'type', 'subject', 'message', 'is_active'];
}
