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
        Schema::create('attributeables', function (Blueprint $table) {
            $table->uuidMorphs('attributeable');
            $table->longText('value')->nullable();
            $table->bigInteger('order')->default(0);
            $table->foreignUuid('attribute_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributeables');
    }
};
