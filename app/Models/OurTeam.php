<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable; // If you want to use Auth
use Illuminate\Notifications\Notifiable;

class OurTeam extends Model
{
    use Notifiable;

    protected $table = 'our_team';

    protected $fillable = [
        'employee_name',
        'designation',
        'user_id',
        'password',
        'joining_date',
        'employee_image',
        'fb_id_link',
        'twitter_link',
        'linkedin_link',
        'instagram_link',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'email');
    }
}

