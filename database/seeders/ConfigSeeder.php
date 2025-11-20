<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Config;

class ConfigSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            ['config_name' => 'app_name', 'config_value' => 'Admin Starter'],
            ['config_name' => 'app_logo', 'config_value' => '/assets/img/favicon/favicon.ico'],
            ['config_name' => 'sidebar_name', 'config_value' => 'Admin Starter'],
            ['config_name' => 'sidebar_logo', 'config_value' => '/assets/svg/icons/vuexy-sidebar.svg'],
            ['config_name' => 'primary_hex', 'config_value' => '#7367f0'],
            ['config_name' => 'app_home', 'config_value' => 'Dashboard'],
            ['config_name' => 'login_bg', 'config_value' => '/assets/img/backgrounds/techno.png'],
            ['config_name' => 'login_bg_style', 'config_value' => 'height: auto; width: 100%;'],
            ['config_name' => 'show_dummy', 'config_value' => 'true'],
        ];

        foreach ($configs as $config) {
            Config::updateOrCreate(
                ['config_name' => $config['config_name']],
                ['config_value' => $config['config_value']]
            );
        }
    }
}
