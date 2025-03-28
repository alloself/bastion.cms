<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fileables', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuidMorphs('fileable');
            $table->enum('type', ['image', 'file']);
            $table->string('key')->nullable();
            $table->bigInteger('order')->default(0);

            $table->foreignUuid('file_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fileables');
    }
};
