<?php

namespace App\Models;

use CodeIgniter\Model;

class JournalArticleTemplateModel extends Model
{
    protected $table = 'journal_article_templates';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'journal_id',
        'original_name',
        'file_path',
        'file_ext',
        'file_size',
    ];
}
