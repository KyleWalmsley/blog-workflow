<?php

namespace Database\Seeders;

use App\Enums\BlogStatus;
use App\Enums\ClientStatus;
use App\Enums\JobStatus;
use App\Enums\NotificationType;
use App\Models\AdminNotification;
use App\Models\Blog;
use App\Models\Client;
use App\Models\EmailTemplate;
use App\Models\Job;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        EmailTemplate::firstOrCreate(
            ['name' => 'review_invitation'],
            [
                'label' => 'Review Invitation',
                'subject' => 'Your Blog Content Is Ready For Review',
                'body' => '',
            ]
        );

        $client = Client::create([
            'name' => 'Lumina Coffee Roasters',
            'email' => 'hello@luminacoffee.example.com',
            'website' => 'https://luminacoffee.example.com',
            'business_description' => 'Specialty coffee roaster serving the Pacific Northwest with ethically sourced beans and expert brewing guidance.',
            'primary_keywords' => 'specialty coffee, coffee roaster, single origin',
            'secondary_keywords' => 'pour over, espresso, cold brew',
            'target_locations' => 'Portland, Seattle, Vancouver',
            'target_audience' => 'Coffee enthusiasts, home brewers, café owners',
            'tone_of_voice' => 'Warm, knowledgeable, approachable',
            'status' => ClientStatus::Active,
            'notes' => 'Demo client for workflow testing.',
        ]);

        $draftJob = Job::create([
            'client_id' => $client->id,
            'title' => 'March 2026 Blog Batch',
            'status' => JobStatus::Draft,
        ]);

        Blog::create([
            'job_id' => $draftJob->id,
            'sort_order' => 0,
            'title' => 'How to Brew the Perfect Pour Over at Home',
            'content' => '<p>Pour over coffee is one of the most rewarding brewing methods for home enthusiasts. With the right technique, you can extract bright, complex flavours that showcase your beans\' origin characteristics.</p><h2>What You Need</h2><p>Start with freshly ground coffee, a gooseneck kettle, and a quality filter. Water temperature between 92–96°C works best for most single-origin beans.</p><h2>The Technique</h2><p>Bloom your grounds for 30 seconds, then pour in slow, concentric circles. Total brew time should land between 3 and 4 minutes for a standard 250ml cup.</p>',
            'meta_title' => 'Perfect Pour Over Guide | Lumina Coffee',
            'meta_description' => 'Learn how to brew pour over coffee at home with Lumina Coffee\'s step-by-step guide.',
            'focus_keyword' => 'pour over coffee',
            'focus_location' => 'Portland',
        ]);

        Blog::create([
            'job_id' => $draftJob->id,
            'sort_order' => 1,
            'title' => 'Understanding Coffee Origins: Ethiopia vs Colombia',
            'content' => '<p>Ethiopian and Colombian coffees represent two of the world\'s most beloved origin profiles. Understanding their differences helps you choose beans that match your palate.</p><h2>Ethiopian Profiles</h2><p>Expect floral aromatics, bergamot, and bright acidity — especially from Yirgacheffe and Sidamo regions.</p><h2>Colombian Profiles</h2><p>Colombian beans typically offer balanced sweetness, caramel notes, and a smooth body that works beautifully in espresso.</p>',
            'meta_title' => 'Ethiopia vs Colombia Coffee | Lumina Coffee',
            'meta_description' => 'Compare Ethiopian and Colombian coffee origins to find your perfect cup.',
            'focus_keyword' => 'coffee origins',
            'focus_location' => 'Seattle',
        ]);

        $reviewJob = Job::create([
            'client_id' => $client->id,
            'title' => 'February 2026 Blog Batch',
            'status' => JobStatus::InReview,
        ]);

        Blog::create([
            'job_id' => $reviewJob->id,
            'sort_order' => 0,
            'title' => '5 Signs Your Espresso Machine Needs Descaling',
            'content' => '<p>Regular descaling keeps your espresso machine performing at its best. Here are five telltale signs that mineral buildup is affecting your shots.</p><p>Slower extraction times, inconsistent pressure, and unusual tastes in your espresso are all red flags worth addressing promptly.</p>',
            'meta_title' => 'Espresso Machine Descaling Signs',
            'meta_description' => 'Know when your espresso machine needs descaling with these five expert tips.',
            'focus_keyword' => 'espresso machine descaling',
            'focus_location' => 'Vancouver',
            'status' => BlogStatus::Pending,
        ]);

        Blog::create([
            'job_id' => $reviewJob->id,
            'sort_order' => 1,
            'title' => 'Cold Brew vs Iced Coffee: What\'s the Difference?',
            'content' => '<p>While both are served cold, cold brew and iced coffee are fundamentally different drinks with distinct flavour profiles and brewing methods.</p><p>Cold brew steeps coarsely ground coffee in cold water for 12–18 hours, producing a smooth, low-acid concentrate.</p>',
            'meta_title' => 'Cold Brew vs Iced Coffee',
            'meta_description' => 'Learn the difference between cold brew and iced coffee.',
            'focus_keyword' => 'cold brew',
            'focus_location' => 'Portland',
            'status' => BlogStatus::Approved,
        ]);

        AdminNotification::create([
            'type' => NotificationType::ReviewSubmitted,
            'message' => 'Client "Lumina Coffee Roasters" submitted a review for "February 2026 Blog Batch".',
            'job_id' => $reviewJob->id,
        ]);
    }
}
