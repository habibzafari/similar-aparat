<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $table = 'users';

    const TYPES_ADMIN = 'admin';
    const TYPES_USER = 'user';
    const TYPES = [self::TYPES_ADMIN, self::TYPES_USER];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "mobile",
        "type",
        "email",
        "name",
        "password",
        "avatar",
        "website",
        "verify_code",
        "verified_at",

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'verify_code',
        // 'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    /**
     * The attributes that should be cast to native types.
     *
     * پیدا کردن یوزر با استفاده از یوزر نیم یا موبایل
     * 
     * @var array<string, string>
     */
    // public function findForPassport($username){
    //     $user = static::where('mobile',$username)
    //     ->orWhere('email',$username)->first();
    //     return $user;
    //     // die($user);
    // }

    // public function findForPassport($username)
    // {
    //     $user = static::where('mobile', $username)->orWhere('email', $username)->first();
    //     return $user;
    // }

    public function setMobileAttribute($value)
    {
        $this->attributes['mobile'] = to_valid_mobile_number($value);
    }
}
