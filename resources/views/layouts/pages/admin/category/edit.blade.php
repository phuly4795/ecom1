<x-app-layout>
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Chỉnh sửa danh mục</h1>

        <form action="{{ route('admin.category.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            {{-- <div class="form-group">
                <label for="name">Mã danh mục</label>
                <input type="text" name="code" class="form-control"  value="{{ $category->code}}" disabled>
            </div> --}}

            <div class="form-group">
                <label for="name">Slug</label>
                <input type="text" name="slug" class="form-control"  value="{{ $category->slug}}" disabled>
            </div>

            <div class="form-group">
                <label for="name">Tên danh mục</label>
                <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn-success mt-2">Cập nhật</button>
            <a href="{{ route('admin.category.index') }}" class="btn btn-secondary mt-2">Hủy</a>
        </form>
    </div>
</x-app-layout>
