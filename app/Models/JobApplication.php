<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/** An application sent from /careers (a specific opening, or a general application). */
class JobApplication extends Model
{
    /** Uploaded files: route key => column. Stored on the private "local" disk. */
    public const FILES = [
        'resume' => 'resume_path',
        'cover-letter' => 'cover_letter_path',
    ];

    protected $fillable = [
        'job_opening_id', 'job_title', 'first_name', 'last_name', 'email', 'phone',
        'resume_path', 'cover_letter_path', 'terms_accepted',
    ];

    protected $casts = [
        'terms_accepted' => 'boolean',
    ];

    protected static function booted()
    {
        // Remove the uploaded files along with the application.
        static::deleted(function (JobApplication $application) {
            foreach (self::FILES as $column) {
                if ($application->$column) {
                    Storage::disk('local')->delete($application->$column);
                }
            }
        });
    }

    public function jobOpening()
    {
        // null for general applications, or once the opening has been deleted; job_title keeps its name.
        return $this->belongsTo(JobOpening::class);
    }

    public function getNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getIsGeneralAttribute(): bool
    {
        return $this->job_title === null;
    }
}
