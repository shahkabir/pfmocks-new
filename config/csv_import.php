<?php

return [
    'max_file_size_kb' => (int) env('CSV_IMPORT_MAX_FILE_SIZE_KB', 10240), // 10 MB
    'chunk_size'       => (int) env('CSV_IMPORT_CHUNK_SIZE', 100),
    'disk'             => env('CSV_IMPORT_DISK', 'local'),
    'path'             => env('CSV_IMPORT_PATH', 'csv_imports'),
];
