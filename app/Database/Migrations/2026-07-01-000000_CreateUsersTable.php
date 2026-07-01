<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('users')) {
            return;
        }

        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'username'   => ['type' => 'VARCHAR', 'constraint' => 80],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 191],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 191],
            'password'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'       => ['type' => 'VARCHAR', 'constraint' => 40, 'default' => 'admin'],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('username');
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('users', true);

        $now = date('Y-m-d H:i:s');
        $this->db->table('users')->insert([
            'username'   => 'superadmin.plpi@plpi',
            'name'       => 'Superadmin PLPI',
            'email'      => 'superadmin.plpi@plpi',
            'password'   => '$2y$10$fwmPdAa7bLyVCLNlsfoop.T4RqvLNHxCNpiGkbYmDxrjPSejXQov6',
            'role'       => 'superadmin',
            'is_active'  => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('users', true);
    }
}
