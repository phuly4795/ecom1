<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\District;
use App\Models\Province;
use App\Models\Role;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $roles = Role::orderBy('name', 'asc')->get();
        $provinces = Province::all();
        return view('profile.edit', [
            'user' => $request->user(),
            'provinces' => $provinces,
            'roles' => $roles
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updateInfo(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'numeric', 'digits_between:10,11'],
            'birthday' => ['nullable', 'date'],
            'province_id' => ['nullable', 'string'],
            'district_id' => ['nullable', 'string'],
            'ward_id' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:255'],
            // 'is_active' => ['nullable', 'boolean'],
            // 'user_roles' => ['nullable', 'exists:roles,id']
        ]);

        if ($request->hasFile('avatar')) {
            // Xóa ảnh cũ nếu có
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        User::where('id', $user->id)->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thành công'
        ]);
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
