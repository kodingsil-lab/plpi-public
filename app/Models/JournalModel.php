<?php

namespace App\Models;

use CodeIgniter\Model;

class JournalModel extends Model
{
    protected $table = 'journals';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'publisher_id',
        'name',
        'code',
        'slug',
        'issn',
        'e_issn',
        'p_issn',
        'website_url',
        'commitment_statement_url',
        'recruitment_intro',
        'logo_path',
        'default_stamp_path',
        'default_signer_name',
        'default_signer_title',
        'default_signature_path',
        'pdf_sig_left_px',
        'pdf_sig_top_px',
        'pdf_sig_height_px',
        'pdf_sig_scale_percent',
    ];
}
