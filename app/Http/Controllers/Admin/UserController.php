<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('wallet')->paginate(15)->through(function($user) {
            return [
                'id' => $user->user_id,
                'email' => $user->email,
                'contact' => $user->username . ' | ' . $user->phone,
                'avatar' => $user->avatar,
                'balance' => $user->wallet?->balance ?? 0,
            ];
        });
        
        return view('admin.auth.index', compact('users'));
     
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.auth.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
public function requestAccess()
{
    $userId = auth()->id();
    $hasPendingRequest = AdminRequest::where('user_id', $userId)
                                    ->where('status', 'pending')
                                    ->exists();

    return view('admin.admin_access.no_access', compact('hasPendingRequest'));
}

public function sendRequest()
{
    $userId = auth()->id();

    // Kiểm tra user đã có request đang pending chưa
    $existingRequest = AdminRequest::where('user_id', $userId)
                                   ->where('status', 'pending')
                                   ->first();

    if ($existingRequest) {
        // Nếu đã có yêu cầu pending, quay lại với thông báo
        return redirect()->back()->with('info', 'Bạn đã gửi yêu cầu, vui lòng chờ admin xét duyệt.');
    }

    // Tạo yêu cầu mới nếu chưa có
    AdminRequest::create([
        'user_id' => $userId,
        'status' => 'pending',
    ]);

    return redirect()->back()->with('success', 'Yêu cầu truy cập đã được gửi.');
}

public function viewRequests()
{
    $requests = AdminRequest::with('user')->get();
    return view('admin.admin_access.request_access', compact('requests'));
}

public function approveRequest($id)
{
    if (!auth()->user()->hasPermission('access_request.approve')) {
        return response()->json(['message' => 'Bạn không có quyền duyệt truy cập Admin.'], 403);
    }
    $request = AdminRequest::findOrFail($id);

    if ($request->status !== 'pending') {
        return response()->json(['message' => 'Yêu cầu đã được xử lý'], 400);
    }

    $request->status = 'approved';
    $request->save();

    // Gán role staff (role_id = 3) nếu chưa có
    $user = $request->user;
      if ($user && !$user->roles()->where('user_roles.role_id', 3)->exists()) {
        $user->roles()->attach(3);
    }
    return response()->json(['message' => 'Yêu cầu đã được duyệt'], 200);
}



public function rejectRequest($id)
{
 if (!auth()->user()->hasPermission('access_request.reject')) {
        return response()->json(['message' => 'Bạn không có quyền duyệt truy cập Admin.'], 403);
    }
    $request = AdminRequest::findOrFail($id);
    $request->status = 'rejected';
    $request->save();

       return response()->json(['success' => true]);
}
public function revoke($id)
{

    if (!auth()->user()->hasPermission('access_request.revoke')) {
        return response()->json(['message' => 'Bạn không có quyền hủy truy cập Admin.'], 403);
    }
    $request = AdminRequest::findOrFail($id);

    if ($request->status !== 'approved') {
        return response()->json(['message' => 'Yêu cầu chưa được duyệt nên không thể hủy quyền.'], 400);
    }

    $request->status = 'revoked';
    $request->save();

    $user = $request->user;
    if ($user) {
        // Lấy role staff
        $staffRole = \App\Models\Role::where('name', 'staff')->first();
        if ($staffRole) {
            // Xóa role staff của user này
            $user->roles()->detach($staffRole->id);
        }
    }

    return response()->json(['message' => 'Hủy quyền truy cập thành công.']);
}


}