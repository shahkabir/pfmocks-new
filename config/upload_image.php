<?php

return [
    'path'        => env('UPLOAD_IMAGE_PATH', 'data/image'),
    'mimes'       => ['jpg', 'jpeg', 'png', 'gif'],
    'max_size_kb' => (int) env('UPLOAD_IMAGE_MAX_SIZE_KB', 5120), // 5 MB
];
