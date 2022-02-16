<?php

return [
    'name' => $_ENV['APP_NAME'] ?? 'Project',
    'version' => $_ENV['APP_VERSION'] ?? '1.0.0',
    'env' => $_ENV['APP_ENV'] ?? 'prod',
    'debug' => (bool) ($_ENV['APP_DEBUG'] ?? false),
];