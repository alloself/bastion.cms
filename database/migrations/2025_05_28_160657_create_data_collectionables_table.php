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
        Schema::create('data_collectionables', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('data_collectionable_id');
            $table->string('data_collectionable_type');
            $table->string('key')->nullable();
            $table->bigInteger('order')->default(0);
            $table->boolean('paginate')->default(false);

            $table->index(["data_collectionable_type", "data_collectionable_id"], 'dcs_dc_type_dc_id_index');
            $table->foreignUuid('data_collection_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_collectionables');
    }
};
