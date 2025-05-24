<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up() : void
    {
        Schema::create('assessment_requests', function (Blueprint $table) {
            $table->id();
            $table->string('assessment_type');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('backend_guy_id')->nullable()->constrained('backend_guys')->onDelete('cascade');
            $table->foreignId('time_slot_id')->nullable()->constrained('time_slots')->onDelete('cascade');
            $table->float('rating')->nullable();
            $table->boolean('is_done')->default(false);
            $table->boolean('showed_up')->nullable()->default(false);
            $table->timestamps();
        });
    }

    public function down() : void
    {
        Schema::dropIfExists('assessment_requests');
    }
};