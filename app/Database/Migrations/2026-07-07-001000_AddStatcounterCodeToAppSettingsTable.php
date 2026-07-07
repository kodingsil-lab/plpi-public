<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatcounterCodeToAppSettingsTable extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('app_settings')) {
            return;
        }

        if (! $this->db->fieldExists('statcounter_code', 'app_settings')) {
            $this->forge->addColumn('app_settings', [
                'statcounter_code' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'app_timezone',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('app_settings') && $this->db->fieldExists('statcounter_code', 'app_settings')) {
            $this->forge->dropColumn('app_settings', 'statcounter_code');
        }
    }
}
