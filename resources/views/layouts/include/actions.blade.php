<a href="{{ route('admin.category.edit', $category) }}" class="btn btn-sm btn-warning">Sửa</a>
<form action="{{ route('admin.category.destroy', $category) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
</form>
