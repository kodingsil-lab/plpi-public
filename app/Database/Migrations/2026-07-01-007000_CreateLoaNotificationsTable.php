<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLoaNotificationsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('loa_notifications')) {
            return;
        }

        $this->forge->addField([
            'id'            => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'loa_letter_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'status'        => ['type' => 'VARCHAR', 'constraint' => 60, 'default' => 'menunggu'],
            'sent_to_email' => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'sent_at'       => ['type' => 'DATETIME', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('loa_letter_id');
        $this->forge->addForeignKey('loa_letter_id', 'loa_letters', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('loa_notifications', true);
    }

    public function down()
    {
        $this->forge->dropTable('loa_notifications', true);
    }
}
