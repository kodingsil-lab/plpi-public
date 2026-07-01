<?php

namespace App\Libraries;

use CodeIgniter\Email\Email;

class EmailService
{
    protected Email $email;

    public function __construct()
    {
        $this->email = \Config\Services::email();
    }

    public function sendLoaApprovedNotification(
        string $recipientEmail,
        array $letter,
        string $pdfPath,
        array $publisher = []
    ): bool {
        try {
            $journalName = $publisher['journal_name'] ?? $publisher['name'] ?? 'Jurnal';
            $editorName = $publisher['editor_name'] ?? $publisher['signer_name'] ?? 'Pimpinan Redaksi';
            $journalUrl = $publisher['journal_url'] ?? '';
            $authors = $this->parseAuthors($letter['authors_json'] ?? '[]');

            $this->email->clear(true);
            $this->email->setFrom(
                config('Email')->fromEmail ?: env('MAIL_FROM_ADDRESS', 'noreply@plpi.id'),
                config('Email')->fromName ?: env('MAIL_FROM_NAME', 'PLPI - Pusat Layanan Publikasi Ilmiah')
            );
            $this->email->setTo($recipientEmail);
            $this->email->setSubject('Notifikasi Letter of Acceptance (LoA) - ' . ($letter['loa_number'] ?? 'LoA'));
            $this->email->setMailType('html');
            $this->email->setMessage(view('email/loa_approved_notification', [
                'letter' => $letter,
                'authors' => $authors,
                'journalName' => $journalName,
                'editorName' => $editorName,
                'journalUrl' => $journalUrl,
            ]));

            if ($pdfPath !== '' && is_file($pdfPath)) {
                $this->email->attach($pdfPath);
            }

            $sent = $this->email->send(false);
            if (! $sent) {
                log_message('error', 'Email send failed. Debug info: ' . $this->email->printDebugger());
            }

            return $sent;
        } catch (\Throwable $e) {
            log_message('error', 'Failed to send LoA notification email: ' . $e->getMessage());
            return false;
        }
    }

    protected function parseAuthors($json): string
    {
        $authors = is_array($json) ? $json : json_decode((string) $json, true);
        if (! is_array($authors) || $authors === []) {
            return '-';
        }

        $names = [];
        foreach ($authors as $author) {
            $name = is_array($author) ? (string) ($author['name'] ?? '') : (string) $author;
            $name = trim(preg_replace('/^(Ketua|Anggota(?:\s*\d*)?)\s*[:\-]\s*/iu', '', $name) ?? $name);
            if ($name !== '') {
                $names[] = $name;
            }
        }

        if ($names === []) {
            return '-';
        }
        if (count($names) === 1) {
            return $names[0];
        }
        if (count($names) === 2) {
            return $names[0] . ' dan ' . $names[1];
        }

        $last = array_pop($names);
        return implode(', ', $names) . ', dan ' . $last;
    }
}
