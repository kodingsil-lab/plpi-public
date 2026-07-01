<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticleCategoryModel extends Model
{
    protected $table = 'article_categories';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['name', 'slug', 'description', 'is_active', 'sort_order'];
}
