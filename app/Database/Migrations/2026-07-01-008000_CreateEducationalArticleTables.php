<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEducationalArticleTables extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('article_categories')) {
            $this->forge->addField([
                'id'          => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'name'        => ['type' => 'VARCHAR', 'constraint' => 191],
                'slug'        => ['type' => 'VARCHAR', 'constraint' => 191],
                'description' => ['type' => 'TEXT', 'null' => true],
                'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'sort_order'  => ['type' => 'INT', 'default' => 0],
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
                'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('slug');
            $this->forge->createTable('article_categories', true);

            $now = date('Y-m-d H:i:s');
            $this->db->table('article_categories')->insertBatch([
                [
                    'name'        => 'Penulisan Ilmiah',
                    'slug'        => 'penulisan-ilmiah',
                    'description' => 'Panduan penulisan naskah ilmiah.',
                    'is_active'   => 1,
                    'sort_order'  => 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ],
                [
                    'name'        => 'Publikasi',
                    'slug'        => 'publikasi',
                    'description' => 'Informasi proses dan strategi publikasi.',
                    'is_active'   => 1,
                    'sort_order'  => 2,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ],
            ]);
        }

        if (! $this->db->tableExists('educational_articles')) {
            $this->forge->addField([
                'id'           => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'category_id'  => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
                'title'        => ['type' => 'VARCHAR', 'constraint' => 255],
                'slug'         => ['type' => 'VARCHAR', 'constraint' => 255],
                'summary'      => ['type' => 'TEXT', 'null' => true],
                'content'      => ['type' => 'LONGTEXT', 'null' => true],
                'cover_path'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'image_alt'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'status'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'draft'],
                'published_at' => ['type' => 'DATETIME', 'null' => true],
                'sort_order'   => ['type' => 'INT', 'default' => 0],
                'created_at'   => ['type' => 'DATETIME', 'null' => true],
                'updated_at'   => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('slug');
            $this->forge->addForeignKey('category_id', 'article_categories', 'id', 'SET NULL', 'CASCADE');
            $this->forge->createTable('educational_articles', true);
        }
    }

    public function down()
    {
        $this->forge->dropTable('educational_articles', true);
        $this->forge->dropTable('article_categories', true);
    }
}
