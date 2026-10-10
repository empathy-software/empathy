<?php

declare(strict_types=1);

// Database settings come from the working directory's config.yml when that
// file names a database, otherwise from eaa/config.yml. Migrations and seeds
// live in db/ next to the chosen file.

$root = getcwd() ?: __DIR__;
$configFile = $root.'/config.yml';
$spyc = new Spyc();
$config = is_file($configFile) ? $spyc->YAMLLoad($configFile) : [];
if (!is_array($config)) {
    $config = [];
}

if (!isset($config['db_name'])) {
    $root = __DIR__;
    $config = $spyc->YAMLLoad($root.'/config.yml');
    if (!is_array($config)) {
        $config = [];
    }
}

$environment = $config['boot_options']['environment'] ?? 'development';

return [
    'paths' => [
        'migrations' => $root.'/db/migrations',
        'seeds' => $root.'/db/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => $environment,
        $environment => [
            'adapter' => 'mysql',
            'host' => $config['db_server'] ?? 'localhost',
            'name' => $config['db_name'] ?? '',
            'user' => $config['db_user'] ?? 'root',
            'pass' => $config['db_pass'] ?? '',
            'port' => 3306,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],
    ],
    'version_order' => 'creation',
];
