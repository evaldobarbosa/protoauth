<?php

namespace ProtoAuth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use JWTAuth;

class Login extends Controller
{
    public function __invoke(Request $request)
    {
        if ( !in_array(config('app.env'), ['testing','local']) ) {
            return response()->json(['error' => 'Not Found'], 404);
        }

        $credentials = $request->only(['email', 'password']);

        if ($this->userExists($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if (is_null($user)) {   
            $user = \App\Models\User::create([
                'guid' => str()->uuid(),
                'name' => $this->getNameFromEmail($credentials['email']),
                'email' => $credentials['email'],
                'password' => bcrypt($credentials['password']),
            ]);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'name' => $user->name,
            'email' => $user->email,
            'guid' => $user->guid,
        ]);

        return response()->json($credentials);
    }

    private function getNameFromEmail($email)
    {
        $ex = explode('@', $email);

        $p1 = preg_replace("([0-9]+)", "", $ex[0]);

        preg_match_all("/([a-zA-Z]+)/", $p1, $ex);

        $x = collect(current($ex));

        $x = $x->map(function($i) {
            return ucfirst($i);
        });

        unset($ex, $p1);

        return $x->implode(" ");
    }

    private function userExists($credentials)
    {
        $testUsers = [];

        $testUsers = array_merge($this->getOtherProtoUsers(), $testUsers);

        $user = array_filter($testUsers, function($item) use ($credentials) {
            if (!isset($item["email"])) {
                try {
                    $item = array_combine(['email', 'password'], $item);
                } catch (\Exception $e) {
                    dd($item);
                }
            }

            return $item['email'] == $credentials['email'] && $item['password'] == $credentials['password'];
        });

        return (count($user) == 0);
    }

    private function getOtherProtoUsers()
    {
        if (is_null(env('PROTO_AUTH_USERS'))) {
            return [];
        }

        $ex = explode("|", env('PROTO_AUTH_USERS'));
        $ex = array_map(function($item) {
            return explode(":", $item);
        }, $ex);

        return $ex;
    }
}