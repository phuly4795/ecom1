<x-app-layout>
    {{-- {{dd($user)}} --}}
    <div class="rounded bg-white">
        <div class="row">
            <div class="col-md-3 border-right">
                <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                    <img class="rounded-circle mt-5" width="150px" src="https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg">
                    <span class="font-weight-bold">{{$user->name ?? ""}}</span>
                    <span class="text-black-50">{{$user->email ?? ""}}</span>
                    <span> </span>
                </div>
            </div>
            <div class="col-md-5 border-right">
                <div class="p-3 py-5">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="text-right">Hồ sơ cá nhân</h4>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12"><label class="labels">Họ và tên</label><input type="text" class="form-control" placeholder="Họ và tên" value="{{$user->name ?? ""}}"></div>
                        <div class="col-md-12"><label class="labels">Email</label><input type="text" class="form-control" placeholder="Họ và tên" value="{{$user->email ?? ""}}"></div>
                        <div class="col-md-12"><label class="labels">Số điện thoại</label><input type="text" class="form-control" placeholder="enter phone number" value="{{$user->phone ?? ""}}"></div>
                        <div class="col-md-12"><label class="labels">Ngày sinh</label><input type="date" class="form-control" placeholder="enter address line 1" value="{{$user->address}}"></div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="labels">Tỉnh/Thành phố</label>
                            <select id="province" name="province_id" class="form-control">
                                <option value="-1">Chọn Quận/Huyện</option>
                               @foreach ($provinces as $province)
                                    <option value="{{ $province->code }}">{{ $province->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6"><label class="labels">Quận/Huyện</label>
                            <select id="district" name="district_id" class="form-control" disabled>
                                <option value="">Chọn Quận/Huyện</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6"><label class="labels">Phường/Xã</label>
                            <select id="ward" name="ward_id" class="form-control" disabled>
                                <option value="">Chọn Phường/Xã</option>
                            </select>           
                        </div>
                        <div class="col-md-6"><label class="labels">Địa chỉ</label>
                           <input type="text" class="form-control" placeholder="country" value=""> 
                        </div>
                    </div>
                    <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="button">Save Profile</button></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 py-5">
                    <div class="d-flex justify-content-between align-items-center experience"><span>Quyền hạn</span><span class="border px-3 p-1 add-experience"><i class="fa fa-plus"></i>&nbsp;Thêm quyền hạn</span></div><br>
                    <div class="col-md-12">
                        <label class="labels">Quyền</label>
                        <select name="" id="" class="form-control">
                            @foreach ($user->roles as $role)
                                <option value="">{{$role->name}}</option>     
                            @endforeach
                        </select>
                    </div> <br>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const districtsUrl = "{{ route('admin.getDistricts', ['provinceId' => ':provinceId']) }}";
        const wardsUrl = "{{ route('admin.getWards', ['districtId' => ':districtId']) }}";

        // document.addEventListener('DOMContentLoaded', function() {
            const provinceSelect = document.getElementById('province');
            const districtSelect = document.getElementById('district');
            const wardSelect = document.getElementById('ward');

            provinceSelect.addEventListener('change', function() {
                const provinceId = this.value;
                districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
                districtSelect.disabled = false;
                wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                wardSelect.disabled = false;

                if (provinceId) {
                    // Thay thế :provinceId trong URL bằng provinceId thực tế
                    const url = districtsUrl.replace(':provinceId', provinceId);

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            districtSelect.disabled = false;
                            data.forEach(district => {
                                const option = document.createElement('option');
                                option.value = district.code;
                                option.textContent = district.name;
                                districtSelect.appendChild(option);
                            });
                        });
                }
            });

            districtSelect.addEventListener('change', function() {
                const districtId = this.value;

                wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                wardSelect.disabled = true;

                if (districtId) {
                    // Thay thế :districtId trong URL bằng districtId thực tế
                    const url = wardsUrl.replace(':districtId', districtId);

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            wardSelect.disabled = false;
                            data.forEach(ward => {
                                const option = document.createElement('option');
                                option.value = ward.code;
                                option.textContent = ward.name;
                                wardSelect.appendChild(option);
                            });
                        });
                }
            });
        // });
    });
</script>
