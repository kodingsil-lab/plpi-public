<?php

namespace App\Models;

use CodeIgniter\Model;

class LoaLetterModel extends Model
{
    protected $table = 'loa_letters';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'journal_id',
        'loa_request_id',
        'loa_number',
        'article_url',
        'article_id_external',
        'title',
        'authors_json',
        'corresponding_email',
        'affiliations_json',
        'volume',
        'issue_number',
        'published_year',
        'status',
        'verification_hash',
        'public_token',
        'pdf_path',
        'published_at',
        'revoked_at',
        'revoked_reason',
    ];
}
