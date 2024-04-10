<?php

return [
    'paths' => ['http://192.168.4.176:8080/*'],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],
    'allowed_origins' => ['*'],
    'allowed_headers' => ['Content-Type', '*'],
    'exposed_headers' => ['X-Custom-Header'], // Tutaj definiujemy nagłówki, które chcemy eksponować
    'max_age' => 0,
    'supports_credentials' => false,
];
