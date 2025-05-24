<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('text'); // The question title
            $table->text('body'); // The full question description
            $table->foreignId("jobType_Id")->constrained('jobs')->onDelete('cascade');
            $table->string('experience_level'); // The experience level of the question
            $table->timestamps(); // Created_at and Updated_at timestamps
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions'); // Drops the table if rolled back
    }
};
