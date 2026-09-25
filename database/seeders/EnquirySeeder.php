<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyInquiry;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Replaces ALL enquiries with five fresh ones dated inside the current week.
 *
 *   php artisan db:seed --class=EnquirySeeder
 *
 * Only the property_inquiries table is wiped; properties, users and the team are untouched.
 * Properties are matched by slug, so this works after any reseed or data edit. An enquiry whose
 * property no longer exists is skipped rather than saved against the wrong listing.
 */
class EnquirySeeder extends Seeder
{
    /** Never seed more than this many enquiries. */
    private const MAX_ENQUIRIES = 5;

    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->error('EnquirySeeder wipes enquiries; refusing to run in production.');

            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('property_inquiries')->truncate();
        Schema::enableForeignKeyConstraints();

        $properties = Property::pluck('title', 'id')->all();
        $bySlug = Property::pluck('id', 'slug')->all();

        // [property slug or null for a general lead, name, email, phone, message, intent, source, terms, [day offset from Monday, hour IST, minute]]
        $rows = [
            ['harbor-view-residences', 'Rohan Mehta', 'rohan.mehta@example.com', '+91 98200 11234', 'Interested in a 2 BHK. Is a site visit possible this weekend?', 'enquiry', 'side', true, [0, 11, 15]],
            ['crestwood-heights', 'Priya Nair', 'priya.nair@example.com', '+91 99870 45678', null, 'brochure', 'modal', true, [1, 14, 40]],
            ['metro-square-offices', 'Amit Kulkarni', null, '+91 90040 77812', 'Please share the payment plan and current offers.', 'enquiry', 'side', true, [2, 10, 5]],
            [null, 'Sneha Iyer', 'sneha.iyer@example.com', '+91 98330 66021', 'Do you help with home loan tie-ups for under-construction projects?', 'general', 'Contact', false, [3, 16, 30]],
            ['orchid-business-park', 'Vikram Singh', 'vikram.singh@example.com', '+91 97690 30455', 'Looking for a commercial space around 600 sq.ft. What is the rate?', 'enquiry', 'side', true, [4, 12, 20]],
        ];

        $created = 0;
        foreach (array_slice($rows, 0, self::MAX_ENQUIRIES) as [$slug, $name, $email, $phone, $message, $intent, $source, $terms, [$dayOffset, $hour, $minute]]) {
            $propertyId = 0;
            $title = null;

            if ($slug !== null) {
                if (!isset($bySlug[$slug])) {
                    continue;
                }
                $propertyId = $bySlug[$slug];
                $title = $properties[$propertyId];
            }

            $when = $this->enquiryTime($dayOffset, $hour, $minute);

            $enquiry = new PropertyInquiry([
                'property_id' => $propertyId,
                'property_title' => $title,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'message' => $message,
                'intent' => $intent,
                'source' => $source,
                'terms_accepted' => $terms,
            ]);
            $enquiry->created_at = $when;
            $enquiry->updated_at = $when;
            $enquiry->save();
            $created++;
        }

        $this->command?->info("Seeded $created enquiries dated this week.");
    }

    /**
     * Day offset from this week's Monday plus a wall-clock time (the app timezone is IST).
     * Never in the future (a slot that has not happened yet becomes "a few
     * minutes ago", still in order) and never before Monday 00:00 IST.
     */
    private function enquiryTime(int $dayOffset, int $hour, int $minute): Carbon
    {
        $now = Carbon::now();
        $monday = $now->copy()->startOfWeek();

        $when = $monday->copy()->addDays($dayOffset)->setTime($hour, $minute);
        if ($when->greaterThan($now)) {
            $when = $now->copy()->subMinutes((5 - $dayOffset) * 9);
        }
        if ($when->lessThan($monday)) {
            $when = $monday->copy()->addMinutes(5 + $dayOffset);
        }

        return $when;
    }
}
