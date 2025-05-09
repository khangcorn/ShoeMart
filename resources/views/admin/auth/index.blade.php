@extends('admin.layout')

@section('content')

<div class="py-4 px-4">
    <div  class="flex  items-center justify-between">

    </div>
    <div class="py-2">

    </div>
    <table class=" w-full">
        <thead>
            <tr>
                <th class="px-2 py-5 border border-gray-300 dark:border-gray-700 items-center text-center font-semibold">#</th>
                <th class="px-2 py-5 border border-gray-300 dark:border-gray-700 items-center text-center font-semibold">Email</th>
                <th class="px-2 py-5 border border-gray-300 dark:border-gray-700 items-center text-center font-semibold">Contact</th>
                <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Số dư ví</th>
                <th class="px-2 py-5 border border-gray-300 dark:border-gray-700 items-center text-center font-semibold">Avatar</th>
                <th class="px-2 py-5 border border-gray-300 dark:border-gray-700 items-center text-center font-semibold">Action</th>
                
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user['id'] }}</td>
                <td>{{ $user['email'] }}</td>
                <td>{{ $user['contact'] }}</td>
                <td class="text-center">{{ number_format($user['balance']) }} đ</td>
                <td><img src="{{asset('storage/avatars/' . $user['avatar'])}}" width="100" alt="" srcset=""></td>


             
                <td> <button class="bg-red-500 "> Lock User</button></td>

                    
                
            </tr>
      
            @endforeach
        </tbody>
        
    </table>
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>

@endsection
