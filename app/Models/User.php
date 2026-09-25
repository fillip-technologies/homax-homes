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

    public function ourteam()
    {
        return $this->hasOne(OurTeam::class, 'user_id', 'email');
    }

    public function permission()
    {
        return $this->hasOne(UserPermission::class, 'user_id', 'id');
    }

    /**
     * True when the user may use any of the given permission flags.
     *
     * An admin with no user_permission row at all is a full-access (owner)
     * account, so the very first admin is never locked out. Every account
     * created from the admin UI gets a row, so it is restricted to the ticked flags.
     */
    public function hasPermission(string ...$flags): bool
    {
        if ($this->role !== 'admin') {
            return false;
        }

        $permission = $this->permission;

        if (!$permission) {
            return true;
        }

        foreach ($flags as $flag) {
            if ($permission->$flag) {
                return true;
            }
        }

        return false;
    }

    /**
     * What this account can do in the admin, as short labels for lists.
     * Empty for accounts that are not admins; ['Full access'] for an owner (no permission row).
     */
    public function accessLabels(): array
    {
        if ($this->role !== 'admin') {
            return [];
        }

        if (!$this->permission) {
            return ['Full access'];
        }

        return collect(UserPermission::LABELS)
            ->filter(fn ($label, $flag) => $this->permission->$flag)
            ->map(fn ($label) => \Illuminate\Support\Str::before($label, ' ('))
            ->values()
            ->all();
    }
}
