<?php

namespace App\Models;

use CodeIgniter\Model;

class AppSettingModel extends Model
{
    protected $table = 'app_settings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'header_logo_path',
        'login_logo_path',
        'public_logo_path',
        'favicon_path',
        'app_timezone',
        'smtp_host',
        'smtp_port',
        'smtp_user',
        'smtp_pass',
        'smtp_crypto',
        'mail_from_email',
        'mail_from_name',
    ];
}
