<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->where('is_active', 1)->firstOrFail();
        // Giải mã JSON thành mảng PHP
        $page->content_json = json_decode($page->content_json, true);
        return view('layouts.pages.guest.show', compact('page'));
    }
    public function index()
    {
        $pages = Page::latest()->paginate(10);
        return view('layouts.pages.admin.page.index', compact('pages'));
    }

    public function create()
    {
        return view('layouts.pages.admin.page.upsert');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:pages,slug',
            'content_json' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Page::create([
            'title' => $request->title,
            'slug' => Str::slug($request->slug),
            'content_json' => $request->content_json,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.pages.index')->with(['status' => 'success', 'message' => 'Tạo trang thành công']);
    }

    public function edit(Page $page)
    {
        $page->content_json = json_decode($page->content_json, true) ?? [];
        return view('layouts.pages.admin.page.upsert', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:pages,slug,' . $page->id,
            'content_json' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $page->update([
            'title' => $request->title,
            'slug' => Str::slug($request->slug),
            'content_json' => $request->content_json,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->back()->with(['status' => 'success', 'message' => 'Cập nhật thành công']);
    }
    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->back()->with(['status' => 'success', 'message' => 'Xóa trang thành công']);
    }
}
