<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('status')->default('open')->after('reason');
            $table->unsignedBigInteger('resolved_by')->nullable()->after('status');
            $table->text('resolution')->nullable()->after('resolved_by');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['status', 'resolved_by', 'resolution']);
        });
    }
};
