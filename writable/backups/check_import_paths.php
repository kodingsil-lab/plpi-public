<?php

$mysqli = new mysqli('localhost', 'root', '', 'plpi_public');
if ($mysqli->connect_error) {
    fwrite(STDERR, $mysqli->connect_error . PHP_EOL);
    exit(1);
}

$checks = [
    'app_settings' => [
        'base' => __DIR__ . '/../../public/',
        'cols' => ['header_logo_path', 'login_logo_path', 'public_logo_path', 'favicon_path'],
    ],
    'journals' => [
        'base' => __DIR__ . '/../uploads/',
        'cols' => ['logo_path', 'default_signature_path', 'default_stamp_path'],
    ],
    'publishers' => [
        'base' => __DIR__ . '/../uploads/',
        'cols' => ['logo_path'],
    ],
    'loa_letters' => [
        'base' => __DIR__ . '/../uploads/',
        'cols' => ['pdf_path'],
    ],
];

$missing = [];

foreach ($checks as $table => $config) {
    $columns = implode(',', $config['cols']);
    $result = $mysqli->query("SELECT {$columns} FROM {$table}");

    while ($row = $result->fetch_assoc()) {
        foreach ($config['cols'] as $column) {
            $path = trim((string) ($row[$column] ?? ''));
            if ($path === '') {
                continue;
            }

            $fullPath = $config['base'] . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
            if (! file_exists($fullPath)) {
                $missing[] = [$table, $column, $path];
            }
        }
    }
}

if ($missing === []) {
    echo "OK: semua path file yang direferensikan tersedia." . PHP_EOL;
    exit(0);
}

foreach ($missing as [$table, $column, $path]) {
    echo "MISSING\t{$table}\t{$column}\t{$path}" . PHP_EOL;
}

exit(2);
