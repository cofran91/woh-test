<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            if(!Auth::attempt($request->only(['email', 'password']))){
                return $this->jsonError(
                    null, 
                    401, 
                    'Email & Password does not match with our record.'
                );
            }

            $user = User::where('email', $request->email)->first();

            return $this->jsonSuccess(
                [ 'token' => $user->createToken("API TOKEN")->plainTextToken ],
                Response::HTTP_OK,
                'User Logged In successfully'
            );

        } catch (\Throwable $th) {
            return $this->jsonError(null, $th->getCode(), $th->getMessage());
        }
    }
}
