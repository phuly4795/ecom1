<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $seenAll = $request->query('seen_all', false);
        if ($seenAll){
            Contact::where('is_read', 0)->update(['is_read' => 1]);
        }
        
        return view('layouts.pages.admin.contacts.index');
    }

    public function data()
    {
        $query = Contact::query();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('actions', function ($contact) {
                $showUrl = route('admin.contacts.show', $contact);
                $markUrl = route('admin.contacts.markAsRead', $contact);

                $html = '<div class="d-flex gap-2">';
                $html .= '<a href="' . $showUrl . '" class="btn btn-sm btn-primary" style="margin-right: 5%;"">Xem</a>';
                $html .= '<form action="' . $markUrl . '" method="POST" class="d-inline">';
                $html .= csrf_field();
                $html .= '<button type="submit" class="btn btn-sm btn-success">Đã đọc</button>';
                $html .= '</form>';
                $html .= '</div>';

                return new HtmlString($html);
            })
            ->editColumn('created_at', function ($contact) {
                return $contact->created_at->format('d/m/Y H:i');
            })
            ->editColumn('content', function ($contact) {
                return Str::limit($contact->content, 50, '...');
            })
            ->editColumn('is_read', function ($contact) {
                return $contact->is_read
                    ? '<span class="badge badge-success">Đã đọc</span>'
                    : '<span class="badge badge-warning">Chưa đọc</span>';
            })
            ->rawColumns(['actions', 'is_read'])
            ->make(true);
    }

    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->is_read = 1;
        $contact->save();
        return view('layouts.pages.admin.contacts.show', compact('contact'));
    }

    public function markAsRead($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->update(['is_read' => 1]);

        return redirect()->back()->with(['status' => 'success', 'message' => 'Đã đánh dấu là đã đọc']);
    }
}
