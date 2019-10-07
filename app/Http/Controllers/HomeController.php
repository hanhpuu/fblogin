<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Bitly;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {
        $bitlyToken = $request->session()->get('bitlyToken');
        $bitlyLoginLink = 'https://bitly.com/oauth/authorize?client_id='.env('BITLY_CLIENT_ID') .
            '&redirect_uri=' . route('bitly.callback') . '/';

        $reports = Cache::get('fbReports');

        return view('home', ['bitlyToken' => $bitlyToken, 'bitlyLoginLink' => $bitlyLoginLink, 'reports' => $reports]);
    }

    public function bitlyCallback(Request $request) {
        $code = $request->input('code');
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', env('BITLY_API_URL') . 'oauth/access_token', ['form_params' => [
            'client_id' => env('BITLY_CLIENT_ID'),
            'client_secret' => env('BITLY_CLIENT_SECRET'),
            'code' => $code,
            'redirect_uri' => route('bitly.callback') . '/'
        ]]);

        $body = $response->getBody();
        $stringBody = (string) $body;
        $token = Bitly::extractTokenFromOauthResponse($stringBody);
        $request->session()->put('bitlyToken', $token);
        return redirect()->route('home');
    }
}
