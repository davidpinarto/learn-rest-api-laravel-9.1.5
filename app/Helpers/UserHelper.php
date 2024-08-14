<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserHelper
{
  public static function validateHashCreateUserData(Request $request): array
  {
    $rules = [
      'name' => 'required|string|max:20',
      'email' => 'required|string|max:255',
      'password' => 'required|string|min:8|max:50',
    ];
    $validatedData = $request->validate($rules);

    $validatedData['id'] = 'user-' . Str::random(16);

    if (Str::endsWith($validatedData['email'], '@admin.com')) {
      $validatedData['is_admin'] = true;
    }

    $hashedPassword = Hash::make($validatedData['password']);
    $validatedData['password'] = $hashedPassword;

    return $validatedData;
  }
}
