<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInvoiceJurnalTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('invoice_jurnal')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nomor_invoice' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
            ],
            'tanggal_invoice' => [
                'type' => 'DATE',
            ],
            'jatuh_tempo' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'judul_artikel' => [
                'type' => 'TEXT',
            ],
            'nama_penulis' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'institusi_penulis' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'nama_jurnal' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'jumlah_tagihan' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
            ],
            'status_pembayaran' => [
                'type'       => 'ENUM',
                'constraint' => ['Belum Dibayar', 'Menunggu Pembayaran', 'Lunas'],
                'default'    => 'Belum Dibayar',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nomor_invoice');
        $this->forge->createTable('invoice_jurnal');
    }

    public function down()
    {
        $this->forge->dropTable('invoice_jurnal', true);
    }
}
