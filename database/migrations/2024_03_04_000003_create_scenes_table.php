<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('scenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('character_scene', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_id')->constrained()->onDelete('cascade');
            $table->foreignId('scene_id')->constrained()->onDelete('cascade');
            $table->text('dialogue')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('character_scene');
        Schema::dropIfExists('scenes');
    }
}; 