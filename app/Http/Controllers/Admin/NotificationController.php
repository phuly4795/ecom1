<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Setting;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function read($id)
    {
        $noti = Notification::findOrFail($id);
        $noti->update(['is_read' => 1]);

        // Điều hướng đến nơi bạn muốn — ví dụ:
        return redirect()->route('admin.contacts.index')
            ->with('status', 'success')
            ->with('message', 'Đã xem thông báo');
    }
}
