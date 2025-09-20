<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('safety_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trail_id')->constrained()->onDelete('cascade');
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->enum('incident_type', ['injury', 'weather', 'equipment', 'wildlife', 'other']);
            $table->enum('severity', ['low', 'medium', 'high', 'critical']);
            $table->text('description');
            $table->date('incident_date');
            $table->enum('resolution_status', ['pending', 'investigating', 'resolved', 'closed']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('safety_incidents');
    }
};
