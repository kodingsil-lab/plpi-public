<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJournalArticleTemplatesTable extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('journal_article_templates')) {
            $this->forge->addField([
                'id'            => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'journal_id'    => ['type' => 'BIGINT', 'unsigned' => true],
                'original_name' => ['type' => 'VARCHAR', 'constraint' => 255],
                'file_path'     => ['type' => 'VARCHAR', 'constraint' => 255],
                'file_ext'      => ['type' => 'VARCHAR', 'constraint' => 12],
                'file_size'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
                'created_at'    => ['type' => 'DATETIME', 'null' => true],
                'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('journal_id');
            $this->forge->addForeignKey('journal_id', 'journals', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('journal_article_templates', true);
        }

        if ($this->db->tableExists('journals') && ! $this->db->fieldExists('article_template_slug', 'journals')) {
            $this->forge->addColumn('journals', [
                'article_template_slug' => ['type' => 'VARCHAR', 'constraint' => 140, 'null' => true, 'after' => 'slug'],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('journals') && $this->db->fieldExists('article_template_slug', 'journals')) {
            $this->forge->dropColumn('journals', 'article_template_slug');
        }

        $this->forge->dropTable('journal_article_templates', true);
    }
}
