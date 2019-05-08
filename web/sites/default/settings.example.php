<?php
$databases['default']['default'] = array(
    'database' => '__DB__',
    'username' => '__DB__',
    'password' => '__DB__',
    'host' => 'localhost',
    'port' => '3306',
    'driver' => 'mysql',
    'prefix' => '',
    'collation' => 'utf8mb4_general_ci',
);

$settings['hash_salt'] = '__SALT__';

$config_directories = [
    'sync' => '../config/sync'
];

$settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.yml';

