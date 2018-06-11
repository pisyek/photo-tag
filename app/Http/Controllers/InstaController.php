<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class InstaController extends Controller
{
    public function login()
    {
        return Socialite::driver('instagram')->scopes(['basic', 'public_content'])->redirect();
    }

    public function handleProviderCallback(Request $request)
    {
        $state = $request->get('state');
        $request->session()->put('state', $state);
        $user = Socialite::driver('instagram')->user();

        return $user;

    }
    /**
     * Display search box
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function me()
    {
        return view('me');
    }

    public function search()
    {

    }
}
