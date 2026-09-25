<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyDetail;
use App\Models\PropertyInquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    private const PRIMARY = 'generativelanguage.googleapis.com/v1beta/models/gemini-a:*';
    private const BACKUP = 'generativelanguage.googleapis.com/v1beta/models/gemini-b:*';

    protected function setUp(): void
    {
        parent::setUp();

        Sleep::fake();
        config([
            'chatbot.enabled' => true,
            'chatbot.gemini.key' => 'test-key',
            'chatbot.gemini.models' => ['gemini-a', 'gemini-b'],
            'chatbot.daily_limit' => 1000,
            'chatbot.per_ip_per_minute' => 100,
        ]);
    }

    private function property(array $overrides = []): Property
    {
        $owner = User::factory()->create(['role' => 'admin']);

        return Property::create($overrides + [
            'user_id' => $owner->id,
            'title' => 'Palm Grove',
            'slug' => 'palm-grove-' . uniqid(),
            'description' => 'Nice place',
            'price' => '68 Lakh',
            'city' => 'Thane',
            'location' => 'Majiwada',
            'category' => 'Residential',
            'project_status' => 'Ready to move',
            'bedrooms' => 2,
            'is_active' => true,
        ]);
    }

    private static function text(string $text): array
    {
        return ['candidates' => [['content' => ['role' => 'model', 'parts' => [['text' => $text]]], 'finishReason' => 'STOP']]];
    }

    private static function fnCall(string $name, array $args): array
    {
        return ['candidates' => [['content' => ['role' => 'model', 'parts' => [
            ['functionCall' => ['name' => $name, 'args' => $args], 'thoughtSignature' => 'sig123'],
        ]]]]];
    }

    private function ask(string $text = 'Hello', array $extra = [])
    {
        return $this->postJson('/chatbot', ['messages' => [['role' => 'user', 'text' => $text]]] + $extra);
    }

    public function test_plain_answer(): void
    {
        Http::fake([self::PRIMARY => Http::response(self::text('**Hi** there!'))]);

        $this->ask()->assertOk()->assertExactJson(['reply' => 'Hi there!', 'mode' => 'ai']);

        Http::assertSent(fn (HttpRequest $r) => $r->hasHeader('x-goog-api-key', 'test-key')
            && $r['contents'][0]['parts'][0]['text'] === 'Hello'
            && str_contains($r['systemInstruction']['parts'][0]['text'], 'Homax Homes'));
    }

    public function test_search_tool_reads_the_live_catalog(): void
    {
        $match = $this->property();
        PropertyDetail::create(['property_id' => $match->id, 'bedrooms' => 3, 'price' => '95 Lakh']);
        $this->property(['title' => 'Sea Tower', 'city' => 'Mumbai', 'price' => '3 Cr']);
        $this->property(['title' => 'Hidden', 'is_active' => false]);

        Http::fake([self::PRIMARY => Http::sequence()
            ->push(self::fnCall('search_properties', ['city' => 'thane', 'budget_max' => 8000000]))
            ->push(self::text('Palm Grove fits.'))]);

        $this->ask('Homes in Thane under 80 lakh')->assertOk()->assertJson(['reply' => 'Palm Grove fits.']);

        $second = Http::recorded()[1][0];
        $contents = $second['contents'];
        // Model turn echoed back with its thought signature, then the function result.
        $this->assertSame('sig123', $contents[1]['parts'][0]['thoughtSignature']);
        $result = $contents[2]['parts'][0]['functionResponse']['response'];
        $this->assertSame(1, $result['total_matches']);
        $this->assertSame('Palm Grove', $result['projects'][0]['name']);
        $this->assertSame('₹68 Lakh - ₹95 Lakh', $result['projects'][0]['price']);
        $this->assertSame(['2 BHK', '3 BHK'], $result['projects'][0]['configurations']);
        $this->assertStringContainsString('/property/' . $match->id, $result['projects'][0]['url']);
    }

    public function test_details_tool_hides_inactive_projects(): void
    {
        $hidden = $this->property(['is_active' => false]);

        Http::fake([self::PRIMARY => Http::sequence()
            ->push(self::fnCall('get_property_details', ['property_id' => $hidden->id]))
            ->push(self::text('Not found.'))]);

        $this->ask()->assertOk();

        $response = Http::recorded()[1][0]['contents'][2]['parts'][0]['functionResponse']['response'];
        $this->assertArrayHasKey('error', $response);
    }

    public function test_callback_tool_saves_one_lead(): void
    {
        $p = $this->property();
        $args = ['name' => 'Ravi', 'phone' => '+91 98765-43210', 'property_id' => $p->id, 'note' => '2 BHK'];

        Http::fake([self::PRIMARY => Http::sequence()
            ->push(self::fnCall('request_callback', $args))->push(self::text('Saved.'))
            ->push(self::fnCall('request_callback', $args))->push(self::text('Saved.'))]);

        $this->ask('Call me')->assertOk();
        $this->ask('Call me again')->assertOk();

        $this->assertSame(1, PropertyInquiry::count());
        $this->assertDatabaseHas('property_inquiries', [
            'intent' => 'chatbot', 'source' => 'Chatbot', 'phone' => '+919876543210',
            'property_id' => $p->id, 'property_title' => 'Palm Grove',
        ]);
    }

    public function test_callback_tool_rejects_a_bad_phone(): void
    {
        Http::fake([self::PRIMARY => Http::sequence()
            ->push(self::fnCall('request_callback', ['name' => 'Ravi', 'phone' => '123']))
            ->push(self::text('Please check the number.'))]);

        $this->ask()->assertOk();

        $this->assertSame(0, PropertyInquiry::count());
    }

    public function test_rate_limited_model_falls_back_to_the_next_and_cools_down(): void
    {
        Http::fake([
            self::PRIMARY => Http::response(['error' => ['code' => 429, 'details' => [
                ['@type' => 'type.googleapis.com/google.rpc.RetryInfo', 'retryDelay' => '42s'],
            ]]], 429),
            self::BACKUP => Http::response(self::text('From backup.')),
        ]);

        $this->ask()->assertJson(['reply' => 'From backup.', 'mode' => 'ai']);
        $this->ask()->assertJson(['reply' => 'From backup.']);

        // The primary was only tried once; the second message skipped it.
        Http::assertSentCount(3);
        $this->assertTrue(Cache::has('chatbot.gemini.cooldown.gemini-a'));
    }

    public function test_server_error_is_retried_once(): void
    {
        Http::fake([self::PRIMARY => Http::sequence()
            ->push('oops', 503)
            ->push(self::text('Second try.'))]);

        $this->ask()->assertJson(['reply' => 'Second try.']);
    }

    public function test_outage_returns_the_contact_fallback(): void
    {
        Http::fake(fn () => throw new ConnectionException('timed out'));

        $this->property();

        $this->ask('2 bhk in thane')->assertOk()
            ->assertJson(['mode' => 'offline'])
            ->assertJsonPath('reply', fn ($r) => str_contains($r, 'Palm Grove'));
    }

    public function test_rejected_key_stops_calling_gemini(): void
    {
        Http::fake([self::PRIMARY => Http::response(['error' => ['code' => 403]], 403)]);

        $this->ask()->assertJson(['mode' => 'offline']);
        $this->ask()->assertJson(['mode' => 'offline']);

        Http::assertSentCount(1);
    }

    public function test_safety_block_gets_a_polite_answer(): void
    {
        Http::fake([self::PRIMARY => Http::response(['promptFeedback' => ['blockReason' => 'SAFETY']])]);

        $this->ask()->assertJson(['mode' => 'ai'])
            ->assertJsonPath('reply', fn ($r) => str_contains($r, "can't help"));
    }

    public function test_endless_tool_calls_are_cut_off(): void
    {
        config(['chatbot.max_tool_rounds' => 2]);
        Http::fake([self::PRIMARY => Http::sequence()
            ->push(self::fnCall('list_locations', []))
            ->push(self::fnCall('list_locations', []))
            ->push(self::text('Final.'))]);

        $this->ask()->assertJson(['reply' => 'Final.']);

        $this->assertSame('NONE', Http::recorded()[2][0]['toolConfig']['functionCallingConfig']['mode']);
    }

    public function test_daily_limit_and_missing_key_skip_gemini(): void
    {
        Http::fake();

        config(['chatbot.daily_limit' => 0]);
        $this->ask()->assertJson(['mode' => 'offline']);

        config(['chatbot.daily_limit' => 1000, 'chatbot.gemini.key' => null]);
        $this->ask()->assertJson(['mode' => 'offline']);

        Http::assertNothingSent();
    }

    public function test_current_project_is_passed_to_the_model(): void
    {
        $p = $this->property();
        Http::fake([self::PRIMARY => Http::response(self::text('ok'))]);

        $this->ask('Tell me about this project', ['page' => '/property/' . $p->id]);

        Http::assertSent(fn (HttpRequest $r) => str_contains(
            $r['systemInstruction']['parts'][0]['text'],
            "project id {$p->id} (Palm Grove)"
        ));
    }

    public function test_invalid_conversations_are_rejected(): void
    {
        Http::fake();

        $this->postJson('/chatbot', [])->assertStatus(422);
        $this->postJson('/chatbot', ['messages' => [['role' => 'model', 'text' => 'hi']]])->assertStatus(422);
        $this->postJson('/chatbot', ['messages' => [['role' => 'user', 'text' => str_repeat('a', 1001)]]])->assertStatus(422);

        Http::assertNothingSent();
    }

    public function test_widget_shows_with_or_without_a_key(): void
    {
        $this->get('/contact')->assertSee('hx-chat-btn', false);

        config(['chatbot.gemini.key' => null]);
        $this->get('/contact')->assertSee('hx-chat-btn', false);

        config(['chatbot.enabled' => false]);
        $this->get('/contact')->assertDontSee('hx-chat-btn', false);
    }

    public function test_offline_mode_answers_from_live_listings(): void
    {
        config(['chatbot.gemini.key' => null]);
        Http::fake();
        $p = $this->property();
        PropertyDetail::create(['property_id' => $p->id, 'bedrooms' => 3, 'price' => '95 Lakh']);
        $this->property(['title' => 'Sea Tower', 'city' => 'Mumbai', 'price' => '3 Cr', 'category' => 'Commercial']);

        $reply = fn ($text, $extra = []) => $this->ask($text, $extra)->assertOk()->assertJson(['mode' => 'offline'])->json('reply');

        $r = $reply('2 BHK in Thane under 80 lakh');
        $this->assertStringContainsString('Palm Grove', $r);
        $this->assertStringNotContainsString('Sea Tower', $r);

        $this->assertStringContainsString('Sea Tower', $reply('commercial projects'));
        $this->assertStringNotContainsString('Palm Grove', $reply('between 1 cr and 5 cr'));
        $this->assertStringContainsString('Palm Grove', $reply('ready to move homes'));
        $this->assertStringContainsString('Thane', $reply('Which cities do you cover?'));
        $this->assertStringContainsString(config('chatbot.contact.phone'), $reply('Request a callback'));
        $this->assertStringContainsString('Try asking', $reply('hello'));
        $this->assertStringContainsString("couldn't find", $reply('3 bhk in mumbai'));
        $this->assertStringContainsString('3 BHK', $reply('Tell me about this project', ['page' => '/property/' . $p->id]));
        // A project name typed on its own.
        $this->assertStringContainsString('Sea Tower', $reply('sea tower'));

        Http::assertNothingSent();
    }
}
