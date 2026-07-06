<x-guest-layout>
    @section('title', $page->title);
    <div class="container">
        <h1 class="mb-5"></h1>

        @foreach ($page->content_json as $block)
            @switch($block['type'])
                @case('text')
                    <div class="ckeditor-content" style="margin-bottom: 20px;">
                        {!! $block['content'] !!}
                    </div>
                @break

                @case('image')
                    <div style="margin-bottom: 20px; text-align: center;">
                        <div style="position: relative; display: inline-block; max-width: 100%;">
                            <img src="{{ $block['url'] ?? '#' }}" alt="" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); display: block;">
                            
                            <!-- Vùng nhấp Hotspots -->
                            @if (!empty($block['hotspots']))
                                @foreach ($block['hotspots'] as $hs)
                                    <a href="{{ $hs['link'] ?? '#' }}" style="position: absolute; top: {{ $hs['top'] }}%; left: {{ $hs['left'] }}%; width: {{ $hs['width'] }}%; height: {{ $hs['height'] }}%; display: block; background-color: transparent;" title="Xem chi tiết"></a>
                                @endforeach
                            @elseif (!empty($block['link']))
                                <a href="{{ $block['link'] }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: block; background-color: transparent;"></a>
                            @endif
                        </div>
                    </div>
                @break

                @case('faq')
                    <div style="padding: 20px; background-color: #f8f9fc; border-left: 5px solid #4e73df; border-radius: 4px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                        <h4 style="font-size: 16px; font-weight: bold; color: #333333; margin-top: 0; margin-bottom: 8px; line-height: 1.4;">
                            ❓ {{ $block['question'] ?? '' }}
                        </h4>
                        <p style="font-size: 14px; color: #666666; margin: 0; line-height: 1.6;">
                            {{ $block['answer'] ?? '' }}
                        </p>
                    </div>
                @break

                @case('banner')
                    <div style="padding: 30px 20px; background: linear-gradient(180deg, #4e73df 10%, #224abe 100%) !important; color: #ffffff !important; border-radius: 6px; text-align: center; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <h3 style="margin: 0; font-weight: bold; color: #ffffff !important; font-size: 24px; line-height: 1.2;">
                            {{ $block['content'] ?? '' }}
                        </h3>
                    </div>
                @break

                @case('slideshow')
                    @if(!empty($block['slides']))
                        @php
                            $carouselId = 'carousel-' . Str::random(8);
                        @endphp
                        <div class="custom-slideshow-container" data-id="{{ $carouselId }}" style="position: relative; max-width: 100%; margin: auto; overflow: hidden; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 25px;">
                            <div class="custom-slides-wrapper" style="display: flex; transition: transform 0.5s ease-in-out; width: 100%;">
                                @foreach ($block['slides'] as $slide)
                                    <div class="custom-slide" style="min-width: 100%; box-sizing: border-box; text-align: center; background-color: #f8f9fc;">
                                        @if (!empty($slide['link']))
                                            <a href="{{ $slide['link'] }}" target="_blank">
                                                <img src="{{ $slide['url'] }}" style="width: 100%; height: auto; max-height: 450px; object-fit: cover; vertical-align: middle;">
                                            </a>
                                        @else
                                            <img src="{{ $slide['url'] }}" style="width: 100%; height: auto; max-height: 450px; object-fit: cover; vertical-align: middle;">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <!-- Buttons -->
                            <button type="button" class="prev-slide" onclick="moveSlide(this, -1)" style="position: absolute; top: 50%; left: 15px; transform: translateY(-50%); background-color: rgba(0,0,0,0.5); color: white; border: none; width: 40px; height: 40px; cursor: pointer; border-radius: 50%; font-size: 18px; z-index: 10; display: flex; align-items: center; justify-content: center; outline: none;">&#10094;</button>
                            <button type="button" class="next-slide" onclick="moveSlide(this, 1)" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); background-color: rgba(0,0,0,0.5); color: white; border: none; width: 40px; height: 40px; cursor: pointer; border-radius: 50%; font-size: 18px; z-index: 10; display: flex; align-items: center; justify-content: center; outline: none;">&#10095;</button>
                        </div>
                    @endif
                @break

                @case('product')
                    @php
                        $productsInBlock = [];
                        if (!empty($block['products'])) {
                            foreach ($block['products'] as $pItem) {
                                if (!empty($pItem['product_id'])) {
                                    $pModel = \App\Models\Product::with(['productImages', 'productVariants'])->find($pItem['product_id']);
                                    if ($pModel) {
                                        $productsInBlock[] = $pModel;
                                    }
                                }
                            }
                        } elseif (!empty($block['product_id'])) {
                            $pModel = \App\Models\Product::with(['productImages', 'productVariants'])->find($block['product_id']);
                            if ($pModel) {
                                $productsInBlock[] = $pModel;
                            }
                        }
                        $blockIndex = 'block-' . Str::random(8);
                    @endphp
                    
                    @if (!empty($productsInBlock))
                        <div class="product-showcase-container" style="background-color: #ffffff; border: 1px solid #e4e7ed; border-radius: 8px; padding: 25px; margin-bottom: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                            <!-- Tab list -->
                            @if (count($productsInBlock) > 1)
                                <div class="product-showcase-tabs" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 25px; justify-content: center; border-bottom: 1px solid #e4e7ed; padding-bottom: 15px;">
                                    @foreach ($productsInBlock as $pIdx => $prodShow)
                                        <button type="button" class="showcase-tab-btn-{{ $blockIndex }}" data-index="{{ $pIdx }}" onclick="switchShowcaseTab('{{ $blockIndex }}', {{ $pIdx }})" style="padding: 10px 22px; border-radius: 20px; border: 1px solid #e4e7ed; font-weight: bold; font-size: 13px; cursor: pointer; transition: all 0.2s; {{ $pIdx === 0 ? 'background-color: #D10024; color: #fff; border-color: #D10024;' : 'background-color: #fff; color: #333;' }}">
                                            {{ $prodShow->title }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Tab contents -->
                            @foreach ($productsInBlock as $pIdx => $prodShow)
                                <div class="showcase-content-{{ $blockIndex }}" data-index="{{ $pIdx }}" style="display: {{ $pIdx === 0 ? 'flex' : 'none' }}; flex-wrap: wrap; align-items: flex-start; justify-content: space-between;">
                                    <!-- Left side: Image, price, colors, buttons -->
                                    <div style="flex: 1; min-width: 250px; text-align: center; padding: 15px; box-sizing: border-box;">
                                        @php
                                            $imageShow = $prodShow->productImages->where('type', 1)->first()->image ?? '';
                                            $imageShowPath = $imageShow ? asset('storage/' . $imageShow) : asset('asset/img/no-image.png');
                                        @endphp
                                        <a href="{{ route('product.show', $prodShow->slug) }}">
                                            <img src="{{ $imageShowPath }}" style="max-height: 250px; max-width: 100%; object-fit: contain; border-radius: 4px; margin-bottom: 15px;">
                                        </a>
                                        
                                        <!-- Colors -->
                                        @php
                                            $variants = $prodShow->productVariants;
                                            $colors = [];
                                            foreach ($variants as $variant) {
                                                $name = trim($variant->variant_name);
                                                if (!empty($name) && !in_array($name, $colors)) {
                                                    $colors[] = $name;
                                                }
                                            }
                                        @endphp
                                        @if (!empty($colors))
                                            <div style="margin-bottom: 15px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                                                <span style="font-size: 13px; color: #666; margin-right: 5px;">Màu sắc:</span>
                                                @foreach ($colors as $color)
                                                    @php
                                                        $colorLower = mb_strtolower($color);
                                                        $hex = '#cccccc';
                                                        if (str_contains($colorLower, 'cam') || str_contains($colorLower, 'orange')) $hex = '#ff7f0e';
                                                        elseif (str_contains($colorLower, 'tím') || str_contains($colorLower, 'purple') || str_contains($colorLower, 'violet')) $hex = '#9467bd';
                                                        elseif (str_contains($colorLower, 'bạc') || str_contains($colorLower, 'silver')) $hex = '#c0c0c0';
                                                        elseif (str_contains($colorLower, 'trắng') || str_contains($colorLower, 'white')) $hex = '#ffffff';
                                                        elseif (str_contains($colorLower, 'đen') || str_contains($colorLower, 'black')) $hex = '#111111';
                                                        elseif (str_contains($colorLower, 'xanh') || str_contains($colorLower, 'blue')) $hex = '#1f77b4';
                                                        elseif (str_contains($colorLower, 'hồng') || str_contains($colorLower, 'pink')) $hex = '#e377c2';
                                                        elseif (str_contains($colorLower, 'đỏ') || str_contains($colorLower, 'red')) $hex = '#d62728';
                                                        elseif (str_contains($colorLower, 'vàng') || str_contains($colorLower, 'gold')) $hex = '#ffd700';
                                                    @endphp
                                                    <span title="{{ $color }}" style="display: inline-block; width: 16px; height: 16px; border-radius: 50%; background-color: {{ $hex }}; border: 1px solid {{ $hex === '#ffffff' ? '#ddd' : 'transparent' }}; box-shadow: 0 0 0 2px #fff, 0 0 0 3px #ccc;"></span>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div style="margin-bottom: 18px;">
                                            @if ($prodShow->compare_price > $prodShow->price)
                                                <del style="color: #8d99ae; font-size: 15px; margin-right: 10px;">
                                                    {{ number_format($prodShow->compare_price) }} đ
                                                </del>
                                            @endif
                                            <span style="font-size: 24px; font-weight: bold; color: #D10024;">
                                                {{ number_format($prodShow->price) }} đ
                                            </span>
                                        </div>

                                        <!-- Buttons -->
                                        <div style="display: flex; gap: 10px; justify-content: center; margin-top: 15px;">
                                            <a href="{{ route('product.show', $prodShow->slug) }}" style="flex: 1; text-align: center; background-color: #ff7f0e; color: white !important; font-weight: bold; padding: 10px; border-radius: 4px; text-decoration: none; font-size: 12px; line-height: 1.3;">
                                                ĐẶT TRƯỚC<br><span style="font-size: 9px; font-weight: normal;">Cọc 500.000đ</span>
                                            </a>
                                            <a href="{{ route('product.show', $prodShow->slug) }}" style="flex: 1; text-align: center; background-color: #17a2b8; color: white !important; font-weight: bold; padding: 10px; border-radius: 4px; text-decoration: none; font-size: 12px; line-height: 1.3;">
                                                TRẢ CHẬM 0%<br><span style="font-size: 9px; font-weight: normal;">Cọc 500.000đ</span>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Right side: Specifications table -->
                                    <div style="flex: 1.2; min-width: 280px; padding: 15px; box-sizing: border-box;">
                                        <h4 style="font-size: 18px; font-weight: bold; margin-top: 0; margin-bottom: 15px; color: #333; text-align: left;">THÔNG SỐ KỸ THUẬT</h4>
                                        @if (!empty($prodShow->specifications))
                                            <table class="table table-striped" style="width: 100%; margin-bottom: 15px; font-size: 13px; border-collapse: collapse; background-color: transparent;">
                                                <tbody>
                                                    @php $specCount = 0; @endphp
                                                    @foreach ($prodShow->specifications as $group => $items)
                                                        @if (is_array($items))
                                                            @foreach ($items as $label => $val)
                                                                @if ($specCount < 10)
                                                                    <tr style="border-bottom: 1px solid #eee;">
                                                                        <td style="padding: 8px 10px; font-weight: bold; color: #666; width: 40%; text-align: left; background-color: transparent; border-top: none;">{{ $label }}:</td>
                                                                        <td style="padding: 8px 10px; color: #333; text-align: left; background-color: transparent; border-top: none;">{{ $val }}</td>
                                                                    </tr>
                                                                    @php $specCount++; @endphp
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <p class="text-muted small" style="margin-bottom: 15px; text-align: left;">Chưa cập nhật thông số kỹ thuật.</p>
                                        @endif

                                        <a href="{{ route('product.show', $prodShow->slug) }}" style="display: block; text-align: center; color: #4e73df; font-size: 13px; text-decoration: none; border: 1px solid #e4e7ed; padding: 8px; border-radius: 4px; background-color: #f8f9fc; font-weight: bold;">
                                            Xem đầy đủ thông số kỹ thuật
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @break

                @case('code')
                    <div style="margin-bottom: 20px;">
                        {!! $block['code'] ?? '' !!}
                    </div>
                @break
            @endswitch
        @endforeach

    </div>
    <!-- Tab script cho Bootstrap nếu chưa có -->
    <script>
        $(function() {
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                e.target // newly activated tab
                e.relatedTarget // previous active tab
            });
        });
    </script>

    <style>
        /* CKEditor Content Styling */
        .ckeditor-content {
            font-family: inherit;
            font-size: 16px;
            line-height: 1.6;
        }

        .ckeditor-content h1,
        .ckeditor-content h2,
        .ckeditor-content h3 {
            font-weight: bold;
            margin: 1em 0 0.5em;
        }

        .ckeditor-content ul,
        .ckeditor-content ol {
            margin-left: 20px;
            margin-bottom: 1em;
        }

        .ckeditor-content blockquote {
            border-left: 4px solid #ccc;
            padding-left: 1em;
            color: #666;
            font-style: italic;
        }

        .ckeditor-content a {
            color: #007bff;
            text-decoration: underline;
        }

        .ckeditor-content ul,
        .ckeditor-content ol {
            padding-left: 1.5rem;
            margin-bottom: 1em;
        }

        .ckeditor-content ul li,
        .ckeditor-content ol li {
            list-style-type: disc;
            /* hoặc decimal nếu là ol */
            margin-bottom: 0.5em;
        }
    </style>
    <script>
        if (typeof window.slideStates === 'undefined') {
            window.slideStates = {};
        }
        window.moveSlide = function(btn, direction) {
            var container = btn.closest('.custom-slideshow-container');
            var wrapper = container.querySelector('.custom-slides-wrapper');
            var slides = wrapper.querySelectorAll('.custom-slide');
            var totalSlides = slides.length;
            if (totalSlides <= 1) return;
            
            var containerId = container.getAttribute('data-id');
            if (!containerId) {
                containerId = Math.random().toString(36).substring(7);
                container.setAttribute('data-id', containerId);
            }
            
            if (!(containerId in window.slideStates)) {
                window.slideStates[containerId] = 0;
            }
            
            window.slideStates[containerId] += direction;
            if (window.slideStates[containerId] >= totalSlides) {
                window.slideStates[containerId] = 0;
            }
            if (window.slideStates[containerId] < 0) {
                window.slideStates[containerId] = totalSlides - 1;
            }
            
            var percentage = -window.slideStates[containerId] * 100;
            wrapper.style.transform = 'translateX(' + percentage + '%)';
        }
    </script>

    <!-- Trade-in Modal (Thu cũ đổi mới) -->
    <div id="tradeInModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); z-index: 99999; justify-content: center; align-items: center; padding: 15px; box-sizing: border-box;">
        <div style="background-color: #ffffff; width: 100%; max-width: 600px; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.2); position: relative; max-height: 90vh; display: flex; flex-direction: column;">
            <!-- Header -->
            <div style="padding: 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background-color: #f8f9fc;">
                <h4 style="margin: 0; font-weight: bold; color: #333; font-size: 16px;">Thu Cũ Đổi Mới - Lên Đời Siêu Phẩm</h4>
                <button type="button" onclick="closeTradeInModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #999; line-height: 1;">&times;</button>
            </div>
            <!-- Body -->
            <div style="padding: 20px; overflow-y: auto; flex: 1;">
                <div style="margin-bottom: 15px; text-align: left;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #444; font-size: 13px;">Nhập tên sản phẩm bạn đang dùng...</label>
                    <input type="text" id="trade_user_device" class="form-control" placeholder="Ví dụ: iPhone 14 Pro Max 256GB..." style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; height: auto;">
                </div>
                
                <div style="margin-bottom: 15px; text-align: left;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #444; font-size: 13px;">Chọn tình trạng máy hiện tại</label>
                    <select id="trade_device_status" onchange="calculateTradeIn()" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; background-color: #fff; box-sizing: border-box; height: 42px;">
                        <option value="1">Loại 1: Máy đẹp hoạt động bình thường, không ám ố</option>
                        <option value="2">Loại 2: Máy trầy xước nhẹ màn hình hoặc vỏ</option>
                        <option value="3">Loại 3: Máy cấn móp vỏ hoặc trầy xước nặng</option>
                        <option value="4">Loại 4: Hỏng màn hình, nứt vỡ mặt kính, nguồn lên</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 20px; text-align: left;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #444; font-size: 13px;">Chọn sản phẩm muốn đổi mới</label>
                    <select id="trade_target_product" onchange="calculateTradeIn()" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; background-color: #fff; box-sizing: border-box; height: 42px;">
                        @foreach ($products as $prod)
                            <option value="{{ $prod->id }}" data-price="{{ $prod->price }}">{{ $prod->title }} ({{ number_format($prod->price) }}đ)</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Calculation box -->
                <div style="background-color: #fff9f0; border: 1px solid #ffeeba; padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: left;">
                    <h5 style="margin: 0 0 12px 0; font-weight: bold; color: #e65c00; font-size: 14px;">BẢNG TÍNH GIÁ ĐỔI MỚI</h5>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px; color: #555;">
                        <span>Giá niêm yết mới:</span>
                        <strong id="trade_new_price">0đ</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px; color: #555;">
                        <span>Giá trị thu mua máy cũ (ước tính):</span>
                        <strong id="trade_old_value" style="color: #28a745;">-0đ</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px; color: #555;">
                        <span>Trợ giá lên đời thêm:</span>
                        <strong style="color: #28a745;">-1,000,000đ</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 10px; padding-top: 10px; border-top: 1px dashed #ddd; font-size: 15px; color: #333;">
                        <span><strong>Cần thanh toán thêm:</strong></span>
                        <strong id="trade_balance" style="color: #D10024; font-size: 18px;">0đ</strong>
                    </div>
                </div>
                
                <!-- Promo highlights -->
                <div style="margin-bottom: 20px; text-align: left;">
                    <h5 style="margin: 0 0 10px 0; font-weight: bold; color: #ff7f0e; font-size: 13px;">KHUYẾN MÃI ĐI KÈM</h5>
                    <div style="font-size: 12px; color: #555; line-height: 1.6;">
                        <div style="display: flex; align-items: center; margin-bottom: 6px;">
                            <span style="color: #28a745; margin-right: 8px; font-weight: bold;">✔</span> Tặng gói Premium Service+ bảo hành VIP
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 6px;">
                            <span style="color: #28a745; margin-right: 8px; font-weight: bold;">✔</span> Tặng thêm 24 tháng bảo hành mở rộng chính hãng
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 6px;">
                            <span style="color: #28a745; margin-right: 8px; font-weight: bold;">✔</span> Tặng 6 tháng bảo hiểm rơi vỡ màn hình đặc quyền
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 6px;">
                            <span style="color: #28a745; margin-right: 8px; font-weight: bold;">✔</span> Trả góp 0% lãi suất qua thẻ tín dụng hoặc công ty tài chính
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer actions -->
            <div style="padding: 15px; border-top: 1px solid #eee; background-color: #f8f9fc; display: flex; gap: 10px;">
                <button type="button" onclick="submitTradeIn('dat_truoc')" style="flex: 1; padding: 12px; background-color: #ff7f0e; color: white; border: none; font-weight: bold; border-radius: 4px; cursor: pointer; line-height: 1.3; font-size: 13px; outline: none;">
                    ĐẶT TRƯỚC<br><span style="font-size: 10px; font-weight: normal;">Cọc 500.000đ</span>
                </button>
                <button type="button" onclick="submitTradeIn('tra_cham')" style="flex: 1; padding: 12px; background-color: #17a2b8; color: white; border: none; font-weight: bold; border-radius: 4px; cursor: pointer; line-height: 1.3; font-size: 13px; outline: none;">
                    TRẢ CHẬM 0%<br><span style="font-size: 10px; font-weight: normal;">Cọc 500.000đ</span>
                </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Tab switcher for product showcase blocks
        window.switchShowcaseTab = function(blockIndex, activeIdx) {
            var buttons = document.querySelectorAll('.showcase-tab-btn-' + blockIndex);
            buttons.forEach(function(btn) {
                var idx = parseInt(btn.getAttribute('data-index'));
                if (idx === activeIdx) {
                    btn.style.backgroundColor = '#D10024';
                    btn.style.color = '#fff';
                    btn.style.borderColor = '#D10024';
                } else {
                    btn.style.backgroundColor = '#fff';
                    btn.style.color = '#333';
                    btn.style.borderColor = '#e4e7ed';
                }
            });
            
            var contents = document.querySelectorAll('.showcase-content-' + blockIndex);
            contents.forEach(function(content) {
                var idx = parseInt(content.getAttribute('data-index'));
                if (idx === activeIdx) {
                    content.style.display = 'flex';
                } else {
                    content.style.display = 'none';
                }
            });
        }

        // Trade-in Popup Modal logic
        window.openTradeInModal = function() {
            document.getElementById('tradeInModal').style.display = 'flex';
            calculateTradeIn();
        }
        
        window.closeTradeInModal = function() {
            document.getElementById('tradeInModal').style.display = 'none';
        }
        
        window.calculateTradeIn = function() {
            var select = document.getElementById('trade_target_product');
            if (!select) return;
            var option = select.options[select.selectedIndex];
            if (!option) return;
            var newPrice = parseFloat(option.getAttribute('data-price')) || 0;
            
            var status = parseInt(document.getElementById('trade_device_status').value) || 1;
            var pct = 0.45;
            if (status === 2) pct = 0.35;
            else if (status === 3) pct = 0.20;
            else if (status === 4) pct = 0.08;
            
            var oldVal = Math.min(newPrice * pct, 12000000);
            var subsidy = 1000000;
            var balance = Math.max(newPrice - oldVal - subsidy, 0);
            
            document.getElementById('trade_new_price').innerText = newPrice.toLocaleString('vi-VN') + ' đ';
            document.getElementById('trade_old_value').innerText = '-' + oldVal.toLocaleString('vi-VN') + ' đ';
            document.getElementById('trade_balance').innerText = balance.toLocaleString('vi-VN') + ' đ';
        }
        
        window.submitTradeIn = function(type) {
            var device = document.getElementById('trade_user_device').value;
            if (!device) {
                alert('Vui lòng nhập tên thiết bị cũ bạn đang dùng để tính giá.');
                return;
            }
            
            var select = document.getElementById('trade_target_product');
            var prodName = select.options[select.selectedIndex].text;
            var balanceStr = document.getElementById('trade_balance').innerText;
            
            alert('Chúc mừng! Bạn đã đăng ký Thu Cũ Đổi Mới thành công.\nSản phẩm nhận mới: ' + prodName + '\nSố tiền cần trả thêm ước tính: ' + balanceStr + '\nChúng tôi sẽ sớm liên hệ qua điện thoại để hỗ trợ quý khách!');
            closeTradeInModal();
        }

        $(document).ready(function() {
            // Listen for any link or button pointing to #thu-cu-doi-moi
            $(document).on('click', 'a[href="#thu-cu-doi-moi"]', function(e) {
                e.preventDefault();
                openTradeInModal();
            });

            // AJAX setup for forms
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Form contact handler
            $('#contactForm').on('submit', function(e) {
                e.preventDefault();

                const formData = {
                    name: $('#user_name').val(),
                    email: $('#email').val(),
                    content: $('#content').val()
                };

                $.ajax({
                    type: 'POST',
                    url: '/lien-he',
                    data: formData,
                    success: function(response) {
                        $('#contactForm')[0].reset();
                        showAlertModal(response.message, 'success');
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON?.errors;
                        if (errors) {
                            var errorMessage = '';
                            $.each(errors, function(key, value) {
                                errorMessage += value[0] + '\n';
                            });
                            showAlertModal(errorMessage, 'error');
                        } else {
                            showAlertModal('Đã xảy ra lỗi, vui lòng thử lại sau...', 'error');
                        }
                    }
                });
            });
        });
    </script>
</x-guest-layout>
