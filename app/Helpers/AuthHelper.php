<?php
namespace App\Helpers;

use App\Exceptions\InvariantException;
use App\Exceptions\UnauthorizedException;
use App\Models\RefreshToken;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Hash;
use stdClass;

class AuthHelper
{
  public static function verifyAndDeleteRefreshTokenFromDB(string $refreshToken): void
  {
    $refreshTokenFromDB = RefreshToken::where('token', $refreshToken)->delete(); // int(0) or int(1)
    if (!$refreshTokenFromDB) {
      throw new InvariantException('Token is not valid');
    }
  }

  public static function verifyRefreshTokenSecretAndGetUserData(string $refreshToken): array
  {
    // if the secret wrong it will throw "Signature verification failed" SignatureInvalidException
    $decoded = JWT::decode($refreshToken,  new Key(env('JWT_SECRET'), 'HS256'));  // object(stdClass)
    $userData = [
      'userId' => $decoded->id,
      'userEmail' => $decoded->email,
    ];
    return $userData;
  }

  public static function verifyRefreshTokenFromDB(string $refreshToken): void
  {
    $refreshTokenFromDB = RefreshToken::where('token', $refreshToken)->first(); // null or class model
    if (!$refreshTokenFromDB) {
      throw new InvariantException('Token is not valid');
    }
  }

  public static function generateAccessToken(string $id, string $email): string
  {
    $accessTokenClaims = [
      'id' => $id,
      'email' => $email,
      'exp' => time() + '360000' // seharusnya 60 detik, namun untuk memperlancar proses developemnt saya set 360000
      // 'exp' => time() + 30
    ];
    $accessToken = JWT::encode($accessTokenClaims, env('JWT_SECRET'), 'HS256');
    return $accessToken;
  }

  public static function generateAndPutRefreshTokenOnDB(string $id, string $email): string
  {
    $refreshTokenClaims = [
      'id' => $id,
      'email' => $email,
      'unique' => uniqid(), // agar token berubah setiap dibuat
    ];
    $refreshToken = JWT::encode($refreshTokenClaims, env('JWT_SECRET'), 'HS256');

    RefreshToken::create(['token' => $refreshToken]);

    return $refreshToken;
  }

  public static function verifyMatchPassword($inputPassword, $hashedPassword): void
  {
    // var_dump(Hash::check($inputPassword, $hashedPassword));
    if (!Hash::check($inputPassword, $hashedPassword)) {
      throw new UnauthorizedException('wrong email or password');
    }
  }

  public static function verifyAndGetUserDataInDB(string $email): User {
    $user = User::where('email', $email)->first(); // null or class model

    if (!$user) {
        throw new UnauthorizedException('wrong email or password');
    }

    return $user;
  }
}
