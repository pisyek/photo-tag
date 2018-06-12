<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

/**
 * Class InstaController
 *
 * @package App\Http\Controllers
 * @author Hafiz Suhaimi <pisyek@gmail.com>
 * @copyright 2018 Pisyek Studios
 */
class InstaController extends Controller
{
    /**
     * Call endpoint to auth user
     *
     * @return mixed
     */
    public function login()
    {
        return Socialite::driver('instagram')->scopes(['basic', 'public_content'])->redirect();
    }

    /**
     * Handle callback from Instagram
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function handleProviderCallback(Request $request)
    {
        try {
            $accessToken = session('access_token');
            if (!isset($accessToken)) {
                $state = $request->get('state');
                $request->session()->put('state', $state);
                $user = Socialite::driver('instagram')->user();
            }
        } catch (\Exception $e) {
            return abort(401, 'Unauthorized');
        }
        return view('me');
    }

    /**
     * Perform search by keyword
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search(Request $request)
    {
        // 1. sanitize input by user
        // 2. call end-point search
        // 3. prepare data from endpoint
        // 4. pass to view()
        $this->validate($request, [
            'keyword' => 'required'
        ]);

        $keyword = $request->input('keyword');

        try {
            $instaMediaUrl = 'https://api.instagram.com/v1/tags/'. $keyword .'/media/recent';

            $client = new Client();
            $response = $client->get($instaMediaUrl, [
                'query' => [
                    'access_token' => session('access_token'),
                    'count' => 6
                ]
            ]);
        } catch (ClientException  $e) {
            return $e->getResponse();
        }

        $jsonData = json_decode($response->getBody(), true);
        return view('search', compact('jsonData', 'keyword'));
    }
}
