<?php

use Illuminate\Support\Facades\Auth;

function getUserResponseData($user = null): array
{
    $user = $user ?? Auth::user();

    return [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email
    ];
}
