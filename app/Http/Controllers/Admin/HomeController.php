<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    public function index()
    {

        return view('layouts.pages.admin.dashboard');
    }

    public function createCustomer()
    {
        $user         =  new User();
        $user->name   =  'Developer';
        $user->email   =  'developer1@gmail.com';
        $user->password = Hash::make('1234');
        $user->save();

        $customer = Role::where('slug', 'customer')->first();

        $user->roles()->attach($customer);
    }
}
