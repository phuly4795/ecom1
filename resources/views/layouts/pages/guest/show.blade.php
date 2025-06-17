<x-guest-layout>
    @section('title', $page->title);
    <div class="container">
        <h1 class="mb-5"></h1>

        @foreach ($page->content_json as $block)
            @switch($block['type'])
                @case('text')
                    <div class="ckeditor-content">
                        {!! $block['content'] !!}
                    </div>
                    {{-- <div class="mb-3">{!! $block['content'] ?? '' !!}</div> --}}
                @break

                @case('image')
                    <div class="mb-3">
                        <img src="{{ $block['url'] ?? '#' }}" alt="" class="img-fluid " style="border-radius: 10px">
                    </div>
                @break

                @case('faq')
                    <div class="mb-3">
                        <strong>Câu hỏi:</strong> {{ $block['question'] ?? '' }}<br>
                        <strong>Trả lời:</strong> {{ $block['answer'] ?? '' }}
                    </div>
                @break

                @case('banner')
                    <div class="banner-block bg-primary text-white p-4 mb-3">
                        {{ $block['content'] ?? '' }}
                    </div>
                @break

                @case('code')
                    {!! $block['code'] ?? '' !!}
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Thiết lập CSRF cho mọi request AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Bắt sự kiện submit form
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
