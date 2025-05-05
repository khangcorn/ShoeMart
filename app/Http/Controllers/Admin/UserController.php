<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
}
