<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Ward;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function getDistricts($provinceId)
    {
        $district = District::where('city_code', $provinceId)->get();
        return  $district;
    }

    public function getWards($districtId)
    {
        $ward = Ward::where('district_code', $districtId)->get();
        return  $ward;
    }
}
