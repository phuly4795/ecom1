<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Province;
use App\Models\Ward;

class LocationController extends Controller
{

    public function provinces()
    {
        return Province::select('code', 'full_name')->orderBy('full_name')->get();
    }

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
