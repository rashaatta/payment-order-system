<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
/**
 * @group Auth endpoints
 */
class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            // If the credentials are invalid, return an error
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid Credentials'], 401);
            }
        } catch (JWTException $e) {
            // Error while creating the token
            return response()->json(['error' => 'Could not create token'], 500);
        }

        // If successful, return the token along with the authenticated user
        return $this->respondWithToken($token);
    }

    // Function to respond with the token details
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,  // Token TTL in seconds
            'user' => auth()->user(),  // Add the authenticated user's details
        ]);
    }
}
