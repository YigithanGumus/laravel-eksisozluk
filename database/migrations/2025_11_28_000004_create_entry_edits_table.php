<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entry_edits', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('entry_id')->constrained('entries')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('content_before');
            $table->text('content_after');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entry_edits');
    }
};
