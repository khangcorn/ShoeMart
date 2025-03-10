<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgetPasswordController extends Controller
{
    public function showLinkRequest()
    {
        return view('client.auth.forget-password');
    }
   

    public function sendResetLink( Request $request) 
    {
        $request->validate([
           'email'=>'required|email|exists:users,email'

        ]);
     
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
