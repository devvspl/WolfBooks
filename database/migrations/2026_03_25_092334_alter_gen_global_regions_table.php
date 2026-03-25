<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::table('gen_global_regions', function (Blueprint $table) {
            $table->string('test')->nullable();        });
    }
    public function down(): void
    {
        Schema::table('gen_global_regions', function (Blueprint $table) {
            $table->dropColumn('test');
        });
    }
};
