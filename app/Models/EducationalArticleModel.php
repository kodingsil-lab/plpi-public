<?php

namespace App\Models;

use CodeIgniter\Model;

class EducationalArticleModel extends Model
{
    protected $table = 'educational_articles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'category_id',
        'title',
        'slug',
        'summary',
        'content',
        'cover_path',
        'image_alt',
        'status',
        'published_at',
        'sort_order',
    ];
}
