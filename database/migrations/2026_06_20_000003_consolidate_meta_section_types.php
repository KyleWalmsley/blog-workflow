<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('copy_sections')->where('section_type', 'homepage_meta')
            ->update(['section_type' => 'meta', 'title' => 'Homepage']);

        DB::table('copy_sections')->where('section_type', 'news_page_meta')
            ->update(['section_type' => 'meta', 'title' => 'News Page']);

        DB::table('copy_sections')->where('section_type', 'contact_page_meta')
            ->update(['section_type' => 'meta', 'title' => 'Contact Page']);
    }

    public function down(): void
    {
        // Not reversible without knowing the original page values
    }
};
