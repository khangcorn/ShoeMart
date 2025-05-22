{{-- @extends('admin.layout')

@section('content')
<style>
    .btn-revoke {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #212529; /* chữ màu tối cho tương phản */
}

.btn-revoke:hover {
    background-color: #e0a800;
    border-color: #d39e00;
}

    .request-container {
        max-width: 960px;
        margin: 40px auto;
        background: #fff;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        color: #000; /* chữ màu đen */
    }

    .request-container h2 {
        font-weight: 600;
        color: #000;
        text-align: center;
        margin-bottom: 30px;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 15px;
        color: #000; /* chữ màu đen */
    }

    .custom-table thead {
        background-color: #dee2e6;
        color: #000; /* tiêu đề bảng chữ đen */
    }

    .custom-table th,
    .custom-table td {
        padding: 14px 12px;
        border: 1px solid #dee2e6;
        text-align: center;
    }

    .custom-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge {
        font-size: 14px;
        padding: 6px 12px;
        border-radius: 20px;
        color: #000; /* badge chữ đen */
        background-color: #e2e3e5; /* nhẹ nhàng */
    }

    .alert {
        text-align: center;
        font-size: 16px;
        color: #000;
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 5px;
        padding: 10px;
    }

    .btn-action {
        margin: 0 2px;
    }
    .btn {
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 6px;
    border: 1px solid transparent;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    cursor: pointer;
    margin: 4px;
    color: #fff;
}

.btn-approve {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-approve:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.btn-reject {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-reject:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

/* Nếu muốn thêm 1 hiệu ứng nhấn */
.btn:active {
    transform: scale(0.97);
}
#error-message {
    display: none;
    background-color: #f8d7da; /* đỏ nhạt */
    color: #721c24;            /* chữ đỏ đậm */
    border: 1px solid #f5c6cb;
    padding: 10px 15px;
    border-radius: 5px;
    margin-top: 10px;
}

</style>


<div class="request-container">
    <h2>Danh sách yêu cầu truy cập Admin</h2>
<div id="error-message" class="alert alert-danger" style="display: none;"></div>


    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="custom-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Người gửi</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
                <tr>
                    <td>{{ $request->id }}</td>
                    <td>{{ $request->user->username ?? 'Không tìm thấy user' }}</td>
                    <td>
                            @if($request->status === 'pending')
                                <span class="badge bg-warning text-dark">Đang chờ</span>
                            @elseif($request->status === 'approved')
                                <span class="badge bg-success">Đã duyệt</span>
                            @elseif($request->status === 'rejected')
                                <span class="badge bg-danger">Từ chối</span>
                            @elseif($request->status === 'revoked')
                                <span class="badge bg-secondary text-dark">Đã hủy</span>
                            @else
                                <span class="badge bg-light text-muted">Không khả dụng</span>
                            @endif
                    </td>
                    <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                   <td>
                        @if($request->status === 'pending')
                            <button class="btn btn-success btn-sm btn-action btn-approve" data-id="{{ $request->id }}">Duyệt</button>
                            <button class="btn btn-danger btn-sm btn-action btn-reject" data-id="{{ $request->id }}">Từ chối</button>
                        @elseif($request->status === 'approved')
                            <em>Đã duyệt</em>
                            
                            <button class="btn btn-warning btn-sm btn-action btn-revoke" data-id="{{ $request->id }}">Hủy quyền truy cập</button>
                       @elseif($request->status === 'rejected')
                            <em>Đã từ chối</em>
                        @elseif($request->status === 'revoked')
                            <em>Đã hủy quyền truy cập</em>
                        @else
                            <em>Không khả dụng</em>
                        @endif
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    setTimeout(() => {
        const flash = document.getElementById('flash-message');
        if (flash) {
            flash.style.display = 'none';
        }
    }, 5000); // 5 giây
     function showError(message) {
    const errorDiv = $('#error-message');
    errorDiv.text(message).fadeIn();

    setTimeout(() => {
        errorDiv.fadeOut();
    }, 5000); // Tự động ẩn sau 5s
}
    $(function() {

    function showError(message) {
        const errorDiv = $('#error-message');
        errorDiv.text(message).css({
            'background-color': 'red',
            'color': 'white',
            'padding': '10px',
            'margin-bottom': '10px',
            'display': 'block'
        }).fadeIn();

        setTimeout(() => {
            errorDiv.fadeOut();
        }, 5000);
    }

    $('.btn-approve').click(function() {
        let id = $(this).data('id');
        if (!confirm('Bạn có chắc muốn duyệt yêu cầu này?')) return;
        $.post("{{ url('admin/requests') }}/" + id + "/approve", {
            _token: "{{ csrf_token() }}"
        }, function(response) {
            let row = $('button[data-id="'+id+'"]').closest('tr');
            row.find('td:nth-child(3)').html('<span class="badge bg-success">Đã duyệt</span>');
            row.find('td:nth-child(5)').html(`
                <em>Đã duyệt</em>
                <button class="btn btn-warning btn-sm btn-action btn-revoke" data-id="${id}">Hủy quyền truy cập</button>
            `);
            alert('Đã duyệt yêu cầu.');
        }).fail(function(jqXHR) {
            let errorMsg = jqXHR.responseJSON?.message || "Có lỗi xảy ra khi duyệt yêu cầu.";
            showError(errorMsg);
        });
    });

    $('.btn-reject').click(function() {
        let id = $(this).data('id');
        if (!confirm('Bạn có chắc muốn từ chối yêu cầu này?')) return;
        $.post("{{ url('admin/requests') }}/" + id + "/reject", {
            _token: "{{ csrf_token() }}"
        }, function(response) {
            let row = $('button[data-id="'+id+'"]').closest('tr');
            row.find('td:nth-child(3)').html('<span class="badge bg-danger">Đã từ chối</span>');
            row.find('td:nth-child(5)').html('<em>Đã từ chối</em>');
            alert('Đã từ chối yêu cầu.');
        }).fail(function(jqXHR) {
            let errorMsg = jqXHR.responseJSON?.message || "Có lỗi xảy ra khi từ chối yêu cầu.";
            showError(errorMsg);
        });
    });

    // Dùng delegated event để xử lý nút btn-revoke được thêm động
    $(document).on('click', '.btn-revoke', function() {
        let id = $(this).data('id');
        if (!confirm('Bạn có chắc muốn hủy quyền truy cập của yêu cầu này?')) return;

        $.post("{{ url('admin/requests') }}/" + id + "/revoke", {
            _token: "{{ csrf_token() }}"
        }, function(response) {
            let row = $('button[data-id="'+id+'"]').closest('tr');
            row.find('td:nth-child(3)').html('<span class="badge bg-secondary text-dark">Đã hủy</span>');
            row.find('td:nth-child(5)').html('<em>Đã hủy quyền truy cập</em>');
            alert('Đã hủy quyền truy cập thành công.');
        }).fail(function(jqXHR) {
            let errorMsg = jqXHR.responseJSON?.message || "Có lỗi xảy ra khi hủy quyền truy cập.";
            showError(errorMsg);
        });
    });
});




</script> --}}