<?php

return [
    'secret' => env('NOCAPTCHA_V3_SECRET'),
    'sitekey' => env('NOCAPTCHA_V3_SITEKEY'),
    'options' => [
        'timeout' => 30,
    ],
];
