<?php

namespace App\Http\Controllers\LoginOut;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    protected $user;
    public function __construct()
    {
        $this->user = new UserModel();
    }

    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        $layout = true;
        // dd(Hash::make('123'));
        return view('login.login', compact('layout'));
    }
  
    public function handleLogin(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Nhập tên đăng nhập',
            'password.required' => 'Nhập mật khẩu',
        ]);
        // dd($request->all());
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput()->with([
                'msg' => 'Vui lòng xem lại tên đăng nhập hoặc mật khẩu',
                'alert-type' => 'danger'
            ]);
        } else {
            $isAuth = Auth::attempt(['username' => $request->username, 'password' => $request->password]);
            if ($isAuth) {
                return  redirect()->route('home');
            } else {
                return redirect()->back()->withInput()->with([
                    'msg' => 'Có lỗi vui lòng liên hệ kỹ thuật',
                    'alert-type' => 'danger'
                ]);
            }
        }
    }
}
