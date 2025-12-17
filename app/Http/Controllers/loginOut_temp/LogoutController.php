<?php

namespace App\Http\Controllers\LoginOut;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function handleLogout()
    {
        Auth::logout();
        return redirect()->route('login')->with([
            'msg' => 'Logout successfully',
            'alert-type' => 'success'
        ]);
    }
}
