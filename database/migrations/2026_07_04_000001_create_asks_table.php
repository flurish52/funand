<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The "asks" table is the core entity of the app.
     * One row = one "interface" a user builds (e.g. "For Agnes", "For My Crush").
     * Everything shown on the 3-step public page is driven from this row
     * (plus its related venues, response and link records).
     */
    public function up(): void
    {
        Schema::create('asks', function (Blueprint $table) {
            $table->id();

            // Owner of this ask (the person who built it from the dashboard)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Who this ask is for, e.g. "My Crush", "Agnes", "My Husband"
            $table->string('for_name');

            // Loose category, purely for sensible defaults in the UI (icons/copy).
            // Everything else stays fully editable regardless of type.
            $table->enum('type', ['date', 'money', 'custom'])->default('custom');

            // --- Step 1: Welcome / Request ---
            $table->string('cover_image')->nullable(); // storage path
            $table->text('question'); // e.g. "Would you go on a date with me?"
            $table->string('accept_label')->default('Yes');   // e.g. "Now"
            $table->string('decline_label')->default('No');   // e.g. "I don't know" (never clickable)

            // --- Step 2: Date, time & venue (optional step) ---
            $table->boolean('has_scheduling_step')->default(true);
            $table->string('scheduling_heading')->nullable(); // e.g. "Pick a date & place"
            $table->date('min_date')->nullable(); // earliest selectable date
            $table->date('max_date')->nullable(); // latest selectable date (prevents "infinity" picks)

            // --- Step 3: Final message ---
            $table->string('final_heading')->nullable(); // e.g. "Hurray!"
            $table->text('final_message')->nullable();    // e.g. "I knew you would say yes!"

            // Visibility toggle, only published asks are publicly reachable
            $table->boolean('is_published')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asks');
    }
};
