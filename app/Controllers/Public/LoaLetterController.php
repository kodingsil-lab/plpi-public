<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Libraries\LoaPdfService;
use App\Models\LoaLetterModel;
use App\Models\JournalModel;
use App\Models\LoaRequestModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class LoaLetterController extends BaseController
{
    public function show(string $token)
    {
        $letter = $this->findLetterByToken($token);
        $journal = null;
        if (! empty($letter['journal_id'])) {
            $journal = (new JournalModel())->find((int) $letter['journal_id']);
        }

        return view('public/loa/show', [
            'title' => 'Detail LoA Terbit',
            'letter' => $letter,
            'journal' => $journal,
        ]);
    }

    public function preview(string $token)
    {
        $letter = $this->findLetterByToken($token);
        $pdfPath = $this->ensurePdf($letter, (bool) $this->request->getGet('refresh'));
        $absolute = WRITEPATH . 'uploads/' . ltrim($pdfPath, '/');

        if (! is_file($absolute)) {
            throw new PageNotFoundException('PDF LoA tidak ditemukan.');
        }

        $safeName = preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $letter['loa_number']) ?: 'loa_letter';
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $safeName . '.pdf"')
            ->setBody((string) file_get_contents($absolute));
    }

    public function download(string $token)
    {
        $letter = $this->findLetterByToken($token);
        $pdfPath = $this->ensurePdf($letter, false);
        $absolute = WRITEPATH . 'uploads/' . ltrim($pdfPath, '/');

        if (! is_file($absolute)) {
            throw new PageNotFoundException('PDF LoA tidak ditemukan.');
        }

        $safeName = preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $letter['loa_number']) ?: 'loa_letter';
        return $this->response->download($absolute, null)->setFileName($safeName . '.pdf');
    }

    private function findLetterByToken(string $token): array
    {
        $letter = (new LoaLetterModel())
            ->where('public_token', $token)
            ->where('status', 'published')
            ->first();

        if (! $letter) {
            throw new PageNotFoundException('LoA tidak ditemukan.');
        }

        return $letter;
    }

    private function ensurePdf(array $letter, bool $forceRefresh): string
    {
        [$letter, $editionHydrated] = $this->hydrateEditionFromRequest($letter);
        $forceRefresh = $forceRefresh || $editionHydrated;

        $current = (string) ($letter['pdf_path'] ?? '');
        $absoluteCurrent = $current !== '' ? WRITEPATH . 'uploads/' . ltrim($current, '/') : '';
        if (! $forceRefresh && $absoluteCurrent !== '' && is_file($absoluteCurrent)) {
            return $current;
        }

        $service = new LoaPdfService();
        $newPath = $service->generate($this->normalizeLetter($letter));

        (new LoaLetterModel())->update((int) $letter['id'], [
            'pdf_path' => $newPath,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $newPath;
    }

    private function hydrateEditionFromRequest(array $letter): array
    {
        if (empty($letter['loa_request_id'])) {
            return [$letter, false];
        }

        $missingFields = [];
        foreach (['volume', 'issue_number', 'published_year'] as $field) {
            if (trim((string) ($letter[$field] ?? '')) === '') {
                $missingFields[] = $field;
            }
        }

        if ($missingFields === []) {
            return [$letter, false];
        }

        $request = (new LoaRequestModel())->find((int) $letter['loa_request_id']);
        if (! is_array($request)) {
            return [$letter, false];
        }

        $payload = [];
        foreach ($missingFields as $field) {
            $value = trim((string) ($request[$field] ?? ''));
            if ($value !== '') {
                $letter[$field] = $request[$field];
                $payload[$field] = $request[$field];
            }
        }

        if ($payload === []) {
            return [$letter, false];
        }

        $payload['updated_at'] = date('Y-m-d H:i:s');
        (new LoaLetterModel())->update((int) $letter['id'], $payload);

        return [$letter, true];
    }

    private function normalizeLetter(array $letter): array
    {
        foreach (['authors_json', 'affiliations_json'] as $field) {
            if (isset($letter[$field]) && is_string($letter[$field])) {
                $decoded = json_decode($letter[$field], true);
                $letter[$field] = is_array($decoded) ? $decoded : [];
            }
        }
        return $letter;
    }
}
