<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use \Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        ];
    }

    protected static function booted()
    {
        static::created(function ($user) {
            // Se não estiver sendo criado via painel admin
            if (!app()->runningInConsole() && !request()->is('admin/*')) {
                // Se não tem role aplicada, aplica user
                if (!$user->roles()->exists()) {
                    $user->assignRole('user');
                }

                // Cria carteira apenas se ainda não existir
                if (! $user->wallet) {
                    $user->wallet()->create([
                        'balance' => 0,
                    ]);
                }
            }
        });
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(\App\Models\Wallet::class);
    }
}
