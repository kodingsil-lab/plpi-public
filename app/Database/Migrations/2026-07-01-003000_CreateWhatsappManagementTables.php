<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWhatsappManagementTables extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('whatsapp_templates')) {
            $this->forge->addField([
                'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'name'       => ['type' => 'VARCHAR', 'constraint' => 191],
                'code'       => ['type' => 'VARCHAR', 'constraint' => 80],
                'message'    => ['type' => 'TEXT'],
                'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('code');
            $this->forge->createTable('whatsapp_templates', true);

            $now = date('Y-m-d H:i:s');
            $this->db->table('whatsapp_templates')->insertBatch([
                [
                    'name'       => 'Notifikasi LoA Terbit',
                    'code'       => 'loa_terbit',
                    'message'    => "Yth. Bapak/Ibu Penulis,\n\nLetter of Acceptance (LoA) untuk artikel berikut telah diterbitkan:\n\nJudul:\n*{judul_artikel}*\n\nLetter of Acceptance (LoA) juga telah kami kirimkan ke email yang terdaftar pada sistem. Silakan mengecek pesan masuk atau folder spam apabila email belum terlihat.\n\nHormat kami,\n*Tim Editor*\n*{nama_jurnal}*",
                    'is_active'  => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name'       => 'Pengingat Revisi',
                    'code'       => 'pengingat_revisi',
                    'message'    => "Yth. Bapak/Ibu Penulis,\n\nKami mengingatkan kembali terkait revisi naskah:\n*{judul_artikel}*\n\nMohon dapat segera melengkapi revisi sesuai catatan editor.\n\nTerima kasih.\n*{nama_jurnal}*",
                    'is_active'  => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);
        }

        if (! $this->db->tableExists('whatsapp_messages')) {
            $this->forge->addField([
                'id'             => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'recipient_name' => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
                'phone_number'   => ['type' => 'VARCHAR', 'constraint' => 40],
                'message'        => ['type' => 'TEXT'],
                'template_id'    => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
                'wa_url'         => ['type' => 'TEXT'],
                'sent_by'        => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
                'created_at'     => ['type' => 'DATETIME', 'null' => true],
                'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('template_id', 'whatsapp_templates', 'id', 'SET NULL', 'CASCADE');
            $this->forge->createTable('whatsapp_messages', true);
        }
    }

    public function down()
    {
        $this->forge->dropTable('whatsapp_messages', true);
        $this->forge->dropTable('whatsapp_templates', true);
    }
}
