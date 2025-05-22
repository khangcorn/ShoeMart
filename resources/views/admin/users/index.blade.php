@extends('admin.layout')

@section('content')
    {{-- Thông báo thành công --}}
    @if(session('success'))
        <div id="success-message" class="bg-green-100 text-green-800 p-3 rounded mb-4 transition-opacity duration-500">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(function () {
                const msg = document.getElementById('success-message');
                if (msg) {
                    msg.style.opacity = '0';
                    setTimeout(() => msg.remove(), 500);
                }
            }, 5000);
        </script>
    @endif

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold">Danh sách tài khoản nhân viên do Admin tạo</h1>
        <a href="{{ route('admin.users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Thêm tài khoản mới</a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 shadow-md rounded">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="py-2 px-4 border-b">ID</th>
                    <th class="py-2 px-4 border-b">Tên</th>
                    <th class="py-2 px-4 border-b">Email</th>
                    <th class="py-2 px-4 border-b">Ngày tạo</th>
                    <th class="py-2 px-4 border-b text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-4 border-b text-center">{{ $user->user_id }}</td>
                        <td class="py-2 px-4 border-b">{{ $user->username }}</td>
                        <td class="py-2 px-4 border-b">{{ $user->email }}</td>
                        <td class="py-2 px-4 border-b text-center">{{ $user->created_at->format('d-m-Y') }}</td>
                        <td class="py-2 px-4 border-b text-center">
                            <div class="flex gap-2 justify-center flex-wrap">
                                <button
                                    onclick="openPermissionModal({{ $user->user_id }}, '{{ $user->username }}')"
                                    class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 text-sm">
                                    Phân quyền
                                </button>
                                <form action="{{ route('admin.users.block', $user) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $user->is_blocked ? 'btn-success' : 'btn-danger' }}"
                                        onclick="return confirm('Bạn có chắc muốn {{ $user->is_blocked ? 'mở khóa' : 'khóa' }} tài khoản này?')">
                                        {{ $user->is_blocked ? 'Mở khóa' : 'Khóa' }}
                                    </button>
                                </form>



                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500">Không có người dùng nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- Modal phân quyền --}}
<div id="permission-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-start z-50 p-4">
    <div class="bg-white w-full max-w-md p-6 rounded shadow-lg max-h-[80vh] overflow-y-auto">
     <h2 class="text-lg font-bold text-gray-900 mb-4" id="modal-title">Phân quyền</h2>

        <form id="permission-form" method="POST" action="">
            @csrf
            <div class="grid grid-cols-2 gap-3 max-h-64 overflow-y-auto">
        @foreach($permissions as $permission)
            <label class="flex items-center gap-2 text-gray-900 font-semibold">
                <input type="checkbox" name="permissions[]" value="{{ $permission->permission_id }}" class="permission-checkbox">
                {{ $permission->name }}
            </label>
        @endforeach
    </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closePermissionModal()" class="px-3 py-1 border rounded">Hủy</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('permission-modal');
    const form = document.getElementById('permission-form');
    const checkboxes = modal.querySelectorAll('.permission-checkbox');
    let currentUserId = null;

    function openPermissionModal(userId, username) {
        currentUserId = userId;
        document.getElementById('modal-title').innerText = 'Phân quyền cho ' + username;

        // Gửi AJAX để lấy quyền hiện có
        fetch(`/admin/admin-users/${userId}/permissions/json`)
            .then(response => response.json())
            .then(data => {
                checkboxes.forEach(cb => {
                    cb.checked = data.includes(parseInt(cb.value));
                });
            });

        form.action = `/admin/admin-users/${userId}/permissions`;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closePermissionModal() {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
</script>
@endsection



