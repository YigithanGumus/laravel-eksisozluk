<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('titles', function (Blueprint $table) {
            $table->text('lock_reason')->nullable()->after('is_locked');
            $table->text('pin_reason')->nullable()->after('is_pinned');
        });

        Schema::table('entries', function (Blueprint $table) {
            $table->text('lock_reason')->nullable()->after('is_locked');
            $table->text('pin_reason')->nullable()->after('is_pinned');
        });
    }

    public function down(): void
    {
        Schema::table('titles', function (Blueprint $table) {
            $table->dropColumn(['lock_reason', 'pin_reason']);
        });

        Schema::table('entries', function (Blueprint $table) {
            $table->dropColumn(['lock_reason', 'pin_reason']);
        });
    }
};
