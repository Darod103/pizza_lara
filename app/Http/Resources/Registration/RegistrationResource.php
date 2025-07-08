<?php

namespace App\Http\Resources\Registration;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegistrationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $token = JWTAuth::fromUser($this->resource);

        return [
            'name'=> $this->name,
            'email'=> $this->email,
            'access_token' => $token,
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }
}
