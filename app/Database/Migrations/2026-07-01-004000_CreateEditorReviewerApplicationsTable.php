<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEditorReviewerApplicationsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('editor_reviewer_applications')) {
            return;
        }

        $this->forge->addField([
            'id'                   => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'journal_id'           => ['type' => 'BIGINT', 'unsigned' => true],
            'application_code'     => ['type' => 'VARCHAR', 'constraint' => 80],
            'full_name'            => ['type' => 'VARCHAR', 'constraint' => 191],
            'institution'          => ['type' => 'VARCHAR', 'constraint' => 191],
            'role_requested'       => ['type' => 'VARCHAR', 'constraint' => 20],
            'email'                => ['type' => 'VARCHAR', 'constraint' => 191],
            'phone'                => ['type' => 'VARCHAR', 'constraint' => 50],
            'google_scholar_id'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'sinta_id'             => ['type' => 'VARCHAR', 'constraint' => 100],
            'scopus_id'            => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'orcid_id'             => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'expertise'            => ['type' => 'TEXT'],
            'status'               => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'baru'],
            'notification_sent_at' => ['type' => 'DATETIME', 'null' => true],
            'notification_error'   => ['type' => 'TEXT', 'null' => true],
            'created_at'           => ['type' => 'DATETIME', 'null' => true],
            'updated_at'           => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('application_code');
        $this->forge->addKey('journal_id');
        $this->forge->addKey('role_requested');
        $this->forge->addForeignKey('journal_id', 'journals', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('editor_reviewer_applications', true);
    }

    public function down()
    {
        $this->forge->dropTable('editor_reviewer_applications', true);
    }
}
