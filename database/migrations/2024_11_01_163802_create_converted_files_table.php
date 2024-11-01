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
        Schema::create('converted_files', function (Blueprint $table) {
            $table->id();
            $table->string('original_url');
            $table->string('file_name');
            $table->string('file_path');
            $table->enum('file_type', ['mp3', 'm4r']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('converted_files');
    }
};

