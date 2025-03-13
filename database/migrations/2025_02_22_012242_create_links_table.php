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
        Schema::create('links', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('title');
            $table->longText('subtitle')->nullable();
            $table->string('slug');
            $table->string('url')->nullable();
            $table->uuidMorphs('linkable');
            $table->softDeletes();
            $table->timestamps();

            $table->index('title', 'title_index');
            $table->index('url', 'url_index');
            $table->index(['linkable_id', 'linkable_type'], 'linkable_index');
            $table->fullText(['title', 'url'], 'fulltext_title_url');
            $table->unique(['title', 'linkable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
