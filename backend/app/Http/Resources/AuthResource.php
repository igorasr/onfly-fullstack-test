<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            "user" => $this['user'],
            "authorization" => [
                "token" => $this['token'],
                "type" => "bearer",
            ],
        ];
    }
}
