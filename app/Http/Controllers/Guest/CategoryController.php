<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;

class CategoryController extends Controller
{
    public function index()
    {
        return view('layouts.pages.guest.category');
    }
}
