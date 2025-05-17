{{-- // Hiển thị modal thành công Js
showAlertModal('Thao tác thành công!', 'success'); --}}

<div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    @if($status === 'success')
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#28a745" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                    @elseif($status === 'error')
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#dc3545" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                        </svg>
                    @elseif($status === 'warning')
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#ffc107" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                        </svg>
                    @elseif($status === 'info')
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#17a2b8" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                        </svg>
                    @endif
                </div>
                <h4 class="mb-3 text-{{ $status }}">
                    @if($status === 'success') Thành công!
                    @elseif($status === 'error') Lỗi!
                    @elseif($status === 'warning') Cảnh báo!
                    @elseif($status === 'info') Thông tin!
                    @endif
                </h4>
                <p class="mb-4">{{ $content }}</p>
                <button type="button" class="btn btn-{{ $status }}" id="alertModalCloseButton" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Hàm hiển thị modal
    function showAlertModal(content, status = 'success', options = {}) {
        // Cấu hình mặc định
        const defaultOptions = {
            autoClose: false,
            closeTimeout: 3000,
            onClose: null,
            redirectUrl: null
        };
        
        // Merge options
        options = {...defaultOptions, ...options};
        
        // Kiểm tra nếu modal đã tồn tại thì xóa đi
        const existingModal = document.getElementById('alertModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // Tạo modal mới
        const modalHtml = `
            <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center p-5">
                            <div class="mb-4">
                                ${status === 'success' ? 
                                    '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#28a745" class="bi bi-check-circle-fill" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>' : 
                                    status === 'error' ? 
                                    '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#dc3545" class="bi bi-x-circle-fill" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/></svg>' :
                                    status === 'warning' ?
                                    '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#ffc107" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>' :
                                    '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#17a2b8" class="bi bi-info-circle-fill" viewBox="0 0 16 16"><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/></svg>'}
                            </div>
                            <h4 class="mb-3 text-${status}">
                                ${status === 'success' ? 'Thành công!' : 
                                 status === 'error' ? 'Lỗi!' : 
                                 status === 'warning' ? 'Cảnh báo!' : 'Thông tin!'}
                            </h4>
                            <p class="mb-4">${content}</p>
                            <button type="button" class="btn btn-${status}" id="alertModalCloseButton" data-bs-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Khởi tạo modal
        const modalElement = document.getElementById('alertModal');
        const modal = new bootstrap.Modal(modalElement);
        
        // Hiển thị modal
        modal.show();
        
        // Tự động đóng nếu được cấu hình
        if (options.autoClose) {
            setTimeout(() => {
                modal.hide();
            }, options.closeTimeout);
        }
        
        // Xử lý sự kiện khi modal đóng
        modalElement.addEventListener('hidden.bs.modal', function () {
            if (typeof options.onClose === 'function') {
                options.onClose();
            }
            
            if (options.redirectUrl) {
                window.location.href = options.redirectUrl;
            }
            
            // Xóa modal sau khi đóng
            modal.dispose();
            modalElement.remove();
        });
        
        // Đảm bảo nút đóng hoạt động
        document.getElementById('alertModalCloseButton').addEventListener('click', function() {
            modal.hide();
        });
    }
    
    // Tự động hiển thị modal nếu có session flash
    @if(session('status') && session('message'))
        document.addEventListener('DOMContentLoaded', function() {
            showAlertModal(
                "{{ session('message') }}", 
                "{{ session('status') }}",
                {
                    autoClose: true,
                    closeTimeout: 3000,
                    @if(session('redirect'))
                        redirectUrl: "{{ session('redirect') }}"
                    @endif
                }
            );
        });
    @endif
</script>