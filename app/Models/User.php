<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'usuario', 
        'email',
        'password',
        'role'
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role'=> UserRole::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this ->role === UserRole::Admin;    
    }

    public function isUser(): bool
    {
        return $this -> role === UserRole::User;
    }

    public function hasRole(string $role): bool
    {
        return $this->role->value === $role;
    }
    

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'usuario'; 
    }

    /**
     * Get the column name for the "username" (usado por Laravel Auth).
     *
     * @return string
     */
    public function username()
    {
        return 'usuario';
    }

    /**
     * Get the name of the "username" column for authentication.
     * Este método es usado por Auth::attempt
     *
     * @return string
     */
    public static function getUsernameColumn()
    {
        return 'usuario';
    }
}