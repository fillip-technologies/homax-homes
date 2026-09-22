<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'name',
        'email',
        'phone',
        'message',
        'intent',
        'source',
        'terms_accepted'
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
