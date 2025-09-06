<?php

namespace App\Imports;

use App\Models\Province;
use App\Models\District;
use App\Models\ShippingFee;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;

class ShippingFeeImport implements ToCollection, WithHeadingRow, SkipsOnFailure, SkipsOnError
{
    use SkipsFailures, SkipsErrors;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $province = Province::where('name', $row['province_name'])->first();
            if (!$province) {
                // Có thể log hoặc bỏ qua
                continue;
            }

            $district = District::where('name', $row['district_name'])
                ->where('city_code', $province->code) // dùng mã tỉnh
                ->first();

            if (!$district) {
                continue;
            }

            ShippingFee::updateOrCreate([
                'province_id' => $province->id,
                'district_id' => $district->id,
            ], [
                'fee' => $row['fee']
            ]);
        }
    }
}
