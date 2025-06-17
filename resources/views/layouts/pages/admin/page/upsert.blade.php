<x-app-layout>
    <?php $title = isset($page->id) ? 'Cập nhật trang' : 'Thêm trang'; ?>
    @section('title', $title)

    <div class="container-fluid">
        <div class="card p-4 bg-white shadow-sm rounded">
            <h1 class="h3 mb-4 text-gray-800">
                {{ isset($page->id) ? 'Cập nhật trang' : 'Thêm trang' }}
            </h1>

            <form action="{{ isset($page) ? route('admin.pages.update', $page->id) : route('admin.pages.store') }}"
                method="POST">
                @csrf
                @if (isset($page))
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label>Tiêu đề</label>
                    <input type="text" name="title" id="title" class="form-control"
                        value="{{ old('title', $page->title ?? '') }}" required placeholder="Nhập tiêu đề trang">
                </div>

                <div class="mb-3">
                    <label>Trạng thái</label>
                    <select name="is_active" id="is_active" class="form-control">
                        <option value="1" {{ old('is_active', $page->is_active ?? '') == 1 ? 'selected' : '' }}>
                            Hiển thị</option>
                        <option value="0" {{ old('is_active', $page->is_active ?? '') == 0 ? 'selected' : '' }}>Ẩn
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Slug</label>
                    <input type="text" id="slug" class="form-control" readonly placeholder="Slug"
                        value="{{ old('slug', $page->slug ?? '') }}">
                    <input type="hidden" name="slug" id="slug-hidden" value="{{ old('slug', $page->slug ?? '') }}">
                </div>

                {{-- Page Builder --}}
                <div x-data="pageBuilder()" x-init="init()" class="mb-3">
                    <template x-for="(block, index) in blocks" :key="index">
                        <div class="card mb-2 p-2 border">
                            <select x-model="block.type" class="form-control mb-2" @change="initEditors()">
                                <option value="text">Text</option>
                                <option value="image">Image</option>
                                <option value="faq">FAQ</option>
                                <option value="banner">Banner</option>
                                <option value="code">Code</option>
                            </select>

                            <template x-if="block.type === 'text'">
                                <div>
                                    <textarea :id="`editor-${index}`" class="form-control"></textarea>
                                </div>
                            </template>

                            <template x-if="block.type === 'image'">
                                <input type="text" x-model="block.url" class="form-control" placeholder="URL ảnh">
                            </template>

                            <template x-if="block.type === 'faq'">
                                <div>
                                    <textarea x-model="block.question" class="form-control mb-1" placeholder="Câu hỏi"></textarea>
                                    <textarea x-model="block.answer" class="form-control" placeholder="Câu trả lời"></textarea>
                                </div>
                            </template>

                            <template x-if="block.type === 'banner'">
                                <input type="text" x-model="block.content" class="form-control"
                                    placeholder="Nội dung banner">
                            </template>
                            <template x-if="block.type === 'code'">
                                <textarea x-model="block.code" class="form-control" rows="30" placeholder="Nhập đoạn mã HTML/CSS/JS"></textarea>
                            </template>
                            <button type="button" class="btn btn-danger mt-2" @click="removeBlock(index)">Xóa</button>
                        </div>
                    </template>

                    <button type="button" class="btn btn-secondary" @click="addBlock()">Thêm block</button>
                    <input type="hidden" name="content_json" :value="JSON.stringify(blocks)">
                </div>

                <button class="btn btn-primary">Lưu trang</button>
            </form>
        </div>
    </div>

    <script>
        function pageBuilder() {
            let blocksData = @json(old('content_json', $page->content_json ?? []));
            if (typeof blocksData === 'string') {
                try {
                    blocksData = JSON.parse(blocksData);
                } catch (e) {
                    blocksData = [];
                }
            }

            return {
                blocks: blocksData,
                editors: {},

                addBlock() {
                    this.blocks.push({
                        type: 'text',
                        content: '',
                        code: '', // để tránh lỗi nếu chọn code block
                    });
                    this.$nextTick(() => this.initEditors());
                },

                removeBlock(index) {
                    this.blocks.splice(index, 1);
                    this.cleanupEditors();
                    this.$nextTick(() => this.initEditors());
                },

                initEditors() {
                    // Destroy existing editors to prevent duplicates
                    this.cleanupEditors();

                    // Initialize new editors
                    this.blocks.forEach((block, index) => {
                        if (block.type === 'text') {
                            const id = `editor-${index}`;
                            const el = document.getElementById(id);
                            if (el && !this.editors[id]) {
                                this.createTextEditor(el, id, index);
                            }
                        }

                    });
                },

                cleanupEditors() {
                    // Destroy all existing CKEditor instances
                    Object.keys(this.editors).forEach(id => {
                        if (this.editors[id]) {
                            this.editors[id].destroy().then(() => {
                                delete this.editors[id];
                            }).catch(err => console.error('Error destroying editor:', err));
                        }
                    });
                },

                createTextEditor(el, id, index) {
                    ClassicEditor.create(el).then(editor => {
                        this.editors[id] = editor;
                        editor.setData(this.blocks[index].content || '');
                        editor.model.document.on('change:data', () => {
                            this.blocks[index].content = editor.getData();
                        });
                    }).catch(err => console.error('CKEditor error:', err));
                },

                createTabEditor(el, id, index, tabIndex) {
                    ClassicEditor.create(el).then(editor => {
                        this.editors[id] = editor;
                    }).catch(err => console.error('CKEditor tab error:', err));
                },

                init() {
                    this.$nextTick(() => this.initEditors());
                }
            };
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('pageBuilder', pageBuilder);
        });
    </script>

    <script>
        function slugify(str) {
            return str.toLowerCase().replace(/đ/g, 'd').normalize('NFD')
                .replace(/[̀-ͯ]/g, '')
                .trim().replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-').replace(/-+/g, '-');
        }

        document.getElementById('title').addEventListener('input', function() {
            const slugValue = slugify(this.value);
            document.getElementById('slug').value = slugValue;
            document.getElementById('slug-hidden').value = slugValue;
        });
    </script>
</x-app-layout>
