<?php

namespace App\Models;

use CodeIgniter\Model;

class LoaRequestModel extends Model
{
    protected $table = 'loa_requests';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'journal_id',
        'request_code',
        'article_url',
        'article_id_external',
        'title',
        'authors_json',
        'corresponding_email',
        'whatsapp_number',
        'affiliations_json',
        'volume',
        'issue_number',
        'published_year',
        'status',
        'notes_admin',
        'rejection_reason',
        'approved_at',
    ];
}
