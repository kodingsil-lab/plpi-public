<?php

namespace App\Models;

use CodeIgniter\Model;

class EditorReviewerApplicationModel extends Model
{
    protected $table = 'editor_reviewer_applications';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'journal_id',
        'application_code',
        'full_name',
        'institution',
        'role_requested',
        'email',
        'phone',
        'google_scholar_id',
        'sinta_id',
        'scopus_id',
        'orcid_id',
        'expertise',
        'status',
        'notification_sent_at',
        'notification_error',
    ];
}
