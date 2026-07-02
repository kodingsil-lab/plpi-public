<?php

if (! function_exists('invoice_currency')) {
    function invoice_currency($value): string
    {
        return 'Rp ' . number_format((float) $value, 0, ',', '.');
    }
}

if (! function_exists('invoice_date')) {
    function invoice_date($date): string
    {
        if (empty($date)) {
            return '-';
        }

        $timestamp = strtotime((string) $date);
        if ($timestamp === false) {
            return '-';
        }

        return date('d/m/Y', $timestamp);
    }
}

if (! function_exists('invoice_status_class')) {
    function invoice_status_class(string $status): string
    {
        return match ($status) {
            'Lunas' => 'done',
            'Menunggu Pembayaran' => 'warning',
            default => 'muted',
        };
    }
}
