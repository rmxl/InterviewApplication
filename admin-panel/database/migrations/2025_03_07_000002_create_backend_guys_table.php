<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('backend_guys', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->string('username')->unique(); // Username must be unique
            $table->string('password'); // Hashed password
            $table->timestamps(); // Created_at & Updated_at timestamps
        });
    }

    public function down() {
        Schema::dropIfExists('backend_guys'); // Drops the table if rolled back
    }
};

