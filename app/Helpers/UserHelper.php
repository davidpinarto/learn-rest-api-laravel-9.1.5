<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserHelper
{
  public static function validateHashCreateUserData(Request $request): array
  {
    $userData = $request->validate([
      'name' => 'required|string|max:20',
      'email' => 'required|string|max:255',
      'password' => 'required|string|min:8|max:50',
    ]);

    $userData['id'] = 'user-' . Str::random(16);

    if (Str::endsWith($userData['email'], '@admin.com')) {
      $userData['is_admin'] = true;
    }

    $hashedPassword = Hash::make($userData['password']);
    $userData['password'] = $hashedPassword;

    return $userData;
  }
}
