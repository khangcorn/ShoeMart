<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
class ForgetPassWordController extends Controller
{
    public function showLinkRequest()
    {
        return view('auth.forget-password');
    }
   

    public function sendResetLink( Request $request) 
    {
        $request->validate([
            'email'=>'required|email|exsit:users,email'
        ]);
     
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
