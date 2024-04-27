<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Passport\Client;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $email = $request->email;
        $password = $request->password;

        $checkLogin = Auth::attempt([
            'email' => $email,
            'password' => $password,
        ]);

        if ($checkLogin) {
            $user = Auth::user();
            // $token = $user->createToken('auth_token')->plainTextToken;

            //     $tokenResult = $user -> createToken('auth_api');

            //     $token = $tokenResult-> token;
            //     $token -> expires_at = Carbon::now()-> addMinutes(60);

            //     $accessToken = $tokenResult-> accessToken;

            //     $expires = Carbon::parse($token -> expires_at) -> toDateTimeString();

            //     $response = [
            //         'status' => 200,
            //         'token' => $accessToken,
            //         'expires' => $expires
            //     ];

            // } else {
            //     $response = [
            //         'status' => 401,
            //         'title' => 'Unauthorized',
            //     ];

            $client = Client::where('password_client', 1)->first();
            if ($client) {
                $clientSecret = $client->secret;
                $clientId = $client->id;
                $response = Http::asForm()->post('http://127.0.0.1:8001/oauth/token', [
                    'grant_type' => 'password',
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'username' => $email,
                    'password' => $password,
                    'scope' => '',
                ]);

                return $response;
            }
        }
    }

    public function logout()
    {
        $user = Auth::user();

        $status = $user->token()->revoke();

        $response = [
            'status' => 200,
            'title' => 'Logout'
        ];

        return $response;
    }

    public function getToken(Request $request)
    {
        return $request->user()->currentAccessToken()->delete();
    }

    public function refreshToken(Request $request)
    {
        // if ($request->header('authorization')) {
        //     $hashToken = $request->header('authorization');
        //     $hashToken = str_replace('Bearer', '', $hashToken);
        //     $hashToken = trim($hashToken);

        //     $token = PersonalAccessToken::findToken($hashToken);

        //     if ($token) {
        //         $tokenCreated = $token->created_at;
        //         $expire = Carbon::parse($tokenCreated)->addMinutes(config('sanctum.expiration'));

        //         if (Carbon::now() >= $expire) {
        //             $userId = $token->tokenable_id;

        //             $user = User::find($userId);
        //             $user->tokens()->delete();

        //             $newToken = $user->createToken('auth_token')->plainTextToken;
        //             $response = [
        //                 'status' => 200,
        //                 'token' => $newToken,
        //             ];
        //         } else {
        //             $response = [
        //                 'status' => 200,
        //                 'title' => 'UnExpires',
        //             ];
        //         }

        //     } else {
        //         $response = [
        //             'status' => 401,
        //             'title' => 'Unauthorized',
        //         ];
        //     }
        //     return $response;
        // }


        $client = Client::where('password_client', 1)->first();
        if ($client) {
            $clientSecret = $client->secret;
            $clientId = $client->id;
            $refreshToken = $request->refresh;
            $response = Http::asForm()->post('http://127.0.0.1:8001/oauth/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'scope' => '',
            ]);

            return $response;
        }
    }
}
