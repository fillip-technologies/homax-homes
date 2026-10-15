<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Database\Seeder;

/** Sample articles so a fresh install has something on /blog. Run: php artisan db:seed --class=BlogSeeder */
class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $img = fn (string $id) => "https://images.unsplash.com/{$id}?auto=format&fit=crop&w=1400&q=75";

        $body = <<<'HTML'
<p>The property market is moving faster than ever, and buyers who understand the fundamentals are the ones who come out ahead. In this article we break down what matters most and how to apply it to your own decisions.</p>
<h2>Start with the numbers</h2>
<p>Before you fall in love with a project, look at the carpet area, the price per square foot, the maintenance costs and the expected possession date. These four figures tell you far more than a glossy brochure ever will.</p>
<blockquote>A good property is not the one that looks best on day one, but the one that still makes sense after ten years.</blockquote>
<figure><img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=1400&q=75" alt="Modern residential tower"><figcaption>Always compare the brochure with the actual site before booking.</figcaption></figure>
<h2>Check the neighbourhood</h2>
<p>Connectivity, schools, hospitals and upcoming infrastructure shape long-term appreciation. Visit the area at different times of the day and talk to people who already live there.</p>
<ul><li>Distance to metro, highway and airport</li><li>Quality of nearby schools and hospitals</li><li>Planned infrastructure and commercial development</li><li>Safety, water supply and power backup</li></ul>
<h2>Verify the paperwork</h2>
<p>Always confirm RERA registration, land title and approvals with a legal professional. A few days of diligence can save years of trouble.</p>
HTML;

        $rows = [
            ['top-5-real-estate-trends', 'Top 5 Real Estate Trends to Watch This Year', 'Discover the emerging trends shaping the market and how they might affect your next investment.', 'trends', '2026-09-28', 'photo-1600585154340-be6161a56a0c', true],
            ['first-time-home-buyer-tips', '10 Essential Tips for First-Time Home Buyers', 'Buying a home can be overwhelming. These ten tips help you make confident, informed decisions.', 'buying-guide', '2026-09-18', 'photo-1605276374104-dee2a0ed3cd6', false],
            ['real-estate-investment-guide', 'The Ultimate Guide to Real Estate Investment', 'Maximise your returns with a clear walkthrough of investment strategies for the current market.', 'investment', '2026-09-05', 'photo-1605146769289-440113cc3d00', false],
            ['commercial-vs-residential', 'Commercial vs Residential: Where Should You Invest?', 'A side-by-side look at yields, risk and liquidity to help you pick the right asset class.', 'investment', '2026-08-22', 'photo-1486406146926-c627a92ad1ab', false],
            ['read-builder-brochure', 'How to Read a Builder’s Brochure Like a Pro', 'Carpet area, super area, possession dates: what the numbers really mean before you sign.', 'buying-guide', '2026-08-10', 'photo-1560518883-ce09059eeffa', false],
            ['home-loan-basics', 'Home Loan Basics: Getting the Best Rate', 'Understand eligibility, credit scores and the small print that decides your EMI.', 'finance', '2026-07-30', 'photo-1554224155-6726b3ff858f', false],
            ['vastu-essentials', 'Vastu Essentials for a Happier Home', 'Simple layout and orientation principles to consider when choosing your next flat.', 'lifestyle', '2026-07-15', 'photo-1600210492486-724fe5c67fb0', false],
        ];

        foreach ($rows as [$slug, $title, $excerpt, $category, $date, $image, $featured]) {
            BlogPost::updateOrCreate(['slug' => $slug], [
                'title' => $title,
                'excerpt' => $excerpt,
                'body' => $body,
                'featured_image' => $img($image),
                'featured_image_alt' => $title,
                'blog_category_id' => BlogCategory::where('slug', $category)->value('id'),
                'author_name' => 'Homax Editorial',
                'status' => 'published',
                'is_featured' => $featured,
                'published_at' => $date . ' 10:00:00',
                'read_time' => BlogPost::readTimeFor($body),
            ]);
        }
    }
}
