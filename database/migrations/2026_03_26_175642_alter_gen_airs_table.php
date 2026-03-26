<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('gen_airs_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gen_air_id')->constrained('gen_airs')->onDelete('cascade');
            $table->integer('col')->nullable();
            $table->string('employee')->nullable();
            $table->string('emp_code')->nullable();
            $table->string('department')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::table('gen_airs', function (Blueprint $table) {
        });
    }
};
