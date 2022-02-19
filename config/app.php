<?php

return [
    'name' => $_ENV['APP_NAME'] ?? 'Project',
    'env' => $_ENV['APP_ENV'] ?? 'prod',
    'debug' => (bool) ($_ENV['APP_DEBUG'] ?? false),
];