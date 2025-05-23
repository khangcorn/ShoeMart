@extends('admin.layout')

@section('content')
<div class="py-4 px-4">

    {{-- Header --}}
    <h2 class="text-3xl font-bold mb-6">Danh sách người dùng </h2> 

    {{-- User Table --}}
    <div class="overflow-x-auto bg-white ">
        <table class="w-full ">
            <thead class=" text-gray-800 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 border text-center">#</th>
                    <th class="px-4 py-3 border text-left">Email</th>
                    <th class="px-4 py-3 border text-left">Liên hệ</th>
                    <th class="px-4 py-3 border text-center">Số dư ví</th>
                    <th class="px-4 py-3 border text-center">Ảnh đại diện</th>
                    <th class="px-4 py-3 border text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="hover:bg-gray-50 transition duration-200">
                    <td class="px-4 py-3 border text-center">{{ $user['id'] }}</td>
                    <td class="px-4 py-3 border">{{ $user['email'] }}</td>
                    <td class="px-4 py-3 border">{{ $user['contact'] }}</td>
                    <td class="px-4 py-3 border text-center font-semibold text-green-600">{{ number_format($user['balance']) }} đ</td>
                    <td class="px-4 py-3 border text-center">
                        <img src="{{ asset('storage/avatars/' . $user['avatar']) }}"
                             class="w-12 h-12 rounded-full mx-auto object-cover border shadow-sm"
                             alt="Avatar">
                    </td>
                    <td class="px-4 py-3 border text-center">
    @if(auth()->user()->hasPermission('lock_user'))
        @if($user['is_blocked'])
            <button 
                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-full text-xs font-semibold transition unlock-user-btn"
                data-user-id="{{ $user['id'] }}"
            >
                Mở khóa
            </button>
        @else
            <button 
                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-full text-xs font-semibold transition lock-user-btn"
                data-user-id="{{ $user['id']  }}"
            >
                Khóa tài khoản
            </button>
        @endif
    @endif
</td>






                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="p-4 border-t">
            {{ $users->links() }}
        </div>
    </div>
</div>
<script>
   function sendBlockRequest(userId, action) {
    if (!confirm(`Bạn có chắc chắn muốn ${action === 'block' ? 'khóa' : 'mở khóa'} tài khoản này không?`)) {
        return;
    }

    fetch(`/admin/users/${userId}/${action}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            "Content-Type": "application/json"
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message || 'Thao tác thành công!');
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error("Lỗi:", error);
        alert('Có lỗi xảy ra, vui lòng thử lại.');
    });
}

document.querySelectorAll(".lock-user-btn").forEach(button => {
    button.addEventListener("click", () => {
        const userId = button.dataset.userId;
        sendBlockRequest(userId, 'block');
    });
});

document.querySelectorAll(".unlock-user-btn").forEach(button => {
    button.addEventListener("click", () => {
        const userId = button.dataset.userId;
        sendBlockRequest(userId, 'unblock');
    });
});


</script>
@endsection
