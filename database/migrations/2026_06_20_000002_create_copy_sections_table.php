<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('copy_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('section_type');
            $table->string('title')->nullable();
            $table->string('headline')->nullable();
            $table->string('sub_headline')->nullable();
            $table->longText('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('status')->default('pending');
            $table->text('client_notes')->nullable();
            $table->timestamps();
            $table->index('job_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('copy_sections');
    }
};
