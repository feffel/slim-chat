<?php
declare(strict_types=1);

// Define root path
defined('DS') ?: define('DS', DIRECTORY_SEPARATOR);
defined('ROOT') ?: define('ROOT', dirname(__DIR__).DS);
// Load .env file
if (file_exists(ROOT.'.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(ROOT);
    $dotenv->load();
}

return [
    'settings' => [
        'displayErrorDetails'    => getenv('APP_DEBUG') === 'true',
        'addContentLengthHeader' => false,
        'app'                    => [
            'name' => getenv('APP_NAME'),
            'env'  => getenv('APP_ENV'),
        ],
        // Database settings
        'database'               => [
            'driver'    => getenv('DB_CONNECTION'),
            'database'  => getenv('DB_DATABASE'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],
        'cors' => getenv('CORS_ALLOWED_ORIGINS') ?? '*',
    ],
];
