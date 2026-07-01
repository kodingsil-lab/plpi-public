<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFullJournalIdentityFields extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('journals')) {
            $fields = [];

            if (! $this->db->fieldExists('issn', 'journals')) {
                $fields['issn'] = ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'slug'];
            }
            if (! $this->db->fieldExists('commitment_statement_url', 'journals')) {
                $fields['commitment_statement_url'] = ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'website_url'];
            }
            if (! $this->db->fieldExists('recruitment_intro', 'journals')) {
                $fields['recruitment_intro'] = ['type' => 'TEXT', 'null' => true, 'after' => 'commitment_statement_url'];
            }
            if (! $this->db->fieldExists('default_stamp_path', 'journals')) {
                $fields['default_stamp_path'] = ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'recruitment_intro'];
            }

            if ($fields !== []) {
                $this->forge->addColumn('journals', $fields);
            }
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('journals')) {
            return;
        }

        foreach (['default_stamp_path', 'recruitment_intro', 'commitment_statement_url', 'issn'] as $field) {
            if ($this->db->fieldExists($field, 'journals')) {
                $this->forge->dropColumn('journals', $field);
            }
        }
    }
}
