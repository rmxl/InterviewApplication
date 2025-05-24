<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up() : void
    {
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('backend_guy_id')->constrained('backend_guys')->onDelete('cascade');
            $table->date('date');
            $table->time('start_time');
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down() : void
    {
        Schema::dropIfExists('time_slots');
    }
};