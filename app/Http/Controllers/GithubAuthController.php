<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GithubAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware(['guest']);
    }

    public function redirect()
    {
        return Socialite::driver('github')->redirect();
    }
    public function callback()
    {
        $user = Socialite::driver('github')->user();
        // dd($user);

        // DB에 사용자 정보를 저장한다.
        //이미 이 사용자 정보가 DB에 저장되어 있다면
        //저장할 필요가 없다.
        $user = User::firstOrCreate(
            [
                'email' => $user->getName()
            ],
            [
                'password' => Hash::make(Str::random(24)),
                'name' => $user->getName()
            ]
        );

        // 로그인 처리...
        Auth::login($user);

        // 사용자가 원래 요청했던 페이지로 redirection
        // 원래 요청했던 페이지가 없으면 dashboard로 redirection
        return redirect()->intended('/dashboard');
    }
}
