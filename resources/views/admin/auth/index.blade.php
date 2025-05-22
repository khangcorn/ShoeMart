@extends('admin.layout')

@section('content')
<div class="py-4 px-4">
    <h1 class="text-2xl font-bold mb-4">Quản lý tài khoản</h1>

 
        <table class="w-full ">
            <thead>
                <tr>
                    <th class="px-2 py-5 border border-gray-300 text-center font-semibold">#</th>
                    <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Email</th>
                    <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Contact</th>
                    <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Số dư ví</th>
                    <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Avatar</th>
                    <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr >
                    <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">{{ $user['id'] }}</td>
                    <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">{{ $user['email'] }}</td>
                    <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">{{ $user['contact'] }}</td>
                    <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center text-green-600">{{ number_format($user['balance']) }} đ</td>
                    <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center justify-center">
                        <img src="{{ asset('storage/avatars/' . $user['avatar']) }}"
                             class="w-12 h-12 rounded-full mx-auto object-cover border shadow-sm"
                             alt="Avatar">
                    </td>
                    <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">
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
@endsection
