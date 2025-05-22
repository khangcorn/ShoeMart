<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAccessController extends Controller
{
    public function index()
    {
        $users = User::where('created_by', auth()->id())->get();
        $permissions = Permission::all();
        return view('admin.users.index', compact('users', 'permissions'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'must_change_password' => true,
            'created_by' => auth()->id(),
        ]);

        $user->roles()->attach(3);

        return redirect()->route('admin.users.index')->with('success', 'Tạo tài khoản nhân viên thành công.');
    }

    public function block(User $user)
    {
        // Đảo trạng thái khóa tài khoản
        $user->is_blocked = ! $user->is_blocked;
        $user->save();

        $status = $user->is_blocked ? 'đã bị khóa' : 'đã được mở khóa';

        return redirect()->route('admin.users.index')
            ->with('success', "Tài khoản {$user->username} $status thành công.");
    }
    public function getPermissionsJson($userId)
    {
        $user = User::findOrFail($userId);
        return response()->json($user->permissions->pluck('permission_id'));
    }

   public function updatePermissions(Request $request, $userId)
{
    $user = User::findOrFail($userId);

    // Lấy mảng permissions, lọc bỏ giá trị rỗng hoặc không phải số nguyên
    $permissions = array_filter($request->input('permissions', []), function ($value) {
        return is_numeric($value) && $value > 0;
    });

    $user->permissions()->sync($permissions);

    return redirect()->route('admin.users.index')->with('success', 'Đã cập nhật quyền cho người dùng.');
}

}
