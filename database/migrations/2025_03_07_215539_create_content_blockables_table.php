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
        Schema::create('content_blockables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_block_id')->constrained()->cascadeOnDelete();
            $table->morphs('content_blockable');
            $table->integer('order')->default(0);
            $table->string('key')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_blockables');
    }
};
