<?php

namespace App\Http\Controllers\Api\Registration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\Registration\RegistrationResource;
use App\Models\User;

class RegistrationController extends Controller
{
    public function __invoke(RegisterRequest $request)
    {
        $user = $request->validated();
        $user = User::create($user);
        return RegistrationResource::make($user)->resolve();
    }
}
