<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('itinerary_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_id')->constrained()->onDelete('cascade');
            $table->integer('day_number');
            $table->enum('item_type', ['hotel', 'sightseeing', 'transfer']);
            $table->foreignId('hotel_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('sightseeing_id')->nullable()->constrained()->onDelete('set null');
            $table->text('notes')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('itinerary_items');
    }
};
