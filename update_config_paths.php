<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

DB::table('config')
    ->whereIn('config_name', ['app_logo', 'sidebar_logo', 'login_bg'])
    ->get()
    ->each(function($config) {
        DB::table('config')
            ->where('config_id', $config->config_id)
            ->update([
                'config_value' => str_replace('/config-pelni/', '/config/', $config->config_value)
            ]);
    });

echo "Database paths updated from /config-pelni/ to /config/\n";
