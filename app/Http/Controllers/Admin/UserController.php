<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function index()
    {
        return view('layouts.pages.admin.users.index');
    }

    public function data()
    {
        $query = User::query(); // Thêm eager loading

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('actions', function ($user) {
                $editUrl = route('admin.users.edit', $user);
                $deleteUrl = route('admin.users.destroy', $user);

                $html = '<div class="d-flex gap-2">';
                $html .= '<a href="' . $editUrl . '" class="btn btn-sm btn-warning mr-2">Sửa</a>';
                $html .= '<form action="' . $deleteUrl . '" method="POST" class="d-inline">';
                $html .= csrf_field();
                $html .= method_field('DELETE');
                $html .= '<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa?\')">Xóa</button>';
                $html .= '</form>';
                $html .= '</div>';

                return new HtmlString($html);
            })

            ->editColumn('user_roles', function ($user) {
                $user = User::with('roles')->find($user->id);
                return optional($user->roles->first())->name ?? 'Không có';
            })

            ->editColumn('is_active', function ($user) {
                return $user->is_active == 1
                    ? '<i class="fa-solid fa-circle-check text-success" style="font-size: 22px"></i>'
                    : '<i class="fa-regular fa-circle-xmark text-danger" style="font-size: 22px"></i>';
            })
            ->rawColumns(['actions', 'is_active'])
            ->make(true);
    }

    public function create()
    {
        return view('layouts.pages.admin.users.upsert');
    }
    public function edit(User $user)
    {   
        return view('layouts.pages.admin.users.upsert', compact('user'));
    }

    public function storeOrUpdate(Request $request, $id = null)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:users,slug,' . $id,
            'status' => 'required|in:0,1',
        ], [
            'slug.unique' => 'Slug này đã tồn tại, vui lòng chọn tên khác.'
        ]);

        User::updateOrCreate(
            ['id' => $id],
            $validated
        );

        return redirect()->back()->with(['status' => 'success', 'message' => $id ? 'Cập nhật thành công' : 'Thêm mới thành công']);
    }


    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with(['status' => 'success', 'message' => 'Xóa thành công']);
    }

    // CategoryController.php
    public function massDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:Users,id'
        ]);

        User::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => 'success',
            'message' => 'Xóa hàng loạt thành công'
        ]);
    }
}
