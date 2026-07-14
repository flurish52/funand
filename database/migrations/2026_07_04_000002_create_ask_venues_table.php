<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Optional venue choices for the scheduling step.
     * Kept as a real table (not JSON on "asks") so the recipient
     * can pick ONE from a clean list, and so venues can be added/removed
     * independently without rewriting the whole ask row.
     */
    public function up(): void
    {
        Schema::create('ask_venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ask_id')->constrained()->cascadeOnDelete();
            $table->string('name');        // e.g. "Cinema at The Palms"
            $table->string('address')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ask_venues');
    }
};
