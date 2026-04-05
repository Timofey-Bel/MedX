<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'first_name',
        'last_name',
        'gender',
        'birthdate',
        'timezone',
        'avatar',
        'visited_days',
        'first_visit_date',
        'freeze_count',
        'used_freezes',
        'achievements',
        'pomodoro_state',
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
            'birthdate' => 'date',
            'first_visit_date' => 'date',
            'visited_days' => 'array',
            'used_freezes' => 'array',
            'achievements' => 'array',
            'pomodoro_state' => 'array',
        ];
    }

    /**
     * The attributes that should have default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'freeze_count' => 5,
        'visited_days' => '[]',
        'used_freezes' => '[]',
        'achievements' => '[]',
    ];

    public function getDisplayNameAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return $this->first_name . ' ' . $this->last_name;
        }
        
        if ($this->first_name) {
            return $this->first_name;
        }
        
        if ($this->last_name) {
            return $this->last_name;
        }
        
        return $this->name;
    }
    
    public function getAvatarLetterAttribute(): string
    {
        if ($this->first_name) {
            return mb_strtoupper(mb_substr($this->first_name, 0, 1));
        }
        
        if ($this->last_name) {
            return mb_strtoupper(mb_substr($this->last_name, 0, 1));
        }
        
        return mb_strtoupper(mb_substr($this->email, 0, 1));
    }
    
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? asset('avatars/' . $this->avatar) : null;
    }
}
