<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('monthly_statistics', function (Blueprint $table) {
            $table->id();
            $table->string('coin'); // Название монеты
            $table->date('date'); // Дата
            $table->decimal('value', 20, 8); // Значение
            $table->unsignedBigInteger('user_id'); // Добавляем поле user_id
            $table->timestamps(); // Временные метки

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Устанавливаем внешний ключ
        });
    }

    public function down()
    {
        Schema::dropIfExists('monthly_statistics');
    }
};
