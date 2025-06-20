<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show()
    {
        // $settings = Setting::all()->pluck('value', 'key');
        // return view('layouts.pages.admin.settings.edit', compact('settings'));
    }

    public function markAsRead(Request $request)
    {
        // foreach ($request->except('_token', 'dynamic_settings') as $key => $value) {
        //     Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        // }

        // // Lưu dynamic settings
        // if ($request->has('dynamic_settings')) {
        //     foreach ($request->dynamic_settings as $key => $value) {
        //         if (!empty($key)) {
        //             Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        //         }
        //     }
        // }

        // return back()->with('success', 'Cập nhật cấu hình thành công.');
    }
}
