<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\Client;
use App\Traits\Response\HasApiResponse;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use HasApiResponse;

    public function login(LoginRequest $request)
    {
        $client = Client::whereEmail($request->get('email'))->first();
        if ($client->blocked) {
            return $this->send(message: __('auth.blocked'), statusCode: Response::HTTP_BAD_REQUEST);
        }
        if (Hash::check($request->get('password'), $client->password)) {
            $token = $client->createToken('api.auth')->accessToken;
            return $this->send(array_merge($client->toArray(), ['accessToken' => $token]), __('messages.auth.logged_in_successfully'));
        }
        return $this->send(message: __('auth.password'), statusCode: Response::HTTP_BAD_REQUEST);
    }

    public function register(RegisterRequest $request)
    {
        $client = Client::create($request->getSanitized());
        return $this->send(data: $client, message: __('messages.auth.registered'), statusCode: Response::HTTP_CREATED);
    }
}
