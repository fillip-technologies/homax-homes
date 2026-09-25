<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyInquiry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Replaces ALL properties and enquiries with a fresh demo data set.
 *
 *   php artisan db:seed --class=PropertySeeder
 *
 * Wipes: full_property_schema, property_details, property_images, similar_properties,
 * property_inquiries. Users, permissions and the team are left alone. Uploaded files
 * are not touched; the demo properties reuse images already in public/properties.
 */
class PropertySeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->error('PropertySeeder wipes properties and enquiries; refusing to run in production.');

            return;
        }

        $ownerId = User::where('role', 'admin')->orderBy('id')->value('id')
            ?? User::orderBy('id')->value('id');

        if (!$ownerId) {
            $this->command?->error('Create an admin user first; properties need an owner.');

            return;
        }

        $this->wipe();

        $files = [
            'main' => $this->filesIn('main_images'),
            'extra' => $this->filesIn('additional_images'),
            'plan' => $this->filesIn('floor_plans'),
            'brochure' => $this->filesIn('brochures'),
        ];

        $properties = [];
        foreach ($this->definitions() as $i => $def) {
            $properties[] = $this->createProperty($def, $i, $ownerId, $files);
        }

        $this->linkSimilar($properties);
        $this->call(EnquirySeeder::class); // five fresh enquiries dated this week

        $this->command?->info(sprintf(
            'Seeded %d properties and %d enquiries.',
            count($properties),
            PropertyInquiry::count()
        ));
    }

    private function wipe(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach (['property_inquiries', 'similar_properties', 'property_images', 'property_details', 'full_property_schema'] as $table) {
            DB::table($table)->truncate();
        }
        Schema::enableForeignKeyConstraints();
    }

    /** @return string[] paths relative to public/, sorted for a stable result */
    private function filesIn(string $dir): array
    {
        $found = glob(public_path("properties/$dir/*")) ?: [];
        sort($found);

        return array_map(fn ($f) => "properties/$dir/" . basename($f), $found);
    }

    private function pick(array $list, int $index): ?string
    {
        return $list ? $list[$index % count($list)] : null;
    }

    private function createProperty(array $d, int $i, int $ownerId, array $files): Property
    {
        $firstUnit = $d['units'][0];
        $created = Carbon::now()->subDays(28 - $i * 3)->setTime(10 + $i, 15);

        $property = new Property([
            'user_id' => $ownerId,
            'title' => $d['title'],
            'slug' => $d['slug'],
            'developer_name' => $d['developer'],
            'description' => '<p>' . implode('</p><p>', $d['description']) . '</p>',
            'rera_id' => $d['rera'],
            'category' => $d['category'],
            'price' => $d['price'],
            'price_unit' => '₹',
            'property_id' => 'PROP-' . $created->format('Ymd') . '-' . strtoupper(substr(md5($d['slug']), 0, 6)),
            'pre_launch_property' => $d['status'] === 'Pre-Launch',
            'project_status' => $d['status'],
            'location' => $d['location'],
            'address' => $d['address'],
            'city' => $d['city'],
            'state' => $d['state'],
            'country' => 'India',
            'zip_code' => $d['zip'],
            'latitude' => $d['lat'],
            'longitude' => $d['lng'],
            'landmark' => $d['landmark'],
            'google_map_link' => 'https://maps.google.com/?q=' . $d['lat'] . ',' . $d['lng'],
            'apartment_per_floor' => $d['per_floor'] ?? null,
            'bedrooms' => $firstUnit['bedrooms'],
            'bathrooms' => $firstUnit['bathrooms'],
            'balconies' => $firstUnit['balconies'],
            'super_area' => $firstUnit['super'],
            'carpet_area' => $firstUnit['carpet'],
            'furnishing' => $d['furnishing'],
            'features' => $d['features'],
            'amenities' => $d['amenities'],
            'possession_date' => $d['possession'] . '-01',
            'main_image' => $this->pick($files['main'], $i * 3),
            'floor_plan_image' => $this->pick($files['plan'], $i),
            // One brochure per property: sharing a file across listings breaks the others when one is deleted.
            'brochure' => $i < count($files['brochure']) ? $files['brochure'][$i] : null,
            'is_featured' => $d['featured'],
            'is_verified' => true,
            'is_active' => true,
            'property_status' => 'Available',
            'notes' => '<p>' . $d['note'] . '</p>',
            'keyfeatures' => implode("\n", array_map(fn ($k) => "<p>$k</p>", $d['key_features'])),
            'bazar_distance_km' => $d['near'][0],
            'hospital_distance_km' => $d['near'][1],
            'school_distance_km' => $d['near'][2],
            'bus_stand_distance_km' => $d['near'][3],
            'junction_distance_km' => $d['near'][4],
            'airport_distance_km' => $d['near'][5],
            'custom_nearby_places' => $d['places'],
            'place_names' => $d['place_names'],
        ]);
        $property->created_at = $created;
        $property->updated_at = $created;
        $property->save();

        foreach ($d['units'] as $unit) {
            $property->details()->create([
                'unit_type' => $unit['type'],
                'bedrooms' => $unit['bedrooms'],
                'bathrooms' => $unit['bathrooms'],
                'balconies' => $unit['balconies'],
                'apartment_per_floor' => $d['per_floor'] ?? null,
                'carpet_area' => $unit['carpet'],
                'super_area' => $unit['super'],
                'price' => $unit['price'],
            ]);
        }

        // Four gallery photos each; the first doubles as the featured image.
        foreach (range(0, 3) as $n) {
            $path = $this->pick($files['extra'], $i * 4 + $n);
            if ($path) {
                $property->images()->create([
                    'image_path' => $path,
                    'is_featured' => $n === 0,
                    'order' => $n,
                ]);
            }
        }

        return $property;
    }

    /** Each property points at the next two, so every detail page has similar listings. */
    private function linkSimilar(array $properties): void
    {
        $count = count($properties);
        foreach ($properties as $i => $property) {
            foreach ([1, 2] as $step) {
                $other = $properties[($i + $step) % $count];
                DB::table('similar_properties')->insert([
                    'property_id' => $property->id,
                    'similar_property_id' => $other->id,
                    'similarity_score' => 100 - $step * 10,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function definitions(): array
    {
        $basic = ['Swimming Pool', 'Gym', 'Parking', 'Garden', 'Security', 'Lift', 'Power Backup', 'WiFi'];
        $premium = ['Clubhouse', 'Gymnasium', 'Kids Play Area', 'Jogging Track', 'Landscaped Gardens', 'CCTV Security', 'Fire Safety', 'Rain Water Harvesting', 'Intercom Facility', 'Vastu Compliant'];

        return [
            [
                'title' => 'Harbor View Residences', 'slug' => 'harbor-view-residences', 'developer' => 'Skyline Realty',
                'category' => 'Residential', 'price' => '1.10 Cr', 'status' => 'Ready to move', 'possession' => '2026-06',
                'rera' => 'P51800012345', 'featured' => true, 'furnishing' => 'Semi Furnished', 'per_floor' => '4',
                'location' => 'Sector 20, Kharghar', 'address' => 'Plot 14, Sector 20, Kharghar, Navi Mumbai', 'city' => 'Navi Mumbai', 'state' => 'Maharashtra', 'zip' => '410210',
                'lat' => 19.0330, 'lng' => 73.0656, 'landmark' => 'Opposite Central Park',
                'description' => ['A ready-to-move community of 2 and 3 BHK homes a short walk from Central Park and the Kharghar railway station.', 'Every home is planned for cross ventilation and natural light, with a landscaped podium and a full clubhouse.'],
                'key_features' => ['Ready to move in, OC received', 'Walk to Kharghar station', 'Podium garden and clubhouse'],
                'note' => 'Bank loan approved from leading lenders.',
                'features' => $basic, 'amenities' => array_slice($premium, 0, 8),
                'near' => ['1 km', '2 km', '1.2 km', '1.5 km', '1 km', '14 km'],
                'place_names' => [
                    'bazar' => 'Kharghar Metro Station',
                    'hospital' => 'MGM Hospital',
                    'school' => 'DAV Public School',
                    'bus_stand' => 'Kharghar Bus Depot',
                    'junction' => 'Kharghar Railway Station',
                    'airport' => 'Navi Mumbai International Airport',
                ],
                'places' => [['group' => 'nearby', 'label' => 'Central Park', 'icon' => 'fa-tree', 'distance' => '0.3 km'], ['group' => 'connectivity', 'label' => 'Kharghar Station', 'icon' => 'fa-train', 'distance' => '1 km']],
                'units' => [
                    ['type' => '2 BHK', 'bedrooms' => 2, 'bathrooms' => 2, 'balconies' => 1, 'carpet' => 640, 'super' => 890, 'price' => '1.10 Cr'],
                    ['type' => '3 BHK', 'bedrooms' => 3, 'bathrooms' => 3, 'balconies' => 2, 'carpet' => 860, 'super' => 1180, 'price' => '1.55 Cr'],
                ],
            ],
            [
                'title' => 'Lakeside Enclave', 'slug' => 'lakeside-enclave', 'developer' => 'Harbor Point Developers',
                'category' => 'Residential', 'price' => '85 Lakh', 'status' => 'Early Possession', 'possession' => '2027-03',
                'rera' => 'P51700023456', 'featured' => true, 'furnishing' => 'Unfurnished', 'per_floor' => '6',
                'location' => 'Ulwe', 'address' => 'Sector 19, Ulwe, Navi Mumbai', 'city' => 'Navi Mumbai', 'state' => 'Maharashtra', 'zip' => '410206',
                'lat' => 18.9894, 'lng' => 73.0205, 'landmark' => 'Near Ulwe Lake',
                'description' => ['Affordable 1 and 2 BHK apartments beside Ulwe Lake, close to the upcoming airport and the Atal Setu link.', 'Compact, efficient layouts with a large sports court and a senior citizen park.'],
                'key_features' => ['Lake-facing towers', 'Close to the new airport', 'Sports court and senior park'],
                'note' => 'Early possession with flexible payment plans.',
                'features' => array_slice($basic, 0, 7), 'amenities' => array_slice($premium, 2, 8),
                'near' => ['800 m', '5 km', '1 km', '500 m', '1.5 km', '6 km'],
                'place_names' => [
                    'bazar' => 'Ulwe Metro Station',
                    'hospital' => 'Apollo Hospital',
                    'school' => 'Ryan International School',
                    'bus_stand' => 'Ulwe Bus Stop',
                    'junction' => 'Ulwe Railway Station',
                    'airport' => 'Navi Mumbai International Airport',
                ],
                'places' => [['group' => 'nearby', 'label' => 'Ulwe Lake', 'icon' => 'fa-tree', 'distance' => '0.2 km']],
                'units' => [
                    ['type' => '1 BHK', 'bedrooms' => 1, 'bathrooms' => 1, 'balconies' => 1, 'carpet' => 420, 'super' => 610, 'price' => '58 Lakh'],
                    ['type' => '2 BHK', 'bedrooms' => 2, 'bathrooms' => 2, 'balconies' => 1, 'carpet' => 610, 'super' => 860, 'price' => '85 Lakh'],
                ],
            ],
            [
                'title' => 'Crestwood Heights', 'slug' => 'crestwood-heights', 'developer' => 'Crestwood Group',
                'category' => 'Residential', 'price' => '2.40 Cr', 'status' => 'Pre-Launch', 'possession' => '2029-12',
                'rera' => 'P52000034567', 'featured' => true, 'furnishing' => 'Unfurnished', 'per_floor' => '3',
                'location' => 'Powai', 'address' => 'Hiranandani Road, Powai, Mumbai', 'city' => 'Mumbai', 'state' => 'Maharashtra', 'zip' => '400076',
                'lat' => 19.1176, 'lng' => 72.9060, 'landmark' => 'Near Powai Lake',
                'description' => ['A pre-launch collection of premium 3 and 4 BHK sky residences with lake and city views.', 'Limited inventory, pre-launch pricing and a dedicated concierge for early buyers.'],
                'key_features' => ['Pre-launch pricing', 'Lake and skyline views', 'Only three homes per floor'],
                'note' => 'Register your interest for pre-launch pricing.',
                'features' => $basic, 'amenities' => $premium,
                'near' => ['500 m', '1 km', '2 km', '300 m', '4 km', '9 km'],
                'place_names' => [
                    'bazar' => 'Powai Metro Station',
                    'hospital' => 'Hiranandani Hospital',
                    'school' => 'IIT Bombay School',
                    'bus_stand' => 'Powai Bus Stop',
                    'junction' => 'Kanjurmarg Railway Station',
                    'airport' => 'Chhatrapati Shivaji Airport',
                ],
                'places' => [['group' => 'nearby', 'label' => 'Powai Lake', 'icon' => 'fa-tree', 'distance' => '0.6 km'], ['group' => 'connectivity', 'label' => 'JVLR Junction', 'icon' => 'fa-road', 'distance' => '1.5 km']],
                'units' => [
                    ['type' => '3 BHK', 'bedrooms' => 3, 'bathrooms' => 3, 'balconies' => 2, 'carpet' => 1020, 'super' => 1380, 'price' => '2.40 Cr'],
                    ['type' => '4 BHK', 'bedrooms' => 4, 'bathrooms' => 4, 'balconies' => 3, 'carpet' => 1400, 'super' => 1850, 'price' => '3.30 Cr'],
                ],
            ],
            [
                'title' => 'Green Valley Villas', 'slug' => 'green-valley-villas', 'developer' => 'Green Valley Estates',
                'category' => 'Residential', 'price' => '1.85 Cr', 'status' => 'Upcoming', 'possession' => '2028-09',
                'rera' => 'P52100045678', 'featured' => false, 'furnishing' => 'Unfurnished', 'per_floor' => null,
                'location' => 'Ghodbunder Road, Thane West', 'address' => 'Kasarvadavali, Ghodbunder Road, Thane West', 'city' => 'Thane', 'state' => 'Maharashtra', 'zip' => '400615',
                'lat' => 19.2660, 'lng' => 72.9720, 'landmark' => 'Near Hiranandani Estate',
                'description' => ['Independent 3 and 4 BHK villas with private gardens in a gated community on Ghodbunder Road, Thane West.', 'Each villa has a terrace, two-car parking and space for a home office, with quick access to Thane, Mulund and the Western Express Highway.'],
                'key_features' => ['Independent villas with gardens', 'Gated community on Ghodbunder Road', 'Quick access to Thane and Mulund'],
                'note' => 'Upcoming launch in Thane; details subject to RERA approval.',
                'features' => array_slice($basic, 0, 6), 'amenities' => array_slice($premium, 0, 7),
                'near' => ['500 m', '6 km', '1.5 km', '300 m', '9 km', '22 km'],
                'place_names' => [
                    'bazar' => 'Kasarvadavali Metro Station',
                    'hospital' => 'Jupiter Hospital',
                    'school' => 'Podar International School',
                    'bus_stand' => 'Kasarvadavali Bus Stop',
                    'junction' => 'Thane Railway Station',
                    'airport' => 'Chhatrapati Shivaji Maharaj International Airport',
                ],
                'places' => [['group' => 'nearby', 'label' => 'Viviana Mall', 'icon' => 'fa-bag-shopping', 'distance' => '6 km'], ['group' => 'nearby', 'label' => 'Upvan Lake', 'icon' => 'fa-tree', 'distance' => '8 km'], ['group' => 'connectivity', 'label' => 'Ghodbunder Road', 'icon' => 'fa-road', 'distance' => '200 m']],
                'units' => [
                    ['type' => '3 BHK Villa', 'bedrooms' => 3, 'bathrooms' => 3, 'balconies' => 2, 'carpet' => 1350, 'super' => 1750, 'price' => '1.85 Cr'],
                    ['type' => '4 BHK Villa', 'bedrooms' => 4, 'bathrooms' => 4, 'balconies' => 3, 'carpet' => 1800, 'super' => 2300, 'price' => '2.60 Cr'],
                ],
            ],
            [
                'title' => 'Metro Square Offices', 'slug' => 'metro-square-offices', 'developer' => 'Metro Square Developers',
                'category' => 'Commercial', 'price' => '95 Lakh', 'status' => 'Ready to move', 'possession' => '2026-01',
                'rera' => 'P51800056789', 'featured' => false, 'furnishing' => 'Fully Furnished', 'per_floor' => '12',
                'location' => 'Belapur CBD', 'address' => 'Sector 11, CBD Belapur, Navi Mumbai', 'city' => 'Navi Mumbai', 'state' => 'Maharashtra', 'zip' => '400614',
                'lat' => 19.0176, 'lng' => 73.0393, 'landmark' => 'Next to CBD Belapur station',
                'description' => ['Ready office units from 450 sq.ft in the heart of the Belapur business district.', 'Furnished floors, central air conditioning and 24x7 power backup.'],
                'key_features' => ['Steps from the railway station', 'Furnished, ready offices', '24x7 power backup and security'],
                'note' => 'Lease-back assistance available for investors.',
                'features' => ['Parking', 'Security', 'Lift', 'Power Backup', 'WiFi'], 'amenities' => ['CCTV Security', 'Fire Safety', 'Goods Lift', 'Intercom Facility'],
                'near' => ['200 m', '2 km', '2.5 km', '500 m', '200 m', '12 km'],
                'place_names' => [
                    'bazar' => 'CBD Belapur Metro Station',
                    'hospital' => 'Apollo Hospital',
                    'school' => 'Ryan International School',
                    'bus_stand' => 'CBD Bus Depot',
                    'junction' => 'CBD Belapur Railway Station',
                    'airport' => 'Navi Mumbai International Airport',
                ],
                'places' => [['group' => 'connectivity', 'label' => 'Palm Beach Road', 'icon' => 'fa-road', 'distance' => '2 km']],
                'units' => [
                    ['type' => 'Office 450 sq.ft', 'bedrooms' => null, 'bathrooms' => 1, 'balconies' => null, 'carpet' => 450, 'super' => 620, 'price' => '95 Lakh'],
                    ['type' => 'Office 900 sq.ft', 'bedrooms' => null, 'bathrooms' => 2, 'balconies' => null, 'carpet' => 900, 'super' => 1240, 'price' => '1.85 Cr'],
                ],
            ],
            [
                'title' => 'Sunrise Meadows', 'slug' => 'sunrise-meadows', 'developer' => 'Sunrise Homes',
                'category' => 'Residential', 'price' => '72 Lakh', 'status' => 'Early Possession', 'possession' => '2027-06',
                'rera' => 'P51700067890', 'featured' => false, 'furnishing' => 'Unfurnished', 'per_floor' => '8',
                'location' => 'Panvel', 'address' => 'Old Panvel Road, Panvel, Navi Mumbai', 'city' => 'Navi Mumbai', 'state' => 'Maharashtra', 'zip' => '410206',
                'lat' => 18.9894, 'lng' => 73.1175, 'landmark' => 'Near Panvel station',
                'description' => ['Value-for-money 1 and 2 BHK homes with a large central garden, minutes from Panvel station and the expressway.', 'A low-density project with plenty of parking and open space.'],
                'key_features' => ['Low-density project', 'Minutes from Panvel station', 'Large central garden'],
                'note' => 'Special festive offer on selected units.',
                'features' => array_slice($basic, 2, 6), 'amenities' => array_slice($premium, 1, 7),
                'near' => ['1.5 km', '2 km', '1 km', '1.8 km', '1.6 km', '10 km'],
                'place_names' => [
                    'bazar' => 'Panvel Metro Station',
                    'hospital' => 'Lifeline Hospital',
                    'school' => 'St. Joseph School',
                    'bus_stand' => 'Panvel Bus Depot',
                    'junction' => 'Panvel Railway Station',
                    'airport' => 'Navi Mumbai International Airport',
                ],
                'places' => [['group' => 'nearby', 'label' => 'Central Garden', 'icon' => 'fa-tree', 'distance' => '0.1 km']],
                'units' => [
                    ['type' => '1 BHK', 'bedrooms' => 1, 'bathrooms' => 1, 'balconies' => 1, 'carpet' => 410, 'super' => 590, 'price' => '48 Lakh'],
                    ['type' => '2 BHK', 'bedrooms' => 2, 'bathrooms' => 2, 'balconies' => 1, 'carpet' => 590, 'super' => 830, 'price' => '72 Lakh'],
                ],
            ],
            [
                'title' => 'Orchid Business Park', 'slug' => 'orchid-business-park', 'developer' => 'Orchid Infra',
                'category' => 'Commercial', 'price' => '1.40 Cr', 'status' => 'Upcoming', 'possession' => '2028-03',
                'rera' => 'P52000078901', 'featured' => true, 'furnishing' => 'Unfurnished', 'per_floor' => '10',
                'location' => 'Andheri East', 'address' => 'MIDC, Andheri East, Mumbai', 'city' => 'Mumbai', 'state' => 'Maharashtra', 'zip' => '400093',
                'lat' => 19.1197, 'lng' => 72.8811, 'landmark' => 'Near MIDC Metro station',
                'description' => ['Grade-A commercial suites and shops with a Metro station at the doorstep.', 'Double-height lobby, ample visitor parking and flexible floor plates for offices and clinics.'],
                'key_features' => ['Metro at the doorstep', 'Grade-A office specification', 'Flexible floor plates'],
                'note' => 'Corporate leasing enquiries are welcome.',
                'features' => ['Parking', 'Security', 'Lift', 'Power Backup', 'WiFi'], 'amenities' => ['CCTV Security', 'Fire Safety', 'Goods Lift', 'Rain Water Harvesting'],
                'near' => ['200 m', '1.5 km', '2 km', '200 m', '4 km', '4 km'],
                'place_names' => [
                    'bazar' => 'MIDC Metro Station',
                    'hospital' => 'Seven Hills Hospital',
                    'school' => 'Podar International School',
                    'bus_stand' => 'MIDC Bus Stop',
                    'junction' => 'Andheri Railway Station',
                    'airport' => 'Chhatrapati Shivaji Airport',
                ],
                'places' => [['group' => 'connectivity', 'label' => 'Western Express Highway', 'icon' => 'fa-road', 'distance' => '1.2 km']],
                'units' => [
                    ['type' => 'Shop 600 sq.ft', 'bedrooms' => null, 'bathrooms' => 1, 'balconies' => null, 'carpet' => 600, 'super' => 790, 'price' => '1.40 Cr'],
                    ['type' => 'Office 1200 sq.ft', 'bedrooms' => null, 'bathrooms' => 2, 'balconies' => null, 'carpet' => 1200, 'super' => 1560, 'price' => '2.75 Cr'],
                ],
            ],
            [
                'title' => 'Palm Grove Apartments', 'slug' => 'palm-grove-apartments', 'developer' => 'Palm Grove Realty',
                'category' => 'Residential', 'price' => '68 Lakh', 'status' => 'Ready to move', 'possession' => '2026-04',
                'rera' => 'P52100089012', 'featured' => false, 'furnishing' => 'Semi Furnished', 'per_floor' => '4',
                'location' => 'Majiwada, Thane West', 'address' => 'Majiwada, Eastern Express Highway, Thane West', 'city' => 'Thane', 'state' => 'Maharashtra', 'zip' => '400601',
                'lat' => 19.2183, 'lng' => 72.9781, 'landmark' => 'Near Majiwada Junction',
                'description' => ['Ready 2 and 3 BHK apartments with modular kitchens and wardrobes included, in the heart of Thane West.', 'Close to Thane station, Viviana Mall and the Eastern Express Highway, with a landscaped courtyard.'],
                'key_features' => ['Modular kitchen and wardrobes included', 'Near Majiwada junction and Thane station', 'Landscaped courtyard'],
                'note' => 'Immediate possession available.',
                'features' => $basic, 'amenities' => array_slice($premium, 0, 6),
                'near' => ['800 m', '1.2 km', '1 km', '400 m', '5 km', '24 km'],
                'place_names' => [
                    'bazar' => 'Majiwada Metro Station',
                    'hospital' => 'Jupiter Hospital',
                    'school' => 'Vasant Vihar High School',
                    'bus_stand' => 'Majiwada Bus Stop',
                    'junction' => 'Thane Railway Station',
                    'airport' => 'Chhatrapati Shivaji Maharaj International Airport',
                ],
                'places' => [['group' => 'nearby', 'label' => 'Viviana Mall', 'icon' => 'fa-bag-shopping', 'distance' => '2 km'], ['group' => 'connectivity', 'label' => 'Eastern Express Highway', 'icon' => 'fa-road', 'distance' => '300 m']],
                'units' => [
                    ['type' => '2 BHK', 'bedrooms' => 2, 'bathrooms' => 2, 'balconies' => 2, 'carpet' => 700, 'super' => 980, 'price' => '68 Lakh'],
                    ['type' => '3 BHK', 'bedrooms' => 3, 'bathrooms' => 3, 'balconies' => 2, 'carpet' => 950, 'super' => 1290, 'price' => '92 Lakh'],
                ],
            ],
        ];
    }
}
