<?php
namespace App\Helpers;

use App\Models\RefreshToken;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Hash;
use stdClass;

class AuthHelper
{
  public static function verifyAndDeleteRefreshTokenFromDB(string $refreshToken): void
  {
    // verify refresh token in db
    $refreshTokenFromDB = RefreshToken::where('token', $refreshToken)->delete(); // int(0) or int(1)
    // var_dump('okay from db');
    // var_dump($refreshTokenFromDB);
    if (!$refreshTokenFromDB) {
      throw new Exception('Token is not valid');
    }
  }

  public static function verifyRefreshTokenSecret(string $refreshToken): stdClass
  {
    // if the secret wrong it will throw "Signature verification failed"
    $decoded = JWT::decode($refreshToken,  new Key(env('JWT_SECRET'), 'HS256'));  // object(stdClass)
    return $decoded;
  }

  public static function verifyRefreshTokenFromDB(string $refreshToken): void
  {
    $refreshTokenFromDB = RefreshToken::where('token', $refreshToken)->first(); // null or class model
    if (!$refreshTokenFromDB) {
      throw new Exception('Token is not valid');
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
      throw new Exception('wrong email or password');
    }
  }
}
