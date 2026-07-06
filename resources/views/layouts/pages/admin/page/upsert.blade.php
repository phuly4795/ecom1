<x-app-layout>
    <?php $title = isset($page->id) ? 'Cập nhật trang' : 'Thêm trang'; ?>
    @section('title', $title)

    <style>
        .hover-card {
            transition: all 0.2s ease-in-out;
            border-width: 1px;
            border-style: solid;
        }
        .hover-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
            background-color: #f8f9fc !important;
        }
        .bg-primary-light { background-color: rgba(78, 115, 223, 0.1); }
        .bg-success-light { background-color: rgba(28, 200, 138, 0.1); }
        .bg-warning-light { background-color: rgba(246, 194, 62, 0.1); }
        .bg-info-light { background-color: rgba(54, 185, 204, 0.1); }
        .bg-danger-light { background-color: rgba(231, 74, 59, 0.1); }
        .bg-secondary-light { background-color: rgba(108, 117, 125, 0.1); }
        .bg-pink-light { background-color: rgba(232, 62, 140, 0.1); }
        
        .border-left-primary { border-left: 5px solid #4e73df !important; }
        .border-left-success { border-left: 5px solid #1cc88a !important; }
        .border-left-warning { border-left: 5px solid #f6c23e !important; }
        .border-left-info { border-left: 5px solid #36b9cc !important; }
        .border-left-danger { border-left: 5px solid #e74a3b !important; }
        .border-left-secondary { border-left: 5px solid #6c757d !important; }
        .border-left-pink { border-left: 5px solid #e83e8c !important; }
        
        .border-pink { border-color: #e83e8c !important; }
        .text-pink { color: #e83e8c !important; }
        
        .last-no-border:last-child {
            border-bottom: none !important;
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }
        .cursor-move {
            cursor: grab;
        }
        .cursor-move:active {
            cursor: grabbing;
        }
    </style>

    <div class="container-fluid">
        <div class="card p-4 bg-white shadow-sm rounded border-0">
            <h1 class="h3 mb-4 text-gray-800 font-weight-bold">
                {{ isset($page->id) ? 'Cập nhật trang' : 'Thêm trang' }}
            </h1>

            <form action="{{ isset($page) ? route('admin.pages.update', $page->id) : route('admin.pages.store') }}"
                method="POST">
                @csrf
                @if (isset($page))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">Tiêu đề trang</label>
                        <input type="text" name="title" id="title" class="form-control py-4 shadow-sm"
                            value="{{ old('title', $page->title ?? '') }}" required placeholder="Nhập tiêu đề trang">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark">Trạng thái hiển thị</label>
                        <select name="is_active" id="is_active" class="form-control shadow-sm" style="height: calc(2.25rem + 18px)">
                            <option value="1" {{ old('is_active', $page->is_active ?? '') == 1 ? 'selected' : '' }}>
                                Hiển thị công khai</option>
                            <option value="0" {{ old('is_active', $page->is_active ?? '') == 0 ? 'selected' : '' }}>Ẩn / Tạm lưu
                            </option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="font-weight-bold text-dark">Đường dẫn liên kết (Slug)</label>
                    <input type="text" id="slug" class="form-control bg-light" readonly placeholder="Tự động tạo từ tiêu đề"
                        value="{{ old('slug', $page->slug ?? '') }}">
                    <input type="hidden" name="slug" id="slug-hidden" value="{{ old('slug', $page->slug ?? '') }}">
                </div>

                {{-- Visual Page Builder --}}
                <div x-data="pageBuilder()" class="mb-4">
                    <hr class="my-4">
                    <h5 class="font-weight-bold text-dark mb-4"><i class="fa-solid fa-layer-group mr-2 text-primary"></i> Trình dựng trang trực quan (Landing Page Builder)</h5>
                    
                    <div class="row">
                        <!-- Cột trái: Bảng công cụ -->
                        <div class="col-lg-4 col-md-5 mb-4">
                            <div class="card shadow-sm border sticky-top" style="top: 20px; z-index: 10;">
                                <div class="card-header bg-gradient-primary text-white py-3">
                                    <h6 class="m-0 font-weight-bold"><i class="fa-solid fa-toolbox mr-2"></i> Bảng công cụ thành phần</h6>
                                </div>
                                <div class="card-body p-3">
                                    <p class="text-muted small">Bấm chọn thành phần để chèn vào Landing Page của bạn:</p>
                                    <div class="d-flex flex-column">
                                        <button type="button" @click="addBlock('text')" class="btn btn-block btn-outline-primary text-left py-3 px-3 mb-2 shadow-xs hover-card border-primary d-flex align-items-center">
                                            <div class="icon-circle bg-primary-light mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%;">
                                                <i class="fa-solid fa-paragraph text-primary fa-lg"></i>
                                            </div>
                                            <div>
                                                <strong class="d-block text-dark small font-weight-bold">Khối Văn bản (Rich Text)</strong>
                                                <span class="text-muted small" style="font-size: 11px;">Soạn thảo bài viết, thông tin chi tiết.</span>
                                            </div>
                                        </button>
                                        
                                        <button type="button" @click="addBlock('image')" class="btn btn-block btn-outline-success text-left py-3 px-3 mb-2 shadow-xs hover-card border-success d-flex align-items-center">
                                            <div class="icon-circle bg-success-light mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%;">
                                                <i class="fa-solid fa-image text-success fa-lg"></i>
                                            </div>
                                            <div>
                                                <strong class="d-block text-dark small font-weight-bold">Khối Hình ảnh</strong>
                                                <span class="text-muted small" style="font-size: 11px;">Chèn ảnh minh họa hoặc ảnh đơn.</span>
                                            </div>
                                        </button>

                                        <button type="button" @click="addBlock('slideshow')" class="btn btn-block btn-outline-secondary text-left py-3 px-3 mb-2 shadow-xs hover-card border-secondary d-flex align-items-center">
                                            <div class="icon-circle bg-secondary-light mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%;">
                                                <i class="fa-solid fa-images text-secondary fa-lg"></i>
                                            </div>
                                            <div>
                                                <strong class="d-block text-dark small font-weight-bold">Khối Slideshow (Trình chiếu)</strong>
                                                <span class="text-muted small" style="font-size: 11px;">Nhiều hình slide chạy kèm liên kết link.</span>
                                            </div>
                                        </button>
                                        
                                        <button type="button" @click="addBlock('product')" class="btn btn-block btn-outline-danger text-left py-3 px-3 mb-2 shadow-xs hover-card border-pink d-flex align-items-center">
                                            <div class="icon-circle bg-pink-light mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%;">
                                                <i class="fa-solid fa-basket-shopping text-pink fa-lg"></i>
                                            </div>
                                            <div>
                                                <strong class="d-block text-dark small font-weight-bold">Khối sản phẩm nổi bật</strong>
                                                <span class="text-muted small" style="font-size: 11px;">Hiển thị thông tin mua nhanh của 1 sản phẩm.</span>
                                            </div>
                                        </button>
                                        
                                        <button type="button" @click="addBlock('faq')" class="btn btn-block btn-outline-warning text-left py-3 px-3 mb-2 shadow-xs hover-card border-warning d-flex align-items-center">
                                            <div class="icon-circle bg-warning-light mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%;">
                                                <i class="fa-solid fa-circle-question text-warning fa-lg"></i>
                                            </div>
                                            <div>
                                                <strong class="d-block text-dark small font-weight-bold">Câu hỏi thường gặp (FAQ)</strong>
                                                <span class="text-muted small" style="font-size: 11px;">Thiết lập phần Hỏi-Đáp cho khách hàng.</span>
                                            </div>
                                        </button>
                                        
                                        <button type="button" @click="addBlock('banner')" class="btn btn-block btn-outline-info text-left py-3 px-3 mb-2 shadow-xs hover-card border-info d-flex align-items-center">
                                            <div class="icon-circle bg-info-light mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%;">
                                                <i class="fa-solid fa-bullhorn text-info fa-lg"></i>
                                            </div>
                                            <div>
                                                <strong class="d-block text-dark small font-weight-bold">Banner Khuyến mãi</strong>
                                                <span class="text-muted small" style="font-size: 11px;">Nổi bật thông điệp giảm giá, chương trình.</span>
                                            </div>
                                        </button>
                                        
                                        <button type="button" @click="addBlock('code')" class="btn btn-block btn-outline-danger text-left py-3 px-3 mb-2 shadow-xs hover-card border-danger d-flex align-items-center">
                                            <div class="icon-circle bg-danger-light mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%;">
                                                <i class="fa-solid fa-code text-danger fa-lg"></i>
                                            </div>
                                            <div>
                                                <strong class="d-block text-dark small font-weight-bold">Mã HTML tùy chỉnh</strong>
                                                <span class="text-muted small" style="font-size: 11px;">Nhúng video, bản đồ, mã nhúng CSS/JS.</span>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cột phải: Vùng thiết kế & Xem trước -->
                        <div class="col-lg-8 col-md-7">
                            <div class="card shadow-sm border mb-4">
                                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary"><i class="fa-solid fa-laptop-code mr-2"></i> Khu vực thiết kế</h6>
                                    <button type="button" @click="showPreview = !showPreview" class="btn btn-sm btn-outline-secondary" :class="showPreview ? 'active btn-secondary text-white' : ''">
                                        <i class="fa-solid" :class="showPreview ? 'fa-eye-slash mr-1' : 'fa-eye mr-1'"></i> 
                                        <span x-text="showPreview ? 'Ẩn xem trước' : 'Xem trước trực quan'"></span>
                                    </button>
                                </div>
                                
                                <div class="card-body bg-light p-3" style="min-height: 400px;">
                                    <div class="workspace-area">
                                        <!-- Khi chưa có khối nào -->
                                        <div class="text-center py-5 text-muted border border-dashed rounded bg-white" x-show="blocks.length === 0">
                                            <i class="fa-solid fa-cubes fa-3x mb-3 text-gray-300"></i>
                                            <h5>Khu vực thiết kế trống</h5>
                                            <p class="small">Hãy nhấp vào các công cụ bên trái để bắt đầu xây dựng trang của bạn.</p>
                                        </div>

                                        <!-- Danh sách khối -->
                                        <template x-for="(block, index) in blocks" :key="index">
                                            <div class="card mb-3 border shadow-sm transition-all builder-block"
                                                 draggable="true"
                                                 @dragstart="draggedIndex = index"
                                                 @dragover.prevent=""
                                                 @drop="swapBlocks(draggedIndex, index); draggedIndex = null; dragEnabled = false"
                                                 :class="{ 'opacity-50 border-primary border-2': draggedIndex === index }">
                                                 
                                                 <!-- Header của khối -->
                                                 <div class="card-header d-flex align-items-center justify-content-between py-2 px-3 bg-white" 
                                                      :class="getHeaderClass(block.type)">
                                                      <div class="d-flex align-items-center">
                                                          <!-- Nút kéo thả -->
                                                          <span class="drag-handle cursor-move mr-3" 
                                                                @mousedown="dragEnabled = true" 
                                                                @mouseup="dragEnabled = false" 
                                                                style="cursor: grab;">
                                                              <i class="fa-solid fa-grip-vertical text-muted"></i>
                                                          </span>
                                                          <!-- Số thứ tự và Icon -->
                                                          <span class="badge badge-secondary mr-2" x-text="index + 1"></span>
                                                          <i class="fa-solid mr-2 fa-lg" :class="getBlockIcon(block.type)"></i>
                                                          <strong class="text-dark font-weight-bold" x-text="getBlockName(block.type)"></strong>
                                                      </div>
                                                      
                                                      <div class="d-flex align-items-center">
                                                          <!-- Thu nhỏ/Mở rộng -->
                                                          <button type="button" class="btn btn-sm btn-link text-secondary py-0 mr-1" @click="block.collapsed = !block.collapsed">
                                                              <i class="fa-solid" :class="block.collapsed ? 'fa-chevron-down' : 'fa-chevron-up'"></i>
                                                          </button>
                                                          <!-- Di chuyển lên -->
                                                          <button type="button" class="btn btn-sm btn-link text-secondary py-0 mr-1" @click="moveUp(index)" :disabled="index === 0">
                                                              <i class="fa-solid fa-arrow-up"></i>
                                                          </button>
                                                          <!-- Di chuyển xuống -->
                                                          <button type="button" class="btn btn-sm btn-link text-secondary py-0 mr-1" @click="moveDown(index)" :disabled="index === blocks.length - 1">
                                                              <i class="fa-solid fa-arrow-down"></i>
                                                          </button>
                                                          <!-- Nhân bản -->
                                                          <button type="button" class="btn btn-sm btn-link text-primary py-0 mr-1" @click="duplicateBlock(index)" title="Nhân bản">
                                                              <i class="fa-solid fa-copy"></i>
                                                          </button>
                                                          <!-- Xóa -->
                                                          <button type="button" class="btn btn-sm btn-link text-danger py-0" @click="removeBlock(index)" title="Xóa">
                                                              <i class="fa-solid fa-trash-can"></i>
                                                          </button>
                                                      </div>
                                                 </div>
                                                 
                                                 <!-- Body của khối -->
                                                 <div class="card-body p-3 bg-white" x-show="!block.collapsed" x-transition>
                                                     <!-- Khối văn bản -->
                                                     <template x-if="block.type === 'text'">
                                                         <div x-init="ClassicEditor.create($el.querySelector('.text-editor')).then(editor => {
                                                             editor.setData(block.content || '');
                                                             editor.model.document.on('change:data', () => {
                                                                 block.content = editor.getData();
                                                             });
                                                         })">
                                                             <div x-ignore>
                                                                 <textarea class="text-editor form-control"></textarea>
                                                             </div>
                                                         </div>
                                                     </template>
                                                     
                                                     <!-- Khối ảnh -->
                                                      <template x-if="block.type === 'image'">
                                                          <div>
                                                              <label class="small font-weight-bold text-dark">Đường dẫn hình ảnh (URL)</label>
                                                              <input type="text" x-model="block.url" class="form-control mb-2 shadow-xs" placeholder="Nhập liên kết ảnh (ví dụ: https://example.com/image.jpg)">
                                                              
                                                              <label class="small font-weight-bold text-dark mt-2">Link liên kết toàn ảnh (nếu không dùng vùng nhấp)</label>
                                                              <input type="text" x-model="block.link" class="form-control mb-2 shadow-xs" placeholder="Nhập URL liên kết hoặc nhập #thu-cu-doi-moi">
                                                              
                                                              <hr class="my-3">
                                                              <div class="d-flex align-items-center justify-content-between mb-2">
                                                                  <label class="small font-weight-bold text-dark mb-0"><i class="fa-solid fa-crosshairs mr-1 text-primary"></i> Vùng nhấp trên ảnh (Hotspots)</label>
                                                                  <button type="button" class="btn btn-sm btn-outline-primary" @click="if(!block.hotspots) block.hotspots=[]; block.hotspots.push({top: 40, left: 40, width: 20, height: 8, link: '#thu-cu-doi-moi'})">
                                                                      <i class="fa-solid fa-plus mr-1"></i> Thêm vùng nhấp
                                                                  </button>
                                                              </div>
                                                              
                                                              <!-- List of hotspots -->
                                                              <template x-for="(hs, hIdx) in (block.hotspots || [])" :key="hIdx">
                                                                  <div class="border p-2 rounded mb-2 bg-light shadow-xs" style="border-left: 4px solid #4e73df !important;">
                                                                      <div class="d-flex align-items-center justify-content-between mb-2">
                                                                          <span class="badge badge-primary" x-text="`Vùng nhấp ${hIdx + 1}`"></span>
                                                                          <button type="button" class="btn btn-sm btn-link text-danger p-0" @click="block.hotspots.splice(hIdx, 1)">
                                                                              Xóa vùng
                                                                          </button>
                                                                      </div>
                                                                      <div class="form-group mb-2">
                                                                          <input type="text" x-model="hs.link" class="form-control form-control-sm" placeholder="Link liên kết vùng này (ví dụ: #thu-cu-doi-moi)">
                                                                      </div>
                                                                      <div class="row">
                                                                          <div class="col-6 mb-1">
                                                                              <span class="small text-muted d-block" x-text="`Cách mép trên: ${hs.top}%`"></span>
                                                                              <input type="range" x-model="hs.top" min="0" max="100" class="form-control-range">
                                                                          </div>
                                                                          <div class="col-6 mb-1">
                                                                              <span class="small text-muted d-block" x-text="`Cách mép trái: ${hs.left}%`"></span>
                                                                              <input type="range" x-model="hs.left" min="0" max="100" class="form-control-range">
                                                                          </div>
                                                                          <div class="col-6 mb-1">
                                                                              <span class="small text-muted d-block" x-text="`Chiều rộng: ${hs.width}%`"></span>
                                                                              <input type="range" x-model="hs.width" min="1" max="100" class="form-control-range">
                                                                          </div>
                                                                          <div class="col-6 mb-1">
                                                                              <span class="small text-muted d-block" x-text="`Chiều cao: ${hs.height}%`"></span>
                                                                              <input type="range" x-model="hs.height" min="1" max="100" class="form-control-range">
                                                                          </div>
                                                                      </div>
                                                                  </div>
                                                              </template>
                                                              
                                                              <!-- Live hotspot editor overlay -->
                                                              <div class="mt-3 text-center border p-2 bg-light rounded" x-show="block.url" style="overflow: hidden;">
                                                                  <span class="small text-muted d-block mb-2 font-weight-bold text-primary"><i class="fa-solid fa-circle-info mr-1"></i> Mẹo: Bạn có thể kéo thả trực tiếp vùng đỏ trên hình ảnh để dịch chuyển vị trí, hoặc kéo chấm tròn đỏ ở góc dưới bên phải để co giãn vùng nhấp!</span>
                                                                  <div style="text-align: center; width: 100%;">
                                                                      <div style="position: relative; display: inline-block; max-width: 100%; cursor: crosshair;" 
                                                                           id="hotspot-editor-container"
                                                                           x-on:mousedown="initDrawHotspot($event, block)"
                                                                           x-on:touchstart="initDrawHotspot($event, block)">
                                                                          <img :src="block.url" style="max-height: 350px; max-width: 100%; height: auto; display: block; margin: 0 auto; user-select: none; pointer-events: none; border-radius: 4px;">
                                                                          <template x-for="(hs, hIdx) in (block.hotspots || [])" :key="'ov-'+hIdx">
                                                                              <div style="position: absolute; border: 2px dashed #ff0000; background-color: rgba(255, 0, 0, 0.3); cursor: move; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 11px; font-weight: bold; text-shadow: 1px 1px 2px #000; box-sizing: border-box; user-select: none; z-index: 5;"
                                                                                   :style="`top: ${hs.top}%; left: ${hs.left}%; width: ${hs.width}%; height: ${hs.height}%;`"
                                                                                   x-on:mousedown="startDragHotspot($event, hs, 'move', block)"
                                                                                   x-on:touchstart="startDragHotspot($event, hs, 'move', block)">
                                                                                   <span x-text="`Vùng ${hIdx + 1}`"></span>
                                                                                   <!-- Nút co giãn góc phải dưới -->
                                                                                   <div style="position: absolute; bottom: -4px; right: -4px; width: 10px; height: 10px; background-color: #ff0000; border: 1px solid #fff; border-radius: 50%; cursor: se-resize; z-index: 10;"
                                                                                        x-on:mousedown.stop="startDragHotspot($event, hs, 'resize', block)"
                                                                                        x-on:touchstart.stop="startDragHotspot($event, hs, 'resize', block)">
                                                                                   </div>
                                                                              </div>
                                                                          </template>
                                                                      </div>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                      </template>

                                                     <!-- Khối Slideshow -->
                                                     <template x-if="block.type === 'slideshow'">
                                                         <div>
                                                             <label class="small font-weight-bold text-dark d-block">Danh sách các slide ảnh trình chiếu</label>
                                                             <div class="border p-3 bg-light rounded mb-2">
                                                                 <div class="slide-list mb-3">
                                                                     <template x-for="(slide, sIdx) in (block.slides || [])" :key="sIdx">
                                                                         <div class="border-bottom pb-3 mb-3 last-no-border">
                                                                             <div class="d-flex align-items-center justify-content-between mb-2">
                                                                                 <span class="badge badge-dark" x-text="`Slide số ${sIdx + 1}`"></span>
                                                                                 <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" @click="block.slides.splice(sIdx, 1)">
                                                                                     <i class="fa-solid fa-xmark mr-1"></i> Xóa slide
                                                                                 </button>
                                                                             </div>
                                                                             <div class="row">
                                                                                 <div class="col-md-6 mb-2">
                                                                                     <input type="text" x-model="slide.url" class="form-control form-control-sm" placeholder="URL hình ảnh slide">
                                                                                 </div>
                                                                                 <div class="col-md-6 mb-2">
                                                                                     <input type="text" x-model="slide.link" class="form-control form-control-sm" placeholder="Link liên kết khi nhấp ảnh (Tùy chọn)">
                                                                                 </div>
                                                                             </div>
                                                                             <div class="text-center mt-2 bg-white border p-1 rounded" x-show="slide.url" style="display: none;">
                                                                                 <img :src="slide.url" class="img-thumbnail" style="max-height: 80px;" x-on:error="$el.style.display='none'" x-on:load="$el.style.display='inline-block'">
                                                                             </div>
                                                                         </div>
                                                                     </template>
                                                                 </div>
                                                                 <button type="button" class="btn btn-sm btn-primary shadow-xs" @click="if(!block.slides) block.slides=[]; block.slides.push({url:'', link:''})">
                                                                     <i class="fa-solid fa-plus mr-1"></i> Thêm slide ảnh mới
                                                                 </button>
                                                             </div>
                                                         </div>
                                                     </template>

                                                      <!-- Khối sản phẩm -->
                                                      <template x-if="block.type === 'product'">
                                                          <div>
                                                              <label class="small font-weight-bold text-dark d-block">Danh sách sản phẩm trưng bày (Hỗ trợ thêm nhiều sản phẩm làm Tab)</label>
                                                              <div class="border p-3 bg-light rounded mb-2">
                                                                  <div class="selected-product-list mb-3">
                                                                      <template x-for="(pItem, pIdx) in (block.products || [])" :key="pIdx">
                                                                          <div class="border-bottom pb-2 mb-2 last-no-border">
                                                                              <div class="d-flex align-items-center justify-content-between mb-1">
                                                                                  <span class="badge badge-dark" x-text="`Sản phẩm số ${pIdx + 1}`"></span>
                                                                                  <button type="button" class="btn btn-sm btn-link text-danger p-0" @click="block.products.splice(pIdx, 1)">
                                                                                      <i class="fa-solid fa-xmark"></i> Xóa
                                                                                  </button>
                                                                              </div>
                                                                              <select x-model="pItem.product_id" class="form-control form-control-sm">
                                                                                  <option value="">-- Click chọn sản phẩm --</option>
                                                                                  @foreach ($products as $prod)
                                                                                      <option value="{{ $prod->id }}">{{ $prod->title }} ({{ number_format($prod->price) }} đ)</option>
                                                                                  @endforeach
                                                                              </select>
                                                                          </div>
                                                                      </template>
                                                                  </div>
                                                                  <button type="button" class="btn btn-sm btn-primary shadow-xs" @click="if(!block.products) block.products=[]; block.products.push({product_id:''})">
                                                                      <i class="fa-solid fa-plus mr-1"></i> Thêm sản phẩm vào nhóm
                                                                  </button>
                                                              </div>
                                                          </div>
                                                      </template>
                                                     
                                                     <!-- Khối FAQ -->
                                                     <template x-if="block.type === 'faq'">
                                                         <div>
                                                             <div class="form-group mb-2">
                                                                 <label class="small font-weight-bold text-dark">Câu hỏi</label>
                                                                 <input type="text" x-model="block.question" class="form-control" placeholder="Nhập câu hỏi (ví dụ: Chính sách giao hàng của shop?)">
                                                             </div>
                                                             <div class="form-group mb-0">
                                                                 <label class="small font-weight-bold text-dark">Câu trả lời</label>
                                                                 <textarea x-model="block.answer" class="form-control" rows="3" placeholder="Nhập nội dung câu trả lời..."></textarea>
                                                             </div>
                                                         </div>
                                                     </template>
                                                     
                                                     <!-- Khối Banner -->
                                                     <template x-if="block.type === 'banner'">
                                                         <div>
                                                             <label class="small font-weight-bold text-dark">Nội dung thông điệp hiển thị</label>
                                                             <input type="text" x-model="block.content" class="form-control" placeholder="Nhập nội dung banner khuyến mãi...">
                                                         </div>
                                                     </template>
                                                     
                                                     <!-- Khối Code tùy chỉnh -->
                                                     <template x-if="block.type === 'code'">
                                                         <div>
                                                             <label class="small font-weight-bold text-dark">Đoạn mã HTML/CSS/JS (Rất hữu ích chèn Bản đồ Google, Video Youtube, script chat...)</label>
                                                             <textarea x-model="block.code" class="form-control font-monospace bg-dark text-light p-3 rounded" rows="8" placeholder="<!-- Nhập code HTML/CSS/JS của bạn -->" style="font-size: 13px; line-height: 1.5;"></textarea>
                                                         </div>
                                                     </template>
                                                 </div>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <!-- Xem trước Landing Page -->
                                    <div x-show="showPreview" class="border p-4 bg-white rounded mt-4 shadow-inner" x-transition>
                                        <h5 class="mb-3 text-secondary font-weight-bold"><i class="fa-solid fa-eye mr-2"></i> Xem trước trang thiết kế</h5>
                                        <div class="p-4 border rounded bg-white shadow-sm" style="min-height: 200px;">
                                            <template x-for="(block, index) in blocks" :key="'prev-'+index">
                                                <div class="mb-4 pb-4 border-bottom last-no-border">
                                                    <template x-if="block.type === 'text'">
                                                        <div class="ckeditor-content" x-html="block.content || '<p class=\'text-muted small\'>[Khối văn bản trống]</p>'"></div>
                                                    </template>
                                                    
                                                    <template x-if="block.type === 'image'">
                                                        <div class="text-center">
                                                            <img :src="block.url || 'https://via.placeholder.com/800x400?text=Chua+nhap+URL+anh'" class="img-fluid rounded shadow-sm" style="max-height: 350px;">
                                                        </div>
                                                    </template>

                                                    <template x-if="block.type === 'slideshow'">
                                                        <div class="border rounded p-2 bg-light text-center">
                                                            <i class="fa-solid fa-images fa-2x text-secondary mb-2"></i>
                                                            <h6 class="font-weight-bold text-dark mb-1">Khối Slideshow (Trình chiếu)</h6>
                                                            <span class="text-muted small" x-text="`${(block.slides || []).length} slides ảnh thiết lập.`"></span>
                                                        </div>
                                                    </template>

                                                    <template x-if="block.type === 'product'">
                                                        <div class="border rounded p-3 bg-light d-flex align-items-center">
                                                            <i class="fa-solid fa-basket-shopping fa-2x text-pink mr-3"></i>
                                                            <div>
                                                                <h6 class="font-weight-bold text-dark mb-1">Khối trưng bày sản phẩm</h6>
                                                                <span class="text-muted small" x-text="block.product_id ? `Đã chọn sản phẩm ID: ${block.product_id}` : 'Chưa cấu hình chọn sản phẩm.'"></span>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    
                                                    <template x-if="block.type === 'faq'">
                                                        <div class="p-3 bg-light rounded border-left-primary" style="border-left: 4px solid #4e73df !important;">
                                                            <h6 class="font-weight-bold text-dark mb-1" x-text="'❓ ' + (block.question || 'Câu hỏi chưa nhập?')"></h6>
                                                            <p class="mb-0 text-secondary small" x-text="block.answer || 'Câu trả lời chưa nhập.'"></p>
                                                        </div>
                                                    </template>
                                                    
                                                    <template x-if="block.type === 'banner'">
                                                        <div class="bg-primary text-white p-4 rounded text-center shadow-sm bg-gradient-primary">
                                                            <h5 class="mb-0 font-weight-bold" x-text="block.content || 'Nội dung banner khuyến mãi'"></h5>
                                                        </div>
                                                    </template>
                                                    
                                                    <template x-if="block.type === 'code'">
                                                        <div class="p-2 bg-dark text-light rounded font-monospace small" style="font-size: 11px;">
                                                            <span class="text-warning">&lt;!-- Custom Code Block --&gt;</span>
                                                            <div x-text="block.code ? (block.code.substring(0, 100) + '...') : 'Chưa nhập mã HTML'"></div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="content_json" :value="JSON.stringify(blocks)">
                </div>

                <div class="text-right">
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary px-4 mr-2">Hủy bỏ</a>
                    <button class="btn btn-primary px-5 shadow-sm">Lưu trang</button>
                </div>
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

            // Đảm bảo có các thuộc tính phụ trợ
            blocksData = blocksData.map(block => ({
                collapsed: false,
                content: block.content || '',
                url: block.url || '',
                link: block.link || '',
                question: block.question || '',
                answer: block.answer || '',
                code: block.code || '',
                product_id: block.product_id || '',
                slides: block.slides || [],
                products: block.products || (block.product_id ? [{product_id: block.product_id}] : []),
                hotspots: block.hotspots || [],
                ...block
            }));

            return {
                blocks: blocksData,
                draggedIndex: null,
                dragEnabled: false,
                showPreview: false,

                startDragHotspot(e, hs, action, block) {
                    e.preventDefault();
                    const isTouch = e.type.startsWith('touch');
                    const startX = isTouch ? e.touches[0].clientX : e.clientX;
                    const startY = isTouch ? e.touches[0].clientY : e.clientY;
                    
                    const container = e.currentTarget.closest('#hotspot-editor-container') || e.target.closest('#hotspot-editor-container');
                    if (!container) return;
                    const rect = container.getBoundingClientRect();
                    const containerWidth = rect.width;
                    const containerHeight = rect.height;
                    
                    const initialLeft = parseFloat(hs.left) || 0;
                    const initialTop = parseFloat(hs.top) || 0;
                    const initialWidth = parseFloat(hs.width) || 10;
                    const initialHeight = parseFloat(hs.height) || 10;
                    
                    const onMouseMove = (moveEvent) => {
                        const currentX = isTouch ? moveEvent.touches[0].clientX : moveEvent.clientX;
                        const currentY = isTouch ? moveEvent.touches[0].clientY : moveEvent.clientY;
                        
                        const deltaX = currentX - startX;
                        const deltaY = currentY - startY;
                        
                        const deltaLeftPct = (deltaX / containerWidth) * 100;
                        const deltaTopPct = (deltaY / containerHeight) * 100;
                        
                        if (action === 'move') {
                            let newLeft = initialLeft + deltaLeftPct;
                            let newTop = initialTop + deltaTopPct;
                            
                            hs.left = Math.min(Math.max(0, Math.round(newLeft)), 100 - (parseFloat(hs.width) || 0));
                            hs.top = Math.min(Math.max(0, Math.round(newTop)), 100 - (parseFloat(hs.height) || 0));
                        } else if (action === 'resize') {
                            let newWidth = initialWidth + deltaLeftPct;
                            let newHeight = initialHeight + deltaTopPct;
                            
                            hs.width = Math.min(Math.max(5, Math.round(newWidth)), 100 - (parseFloat(hs.left) || 0));
                            hs.height = Math.min(Math.max(3, Math.round(newHeight)), 100 - (parseFloat(hs.top) || 0));
                        }
                    };
                    
                    const onMouseUp = () => {
                        document.removeEventListener(isTouch ? 'touchmove' : 'mousemove', onMouseMove);
                        document.removeEventListener(isTouch ? 'touchend' : 'mouseup', onMouseUp);
                    };
                    
                    document.addEventListener(isTouch ? 'touchmove' : 'mousemove', onMouseMove);
                    document.addEventListener(isTouch ? 'touchend' : 'mouseup', onMouseUp);
                },

                initDrawHotspot(e, block) {
                    e.preventDefault();
                    const container = e.currentTarget;
                    const rect = container.getBoundingClientRect();
                    const containerWidth = rect.width;
                    const containerHeight = rect.height;
                    
                    const isTouch = e.type.startsWith('touch');
                    const clientX = isTouch ? e.touches[0].clientX : e.clientX;
                    const clientY = isTouch ? e.touches[0].clientY : e.clientY;
                    
                    const startX = clientX - rect.left;
                    const startY = clientY - rect.top;
                    
                    const startLeftPct = Math.round((startX / containerWidth) * 100);
                    const startTopPct = Math.round((startY / containerHeight) * 100);
                    
                    if (!block.hotspots) block.hotspots = [];
                    
                    // Thêm phần tử thô
                    block.hotspots.push({
                        top: startTopPct,
                        left: startLeftPct,
                        width: 1,
                        height: 1,
                        link: '#thu-cu-doi-moi'
                    });
                    
                    // Lấy ra proxy phản ứng từ Alpine để cập nhật giao diện thời gian thực
                    const newHsIndex = block.hotspots.length - 1;
                    const newHs = block.hotspots[newHsIndex];
                    
                    const onMouseMove = (moveEvent) => {
                        const curClientX = isTouch ? moveEvent.touches[0].clientX : moveEvent.clientX;
                        const curClientY = isTouch ? moveEvent.touches[0].clientY : moveEvent.clientY;
                        
                        const currentX = curClientX - rect.left;
                        const currentY = curClientY - rect.top;
                        
                        const currentLeftPct = Math.round((currentX / containerWidth) * 100);
                        const currentTopPct = Math.round((currentY / containerHeight) * 100);
                        
                        const left = Math.min(startLeftPct, currentLeftPct);
                        const top = Math.min(startTopPct, currentTopPct);
                        const width = Math.abs(currentLeftPct - startLeftPct);
                        const height = Math.abs(currentTopPct - startTopPct);
                        
                        newHs.left = Math.min(Math.max(0, left), 100);
                        newHs.top = Math.min(Math.max(0, top), 100);
                        newHs.width = Math.min(width, 100 - newHs.left);
                        newHs.height = Math.min(height, 100 - newHs.top);
                    };
                    
                    const onMouseUp = () => {
                        document.removeEventListener(isTouch ? 'touchmove' : 'mousemove', onMouseMove);
                        document.removeEventListener(isTouch ? 'touchend' : 'mouseup', onMouseUp);
                        
                        if (newHs.width < 2 || newHs.height < 2) {
                            block.hotspots.splice(newHsIndex, 1);
                        }
                    };
                    
                    document.addEventListener(isTouch ? 'touchmove' : 'mousemove', onMouseMove);
                    document.addEventListener(isTouch ? 'touchend' : 'mouseup', onMouseUp);
                },

                addBlock(type) {
                    this.blocks.push({
                        type: type,
                        content: '',
                        url: '',
                        link: '',
                        question: '',
                        answer: '',
                        code: '',
                        product_id: '',
                        slides: type === 'slideshow' ? [{url: '', link: ''}] : [],
                        products: type === 'product' ? [{product_id: ''}] : [],
                        hotspots: [],
                        collapsed: false
                    });
                },

                removeBlock(index) {
                    if (confirm('Bạn có chắc chắn muốn xóa khối này?')) {
                        this.blocks.splice(index, 1);
                    }
                },

                duplicateBlock(index) {
                    const blockToCopy = this.blocks[index];
                    const copiedBlock = JSON.parse(JSON.stringify(blockToCopy));
                    copiedBlock.collapsed = false;
                    this.blocks.splice(index + 1, 0, copiedBlock);
                },

                moveUp(index) {
                    if (index > 0) {
                        this.swapBlocks(index, index - 1);
                    }
                },

                moveDown(index) {
                    if (index < this.blocks.length - 1) {
                        this.swapBlocks(index, index + 1);
                    }
                },

                swapBlocks(from, to) {
                    if (from === null || from === to || from < 0 || to < 0 || from >= this.blocks.length || to >= this.blocks.length) return;
                    const temp = this.blocks[from];
                    this.blocks.splice(from, 1);
                    this.blocks.splice(to, 0, temp);
                },

                getBlockName(type) {
                    const names = {
                        text: 'Khối Văn bản (Rich Text)',
                        image: 'Khối Hình ảnh',
                        faq: 'Câu hỏi thường gặp (FAQ)',
                        banner: 'Banner Khuyến mãi',
                        code: 'Mã HTML tùy chỉnh',
                        slideshow: 'Khối Slideshow (Trình chiếu)',
                        product: 'Khối sản phẩm trưng bày'
                    };
                    return names[type] || 'Khối không xác định';
                },

                getBlockIcon(type) {
                    const icons = {
                        text: 'fa-paragraph text-primary',
                        image: 'fa-image text-success',
                        faq: 'fa-circle-question text-warning',
                        banner: 'fa-bullhorn text-info',
                        code: 'fa-code text-danger',
                        slideshow: 'fa-images text-secondary',
                        product: 'fa-basket-shopping text-pink'
                    };
                    return icons[type] || 'fa-cubes';
                },

                getHeaderClass(type) {
                    const classes = {
                        text: 'border-left-primary',
                        image: 'border-left-success',
                        faq: 'border-left-warning',
                        banner: 'border-left-info',
                        code: 'border-left-danger',
                        slideshow: 'border-left-secondary',
                        product: 'border-left-pink'
                    };
                    return classes[type] || '';
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
