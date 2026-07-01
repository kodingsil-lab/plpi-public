<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InvoiceJurnalModel;
use App\Models\JournalModel;

class InvoiceJurnalController extends BaseController
{
    protected $helpers = ['url', 'admin_table', 'invoice'];

    private const STATUSES = ['Belum Dibayar', 'Menunggu Pembayaran', 'Lunas'];

    public function index()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $allowedPerPage = [10, 25, 50];
        $perPage = in_array((int) $this->request->getGet('perPage'), $allowedPerPage, true)
            ? (int) $this->request->getGet('perPage')
            : 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $search = trim((string) $this->request->getGet('q'));
        $status = trim((string) $this->request->getGet('status'));
        $rows = [];
        $pager = null;
        $databaseError = null;
        $createRow = [
            'nomor_invoice'      => '',
            'tanggal_invoice'    => date('Y-m-d'),
            'jatuh_tempo'        => '',
            'status_pembayaran'  => 'Belum Dibayar',
            'jumlah_tagihan'     => '',
            'judul_artikel'      => '',
            'nama_penulis'       => '',
            'institusi_penulis'  => '',
            'nama_jurnal'        => '',
            'keterangan'         => '',
        ];

        if ($this->tableReady()) {
            $model = new InvoiceJurnalModel();
            if ($search !== '') {
                $model->groupStart()
                    ->like('nomor_invoice', $search)
                    ->orLike('judul_artikel', $search)
                    ->orLike('nama_penulis', $search)
                    ->orLike('institusi_penulis', $search)
                    ->orLike('nama_jurnal', $search)
                    ->groupEnd();
            }
            if (in_array($status, self::STATUSES, true)) {
                $model->where('status_pembayaran', $status);
            } else {
                $status = '';
            }

            $rows = $model->orderBy('tanggal_invoice', 'DESC')->orderBy('id', 'DESC')->paginate($perPage);
            $pager = $model->pager;
            $createRow['nomor_invoice'] = $this->generateInvoiceNumber();
        } else {
            $databaseError = 'Tabel invoice_jurnal belum tersedia. Jalankan migrasi terlebih dahulu.';
        }

        return view('admin/invoice_jurnal/index', $this->viewData('Invoice Jurnal') + [
            'rows'          => $rows,
            'pager'         => $pager,
            'startNumber'   => (($page - 1) * $perPage) + 1,
            'perPage'       => $perPage,
            'search'        => $search,
            'status'        => $status,
            'statuses'      => self::STATUSES,
            'journals'      => $this->journals(),
            'createRow'     => $createRow,
            'showCreateModal' => (string) $this->request->getGet('modal') === 'create' || old('judul_artikel') !== null,
            'databaseError' => $databaseError,
        ]);
    }

    public function create()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }
        if (! $this->tableReady()) {
            return redirect()->to(site_url('dashboard/invoice-jurnal'))->with('error', 'Tabel invoice_jurnal belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        return redirect()->to(site_url('dashboard/invoice-jurnal?modal=create'));
    }

    public function store()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        if (! $this->validate($this->rules())) {
            return redirect()->back()->withInput()->with('error', 'Periksa form invoice.');
        }

        $payload = $this->payload();
        if (($payload['nomor_invoice'] ?? '') === '') {
            $payload['nomor_invoice'] = $this->generateInvoiceNumber();
        }

        (new InvoiceJurnalModel())->insert($payload);

        return redirect()->to(site_url('dashboard/invoice-jurnal'))->with('success', 'Invoice jurnal berhasil ditambahkan.');
    }

    public function show(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $row = $this->findInvoice($id);
        if (! $row) {
            return redirect()->to(site_url('dashboard/invoice-jurnal'))->with('error', 'Invoice tidak ditemukan.');
        }

        return view('admin/invoice_jurnal/detail', $this->viewData('Detail Invoice') + ['row' => $row]);
    }

    public function edit(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $row = $this->findInvoice($id);
        if (! $row) {
            return redirect()->to(site_url('dashboard/invoice-jurnal'))->with('error', 'Invoice tidak ditemukan.');
        }

        return view('admin/invoice_jurnal/form', $this->formData('Edit Invoice Jurnal') + ['row' => $row]);
    }

    public function update(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $model = new InvoiceJurnalModel();
        if (! $model->find($id)) {
            return redirect()->to(site_url('dashboard/invoice-jurnal'))->with('error', 'Invoice tidak ditemukan.');
        }

        if (! $this->validate($this->rules($id))) {
            return redirect()->back()->withInput()->with('error', 'Periksa form invoice.');
        }

        $model->update($id, $this->payload());

        return redirect()->to(site_url('dashboard/invoice-jurnal'))->with('success', 'Invoice jurnal berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        (new InvoiceJurnalModel())->delete($id);

        return redirect()->to(site_url('dashboard/invoice-jurnal'))->with('success', 'Invoice jurnal berhasil dihapus.');
    }

    public function bulkDelete()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $ids = $this->request->getPost('ids');
        if (! is_array($ids) || $ids === []) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $invoiceIds = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if ($invoiceIds === []) {
            return redirect()->back()->with('error', 'Tidak ada data valid yang dipilih.');
        }

        (new InvoiceJurnalModel())->delete($invoiceIds);

        return redirect()->to(site_url('dashboard/invoice-jurnal'))->with('success', 'Invoice terpilih berhasil dihapus.');
    }

    public function print(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $row = $this->findInvoice($id);
        if (! $row) {
            return redirect()->to(site_url('dashboard/invoice-jurnal'))->with('error', 'Invoice tidak ditemukan.');
        }

        return view('admin/invoice_jurnal/print', [
            'title' => 'Cetak Invoice ' . $row['nomor_invoice'],
            'row'   => $row,
        ]);
    }

    private function rules(?int $id = null): array
    {
        $uniqueRule = 'is_unique[invoice_jurnal.nomor_invoice]';
        if ($id !== null) {
            $uniqueRule = 'is_unique[invoice_jurnal.nomor_invoice,id,' . $id . ']';
        }

        return [
            'nomor_invoice'     => 'permit_empty|max_length[120]|' . $uniqueRule,
            'tanggal_invoice'   => 'required|valid_date[Y-m-d]',
            'jatuh_tempo'       => 'permit_empty|valid_date[Y-m-d]',
            'judul_artikel'     => 'required|max_length[5000]',
            'nama_penulis'      => 'required|max_length[255]',
            'institusi_penulis' => 'permit_empty|max_length[255]',
            'nama_jurnal'       => 'required|max_length[255]',
            'jumlah_tagihan'    => 'required|numeric|greater_than_equal_to[0]',
            'status_pembayaran' => 'required|in_list[' . implode(',', self::STATUSES) . ']',
            'keterangan'        => 'permit_empty|max_length[5000]',
        ];
    }

    private function payload(): array
    {
        $data = $this->validator->getValidated();

        return [
            'nomor_invoice'     => strtoupper(trim((string) ($data['nomor_invoice'] ?? ''))),
            'tanggal_invoice'   => $data['tanggal_invoice'],
            'jatuh_tempo'       => $this->nullable((string) ($data['jatuh_tempo'] ?? '')),
            'judul_artikel'     => trim((string) $data['judul_artikel']),
            'nama_penulis'      => trim((string) $data['nama_penulis']),
            'institusi_penulis' => $this->nullable((string) ($data['institusi_penulis'] ?? '')),
            'nama_jurnal'       => trim((string) $data['nama_jurnal']),
            'jumlah_tagihan'    => (float) str_replace(',', '.', (string) $data['jumlah_tagihan']),
            'status_pembayaran' => $data['status_pembayaran'],
            'keterangan'        => $this->nullable((string) ($data['keterangan'] ?? '')),
        ];
    }

    private function formData(string $title): array
    {
        return $this->viewData($title) + [
            'journals' => $this->journals(),
            'statuses' => self::STATUSES,
        ];
    }

    private function viewData(string $title): array
    {
        return [
            'title'      => $title,
            'activeMenu' => 'invoice_jurnal',
            'eyebrow'    => 'Manajemen Invoice',
            'pageTitle'  => $title,
            'adminName'  => session()->get('admin_name'),
            'adminEmail' => session()->get('admin_email'),
            'adminRole'  => session()->get('admin_role'),
        ];
    }

    private function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $model = new InvoiceJurnalModel();
        $last = $model->withDeleted()
            ->like('nomor_invoice', '/'. $year, 'before')
            ->orderBy('id', 'DESC')
            ->first();

        $next = 1;
        if ($last && preg_match('/INV-JRN-(\d+)\/' . preg_quote($year, '/') . '$/', (string) $last['nomor_invoice'], $matches)) {
            $next = ((int) $matches[1]) + 1;
        }

        return 'INV-JRN-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT) . '/' . $year;
    }

    private function findInvoice(int $id): ?array
    {
        return (new InvoiceJurnalModel())->find($id) ?: null;
    }

    private function journals(): array
    {
        try {
            if (! \Config\Database::connect()->tableExists('journals')) {
                return [];
            }

            return (new JournalModel())->orderBy('name', 'ASC')->findAll();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function nullable(string $value): ?string
    {
        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function tableReady(): bool
    {
        try {
            return \Config\Database::connect()->tableExists('invoice_jurnal');
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function isLoggedIn(): bool
    {
        return (bool) session()->get('admin_logged_in');
    }
}
