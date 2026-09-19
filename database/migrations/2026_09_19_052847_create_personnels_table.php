<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('personnels', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('current_committee')->index(); // current_committee, past_president, ratchaburi_lawyer
            $table->string('name');
            $table->string('position')->nullable();
            $table->string('term')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('license_no')->nullable();
            $table->string('office_name')->nullable();
            $table->string('image_path')->nullable();
            $table->text('bio')->nullable();
            $table->integer('order_column')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnels');
    }
};
