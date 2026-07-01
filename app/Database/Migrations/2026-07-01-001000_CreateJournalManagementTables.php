<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJournalManagementTables extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('publishers')) {
            $this->forge->addField([
                'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'code'       => ['type' => 'VARCHAR', 'constraint' => 50],
                'name'       => ['type' => 'VARCHAR', 'constraint' => 255],
                'email'      => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
                'phone'      => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'address'    => ['type' => 'TEXT', 'null' => true],
                'logo_path'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('code');
            $this->forge->createTable('publishers', true);

            $now = date('Y-m-d H:i:s');
            $this->db->table('publishers')->insert([
                'code'       => 'PLPI',
                'name'       => 'Pusat Layanan Publikasi Ilmiah',
                'email'      => 'plpi@unisapalu.ac.id',
                'phone'      => null,
                'address'    => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (! $this->db->tableExists('journals')) {
            $this->forge->addField([
                'id'                     => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'publisher_id'           => ['type' => 'BIGINT', 'unsigned' => true],
                'name'                   => ['type' => 'VARCHAR', 'constraint' => 255],
                'code'                   => ['type' => 'VARCHAR', 'constraint' => 80],
                'slug'                   => ['type' => 'VARCHAR', 'constraint' => 120],
                'e_issn'                 => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'p_issn'                 => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'website_url'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'logo_path'              => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'default_signer_name'    => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
                'default_signer_title'   => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
                'default_signature_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'pdf_sig_left_px'        => ['type' => 'INT', 'null' => true],
                'pdf_sig_top_px'         => ['type' => 'INT', 'null' => true],
                'pdf_sig_height_px'      => ['type' => 'INT', 'null' => true],
                'pdf_sig_scale_percent'  => ['type' => 'INT', 'null' => true],
                'created_at'             => ['type' => 'DATETIME', 'null' => true],
                'updated_at'             => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('code');
            $this->forge->addUniqueKey('slug');
            $this->forge->addForeignKey('publisher_id', 'publishers', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('journals', true);
        }
    }

    public function down()
    {
        $this->forge->dropTable('journals', true);
        $this->forge->dropTable('publishers', true);
    }
}
