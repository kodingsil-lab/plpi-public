<?php

namespace App\Models;

use CodeIgniter\Model;

class PublisherModel extends Model
{
    protected $table = 'publishers';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['code', 'name', 'email', 'phone', 'address', 'logo_path'];
}
