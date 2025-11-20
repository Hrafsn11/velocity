<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = AppSetting::getAllSettings();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
        ]);

        // Handle logo upload separately if present
        if ($request->hasFile('app_logo')) {
            $request->validate([
                'app_logo' => 'image|max:2048',
            ]);

            $setting = AppSetting::where('key', 'app_logo')->first();

            if ($setting && $setting->value) {
                Storage::disk('public')->delete($setting->value);
            }

            $path = $request->file('app_logo')->store('logos', 'public');
            AppSetting::set('app_logo', $path);
        }

        // Update other settings
        foreach ($validated['settings'] as $key => $value) {
            AppSetting::set($key, $value);
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
