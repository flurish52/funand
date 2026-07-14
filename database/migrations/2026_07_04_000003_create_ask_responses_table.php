<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stores what the recipient actually picked once they "accept".
     * Also keeps a fun little stat: how many times they tried to tap
     * the decline button before it vanished.
     */
    public function up(): void
    {
        Schema::create('ask_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ask_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ask_venue_id')->nullable()->constrained()->nullOnDelete();

            $table->date('responded_date')->nullable();
            $table->time('responded_time')->nullable();

            $table->unsignedInteger('decline_taps')->default(0);

            $table->timestamp('responded_at')->nullable();
            $table->string('ip_address')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ask_responses');
    }
};
