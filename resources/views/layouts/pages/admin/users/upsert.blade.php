<x-app-layout>
    <?php $title = isset($user->id) ? 'Cập nhật người dùng' : 'Thêm người dùng'; ?>
    @section('title', $title)
    <div class="container-fluid">
        <div class="card p-4 bg-white shadow-sm rounded">
            @if (isset($user->id))
                <h1 class="h3 mb-4 text-gray-800">Cập nhật người dùng</h1>
            @else
                <h1 class="h3 mb-4 text-gray-800">Thêm người dùng</h1>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
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
                            readonly value="{{ old('email', $user->email ?? '') }}">
                        @error('email')
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
                        <div class="col-md-6">
                            <label for="role_id" class="form-label">Quyền hạn</label>
                            <select name="role_id" id="role_id" class="form-control">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ old('role_id', $user->roles->first()->id ?? '') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
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
