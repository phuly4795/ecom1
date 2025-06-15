<x-app-layout>
    <?php $title = isset($user->id) ? 'Cập nhật sản phẩm' : 'Thêm sản phẩm'; ?>
    @section('title', $title)
    <div class="container-fluid">
        <div class="card p-4 bg-white shadow-sm rounded">
            @if (isset($user->id))
                <h1 class="h3 mb-4 text-gray-800">Cập nhật người dùng</h1>
            @else
                <h1 class="h3 mb-4 text-gray-800">Thêm người dùng</h1>
            @endif

            <form action="{{ isset($user->id) ? route('admin.users.update', $user->id) : route('admin.users.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($user->id))
                    @method('PUT')
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Tên người dùng</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror" required
                            placeholder="Nhập tên người dùng" value="{{ old('name', $user->name ?? '') }}">
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Địa chỉ email</label>
                        <input type="text" id="email" class="form-control" placeholder="Nhập địa chỉ email"
                            value="{{ old('email', $user->email ?? '') }}">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">

                    <div class="col-md-6">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1" {{ old('status', $user->status ?? '') == 1 ? 'selected' : '' }}>
                                Hiển thị</option>
                            <option value="0" {{ old('status', $user->status ?? '') == 0 ? 'selected' : '' }}>
                                Ẩn</option>
                        </select>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <!-- Status -->
                    <div class="col-md-6">
                        <label for="is_active" class="form-label">Trạng thái</label>
                        <select name="is_active" id="is_active" class="form-control">
                            <option value="1"
                                {{ old('is_active', $user->is_active ?? '') == 1 ? 'selected' : '' }}>
                                Kích hoạt</option>
                            <option value="0"
                                {{ old('is_active', $user->is_active ?? '') == 0 ? 'selected' : '' }}>
                                Hủy kích hoạt</option>
                        </select>
                        @error('is_active')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    @if (Auth::user()->hasRoles('admin'))
                        <!-- Status -->
                        <div class="col-md-6">
                            <label for="status" class="form-label">Quyền hạn</label>
                            <select name="status" id="status" class="form-control"  @readonly( (!Auth::user()->hasRoles('admin')) ? true : false)>
                                <option value="1" {{ old('status', $user->status ?? '') == 1 ? 'selected' : '' }}>
                                    Hiển thị</option>
                                <option value="0" {{ old('status', $user->status ?? '') == 0 ? 'selected' : '' }}>
                                    Ẩn</option>
                            </select>
                            @error('status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ isset($user->id) ? 'Cập nhật' : 'Thêm mới' }}</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Trở về</a>
            </form>
        </div>
    </div>
</x-app-layout>
