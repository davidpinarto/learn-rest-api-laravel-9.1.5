<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefreshToken extends Model
{
    protected $table = 'refresh_token';
    protected $fillable = [
        'token'
    ];

    public $timestamps = false;
    public $incrementing = false;
}
