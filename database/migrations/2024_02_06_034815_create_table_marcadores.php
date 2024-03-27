<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipos_marcadores', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->timestamps();
        });
        
        Schema::create('marcadores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tipos_marcadores');
            $table->string('caractere');
            $table->string('marcador');
            $table->timestamps();

            $table->foreign('id_tipos_marcadores')->references('id')->on('tipos_marcadores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('marcadores');
        Schema::dropIfExists('tipos_marcadores');
    }
};
