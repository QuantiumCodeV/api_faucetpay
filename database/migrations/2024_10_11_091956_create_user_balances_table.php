<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('currency_id')->constrained()->onDelete('cascade');
            $table->decimal('balance', 20, 8)->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'currency_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_balances');
    }
};