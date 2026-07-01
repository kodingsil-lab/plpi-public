<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ExtendMessageManagementTables extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('whatsapp_templates')) {
            $fields = $this->db->getFieldNames('whatsapp_templates');

            if (! in_array('type', $fields, true)) {
                $this->forge->addColumn('whatsapp_templates', [
                    'type' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 20,
                        'default'    => 'whatsapp',
                        'after'      => 'code',
                    ],
                ]);
            }

            if (! in_array('subject', $fields, true)) {
                $this->forge->addColumn('whatsapp_templates', [
                    'subject' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 191,
                        'null'       => true,
                        'after'      => 'type',
                    ],
                ]);
            }

            $now = date('Y-m-d H:i:s');
            $exists = $this->db->table('whatsapp_templates')->where('code', 'email_loa_terbit')->countAllResults() > 0;
            if (! $exists) {
                $this->db->table('whatsapp_templates')->insert([
                    'name'       => 'Email Notifikasi LoA Terbit',
                    'code'       => 'email_loa_terbit',
                    'type'       => 'email',
                    'subject'    => 'Notifikasi Letter of Acceptance (LoA) - {judul_artikel}',
                    'message'    => "Yth. Bapak/Ibu {nama_penerima},\n\nLetter of Acceptance (LoA) untuk artikel berikut telah diterbitkan:\n\nJudul: {judul_artikel}\nJurnal: {nama_jurnal}\n\nHormat kami,\nTim Editor\n{nama_jurnal}",
                    'is_active'  => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        if (! $this->db->tableExists('email_messages')) {
            $this->forge->addField([
                'id'              => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'recipient_name'  => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
                'recipient_email' => ['type' => 'VARCHAR', 'constraint' => 191],
                'subject'         => ['type' => 'VARCHAR', 'constraint' => 191],
                'message'         => ['type' => 'TEXT'],
                'template_id'     => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
                'sent_by'         => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
                'status'          => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'sent'],
                'error_message'   => ['type' => 'TEXT', 'null' => true],
                'created_at'      => ['type' => 'DATETIME', 'null' => true],
                'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('template_id', 'whatsapp_templates', 'id', 'SET NULL', 'CASCADE');
            $this->forge->createTable('email_messages', true);
        }

        if ($this->db->tableExists('app_settings')) {
            $fields = $this->db->getFieldNames('app_settings');
            $columns = [];

            foreach ([
                'smtp_host'       => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
                'smtp_port'       => ['type' => 'INT', 'constraint' => 5, 'null' => true],
                'smtp_user'       => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
                'smtp_pass'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'smtp_crypto'     => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
                'mail_from_email' => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
                'mail_from_name'  => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            ] as $name => $definition) {
                if (! in_array($name, $fields, true)) {
                    $columns[$name] = $definition;
                }
            }

            if ($columns !== []) {
                $this->forge->addColumn('app_settings', $columns);
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('email_messages', true);
    }
}
