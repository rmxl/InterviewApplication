<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('url_table', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_request_id')->constrained('assessment_requests')->onDelete('cascade');
            $table->string('url');
            $table->string('channel'); // Adding channel field
            $table->string('token'); // Adding channel field
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('url_table');
    }
};

