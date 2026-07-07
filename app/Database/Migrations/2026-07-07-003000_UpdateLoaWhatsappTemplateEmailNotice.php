<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateLoaWhatsappTemplateEmailNotice extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('whatsapp_templates')) {
            return;
        }

        $row = $this->db->table('whatsapp_templates')
            ->where('code', 'loa_terbit')
            ->get()
            ->getRowArray();

        if (! $row) {
            return;
        }

        $notice = 'Letter of Acceptance (LoA) juga telah kami kirimkan ke email yang terdaftar pada sistem. Silakan mengecek pesan masuk atau folder spam apabila email belum terlihat.';
        $message = (string) ($row['message'] ?? '');

        if ($message === '' || str_contains($message, $notice)) {
            return;
        }

        $needle = "\n\nHormat kami,";
        $message = str_contains($message, $needle)
            ? str_replace($needle, "\n\n" . $notice . $needle, $message)
            : rtrim($message) . "\n\n" . $notice;

        $this->db->table('whatsapp_templates')
            ->where('id', (int) $row['id'])
            ->update([
                'message'    => $message,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }

    public function down()
    {
    }
}
