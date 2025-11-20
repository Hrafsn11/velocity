<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Config;
use Illuminate\Support\Facades\File;

class ConfigController extends Controller
{
    // No constructor needed - middleware handled in routes

    public function index()
    {
        return view('admin.config.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'sidebar_name' => 'required|string|max:255',
            'sidebar_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'primary_hex' => 'required|string',
            'app_home' => 'required|in:Dashboard,Landing Page',
            'login_bg' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'login_bg_style' => 'required|string',
            'show_dummy' => 'required|in:true,false',
        ]);

        try {
            $data = $request->only('app_name', 'sidebar_name', 'primary_hex', 'app_home', 'login_bg_style', 'show_dummy');
            
            // Handle file uploads
            foreach (['app_logo', 'sidebar_logo', 'login_bg'] as $key) {
                if ($request->hasFile($key)) {
                    // Delete old file
                    $oldConfig = Config::where('config_name', $key)->first();
                    if ($oldConfig && $oldConfig->config_value && File::exists(public_path($oldConfig->config_value))) {
                        File::delete(public_path($oldConfig->config_value));
                    }
                    
                    // Upload new file
                    $dir = 'uploads';
                    if (!file_exists(public_path($dir))) {
                        mkdir(public_path($dir), 0755, true);
                    }
                    
                    $name = $key . '.' . $request->file($key)->getClientOriginalExtension();
                    $request->file($key)->move(public_path($dir), $name);
                    $data[$key] = '/' . $dir . '/' . $name;
                }
            }
            
            // Save all configs
            foreach ($data as $key => $value) {
                Config::updateOrCreate(
                    ['config_name' => $key],
                    ['config_value' => $value]
                );
            }
            
            return redirect()->back()->with('success', 'Configuration updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update configuration: ' . $e->getMessage());
        }
    }

    public function reset()
    {
        try {
            $configs = Config::all();
            foreach ($configs as $config) {
                if (in_array($config->config_name, ['app_logo', 'sidebar_logo', 'login_bg'])) {
                    if (File::exists(public_path($config->config_value))) {
                        File::delete(public_path($config->config_value));
                    }
                }
                $config->delete();
            }
            
            // Re-seed default configs
            $seeder = new \Database\Seeders\ConfigSeeder();
            $seeder->run();
            
            return redirect()->back()->with('success', 'Configuration reset to default successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reset configuration: ' . $e->getMessage());
        }
    }
}
