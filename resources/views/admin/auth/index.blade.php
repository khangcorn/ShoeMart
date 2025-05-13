@extends('admin.layout')

@section('content')
<div class="py-6 px-4">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
        <h2 class="text-xl font-bold text-gray-800">Danh sách người dùng</h2>
    </div>

    {{-- User Table --}}
    <div class="overflow-x-auto bg-white shadow-lg rounded-xl">
        <table class="w-full table-auto text-sm text-gray-700">
            <thead class="bg-gray-100 text-gray-800 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 border text-center">#</th>
                    <th class="px-4 py-3 border text-left">Email</th>
                    <th class="px-4 py-3 border text-left">Contact</th>
                    <th class="px-4 py-3 border text-center">Số dư ví</th>
                    <th class="px-4 py-3 border text-center">Avatar</th>
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
                            <button 
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-full text-xs font-semibold transition"
                                @if (!auth()->user()->hasPermission('lock_user')) 
                                    onclick="alert('Bạn không có quyền khóa tài khoản!');" 
                                    disabled 
                                @else
                                    onclick="lockAccountFunction()"
                                @endif
                            >
                                Khóa tài khoản
                            </button>
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
@endsection
