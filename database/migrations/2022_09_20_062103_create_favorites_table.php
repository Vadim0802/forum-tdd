<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('favorited_id');
            $table->string('favorited_type');
            $table->timestamps();

            $table->unique(['user_id', 'favorited_id', 'favorited_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
