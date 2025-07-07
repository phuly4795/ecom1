<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Http\Request;
use App\Mail\NewsletterWelcomeMail;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{

    public function index()
    {
        $getAll = Newsletter::all();
        return view('layouts.pages.admin.newsletter.index', compact(
            'getAll'
        ));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletters,email',
        ], [
            'email.unique' => 'Địa chỉ email đã được đăng ký!',
            'email.email' => 'Sai định dạng địa chỉ email!',
            'email.required' => 'Địa chỉ email không được bỏ trống!'
        ]);

        Newsletter::create(['email' => $request->email]);
        Mail::to($request->email)->send(new NewsletterWelcomeMail($request->email));

        return redirect()->back()->with('success', 'Đăng ký nhận bản tin thành công!');
    }
}
