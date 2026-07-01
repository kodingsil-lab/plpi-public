<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLoaManagementTables extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('loa_requests')) {
            $this->forge->addField([
                'id'                  => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'journal_id'          => ['type' => 'BIGINT', 'unsigned' => true],
                'request_code'        => ['type' => 'VARCHAR', 'constraint' => 80],
                'article_url'         => ['type' => 'VARCHAR', 'constraint' => 255],
                'article_id_external' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'title'               => ['type' => 'TEXT'],
                'authors_json'        => ['type' => 'LONGTEXT'],
                'corresponding_email' => ['type' => 'VARCHAR', 'constraint' => 191],
                'whatsapp_number'     => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'affiliations_json'   => ['type' => 'LONGTEXT', 'null' => true],
                'volume'              => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'issue_number'        => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'published_year'      => ['type' => 'VARCHAR', 'constraint' => 4, 'null' => true],
                'status'              => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
                'notes_admin'         => ['type' => 'TEXT', 'null' => true],
                'rejection_reason'    => ['type' => 'TEXT', 'null' => true],
                'approved_at'         => ['type' => 'DATETIME', 'null' => true],
                'created_at'          => ['type' => 'DATETIME', 'null' => true],
                'updated_at'          => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('request_code');
            $this->forge->addForeignKey('journal_id', 'journals', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('loa_requests', true);
        }

        if (! $this->db->tableExists('loa_letters')) {
            $this->forge->addField([
                'id'                  => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'journal_id'          => ['type' => 'BIGINT', 'unsigned' => true],
                'loa_request_id'      => ['type' => 'BIGINT', 'unsigned' => true],
                'loa_number'          => ['type' => 'VARCHAR', 'constraint' => 120],
                'article_url'         => ['type' => 'VARCHAR', 'constraint' => 255],
                'article_id_external' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'title'               => ['type' => 'TEXT'],
                'authors_json'        => ['type' => 'LONGTEXT'],
                'corresponding_email' => ['type' => 'VARCHAR', 'constraint' => 191],
                'affiliations_json'   => ['type' => 'LONGTEXT', 'null' => true],
                'volume'              => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'issue_number'        => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'published_year'      => ['type' => 'VARCHAR', 'constraint' => 4, 'null' => true],
                'status'              => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'published'],
                'verification_hash'   => ['type' => 'VARCHAR', 'constraint' => 191],
                'public_token'        => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
                'pdf_path'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'published_at'        => ['type' => 'DATETIME', 'null' => true],
                'revoked_at'          => ['type' => 'DATETIME', 'null' => true],
                'revoked_reason'      => ['type' => 'TEXT', 'null' => true],
                'created_at'          => ['type' => 'DATETIME', 'null' => true],
                'updated_at'          => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('loa_number');
            $this->forge->addUniqueKey('verification_hash');
            $this->forge->addUniqueKey('public_token');
            $this->forge->addForeignKey('journal_id', 'journals', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('loa_request_id', 'loa_requests', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('loa_letters', true);
        }
    }

    public function down()
    {
        $this->forge->dropTable('loa_letters', true);
        $this->forge->dropTable('loa_requests', true);
    }
}
