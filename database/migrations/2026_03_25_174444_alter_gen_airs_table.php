<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::table('gen_airs', function (Blueprint $table) {
            $table->string('ticket_number')->nullable();            $table->dropColumn('ticket_number:');
        });
    }
    public function down(): void
    {
        Schema::table('gen_airs', function (Blueprint $table) {
            $table->string('ticket_number:')->nullable();
            $table->dropColumn('ticket_number');
        });
    }
};
