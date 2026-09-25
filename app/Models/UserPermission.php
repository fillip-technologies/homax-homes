<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    protected $table = 'user_permission';

    protected $fillable = [
        'user_id',
        'all_property',
        'featured_image',
        'add_now',
        'property_image',
        'our_team',
        'manage_users',
        'blog',
    ];

    /** Flags shown in the admin UI, with the sidebar section each one controls. */
    public const LABELS = [
        'all_property' => 'All Properties (list, edit, delete)',
        'featured_image' => 'Featured Properties',
        'add_now' => 'Add New Property',
        'property_image' => 'Property Enquiries',
        'our_team' => 'Our Team',
        'manage_users' => 'User Permissions',
    ];

    protected $casts = [
        'all_property' => 'boolean',
        'featured_image' => 'boolean',
        'add_now' => 'boolean',
        'property_image' => 'boolean',
        'our_team' => 'boolean',
        'manage_users' => 'boolean',
        'blog' => 'boolean',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
