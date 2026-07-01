<?php

namespace App\Libraries;

use App\Models\JournalModel;
use App\Models\PublisherModel;
use App\Models\LoaRequestModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class LoaPdfService
{
    public function generate(array $letter): string
    {
        $letter = $this->hydrateEditionFromRequest($letter);

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $verifyUrl = rtrim((string) env('app.baseURL', 'http://localhost/plpi-public/public/'), '/') . '/loa/v/' . ($letter['public_token'] ?? '');

        $journal = null;
        $publisher = null;
        if (! empty($letter['journal_id'])) {
            $journal = (new JournalModel())->find((int) $letter['journal_id']);
            if (is_array($journal) && ! empty($journal['publisher_id'])) {
                $publisher = (new PublisherModel())->find((int) $journal['publisher_id']);
            }
        }

        $authors = $this->decodeJson($letter['authors_json'] ?? null);
        $affiliations = $this->decodeJson($letter['affiliations_json'] ?? null);
        $logoBase64 = $this->toBase64Image((string) ($journal['logo_path'] ?? ''));
        $publisherLogoBase64 = $this->toBase64Image((string) ($publisher['logo_path'] ?? ''));
        $stampBase64 = $this->toBase64Image((string) ($journal['default_stamp_path'] ?? ''));
        $sigBase64 = $this->toBase64Image((string) ($journal['default_signature_path'] ?? ''));
        $qrcodeBase64 = $this->generateQrCodeBase64($verifyUrl);

        $html = view('pdf/loa_issued', [
            'letter' => $letter,
            'verifyUrl' => $verifyUrl,
            'issuedDate' => date('d-m-Y', strtotime((string) ($letter['published_at'] ?? 'now'))),
            'journal' => $journal ?: [],
            'publisher' => $publisher ?: [],
            'authors' => $authors,
            'affiliations' => $affiliations,
            'logoBase64' => $logoBase64,
            'publisherLogoBase64' => $publisherLogoBase64,
            'stampBase64' => $stampBase64,
            'sigBase64' => $sigBase64,
            'qrcodeBase64' => $qrcodeBase64,
            'loaNumber' => (string) ($letter['loa_number'] ?? '-'),
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $safeLoaNumber = preg_replace('/[^A-Za-z0-9_-]/', '_', (string) ($letter['loa_number'] ?? 'loa_letter')) ?: 'loa_letter';
        $relativePath = 'loa/LoA-' . $safeLoaNumber . '.pdf';
        $absolutePath = WRITEPATH . 'uploads/' . $relativePath;

        $dir = dirname($absolutePath);
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        file_put_contents($absolutePath, $dompdf->output());

        return $relativePath;
    }

    private function decodeJson($value): array
    {
        if (is_array($value)) {
            return $value;
        }
        if (! is_string($value) || trim($value) === '') {
            return [];
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function hydrateEditionFromRequest(array $letter): array
    {
        if (empty($letter['loa_request_id'])) {
            return $letter;
        }

        $needsHydration = false;
        foreach (['volume', 'issue_number', 'published_year'] as $field) {
            if (trim((string) ($letter[$field] ?? '')) === '') {
                $needsHydration = true;
                break;
            }
        }

        if (! $needsHydration) {
            return $letter;
        }

        $request = (new LoaRequestModel())->find((int) $letter['loa_request_id']);
        if (! is_array($request)) {
            return $letter;
        }

        foreach (['volume', 'issue_number', 'published_year'] as $field) {
            if (trim((string) ($letter[$field] ?? '')) === '' && trim((string) ($request[$field] ?? '')) !== '') {
                $letter[$field] = $request[$field];
            }
        }

        return $letter;
    }

    private function toBase64Image(string $path): ?string
    {
        $resolved = $this->resolvePath($path);
        if ($resolved === null || ! is_file($resolved)) {
            return null;
        }

        $mime = mime_content_type($resolved);
        if (! is_string($mime) || strpos($mime, 'image/') !== 0) {
            return null;
        }

        $content = file_get_contents($resolved);
        if ($content === false) {
            return null;
        }

        return 'data:' . $mime . ';base64,' . base64_encode($content);
    }

    private function resolvePath(string $path): ?string
    {
        $clean = trim($path);
        if ($clean === '') {
            return null;
        }

        if (preg_match('/^[A-Za-z]:\\\\/', $clean) === 1 || str_starts_with($clean, '/')) {
            return $clean;
        }

        $normalized = ltrim(str_replace('\\', '/', $clean), '/');
        $candidates = [
            FCPATH . $normalized,
            WRITEPATH . $normalized,
            WRITEPATH . 'uploads/' . $normalized,
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function generateQrCodeBase64(string $verifyUrl): ?string
    {
        try {
            $qrCode = new QrCode($verifyUrl);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            
            // Gunakan getDataUri() untuk mendapatkan data:image/png;base64,...
            return $result->getDataUri();
        } catch (\Throwable $e) {
            // Fallback jika gagal
            error_log('QR Code generation failed: ' . $e->getMessage());
            return null;
        }
    }
}
