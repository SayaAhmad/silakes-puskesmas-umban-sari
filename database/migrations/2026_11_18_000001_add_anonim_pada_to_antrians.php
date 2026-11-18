<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('antrians', function (Blueprint $table) {
            $table->timestamp('anonim_pada')->nullable()->after('selesai_at');
            $table->index(['tanggal', 'anonim_pada']);
        });
    }

    public function down(): void
    {
        Schema::table('antrians', function (Blueprint $table) {
            $table->dropIndex(['tanggal', 'anonim_pada']);
            $table->dropColumn('anonim_pada');
        });
    }
};
