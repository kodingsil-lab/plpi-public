<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceJurnalModel extends Model
{
    protected $table = 'invoice_jurnal';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;

    protected $allowedFields = [
        'nomor_invoice',
        'tanggal_invoice',
        'jatuh_tempo',
        'judul_artikel',
        'nama_penulis',
        'institusi_penulis',
        'nama_jurnal',
        'jumlah_tagihan',
        'status_pembayaran',
        'keterangan',
    ];
}
