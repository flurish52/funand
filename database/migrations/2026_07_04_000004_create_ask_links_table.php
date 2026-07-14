<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The shareable link for an ask.
     * "slug" is the user-typed part of the URL: mydomain.com/for-{slug}
     * "share_text" is the caption the owner writes to go along with the link
     * when they paste it into WhatsApp/Twitter/etc.
     *
     * Kept as its own table (rather than columns on "asks") so one ask can
     * later have more than one share link/caption if needed, without
     * touching the ask itself.
     */
    public function up(): void
    {
        Schema::create('ask_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ask_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique(); // the "for-slug" part of the URL
            $table->string('share_text')->nullable();
            $table->unsignedInteger('clicks')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ask_links');
    }
};
