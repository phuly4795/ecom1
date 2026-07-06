<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('layouts.pages.admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $allowedKeys = ['phone', 'email', 'address', 'info', 'default_shipping_fee', 'company_name', 'facebook', 'youtube', 'zalo', 'map'];
        $dynamicKeys = [];

        foreach ($request->except('_token', 'dynamic_settings') as $key => $value) {
            if (in_array($key, $allowedKeys)) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        if ($request->has('dynamic_settings')) {
            foreach ($request->dynamic_settings as $key => $value) {
                if (!empty($key) && preg_match('/^[a-zA-Z0-9_]+$/', $key)) {
                    Setting::updateOrCreate(['key' => $key], ['value' => $value]);
                }
            }
        }

        return back()->with('success', 'Cập nhật cấu hình thành công.');
    }
}
