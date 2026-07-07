<?php

namespace App\Libraries;

use App\Models\AppSettingModel;
use CodeIgniter\Email\Email;

class EmailService
{
    private const DEFAULT_FROM_EMAIL = 'info@plpi.unisap.ac.id';
    private const DEFAULT_FROM_NAME = 'PLPI - Pusat Layanan Publikasi Ilmiah';

    protected Email $email;

    public function __construct()
    {
        $this->email = \Config\Services::email(null, false);
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

            $settings = $this->mailSettings();

            if ($settings['smtp_host'] !== '' && $settings['smtp_user'] !== '' && $settings['smtp_pass'] !== '') {
                $this->email->initialize([
                    'protocol'    => 'smtp',
                    'SMTPHost'    => $settings['smtp_host'],
                    'SMTPUser'    => $settings['smtp_user'],
                    'SMTPPass'    => $settings['smtp_pass'],
                    'SMTPPort'    => $settings['smtp_port'],
                    'SMTPCrypto'  => $settings['smtp_crypto'],
                    'userAgent'   => 'PLPI Mailer',
                    'mailType'    => 'html',
                    'charset'     => 'UTF-8',
                    'wordWrap'    => true,
                    'SMTPTimeout' => 15,
                ]);
            }

            $this->email->clear(true);
            $this->email->setFrom(
                $settings['mail_from_email'],
                $settings['mail_from_name']
            );
            $this->email->setTo($recipientEmail);
            $this->email->setSubject('Letter of Acceptance Artikel Anda Telah Terbit');
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

    private function mailSettings(): array
    {
        $database = [];
        try {
            $database = (new AppSettingModel())->first() ?: [];
        } catch (\Throwable $e) {
            $database = [];
        }

        $smtpHost = trim((string) (env('plpi.smtp.host') ?: env('MAIL_HOST', '')));
        $smtpUser = trim((string) (env('plpi.smtp.user') ?: env('MAIL_USERNAME', '')));
        $mailFromEmail = trim((string) (env('plpi.mail.fromEmail') ?: env('MAIL_FROM_ADDRESS', '')));

        if ($smtpUser !== '' || $mailFromEmail !== '' || $smtpHost !== '') {
            return [
                'smtp_host'       => $smtpHost,
                'smtp_user'       => $smtpUser,
                'smtp_pass'       => (string) (env('plpi.smtp.password') ?: env('MAIL_PASSWORD', '')),
                'smtp_port'       => (int) (env('plpi.smtp.port') ?: env('MAIL_PORT', 587)),
                'smtp_crypto'     => trim((string) (env('plpi.smtp.crypto') ?: env('MAIL_ENCRYPTION', 'tls'))),
                'mail_from_email' => $mailFromEmail !== '' ? $mailFromEmail : ($smtpUser !== '' ? $smtpUser : self::DEFAULT_FROM_EMAIL),
                'mail_from_name'  => trim((string) (env('plpi.mail.fromName') ?: env('MAIL_FROM_NAME', self::DEFAULT_FROM_NAME))),
            ];
        }

        $databaseSmtpUser = trim((string) ($database['smtp_user'] ?? ''));
        $databaseFromEmail = trim((string) ($database['mail_from_email'] ?? ''));

        return [
            'smtp_host'       => trim((string) ($database['smtp_host'] ?? '')),
            'smtp_user'       => $databaseSmtpUser,
            'smtp_pass'       => $this->decryptSecret((string) ($database['smtp_pass'] ?? '')),
            'smtp_port'       => (int) ($database['smtp_port'] ?? 587),
            'smtp_crypto'     => trim((string) ($database['smtp_crypto'] ?? 'tls')),
            'mail_from_email' => $databaseFromEmail !== '' ? $databaseFromEmail : ($databaseSmtpUser !== '' ? $databaseSmtpUser : self::DEFAULT_FROM_EMAIL),
            'mail_from_name'  => trim((string) ($database['mail_from_name'] ?? self::DEFAULT_FROM_NAME)),
        ];
    }

    private function decryptSecret(string $value): string
    {
        $stored = trim($value);
        if ($stored === '') {
            return '';
        }

        try {
            $decoded = base64_decode($stored, true);
            if ($decoded !== false) {
                return (string) service('encrypter')->decrypt($decoded);
            }
        } catch (\Throwable $e) {
            // Plaintext fallback for older saved SMTP settings.
        }

        return $stored;
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
