<x-app-layout>
    @section('title', 'Cập nhật cấu hình')

    <div class="container-fluid">
        <div class="card p-4 bg-white shadow-sm rounded">
            <h1 class="h3 mb-4 text-gray-800">
                Cập nhật cấu hình
            </h1>
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data"
                x-data="settingForm()">
                @csrf

                <div class="mb-3">
                    <label>Email liên hệ:</label>
                    <input type="email" name="contact_email" class="form-control"
                        value="{{ $settings['contact_email'] ?? '' }}">
                </div>

                <div class="mb-3">
                    <label>Facebook:</label>
                    <input type="text" name="facebook_page" class="form-control"
                        value="{{ $settings['facebook_page'] ?? '' }}">
                </div>

                <div class="mb-3">
                    <label>Số điện thoại:</label>
                    <input type="text" name="phone" class="form-control" value="{{ $settings['phone'] ?? '' }}">
                </div>

                <div class="mb-3">
                    <label>Địa chỉ:</label>
                    <input type="text" name="address" class="form-control" value="{{ $settings['address'] ?? '' }}">
                </div>

                <hr>
                @php
                    $fixedKeys = ['contact_email', 'facebook_page', 'phone', 'address'];
                @endphp
                <h5 class="mb-2">Cấu hình bổ sung</h5>
                @foreach ($settings as $key => $value)
                    @if (!in_array($key, $fixedKeys))
                        <div class="mb-3">
                            <label>{{ Str::headline(str_replace('_', ' ', $key)) }}:</label>
                            <input type="text" name="dynamic_settings[{{ $key }}]" class="form-control"
                                value="{{ $value }}">
                        </div>
                    @endif
                @endforeach
                <!-- Dynamic settings -->
                <template x-for="(item, index) in dynamicSettings" :key="index">
                    <div class="row mb-2">
                        <div class="col-md-5">
                            <input type="text" class="form-control" :name="'dynamic_settings[' + item.key + ']'"
                                x-model="item.key" placeholder="Tên cấu hình (ví dụ: zalo_url)">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" :name="'dynamic_settings[' + item.key + ']'"
                                x-model="item.value" placeholder="Giá trị (value)">
                        </div>
                        <div class="col-md-1 text-end">
                            <button type="button" class="btn btn-danger" @click="remove(index)">✕</button>
                        </div>
                    </div>
                </template>

                <button type="button" class="btn btn-secondary mb-3" @click="add()">+ Thêm cấu hình mới</button>

                <button class="btn btn-primary">Lưu</button>
            </form>

        </div>
    </div>
    <script>
        function settingForm() {
            return {
                dynamicSettings: [],
                add() {
                    this.dynamicSettings.push({
                        key: '',
                        value: ''
                    });
                },
                remove(index) {
                    this.dynamicSettings.splice(index, 1);
                }
            };
        }
    </script>

</x-app-layout>
