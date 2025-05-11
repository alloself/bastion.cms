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
        Schema::create('data_entityables', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuidMorphs('data_entityable');
            $table->string('key')->nullable();
            $table->bigInteger('order')->default(0);
            $table->foreignUuid('data_entity_id')->nullable()->constrained();
            
            // Добавляем составной индекс для быстрой выборки
            $table->index(['data_entityable_id', 'data_entityable_type', 'order'], 'entity_relation_idx');
            
            // Индекс для сортировки по order
            $table->index('order');
            
            // Индекс для поиска по key
            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_entityables');
    }
};
