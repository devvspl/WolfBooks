<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('gen_airs')) return;
        Schema::create('gen_airs', function (Blueprint $table) {
            $table->id();
            $table->string('mode')->nullable()->default('Air');
            $table->string('agent_name')->nullable();
            $table->string('p_n_r_number')->nullable();
            $table->date('date_of_booking')->nullable();
            $table->date('journey_date')->nullable();
            $table->string('air_line')->nullable();
            $table->string('ticket_number')->nullable();
            $table->string('journey_from')->nullable();
            $table->string('journey_upto')->nullable();
            $table->string('travel_class')->nullable();
            $table->string('location')->nullable();
            $table->json('items')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('gen_airs'); }
};
